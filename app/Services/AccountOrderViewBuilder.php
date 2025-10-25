<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\GlobalHelper;

class AccountOrderViewBuilder
{
    public function build(\App\Models\Order $order): array
    {
        $addrText = $this->buildAddressText($order);
        $shipmentData = $this->buildShipmentStages($order);
        $itemRows = $this->buildItemRows($order);
        $subtotal = $order->subtotal ?? $order->total - ($order->shipping_price ?? 0);
        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currencySymbol = $currencyContext['currencySymbol'];
        $displaySubtotal = GlobalHelper::convertCurrency($subtotal, $defaultCurrency, $currentCurrency, 2);
        $displayTotal = GlobalHelper::convertCurrency($order->total, $defaultCurrency, $currentCurrency, 2);
        $displayShipping = $order->shipping_price ? GlobalHelper::convertCurrency($order->shipping_price, $defaultCurrency, $currentCurrency, 2) : null;
        $displayTax = $order->tax_amount ? GlobalHelper::convertCurrency($order->tax_amount, $defaultCurrency, $currentCurrency, 2) : null;

        return [
            'addrText' => $addrText,
            'stages' => $shipmentData['stages'],
            'current' => $shipmentData['current'],
            'reached' => $shipmentData['reached'],
            'itemRows' => $itemRows,
            'subtotal' => $displaySubtotal,
            'total' => $displayTotal,
            'shipping_price' => $displayShipping,
            'tax_amount' => $displayTax,
            'currency_symbol' => $currencySymbol,
        ];
    }

    private function buildAddressText(\App\Models\Order $order): string
    {
        $addrSource = $order->shipping_address ?? $order->billing_address ?? $order->address;
        if (! is_array($addrSource)) {
            return (string) ($addrSource ?? '');
        }

        try {
            $this->resolveCountry($addrSource);
            $this->resolveGovernorate($addrSource);
            $this->resolveCity($addrSource);
        } catch (\Throwable $e) {
            logger()->warning('Failed to resolve address components: ' . $e->getMessage());
        }

        // Build text from array
        $orderedKeys = [
            'name',
            'title',
            'line1',
            'line2',
            'city',
            'governorate',
            'postal_code',
            'country',
            'phone',
        ];
        $parts = collect($orderedKeys)
            ->filter(fn($k) => ! empty($addrSource[$k]))
            ->map(fn($k) => $addrSource[$k])
            ->toArray();

        $extraParts = collect($addrSource)
            ->filter(fn($v) => is_scalar($v) && ! in_array($v, $parts, true))
            ->values()
            ->toArray();

        return implode("\n", array_merge($parts, $extraParts));
    }

    private function resolveCountry(array &$addrSource): void
    {
        $countryId = $addrSource['country_id'] ?? $addrSource['country'] ?? null;
        if (! $countryId || ! is_numeric($countryId)) {
            return;
        }
        $c = \App\Models\Country::find($countryId);
        if (! $c) {
            return;
        }
        $addrSource['country'] = $c->name;
        $addrSource['country_id'] = $countryId;
    }

    private function resolveGovernorate(array &$addrSource): void
    {
        $govId = $addrSource['governorate_id'] ?? $addrSource['governorate'] ?? null;
        if (! $govId || ! is_numeric($govId)) {
            return;
        }
        $g = \App\Models\Governorate::find($govId);
        if (! $g) {
            return;
        }
        $addrSource['governorate'] = $g->name;
        $addrSource['governorate_id'] = $govId;
    }

    private function resolveCity(array &$addrSource): void
    {
        $cityId = $addrSource['city_id'] ?? $addrSource['city'] ?? null;
        if (! $cityId || ! is_numeric($cityId)) {
            return;
        }
        $ci = \App\Models\City::find($cityId);
        if (! $ci) {
            return;
        }
        $addrSource['city'] = $ci->name;
        $addrSource['city_id'] = $cityId;
    }

    private function buildShipmentStages(\App\Models\Order $order): array
    {
        $stages = [
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'refunded' => __('Refunded'),
        ];
        $current = $order->status;
        $reached = [];
        if (in_array($current, ['cancelled', 'refunded'], true)) {
            $reached = [$current];
        } else {
            foreach (['pending', 'processing', 'completed'] as $linear) {
                $reached[] = $linear;
                if ($linear === $current) {
                    break;
                }
            }
        }

        return compact('stages', 'current', 'reached');
    }

    private function buildItemRows(\App\Models\Order $order): array
    {
        $itemRows = [];
        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        foreach ($order->items as $it) {
            $variantLabel = $this->buildItemVariantLabel($it);
            $displayPrice = GlobalHelper::convertCurrency($it->price, $defaultCurrency, $currentCurrency, 2);
            $itemRows[] = [
                'name' => $it->name,
                'variant_label' => $variantLabel,
                'qty' => $it->qty,
                'price' => $displayPrice,
            ];
        }

        return $itemRows;
    }

    private function buildItemVariantLabel($it): ?string
    {
        if (! is_array($it->meta)) {
            return null;
        }

        if (! empty($it->meta['variant_name'])) {
            return $it->meta['variant_name'];
        }

        if (! empty($it->meta['attribute_data']) && is_array($it->meta['attribute_data'])) {
            return collect($it->meta['attribute_data'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;

class AccountOrderViewBuilder
{
    public function build(Order $order): array
    {
        $addrText = $this->buildAddressText($order);
        $shipmentData = $this->buildShipmentStages($order);
        $itemRows = $this->buildItemRows($order);
        $subtotal = $order->subtotal ?? $order->total - ($order->shipping_price ?? 0);

        return [
            'addrText' => $addrText,
            'stages' => $shipmentData['stages'],
            'current' => $shipmentData['current'],
            'reached' => $shipmentData['reached'],
            'itemRows' => $itemRows,
            'subtotal' => $subtotal,
        ];
    }

    private function buildAddressText(Order $order): string
    {
        $addrSource = $order->shipping_address ?? $order->billing_address ?? $order->address;
        if (!is_array($addrSource)) {
            return (string) ($addrSource ?? '');
        }

        try {
            $this->resolveAddressComponents($addrSource);
        } catch (\Throwable $e) {
            logger()->warning('Failed to resolve address components: ' . $e->getMessage());
        }

        return $this->buildAddressTextFromArray($addrSource);
    }

    private function resolveAddressComponents(array &$addrSource): void
    {
        $this->resolveCountry($addrSource);
        $this->resolveGovernorate($addrSource);
        $this->resolveCity($addrSource);
    }

    private function resolveCountry(array &$addrSource): void
    {
        $countryId = $addrSource['country_id'] ?? $addrSource['country'] ?? null;
        if (!$countryId || !is_numeric($countryId)) {
            return;
        }

        $c = Country::find($countryId);
        if ($c) {
            $addrSource['country'] = $c->name;
            $addrSource['country_id'] = $countryId;
        }
    }

    private function resolveGovernorate(array &$addrSource): void
    {
        $govId = $addrSource['governorate_id'] ?? $addrSource['governorate'] ?? null;
        if (!$govId || !is_numeric($govId)) {
            return;
        }

        $g = Governorate::find($govId);
        if ($g) {
            $addrSource['governorate'] = $g->name;
            $addrSource['governorate_id'] = $govId;
        }
    }

    private function resolveCity(array &$addrSource): void
    {
        $cityId = $addrSource['city_id'] ?? $addrSource['city'] ?? null;
        if (!$cityId || !is_numeric($cityId)) {
            return;
        }

        $ci = City::find($cityId);
        if ($ci) {
            $addrSource['city'] = $ci->name;
            $addrSource['city_id'] = $cityId;
        }
    }

    private function buildAddressTextFromArray(array $addrSource): string
    {
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
            ->filter(fn($k) => !empty($addrSource[$k]))
            ->map(fn($k) => $addrSource[$k])
            ->toArray();

        $extraParts = collect($addrSource)
            ->filter(fn($v) => is_scalar($v) && !in_array($v, $parts, true))
            ->values()
            ->toArray();

        return implode("\n", array_merge($parts, $extraParts));
    }

    private function buildShipmentStages(Order $order): array
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

    private function buildItemRows(Order $order): array
    {
        $itemRows = [];
        foreach ($order->items as $it) {
            $variantLabel = $this->buildItemVariantLabel($it);
            $itemRows[] = [
                'name' => $it->name,
                'variant_label' => $variantLabel,
                'qty' => $it->qty,
                'price' => $it->price,
            ];
        }

        return $itemRows;
    }

    private function buildItemVariantLabel($it): ?string
    {
        if (!is_array($it->meta)) {
            return null;
        }

        if (!empty($it->meta['variant_name'])) {
            return $it->meta['variant_name'];
        }

        if (!empty($it->meta['attribute_data']) && is_array($it->meta['attribute_data'])) {
            return collect($it->meta['attribute_data'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }

        return null;
    }
}

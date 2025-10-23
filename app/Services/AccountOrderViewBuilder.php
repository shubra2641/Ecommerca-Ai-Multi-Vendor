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
        $addrSource = $order->shipping_address ?? $order->billing_address ?? $order->address;
        $addrText = '';
        if (is_array($addrSource)) {
            try {
                $countryId = $addrSource['country_id'] ?? $addrSource['country'] ?? null;
                $govId = $addrSource['governorate_id'] ?? $addrSource['governorate'] ?? null;
                $cityId = $addrSource['city_id'] ?? $addrSource['city'] ?? null;
                if ($countryId && is_numeric($countryId)) {
                    $c = Country::find($countryId);
                    if ($c) {
                        $addrSource['country'] = $c->name;
                        $addrSource['country_id'] = $countryId;
                    }
                }
                if ($govId && is_numeric($govId)) {
                    $g = Governorate::find($govId);
                    if ($g) {
                        $addrSource['governorate'] = $g->name;
                        $addrSource['governorate_id'] = $govId;
                    }
                }
                if ($cityId && is_numeric($cityId)) {
                    $ci = City::find($cityId);
                    if ($ci) {
                        $addrSource['city'] = $ci->name;
                        $addrSource['city_id'] = $cityId;
                    }
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to resolve address components: ' . $e->getMessage());
            }
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
            $parts = [];
            foreach ($orderedKeys as $k) {
                if (! empty($addrSource[$k])) {
                    $parts[] = $addrSource[$k];
                }
            }
            foreach ($addrSource as $k => $v) {
                if (is_scalar($v) && ! in_array($v, $parts, true)) {
                    $parts[] = $v;
                }
            }
            $addrText = implode("\n", $parts);
        } else {
            $addrText = (string) ($addrSource ?? '');
        }

        // Shipment stages logic
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

        // Items variant labeling
        $itemRows = [];
        foreach ($order->items as $it) {
            $variantLabel = null;
            if (is_array($it->meta)) {
                if (! empty($it->meta['variant_name'])) {
                    $variantLabel = $it->meta['variant_name'];
                } elseif (! empty($it->meta['attribute_data']) && is_array($it->meta['attribute_data'])) {
                    $variantLabel = collect($it->meta['attribute_data'])
                        ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                        ->values()
                        ->join(', ');
                }
            }
            $itemRows[] = [
                'name' => $it->name,
                'variant_label' => $variantLabel,
                'qty' => $it->qty,
                'price' => $it->price,
            ];
        }

        $subtotal = $order->subtotal ?? $order->total - ($order->shipping_price ?? 0);

        return compact('addrText', 'stages', 'current', 'reached', 'itemRows', 'subtotal');
    }
}

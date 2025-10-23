<?php

namespace App\View\Builders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;

class OrderViewBuilder
{
    public static function buildAddressText($order): string
    {
        $addrSource = $order->shipping_address ?? $order->billing_address ?? $order->address;
        if (is_array($addrSource)) {
            $countryId = $addrSource['country_id'] ?? $addrSource['country'] ?? null;
            $govId = $addrSource['governorate_id'] ?? $addrSource['governorate'] ?? null;
            $cityId = $addrSource['city_id'] ?? $addrSource['city'] ?? null;
            if ($countryId && is_numeric($countryId) && ($c = Country::find($countryId))) {
                $addrSource['country'] = $c->name;
                $addrSource['country_id'] = $countryId;
            }
            if ($govId && is_numeric($govId) && ($g = Governorate::find($govId))) {
                $addrSource['governorate'] = $g->name;
                $addrSource['governorate_id'] = $govId;
            }
            if ($cityId && is_numeric($cityId) && ($ci = City::find($cityId))) {
                $addrSource['city'] = $ci->name;
                $addrSource['city_id'] = $cityId;
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
            foreach ($addrSource as $v) {
                if (is_scalar($v) && ! in_array($v, $parts, true)) {
                    $parts[] = $v;
                }
            }

            return implode("\n", $parts);
        }

        return (string) ($addrSource ?? '');
    }

    public static function shipmentStages($status): array
    {
        $stages = [
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
            'refunded' => __('Refunded'),
        ];
        $reached = [];
        if (in_array($status, ['cancelled', 'refunded'], true)) {
            $reached = [$status];
        } else {
            foreach (['pending', 'processing', 'completed'] as $linear) {
                $reached[] = $linear;
                if ($linear === $status) {
                    break;
                }
            }
        }

        return ['stages' => $stages, 'reached' => $reached, 'current' => $status];
    }

    public static function variantLabel($it): ?string
    {
        $variantLabel = null;
        if (! empty($it->meta) && is_array($it->meta)) {
            if (! empty($it->meta['variant_name'])) {
                $variantLabel = $it->meta['variant_name'];
            } elseif (! empty($it->meta['attribute_data']) && is_array($it->meta['attribute_data'])) {
                $variantLabel = collect($it->meta['attribute_data'])
                    ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                    ->values()
                    ->join(', ');
            }
        }

        return $variantLabel;
    }
}

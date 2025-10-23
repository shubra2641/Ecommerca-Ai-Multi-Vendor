<?php

declare(strict_types=1);

namespace App\View\Builders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;

class OrderViewBuilder
{
    public static function buildAddressText($order): string
    {
        $addrSource = $order->shipping_address ?? $order->billing_address ?? $order->address;
        if (! is_array($addrSource)) {
            return (string) ($addrSource ?? '');
        }

        $addrSource = self::resolveAddressIds($addrSource);

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
            $stages_order = ['pending', 'processing', 'completed'];
            $index = array_search($status, $stages_order, true);
            if ($index !== false) {
                $reached = array_slice($stages_order, 0, $index + 1);
            } else {
                $reached = $stages_order;
            }
        }

        return ['stages' => $stages, 'reached' => $reached, 'current' => $status];
    }

    public static function variantLabel($it): ?string
    {
        if (empty($it->meta) || ! is_array($it->meta)) {
            return null;
        }

        return match (true) {
            !empty($it->meta['variant_name']) => $it->meta['variant_name'],
            !empty($it->meta['attribute_data']) && is_array($it->meta['attribute_data']) => collect($it->meta['attribute_data'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', '),
            default => null,
        };
    }

    private static function resolveAddressIds(array $addrSource): array
    {
        $types = [
            'country' => Country::class,
            'governorate' => Governorate::class,
            'city' => City::class,
        ];

        foreach ($types as $type => $modelClass) {
            $id = $addrSource[$type . '_id'] ?? $addrSource[$type] ?? null;
            if ($id && is_numeric($id)) {
                $model = $modelClass::find($id);
                if ($model) {
                    $addrSource[$type] = $model->name;
                    $addrSource[$type . '_id'] = $id;
                }
            }
        }

        return $addrSource;
    }
}

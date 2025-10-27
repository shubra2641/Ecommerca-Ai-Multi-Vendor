<?php

declare(strict_types=1);

namespace App\View\Builders;

class CheckoutViewBuilder
{
    public static function prepareItems(array $items): array
    {
        return collect($items)->map(function ($it) {
            $p = $it['product'];
            $img = null;
            if ($p->image_url) {
                $img = $p->image_url;
            } elseif (method_exists($p, 'getFirstMediaUrl')) {
                try {
                    $img = $p->getFirstMediaUrl('images');
                } catch (\Throwable $e) {
                    $img = null;
                }
            }
            // Use unified image logic with GlobalHelper
            $img = $img ? \App\Helpers\GlobalHelper::storageImageUrl($img) : asset('images/placeholder.png');

            $variantLabel = self::variantLabel($it);

            return $it + [
                'display_image' => $img,
                'variant_label' => $variantLabel,
            ];
        })->all();
    }

    public static function variantLabel($it): ?string
    {
        if ($it['variant']) {
            return match (true) {
                is_object($it['variant']) => self::handleObjectVariant($it['variant']),
                is_string($it['variant']) => self::handleStringVariant($it['variant']),
                default => (string) $it['variant'],
            };
        }

        if ($it['attributes']) {
            return is_array($it['attributes']) ? implode(', ', $it['attributes']) : (string) $it['attributes'];
        }

        return null;
    }

    public static function buildCheckoutConfig(array $base): array
    {
        return $base;
    }

    private static function handleObjectVariant($variant): ?string
    {
        $name = $variant->name ?? null;
        if ($name) {
            return $name;
        }
        if ($variant->attribute_data) {
            return collect($variant->attribute_data)
                ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }
        return null;
    }

    private static function handleStringVariant($variant): string
    {
        $parsed = json_decode($variant, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['attribute_data'])) {
            return collect($parsed['attribute_data'])
                ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }
        return $variant;
    }
}

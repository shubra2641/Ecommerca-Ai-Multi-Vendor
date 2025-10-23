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
            if (! empty($p->image_url)) {
                $img = $p->image_url;
            } elseif (method_exists($p, 'getFirstMediaUrl')) {
                try {
                    $img = $p->getFirstMediaUrl('images');
                } catch (\Throwable $e) {
                    $img = null;
                }
            }
            $img = $img ? $img : asset('images/placeholder.svg');

            $variantLabel = self::variantLabel($it);

            return $it + [
                'display_image' => $img,
                'variant_label' => $variantLabel,
            ];
        })->all();
    }

    public static function variantLabel($it): ?string
    {
        $variantLabel = null;
        if (! empty($it['variant'])) {
            if (is_object($it['variant'])) {
                $variantLabel = $it['variant']->name ?? null;
                if (! $variantLabel && ! empty($it['variant']->attribute_data)) {
                    $variantLabel = collect($it['variant']->attribute_data)
                        ->map(fn ($v, $k) => ucfirst($k).': '.$v)
                        ->values()
                        ->join(', ');
                }
            } else {
                if (is_string($it['variant']) && ($parsed = @json_decode($it['variant'], true))) {
                    if (is_array($parsed) && isset($parsed['attribute_data'])) {
                        $variantLabel = collect($parsed['attribute_data'])
                            ->map(fn ($v, $k) => ucfirst($k).': '.$v)
                            ->values()
                            ->join(', ');
                    } else {
                        $variantLabel = $it['variant'];
                    }
                } else {
                    $variantLabel = (string) $it['variant'];
                }
            }
        } elseif (! empty($it['attributes'])) {
            $variantLabel = is_array($it['attributes']) ? implode(', ', $it['attributes']) : $it['attributes'];
        }

        return $variantLabel ? $variantLabel : null;
    }

    public static function buildCheckoutConfig(array $base): array
    {
        return $base;
    }
}

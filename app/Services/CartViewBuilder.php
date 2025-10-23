<?php

declare(strict_types=1);

namespace App\Services;

class CartViewBuilder
{
    public function build(array $rawItems, string $currencySymbol = '$'): array
    {
        $items = [];
        foreach ($rawItems as $it) {
            $p = $it['product'];
            $variantLabel = null;
            if (! empty($it['variant'])) {
                $vObj = $it['variant'];
                if (is_object($vObj)) {
                    $variantLabel = $vObj->name ?? null;
                    if (! $variantLabel && ! empty($vObj->attribute_data)) {
                        $variantLabel = collect($vObj->attribute_data)
                            ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                            ->values()
                            ->join(', ');
                    }
                } else {
                    if (
                        is_string($it['variant']) && strlen($it['variant']) > 0
                    ) {
                        $parsed = json_decode($it['variant'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['attribute_data'])) {
                            $variantLabel = collect($parsed['attribute_data'])
                                ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
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
            // availability for quantity max
            $available = null;
            if (! empty($it['variant']) && is_object($it['variant'])) {
                $v = $it['variant'];
                if ($v->manage_stock) {
                    $available = max(0, (int) $v->stock_qty - (int) $v->reserved_qty);
                }
            } else {
                if ($p->manage_stock) {
                    $available = max(0, (int) ($p->stock_qty ?? 0) - (int) ($p->reserved_qty ?? 0));
                }
            }
            $onSale = ($p->sale_price ?? null) && ($p->sale_price < ($p->price ?? 0));
            $salePercent = $onSale && $p->price ? (int) round(($p->price - $p->sale_price) / $p->price * 100) : null;
            $items[] = [
                'product' => $p,
                'price' => $it['price'],
                'qty' => $it['qty'],
                'line_total' => $it['line_total'],
                'display_price' => $it['display_price'] ?? $it['price'],
                'display_line_total' => $it['display_line_total'] ?? $it['line_total'],
                'cart_key' => $it['cart_key'],
                'variant_label' => $variantLabel,
                'available' => $available,
                'on_sale' => $onSale,
                'sale_percent' => $salePercent,
                'variant' => $it['variant'] ?? null,
            ];
        }

        return [
            'items' => $items,
            'currency_symbol' => $currencySymbol,
        ];
    }
}

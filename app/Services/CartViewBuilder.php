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
            $variantLabel = $this->buildVariantLabel($it);
            if (! empty($it['variant']) && is_object($it['variant'])) {
                $v = $it['variant'];
                $available = $v->manage_stock ? max(0, (int) $v->stock_qty - (int) $v->reserved_qty) : null;
            } else {
                $available = $p->manage_stock ? max(0, (int) ($p->stock_qty ?? 0) - (int) ($p->reserved_qty ?? 0)) : null;
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

    private function buildVariantLabel(array $it): ?string
    {
        $variant = $it['variant'] ?? null;
        if (! $variant) {
            return ! empty($it['attributes']) ? (is_array($it['attributes']) ? implode(', ', $it['attributes']) : $it['attributes']) : null;
        }

        if (is_object($variant)) {
            if (! empty($variant->name)) {
                return $variant->name;
            }
            if (! empty($variant->attribute_data)) {
                return collect($variant->attribute_data)
                    ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
                    ->values()
                    ->join(', ');
            }
            return null;
        }

        if (! is_string($variant)) {
            return (string) $variant;
        }

        if (strlen($variant) === 0) {
            return '';
        }

        $parsed = json_decode($variant, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed) || ! isset($parsed['attribute_data'])) {
            return $variant;
        }

        return collect($parsed['attribute_data'])
            ->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)
            ->values()
            ->join(', ');
    }
}

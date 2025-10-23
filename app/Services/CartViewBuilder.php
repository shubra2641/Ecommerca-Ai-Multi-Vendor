<?php

declare(strict_types=1);

namespace App\Services;

class CartViewBuilder
{
    public function build(array $rawItems, string $currencySymbol = '$'): array
    {
        $items = [];
        foreach ($rawItems as $it) {
            $items[] = $this->buildItem($it);
        }

        return [
            'items' => $items,
            'currency_symbol' => $currencySymbol,
        ];
    }

    private function buildItem(array $it): array
    {
        $p = $it['product'];
        $variantLabel = $this->buildVariantLabel($it);
        $available = $this->calculateAvailable($it['variant'], $p);
        $onSale = ($p->sale_price ?? null) && ($p->sale_price < ($p->price ?? 0));
        $salePercent = $onSale && $p->price ? (int) round(($p->price - $p->sale_price) / $p->price * 100) : null;

        return [
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

    private function calculateAvailable($variant, $product): ?int
    {
        if (! empty($variant) && is_object($variant)) {
            $v = $variant;
            return $v->manage_stock ? max(0, (int) $v->stock_qty - (int) $v->reserved_qty) : null;
        }
        return $product->manage_stock ? max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0)) : null;
    }

    private function buildVariantLabel(array $it): ?string
    {
        $variant = $it['variant'] ?? null;
        if (! $variant) {
            $attributes = $it['attributes'] ?? null;
            return $attributes ? (is_array($attributes) ? implode(', ', $attributes) : $attributes) : null;
        }

        if (is_object($variant)) {
            return match (true) {
                ! empty($variant->name) => $variant->name,
                ! empty($variant->attribute_data) => $this->buildAttributeLabel($variant->attribute_data),
                default => null,
            };
        }

        if (! is_string($variant)) {
            return (string) $variant;
        }

        return $this->parseVariantJson($variant);
    }

    private function parseVariantJson(string $variant): string
    {
        if (strlen($variant) === 0) {
            return '';
        }

        $parsed = json_decode($variant, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed) || ! isset($parsed['attribute_data'])) {
            return $variant;
        }

        return $this->buildAttributeLabel($parsed['attribute_data']);
    }

    private function buildAttributeLabel(array $attributeData): string
    {
        return collect($attributeData)
            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
            ->values()
            ->join(', ');
    }
}

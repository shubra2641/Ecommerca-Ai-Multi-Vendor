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
            $variantLabel = $this->buildCartVariantLabel($it);
            $available = $this->calculateAvailability($it, $p);
            [$onSale, $salePercent] = $this->calculateSaleData($p);

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

    private function buildCartVariantLabel(array $it): ?string
    {
        $variant = $it['variant'] ?? null;
        if (!$variant) {
            return $this->handleAttributes($it);
        }

        return match (true) {
            is_object($variant) => $this->buildObjectCartVariantLabel($variant),
            default => $this->buildStringCartVariantLabel($variant),
        };
    }

    private function handleAttributes(array $it): ?string
    {
        if (!empty($it['attributes'])) {
            return is_array($it['attributes']) ? implode(', ', $it['attributes']) : $it['attributes'];
        }
        return null;
    }

    private function buildObjectCartVariantLabel($variant): ?string
    {
        if (!empty($variant->name)) {
            return $variant->name;
        }
        if (!empty($variant->attribute_data)) {
            return collect($variant->attribute_data)
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }
        return null;
    }

    private function buildStringCartVariantLabel($variant): string
    {
        if (!is_string($variant)) {
            return (string) $variant;
        }

        if (strlen($variant) === 0) {
            return '';
        }

        $parsed = json_decode($variant, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $variant;
        }

        if (!is_array($parsed)) {
            return $variant;
        }

        if (!isset($parsed['attribute_data'])) {
            return $variant;
        }

        return $this->formatAttributeData($parsed['attribute_data']);
    }

    private function formatAttributeData(array $attributeData): string
    {
        return collect($attributeData)
            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
            ->values()
            ->join(', ');
    }

    private function calculateAvailability(array $it, $p): ?int
    {
        if (!empty($it['variant']) && is_object($it['variant'])) {
            $v = $it['variant'];
            if ($v->manage_stock) {
                return max(0, (int) $v->stock_qty - (int) $v->reserved_qty);
            }
        } else {
            if ($p->manage_stock) {
                return max(0, (int) ($p->stock_qty ?? 0) - (int) ($p->reserved_qty ?? 0));
            }
        }

        return null;
    }

    private function calculateSaleData($p): array
    {
        $onSale = ($p->sale_price ?? null) && ($p->sale_price < ($p->price ?? 0));
        $salePercent = $onSale && $p->price ? (int) round(($p->price - $p->sale_price) / $p->price * 100) : null;
        return [$onSale, $salePercent];
    }
}

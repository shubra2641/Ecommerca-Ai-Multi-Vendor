<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;

final class OrderItemsCreator
{
    /**
     * @param array<string, mixed> $checkoutData
     */
    public function createOrderItems(Order $order, array $checkoutData): void
    {
        foreach ($checkoutData['items'] as $item) {
            $this->createOrderItem($order, $item);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function createOrderItem(Order $order, array $item): void
    {
        $meta = $this->prepareItemMeta($item);
        $name = $this->prepareItemName($item, $meta['variant'] ?? null);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item['product']->id,
            'name' => $name,
            'qty' => $item['qty'],
            'price' => $item['price'],
            'meta' => $meta,
            'purchased_at' => now(),
        ]);

        $this->reserveStock($item);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    private function prepareItemMeta(array $item): array
    {
        $meta = [];
        $variantId = $item['variant'] ?? null;

        $variant = $variantId ? ProductVariation::find($variantId) : null;

        if (! $variant) {
            return $meta;
        }

        $meta['variant_id'] = $variant->id;
        $meta['variant'] = [
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'price' => $variant->price,
        ];

        return $meta;
    }

    /**
     * @param array<string, mixed> $item
     * @param array<string, mixed>|null $variant
     */
    private function prepareItemName(array $item, ?array $variant): string
    {
        $name = $item['product']->name;
        if ($variant) {
            $name .= ' - ' . $variant['name'];
        }

        return $name;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function reserveStock(array $item): void
    {
        $variantId = $item['variant'] ?? null;
        $qty = max(1, (int) ($item['qty'] ?? 1));

        if ($variantId) {
            $this->reserveVariantStock($variantId, $qty);
            return;
        }

        \App\Services\StockService::reserve($item['product'], $qty);
    }

    private function reserveVariantStock(int $variantId, int $qty): void
    {
        $variant = ProductVariation::find($variantId);
        if (! $variant) {
            return;
        }

        \App\Services\StockService::reserveVariation($variant, $qty);
    }
}

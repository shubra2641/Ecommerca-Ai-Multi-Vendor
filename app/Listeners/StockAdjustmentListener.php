<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Events\OrderPaid;
use App\Events\OrderRefunded;
use App\Models\ProductVariation;
use App\Services\StockService;

final class StockAdjustmentListener
{
    public function handleOrderPaid(OrderPaid $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            $this->processPaidItem($item, $order);
        }
    }

    public function handleOrderCancelled(OrderCancelled $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            $this->processCancelledItem($item);
        }
    }

    public function handleOrderRefunded(OrderRefunded $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            $this->processRefundItem($item);
        }
    }

    private function processPaidItem($item, $order): void
    {
        if ($item->committed) {
            return;
        }
        $qty = (int) $item->qty;
        $product = $item->product;
        if (! $product) {
            return;
        }
        $variantId = $this->getVariantId($item);
        if ($variantId) {
            $this->commitVariation($item, $variantId, $qty, $order);
        } else {
            $this->commitProduct($item, $product, $qty, $order);
        }
    }

    private function processCancelledItem($item): void
    {
        if ($item->committed) {
            $this->handleCommittedItem($item);
        } else {
            $this->handleUncommittedItem($item);
        }
    }

    private function processRefundItem($item): void
    {
        if (! $item->committed || $item->restocked || ! $item->product) {
            return;
        }
        $qty = (int) $item->qty;
        $product = $item->product;
        $variantId = $this->getVariantId($item);
        if ($variantId) {
            $this->restockVariation($item, $variantId, $qty);
        } else {
            $this->restockProduct($item, $product, $qty);
        }
    }

    private function getVariantId($item): ?int
    {
        return is_array($item->meta) ? ($item->meta['variant_id'] ?? null) : null;
    }

    private function commitVariation($item, int $variantId, int $qty, $order): void
    {
        $variation = ProductVariation::find($variantId);
        if ($variation) {
            $ok = StockService::commitVariation($variation, $qty);
            if ($ok) {
                $item->committed = true;
                $item->save();
                logger()->info(
                    'StockAdjustmentListener: committed variation ' . $variation->id .
                        ' qty ' . $qty . ' for order ' . $order->id
                );
            } else {
                logger()->warning(
                    'StockAdjustmentListener: failed to commit variation ' . $variation->id .
                        ' qty ' . $qty . ' for order ' . $order->id
                );
            }
        }
    }

    private function commitProduct($item, $product, int $qty, $order): void
    {
        $ok = StockService::commit($product, $qty);
        if ($ok) {
            $item->committed = true;
            $item->save();
            logger()->info(
                'StockAdjustmentListener: committed product ' . $product->id .
                    ' qty ' . $qty . ' for order ' . $order->id
            );
        } else {
            logger()->warning(
                'StockAdjustmentListener: failed to commit product ' . $product->id .
                    ' qty ' . $qty . ' for order ' . $order->id
            );
        }
    }

    private function handleCommittedItem($item): void
    {
        if ($item->restocked) {
            return;
        }
        $qty = (int) $item->qty;
        $product = $item->product;
        if (! $product) {
            return;
        }
        $variantId = $this->getVariantId($item);
        if ($variantId) {
            $this->restockVariation($item, $variantId, $qty);
        } else {
            $this->restockProduct($item, $product, $qty);
        }
    }

    private function handleUncommittedItem($item): void
    {
        $qty = (int) $item->qty;
        $product = $item->product;
        if (! $product) {
            return;
        }
        $variantId = $this->getVariantId($item);
        if ($variantId) {
            $this->releaseVariation($variantId, $qty);
        } else {
            $this->releaseProduct($product, $qty);
        }
    }

    private function restockVariation($item, int $variantId, int $qty): void
    {
        $variation = ProductVariation::find($variantId);
        if ($variation) {
            StockService::restockVariation($variation, $qty);
            $item->restocked = true;
            $item->save();
        }
    }

    private function restockProduct($item, $product, int $qty): void
    {
        StockService::restock($product, $qty);
        $item->restocked = true;
        $item->save();
    }

    private function releaseVariation(int $variantId, int $qty): void
    {
        $variation = ProductVariation::find($variantId);
        if ($variation) {
            StockService::releaseVariation($variation, $qty);
        }
    }

    private function releaseProduct($product, int $qty): void
    {
        StockService::release($product, $qty);
    }
}

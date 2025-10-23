<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Events\OrderPaid;
use App\Events\OrderRefunded;
use App\Models\ProductVariation;
use App\Services\StockService;

class StockAdjustmentListener
{
    public function handleOrderPaid(OrderPaid $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            if ($item->committed) {
                continue;
            } // already committed
            $qty = (int) $item->qty;
            $product = $item->product;
            if (! $product) {
                continue;
            }
            $variantId = is_array($item->meta) ? ($item->meta['variant_id'] ?? null) : null;
            if ($variantId) {
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
            } else {
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
        }
    }

    public function handleOrderCancelled(OrderCancelled $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            if ($item->committed) { // if already committed and now cancelled before shipment => restock
                if ($item->restocked) {
                    continue;
                }
                $qty = (int) $item->qty;
                $product = $item->product;
                if (! $product) {
                    continue;
                }
                $variantId = is_array($item->meta) ? ($item->meta['variant_id'] ?? null) : null;
                if ($variantId) {
                    $variation = ProductVariation::find($variantId);
                    if ($variation) {
                        StockService::restockVariation($variation, $qty);
                        $item->restocked = true;
                        $item->save();
                    }
                } else {
                    StockService::restock($product, $qty);
                    $item->restocked = true;
                    $item->save();
                }
            } else { // just release reserved
                $qty = (int) $item->qty;
                $product = $item->product;
                if (! $product) {
                    continue;
                }
                $variantId = is_array($item->meta) ? ($item->meta['variant_id'] ?? null) : null;
                if ($variantId) {
                    $variation = ProductVariation::find($variantId);
                    if ($variation) {
                        StockService::releaseVariation($variation, $qty);
                    }
                } else {
                    StockService::release($product, $qty);
                }
            }
        }
    }

    public function handleOrderRefunded(OrderRefunded $event): void
    {
        $order = $event->order->loadMissing('items.product');
        foreach ($order->items as $item) {
            if (! $item->committed || $item->restocked || ! $item->product) {
                continue;
            } // nothing to do
            $qty = (int) $item->qty;
            $product = $item->product;
            $variantId = is_array($item->meta) ? ($item->meta['variant_id'] ?? null) : null;
            if ($variantId) {
                $variation = ProductVariation::find($variantId);
                if ($variation) {
                    StockService::restockVariation($variation, $qty);
                    $item->restocked = true;
                    $item->save();
                }
            } else {
                StockService::restock($product, $qty);
                $item->restocked = true;
                $item->save();
            }
        }
    }
}

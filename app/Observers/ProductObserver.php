<?php

namespace App\Observers;

use App\Jobs\NotifyBackInStockJob;
use App\Models\PriceChange;
use App\Models\Product;

class ProductObserver
{
    public function updated(Product $product): void
    {
        // Only if managing stock
        if (! $product->manage_stock) {
            return;
        }
        $originalStock = (int) $product->getOriginal('stock_qty') - (int) $product->getOriginal('reserved_qty');
        $newStock = (int) $product->stock_qty - (int) $product->reserved_qty;
        if ($originalStock <= 0 && $newStock > 0) {
            // Dispatch async notification job after response
            dispatch(new NotifyBackInStockJob($product->id))->afterResponse();
        }

        // Price drop detection (compare original price/sale_price vs new)
        $origPrice = (float) $product->getOriginal('price');
        $origSale = (float) $product->getOriginal('sale_price');
        $newPrice = (float) $product->price;
        $newSale = (float) $product->sale_price;
        $priceBefore = $origSale && $origSale < $origPrice ? $origSale : $origPrice;
        $priceAfter = $newSale && $newSale < $newPrice ? $newSale : $newPrice;
        if ($priceAfter < $priceBefore) {
            $minPercent = (int) config('interest.price_drop_min_percent', 5);
            $dropPercent = $priceBefore > 0 ? (($priceBefore - $priceAfter) / $priceBefore) * 100 : 0;
            if ($dropPercent < $minPercent) {
                return; // below threshold; ignore
            }
            // Update price tracking fields quickly (avoid recursion via withoutEvents)
            Product::withoutEvents(function () use ($product, $priceBefore, $priceAfter, $dropPercent) {
                $product->update([
                    'last_price' => $priceBefore,
                    'last_sale_price' => $priceBefore,
                    'price_changed_at' => now(),
                ]);
                PriceChange::create([
                    'product_id' => $product->id,
                    'old_price' => $priceBefore,
                    'new_price' => $priceAfter,
                    'percent' => $dropPercent,
                ]);
            });
            // Dispatch price drop notifications via existing job (reuse back-in-stock for simplicity now or create new job)
            dispatch(new \App\Jobs\NotifyPriceDropJob($product->id))->afterResponse();
        }
    }
}

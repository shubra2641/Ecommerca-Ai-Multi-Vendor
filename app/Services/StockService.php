<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\OutOfStockException;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class StockService
{
    public static function reserve(Product $product, int $qty): bool
    {
        if (! $product->manage_stock) {
            return true;
        }

        return self::atomicAdjust($product, $qty, 'reserve');
    }

    public static function commit(Product $product, int $qty): bool
    {
        if (! $product->manage_stock) {
            return true;
        }

        return self::atomicAdjust($product, $qty, 'commit');
    }

    public static function release(Product $product, int $qty): bool
    {
        if (! $product->manage_stock) {
            return true;
        }

        return self::atomicAdjust($product, $qty, 'release');
    }

    public static function reserveVariation(ProductVariation $variation, int $qty): bool
    {
        if (! $variation->manage_stock) {
            return true;
        }

        return self::atomicAdjustVariation($variation, $qty, 'reserve');
    }

    public static function commitVariation(ProductVariation $variation, int $qty): bool
    {
        if (! $variation->manage_stock) {
            return true;
        }

        return self::atomicAdjustVariation($variation, $qty, 'commit');
    }

    public static function releaseVariation(ProductVariation $variation, int $qty): bool
    {
        if (! $variation->manage_stock) {
            return true;
        }

        return self::atomicAdjustVariation($variation, $qty, 'release');
    }

    public static function restock(Product $product, int $qty): bool
    {
        if (! $product->manage_stock) {
            return true;
        }

        return self::atomicAdjust($product, $qty, 'restock');
    }

    public static function restockVariation(ProductVariation $variation, int $qty): bool
    {
        if (! $variation->manage_stock) {
            return true;
        }

        return self::atomicAdjustVariation($variation, $qty, 'restock');
    }

    // Immediate consumption (skip reserve/commit cycle). Allows negative stock only if backorder enabled.
    public static function consume(Product $product, int $qty): bool
    {
        if (! $product->manage_stock) {
            return true;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($product, $qty) {
            $product->refresh();
            $available = $product->stock_qty - $product->reserved_qty; // reserved might be legacy usage
            if ($available < $qty && ! $product->backorder) {
                throw new OutOfStockException($product->name);
            }
            // Deduct directly; allow negative if backorder true
            $product->stock_qty -= $qty;
            $product->save();

            return true;
        });
    }

    public static function consumeVariation(ProductVariation $variation, int $qty): bool
    {
        if (! $variation->manage_stock) {
            return true;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($variation, $qty) {
            $variation->refresh();
            $available = $variation->stock_qty - $variation->reserved_qty;
            if ($available < $qty && ! $variation->backorder) {
                throw new OutOfStockException($variation->name ?? $variation->id);
            }
            $variation->stock_qty -= $qty;
            $variation->save();

            return true;
        });
    }

    protected static function atomicAdjust(Product $product, int $qty, string $mode): bool
    {
        return DB::transaction(function () use ($product, $qty, $mode) {
            $product->refresh();
            $available = $product->stock_qty - $product->reserved_qty;
            if ($mode === 'reserve') {
                if ($available < $qty && ! $product->backorder) {
                    return false;
                } $product->reserved_qty += $qty;
            } elseif ($mode === 'commit') {
                if ($product->reserved_qty < $qty) {
                    return false;
                } $product->reserved_qty -= $qty;
                $product->stock_qty -= $qty;
            } elseif ($mode === 'release') {
                $product->reserved_qty = max(0, $product->reserved_qty - $qty);
            } elseif ($mode === 'restock') {
                // simply add back to available stock (used after refund when already committed earlier)
                $product->stock_qty += $qty;
            }
            $product->save();

            return true;
        });
    }

    protected static function atomicAdjustVariation(ProductVariation $variation, int $qty, string $mode): bool
    {
        return DB::transaction(function () use ($variation, $qty, $mode) {
            $variation->refresh();
            $available = $variation->stock_qty - $variation->reserved_qty;
            if ($mode === 'reserve') {
                if ($available < $qty && ! $variation->backorder) {
                    return false;
                } $variation->reserved_qty += $qty;
            } elseif ($mode === 'commit') {
                if ($variation->reserved_qty < $qty) {
                    return false;
                } $variation->reserved_qty -= $qty;
                $variation->stock_qty -= $qty;
            } elseif ($mode === 'release') {
                $variation->reserved_qty = max(0, $variation->reserved_qty - $qty);
            } elseif ($mode === 'restock') {
                $variation->stock_qty += $qty;
            }
            $variation->save();

            return true;
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for assigning available serial numbers to order items.
 * Ensures serials are locked (FOR UPDATE) within a transaction to avoid race conditions.
 */
class SerialAssignmentService
{
    /**
     * Assign available serials to an order.
     *
     * Items should be an array of ['product_id' => int, 'qty' => int]
     * Returns an array mapping product_id => array of assigned serial strings.
     *
     * Throws \Exception on errors (missing product or insufficient serials).
     */
    public static function assignForOrder(int $orderId, array $items): array
    {
        return DB::transaction(function () use ($orderId, $items) {
            $results = [];

            foreach ($items as $item) {
                $productId = isset($item['product_id']) ? (int) $item['product_id'] : null;
                $qty = isset($item['qty']) ? (int) $item['qty'] : 0;
                if (! $productId || $qty <= 0) {
                    continue;
                }

                $product = Product::find($productId);
                if (! $product) {
                    throw new \Exception("Product {$productId} not found");
                }

                // only assign for products using serials
                if (! $product->has_serials) {
                    $results[$productId] = [];

                    continue;
                }

                // lock available serials for update to avoid races
                $available = ProductSerial::where('product_id', $productId)
                    ->whereNull('sold_at')
                    ->lockForUpdate()
                    ->limit($qty)
                    ->get();

                if ($available->count() < $qty) {
                    throw new \Exception(__('errors.insufficient_serials', ['id' => $productId]));
                }

                $assigned = [];
                $now = now();
                foreach ($available as $serial) {
                    $serial->order_id = $orderId;
                    $serial->sold_at = $now;
                    $serial->save();
                    $assigned[] = $serial->serial;
                }

                $results[$productId] = $assigned;
            }

            return $results;
        }, 5);
    }
}

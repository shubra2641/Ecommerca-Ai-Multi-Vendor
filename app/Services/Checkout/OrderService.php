<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\StockService;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create order with items in database transaction
     */
    public function createOrder(array $orderData, array $itemRows, ?array $shippingAddressPayload = null): Order
    {
        return DB::transaction(function () use ($orderData, $itemRows, $shippingAddressPayload) {
            $order = Order::create($orderData);
            $orderHasBackorder = false;

            foreach ($itemRows as $item) {
                $orderItem = $this->createOrderItem($order, $item);
                
                if ($orderItem['is_backorder']) {
                    $orderHasBackorder = true;
                }
            }

            if ($orderHasBackorder) {
                $order->has_backorder = true;
                $order->save();
            }

            return $order;
        });
    }

    /**
     * Create individual order item
     */
    private function createOrderItem(Order $order, array $item): array
    {
        $product = $item['product'];
        $qty = (int) $item['qty'];
        $variant = $item['variant'] ?? null;

        // Handle variant information
        $variantInfo = $this->processVariant($variant);
        
        // Build item name with variant
        $itemName = $this->buildItemName($product, $variantInfo);
        
        // Handle stock management
        $stockResult = $this->handleStockManagement($product, $variant, $qty);
        
        // Calculate commission
        $commissionData = $this->calculateCommission($product, $qty, $item['price']);
        
        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'sku' => $product->sku ?? null,
            'name' => $itemName,
            'qty' => $qty,
            'price' => $item['price'],
            'vendor_commission_rate' => $commissionData['rate'],
            'vendor_commission_amount' => $commissionData['commission'],
            'vendor_earnings' => $commissionData['vendor_earnings'],
            'meta' => $variantInfo['meta'],
            'is_backorder' => $stockResult['is_backorder'],
            'committed' => $stockResult['committed'],
            'purchased_at' => now(),
            'refund_expires_at' => $this->calculateRefundExpiry($product),
            'restocked' => false,
        ]);

        return $stockResult;
    }

    /**
     * Process variant information
     */
    private function processVariant($variant): array
    {
        $variantName = null;
        $variantId = null;
        $variantAttributes = null;
        $meta = null;

        if ($variant) {
            if (is_object($variant)) {
                $variantName = $variant->name ?? null;
                $variantId = $variant->id ?? null;
                $variationModel = $variant instanceof ProductVariation ? $variant : null;
                
                if ($variationModel) {
                    $variantAttributes = $variationModel->attribute_data ?? null;
                }
            } else {
                try {
                    $variationModel = ProductVariation::find($variant);
                    if ($variationModel) {
                        $variantName = $variationModel->name ?? null;
                        $variantId = $variationModel->id;
                        $variantAttributes = $variationModel->attribute_data ?? null;
                    }
                } catch (\Exception $e) {
                    // Handle error silently
                }
            }

            if ($variantId || $variantName || $variantAttributes) {
                $meta = ['variant_id' => $variantId];
                if ($variantName) {
                    $meta['variant_name'] = $variantName;
                }
                if ($variantAttributes) {
                    $meta['attribute_data'] = $variantAttributes;
                }
            }
        }

        return [
            'name' => $variantName,
            'id' => $variantId,
            'attributes' => $variantAttributes,
            'meta' => $meta
        ];
    }

    /**
     * Build item name with variant information
     */
    private function buildItemName(Product $product, array $variantInfo): string
    {
        $itemName = $product->name_translations['en'] ?? $product->name ?? '';
        
        if ($variantInfo['name']) {
            $itemName = trim($itemName . ' - ' . $variantInfo['name']);
        } elseif ($variantInfo['attributes'] && is_array($variantInfo['attributes']) && count($variantInfo['attributes'])) {
            $label = collect($variantInfo['attributes'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->join(', ');
            
            if ($label) {
                $itemName = trim($itemName . ' - ' . $label);
            }
        }

        return $itemName;
    }

    /**
     * Handle stock management for product/variant
     */
    private function handleStockManagement(Product $product, $variant, int $qty): array
    {
        $isBackorder = false;
        $committed = false;

        try {
            if ($variant && is_object($variant) && $variant->manage_stock) {
                $available = $variant->stock_qty - $variant->reserved_qty;
                $isBackorder = ($available < $qty && $variant->backorder);
                
                if (!StockService::consumeVariation($variant, $qty)) {
                    throw new \Exception(__('out_of_stock', ['name' => $product->name]));
                }
                $committed = true;
            } elseif ($product->manage_stock) {
                $available = $product->stock_qty - $product->reserved_qty;
                $isBackorder = ($available < $qty && $product->backorder);
                
                if (!StockService::consume($product, $qty)) {
                    throw new \Exception(__('out_of_stock', ['name' => $product->name]));
                }
                $committed = true;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return [
            'is_backorder' => $isBackorder,
            'committed' => $committed
        ];
    }

    /**
     * Calculate commission for product
     */
    private function calculateCommission(Product $product, int $qty, float $price): array
    {
        if (!$product->vendor_id) {
            return ['rate' => null, 'commission' => null, 'vendor_earnings' => null];
        }

        return CommissionService::breakdown($product, $qty, $price);
    }

    /**
     * Calculate refund expiry date
     */
    private function calculateRefundExpiry(Product $product): ?\Carbon\Carbon
    {
        $refundDays = (int) ($product->refund_days ?? 0);
        
        if ($refundDays > 0) {
            return now()->addDays($refundDays);
        }

        return null;
    }
}

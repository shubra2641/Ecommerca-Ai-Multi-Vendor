<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\OrderItem;

class OrderService
{
    /**
     * Create simple order
     */
    public function createOrder(array $orderData, array $items): Order
    {
        $order = Order::create($orderData);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'name' => $item['product']->name,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'purchased_at' => now(),
            ]);
        }

        return $order;
    }
}

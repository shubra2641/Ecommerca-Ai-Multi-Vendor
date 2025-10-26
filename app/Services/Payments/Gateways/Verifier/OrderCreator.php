<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Verifier;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final class OrderCreator
{
    public function handlePaidPayment(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) {
            $order = $this->createOrderFromSnapshot($payment);
        }

        if ($order && $order->status !== 'paid') {
            $order->status = 'paid';
            $order->save();
        }

        try {
            session()->forget('cart');
        } catch (\Throwable $_) {
            // Ignore cart clearing errors
            null;
        }
    }

    private function createOrderFromSnapshot(Payment $payment): ?\App\Models\Order
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        if (!$snapshot) {
            return null;
        }

        try {
            return DB::transaction(function () use ($snapshot, $payment) {
                $order = \App\Models\Order::create([
                    'user_id' => $snapshot['user_id'] ?? null,
                    'status' => 'completed',
                    'total' => $snapshot['total'] ?? 0,
                    'items_subtotal' => $snapshot['total'] ?? 0,
                    'currency' => $snapshot['currency'] ?? config('app.currency', 'USD'),
                    'shipping_address' => $snapshot['shipping_address'] ?? null,
                    'payment_method' => $payment->method,
                    'payment_status' => 'paid',
                ]);

                foreach ($snapshot['items'] ?? [] as $item) {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'name' => $item['name'] ?? null,
                        'qty' => $item['qty'] ?? 1,
                        'price' => $item['price'] ?? 0,
                    ]);
                }

                $payment->order_id = $order->id;
                $payment->save();

                return $order;
            });
        } catch (\Throwable $e) {
            return null;
        }
    }
}

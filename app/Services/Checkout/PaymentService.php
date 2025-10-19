<?php

namespace App\Services\Checkout;

use App\Models\Payment;

class PaymentService
{
    /**
     * Create simple payment
     */
    public function createPayment(Order $order, string $method): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => $method,
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);
    }
}

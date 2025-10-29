<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

final class CodPaymentHandler
{
    /**
     * @return array<string, string>
     */
    public function handleCodPayment(Order $order, Request $request): array
    {
        $payment = $this->createCodPayment($order);

        // Mark order as completed for COD
        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->save();

        // Dispatch OrderPaid event to trigger stock deduction
        event(new OrderPaid($order));

        return [
            'type' => 'cod',
            'redirect_url' => route('orders.show', $order),
        ];
    }

    private function createCodPayment(Order $order): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'cod',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'completed',
        ]);
    }
}

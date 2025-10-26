<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Tap;

use App\Models\Order;
use App\Models\Payment;

final class TapPaymentCreator
{
    public function createPayment(?Order $order, ?int $orderId, ?array $snapshot): Payment
    {
        return Payment::create([
            'order_id' => $orderId,
            'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
            'method' => 'tap',
            'amount' => $order?->total ?? $snapshot['total'] ?? 0,
            'currency' => $order?->currency ?? $snapshot['currency'] ?? 'USD',
            'status' => 'pending',
            'payload' => [
                'order_reference' => $orderId,
                'checkout_snapshot' => $snapshot,
            ],
        ]);
    }

    public function updatePaymentPayload(Payment $payment, array $data): void
    {
        $payment->payload = array_merge($payment->payload ?? [], [
            'tap_charge_id' => $data['id'] ?? null,
        ]);
        $payment->save();
    }
}
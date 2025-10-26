<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Generic;

use App\Models\Payment;

final class GenericPaymentCreator
{
    public function createPayment(array $snapshot, string $slug): Payment
    {
        return Payment::create([
            'order_id' => null,
            'user_id' => $snapshot['user_id'] ?? null,
            'method' => $slug,
            'amount' => $snapshot['total'] ?? 0,
            'currency' => $snapshot['currency'] ?? 'USD',
            'status' => 'pending',
            'payload' => [
                'order_reference' => null,
                'checkout_snapshot' => $snapshot,
            ],
        ]);
    }

    public function updatePaymentPayload(Payment $payment, string $slug, ?string $chargeId): void
    {
        $payment->payload = array_merge($payment->payload ?? [], [
            $slug . '_charge_id' => $chargeId,
        ]);
        $payment->save();
    }
}

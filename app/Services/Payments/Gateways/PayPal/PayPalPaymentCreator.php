<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\PayPal;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final class PayPalPaymentCreator
{
    public function createPayment(?Order $order, ?int $orderId, ?array $snapshot): Payment
    {
        return Payment::create([
            'order_id' => $orderId,
            'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
            'method' => 'paypal',
            'amount' => $order?->total ?? $snapshot['total'] ?? 0,
            'currency' => $order?->currency ?? $snapshot['currency'] ?? 'USD',
            'status' => 'pending',
            'payload' => [
                'order_reference' => $orderId,
                'checkout_snapshot' => $snapshot,
            ],
        ]);
    }

    public function updatePaymentPayload(Payment $payment, array $data, string $token): void
    {
        $payment->payload = array_merge($payment->payload ?? [], [
            'paypal_order_id' => $data['id'] ?? null,
            'paypal_approval_url' => $this->extractApprovalUrl($data),
            'paypal_access_token' => $token,
        ]);

        // Update return/cancel URLs with actual payment ID
        $payment->payload['application_context'] = [
            'return_url' => route('paypal.return', ['payment' => $payment->id]),
            'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
            'shipping_preference' => 'NO_SHIPPING',
        ];

        $payment->save();
    }

    private function extractApprovalUrl(array $data): string
    {
        foreach (($data['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? '';
            }
        }

        throw new \Exception('Approval link missing');
    }
}
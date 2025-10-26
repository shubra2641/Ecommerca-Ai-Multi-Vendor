<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

final class PaymentVerifier
{
    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $chargeId = $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;
        $cfg = $gateway->config ?? [];
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);

        if (!$secret || !$chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }

        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');

        try {
            $response = $this->makeApiRequest($apiBase, $secret, $chargeId);
            return $this->processApiResponse($payment, $gateway, $response);
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'pending', 'data' => null];
        }
    }

    private function makeApiRequest(string $apiBase, string $secret, string $chargeId): array
    {
        $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);

        if (!$resp->ok()) {
            return ['status' => 'pending', 'data' => null];
        }

        return $resp->json();
    }

    private function processApiResponse(Payment $payment, PaymentGateway $gateway, array $response): array
    {
        $status = $response['status'] ?? $response['data']['status'] ?? null;
        $finalStatus = $this->determineStatus($status);

        $payment->status = $finalStatus;
        $payment->payload = array_merge($payment->payload ?? [], [
            $gateway->slug . '_charge_status' => $finalStatus
        ]);
        $payment->save();

        if ($finalStatus === 'paid') {
            $this->handlePaidPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $response];
    }

    private function determineStatus(?string $status): string
    {
        if (! $status) {
            return 'processing';
        }

        return match (strtoupper($status)) {
            'CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS' => 'paid',
            'FAILED', 'CANCELLED', 'DECLINED' => 'failed',
            default => 'processing',
        };
    }

    private function handlePaidPayment(Payment $payment): void
    {
        $order = $payment->order;
        if (! $order) {
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

    private function createOrderFromSnapshot(Payment $payment): ?Order
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        if (! $snapshot) {
            return null;
        }

        try {
            return DB::transaction(function () use ($snapshot, $payment) {
                $order = Order::create([
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

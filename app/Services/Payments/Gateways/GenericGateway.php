<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

final class GenericGateway
{
    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway, string $slug): array
    {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper($cfg[$slug . '_currency'] ?? ($snapshot['currency'] ?? 'USD'));
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $slug . '.com'), '/');

        return DB::transaction(function () use ($snapshot, $cfg, $currency, $apiBase, $slug) {
            $payment = $this->createPayment($snapshot, $slug);

            $payload = $this->buildChargePayload($snapshot, $payment);
            $response = $this->createCharge($cfg, $apiBase, $payload);

            $data = $response->json();
            $redirectUrl = $this->extractRedirectUrl($data);
            $chargeId = $this->extractChargeId($data);

            $this->updatePaymentPayload($payment, $slug, $chargeId);

            return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $data];
        });
    }

    private function createPayment(array $snapshot, string $slug): Payment
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

    private function buildChargePayload(array $snapshot, Payment $payment): array
    {
        return [
            'amount' => (float) number_format($snapshot['total'] ?? 0, 2, '.', ''),
            'currency' => strtoupper($snapshot['currency'] ?? 'USD'),
            'description' => 'Checkout',
            'metadata' => ['order_id' => null, 'payment_id' => $payment->id],
            'redirect' => [
                'url' => route($payment->method . '.return', ['payment' => $payment->id]),
            ],
            'customer' => [
                'first_name' => $snapshot['customer_name'] ?? 'Customer',
                'email' => $snapshot['customer_email'] ?? 'customer@example.com',
            ],
        ];
    }

    private function createCharge(array $cfg, string $apiBase, array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken(
            $cfg['secret_key'] ?? ($cfg['api_key'] ?? null)
        )
            ->acceptJson()
            ->post($apiBase . '/charges', $payload);

        if (!$response->ok()) {
            throw new \Exception('Charge error: ' . $response->status());
        }

        return $response;
    }

    private function extractRedirectUrl(array $data): ?string
    {
        return $data['transaction']['url'] ??
            $data['redirect_url'] ??
            $data['data']['redirect_url'] ??
            null;
    }

    private function extractChargeId(array $data): ?string
    {
        return $data['id'] ?? $data['data']['id'] ?? null;
    }

    private function updatePaymentPayload(Payment $payment, string $slug, ?string $chargeId): void
    {
        $payment->payload = array_merge($payment->payload ?? [], [
            $slug . '_charge_id' => $chargeId,
        ]);
        $payment->save();
    }
}
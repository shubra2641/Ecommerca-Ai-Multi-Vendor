<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Generic;

use App\Models\Payment;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class GenericChargeBuilder
{
    public function buildChargePayload(array $snapshot, Payment $payment): array
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

    public function createCharge(array $cfg, string $apiBase, array $payload): Response
    {
        $response = Http::withToken(
            $cfg['secret_key'] ?? ($cfg['api_key'] ?? null)
        )
            ->acceptJson()
            ->post($apiBase . '/charges', $payload);

        if (! $response->ok()) {
            throw new \Exception('Charge error: ' . $response->status());
        }

        return $response;
    }

    public function extractRedirectUrl(array $data): ?string
    {
        return $data['transaction']['url'] ??
            $data['redirect_url'] ??
            $data['data']['redirect_url'] ??
            null;
    }

    public function extractChargeId(array $data): ?string
    {
        return $data['id'] ?? $data['data']['id'] ?? null;
    }
}

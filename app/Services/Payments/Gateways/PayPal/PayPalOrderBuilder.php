<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\PayPal;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

final class PayPalOrderBuilder
{
    public function buildOrderPayload(?Order $order, ?array $snapshot): array
    {
        $currency = $order?->currency ?? $snapshot['currency'] ?? 'USD';
        $amount = $order?->total ?? $snapshot['total'] ?? 0;

        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('paypal.return', ['payment' => 0]),
                'cancel_url' => route('paypal.cancel', ['payment' => 0]),
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];
    }

    public function createPayPalOrder(string $baseUrl, string $token, array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->retry(2, 500)
            ->post($baseUrl . '/v2/checkout/orders', $payload);

        if ($response->status() < 200 || $response->status() >= 300) {
            throw new \Exception('Create error: ' . $response->status());
        }

        return $response;
    }

    public function extractApprovalUrl(array $data): string
    {
        foreach (($data['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? '';
            }
        }

        throw new \Exception('Approval link missing');
    }

    public function getBaseUrl(array $config): string
    {
        $mode = ($config['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        return $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}

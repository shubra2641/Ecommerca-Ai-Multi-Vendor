<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;

class PaymentGatewayHelper
{
    public static function getChargeId(array $payload, string $slug): ?string
    {
        return $payload[$slug . '_charge_id'] ?? $payload['charge_id'] ?? null;
    }

    public static function getGatewaySecret(array $config): ?string
    {
        return $config['secret_key'] ?? ($config['api_key'] ?? null);
    }

    public static function getApiBase(array $config, string $slug): string
    {
        return rtrim($config['api_base'] ?? ('https://api.' . $slug . '.com'), '/');
    }

    public static function fetchChargeData(string $apiBase, string $secret, string $chargeId): ?array
    {
        $resp = \Illuminate\Support\Facades\Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);
        return $resp->ok() ? $resp->json() : null;
    }

    public static function mapChargeStatus(array $chargeData): string
    {
        $status = $chargeData['status'] ?? $chargeData['data']['status'] ?? null;
        if (in_array(strtoupper($status), ['CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS'], true)) {
            return 'paid';
        } elseif (in_array(strtoupper($status), ['FAILED', 'CANCELLED', 'DECLINED'], true)) {
            return 'failed';
        }
        return 'processing';
    }

    public static function getPayPalBaseUrl(array $config): string
    {
        $mode = ($config['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        return $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    public static function getPayPalAccessToken(string $baseUrl, array $config): string
    {
        $response = \Illuminate\Support\Facades\Http::withBasicAuth($config['paypal_client_id'], $config['paypal_secret'])
            ->asForm()
            ->timeout(25)
            ->retry(2, 400)
            ->post($baseUrl . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (!$response->ok()) {
            throw new \Exception('Token error: ' . $response->status());
        }

        $token = $response->json('access_token');
        if (!$token) {
            throw new \Exception('Token empty');
        }

        return $token;
    }

    public static function buildPayPalOrderPayload(?Order $order, ?array $snapshot, Payment $payment): array
    {
        $currency = $order?->currency ?? $snapshot['currency'] ?? 'USD';
        $amount = $order?->total ?? $snapshot['total'] ?? 0;

        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($currency),
                    'value' => number_format($amount, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => route('paypal.return', ['payment' => $payment->id]),
                'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];
    }

    public static function createPayPalOrder(string $baseUrl, string $token, array $payload): array
    {
        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->retry(2, 500)
            ->post($baseUrl . '/v2/checkout/orders', $payload);

        if ($response->status() < 200 || $response->status() >= 300) {
            throw new \Exception('Create error: ' . $response->status());
        }

        $data = $response->json();
        $approvalUrl = '';
        foreach (($data['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approvalUrl = $link['href'] ?? '';
                break;
            }
        }

        if (!$approvalUrl) {
            throw new \Exception('Approval link missing');
        }

        return ['data' => $data, 'approval_url' => $approvalUrl];
    }
}

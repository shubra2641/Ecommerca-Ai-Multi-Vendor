<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\PayPal;

use Illuminate\Support\Facades\Http;

final class PayPalTokenManager
{
    public function getAccessToken(array $config): string
    {
        $response = Http::withBasicAuth($config['paypal_client_id'], $config['paypal_secret'])
            ->asForm()
            ->timeout(25)
            ->retry(2, 400)
            ->post($this->getBaseUrl($config) . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (!$response->ok()) {
            throw new \Exception('Token error: ' . $response->status());
        }

        $token = $response->json('access_token');
        if (!$token) {
            throw new \Exception('Token empty');
        }

        return $token;
    }

    private function getBaseUrl(array $config): string
    {
        $mode = ($config['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        return $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
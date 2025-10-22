<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytabsGateway
{
    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? 'https://api.paytabs.com', '/');
        // Accept multiple possible config keys used in different installs
        $secret = $cfg['secret_key'] ??
            ($cfg['api_key'] ?? ($cfg['paytabs_server_key'] ?? ($cfg['server_key'] ?? null)));
        $usedSecretKey = null;
        if (! empty($cfg['secret_key'])) {
            $usedSecretKey = 'secret_key';
        } elseif (! empty($cfg['api_key'])) {
            $usedSecretKey = 'api_key';
        } elseif (! empty($cfg['paytabs_server_key'])) {
            $usedSecretKey = 'paytabs_server_key';
        } elseif (! empty($cfg['server_key'])) {
            $usedSecretKey = 'server_key';
        }
        $currency = strtoupper(
            $cfg['paytabs_currency'] ?? ($snapshot['currency'] ?? 'USD')
        );

        return \Illuminate\Support\Facades\DB::transaction(
            function () use ($snapshot, $apiBase, $secret, $currency, $cfg, $usedSecretKey, $gateway) {
                $payment = Payment::create([
                    'order_id' => null,
                    'user_id' => $snapshot['user_id'] ?? null,
                    'method' => 'paytabs',
                    'amount' => $snapshot['total'] ?? 0,
                    'currency' => $currency,
                    'status' => 'pending',
                    'payload' => ['checkout_snapshot' => $snapshot],
                ]);

                $amountVal = (float) number_format($snapshot['total'] ?? 0, 2, '.', '');
                $chargePayload = [
                    'amount' => $amountVal,
                    'currency' => $currency,
                    'description' => 'Checkout',
                    'metadata' => ['order_id' => null, 'payment_id' => $payment->id],
                    'redirect' => ['url' => route('paytabs.return', ['payment' => $payment->id])],
                    'customer' => [
                        'first_name' => $snapshot['customer_name'] ?? 'Customer',
                        'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                    ],
                ];

                // If config contains PayTabs-specific keys, include them in the payload (do not log secrets)
                if (! empty($cfg['paytabs_profile_id'] ?? null)) {
                    $chargePayload['profile_id'] = $cfg['paytabs_profile_id'];
                }
                if (! empty($cfg['paytabs_server_key'] ?? null)) {
                    // Some older installs store a server key under this name
                    $chargePayload['server_key'] = $cfg['paytabs_server_key'];
                }

                // choose auth style: default Bearer token, or header X-API-KEY if configured
                try {
                    $client = Http::acceptJson();
                    // Log which config key is used (do not log the secret value)
                    try {
                    } catch (\Throwable $_) {
                    }
                    if (! empty($secret)) {
                        $authType = $gateway->config['auth_type'] ?? null;
                        if ($authType === 'header') {
                            $client = $client->withHeaders(['X-API-KEY' => $secret]);
                        } else {
                            // PayTabs expects server_key as "server_key" in payload or header.
                            // withToken sends Authorization: Bearer <secret> which works for many APIs.
                            // For different auth, set config.auth_type = 'header' to force X-API-KEY.
                            $client = $client->withToken($secret);
                        }
                    }
                    $resp = $client->post(
                        $apiBase . '/charges',
                        $chargePayload
                    );
                    try {
                    } catch (\Throwable $_) {
                    }
                    if (! $resp->ok()) {
                        throw new \Exception(
                            'Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200)
                        );
                    }
                    $json = $resp->json();
                    $redirectUrl = $json['transaction']['url'] ??
                        $json['redirect_url'] ??
                        ($json['data']['redirect_url'] ?? null);
                    if (! $redirectUrl) {
                        throw new \Exception('Missing redirect URL');
                    }
                    $payment->payload = array_merge(
                        $payment->payload ?? [],
                        ['paytabs_charge_id' => $json['id'] ?? ($json['data']['id'] ?? null)]
                    );
                    $payment->save();

                    return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $json];
                } catch (\Throwable $e) {
                    throw $e;
                }
            }
        );
    }

    public function verifyCharge(\App\Models\Payment $payment, PaymentGateway $gateway): array
    {
        // Delegate to the generic verifier available on the service class
        $svc = app(
            \App\Services\Payments\PaymentGatewayService::class
        );

        return $svc->verifyGenericGatewayCharge(
            $payment,
            $gateway
        );
    }
}

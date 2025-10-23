<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class WeacceptGateway
{
    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];

        // allow explicit api_base in gateway config, then env override, default to PayMob accept endpoint
        $cfgApiBase = $cfg['api_base'] ?? env('WEACCEPT_API_BASE', env(
            'PAYMOB_API_BASE',
            'https://accept.paymob.com'
        ));
        $apiBase = rtrim($cfgApiBase, '/');

        // API key used for auth/tokens
        $apiKey = $cfg['api_key'] ?? $cfg['weaccept_api_key'] ?? $cfg['paymob_api_key'] ??
            env('PAYMOB_API_KEY');

        // Integration ID used in payment_key request
        $integrationId = $cfg['integration_id'] ?? $cfg['weaccept_integration_id'] ??
            $cfg['paymob_integration_id'] ?? env('PAYMOB_INTEGRATION_ID');

        // Iframe ID used in redirect URL
        $iframeId = $cfg['iframe_id'] ?? $cfg['weaccept_iframe_id'] ?? $cfg['paymob_iframe_id'] ??
            env('PAYMOB_IFRAME_ID');

        // default to Egyptian Pound unless explicitly configured (accepts PAYMOB_CURRENCY)
        $currency = strtoupper($cfg['weaccept_currency'] ?? $cfg['paymob_currency'] ??
            env('PAYMOB_CURRENCY', ($snapshot['currency'] ?? 'EGP')));
        $mock = (bool) ($cfg['mock'] ?? env('WEACCEPT_MOCK', false));
        // HTTP client robustness (timeouts/CA bundle)
        $timeoutSec = (int) ($cfg['http_timeout'] ?? env('PAYMOB_HTTP_TIMEOUT', 20));
        $connectTimeout = (int) ($cfg['http_connect_timeout'] ?? env('PAYMOB_HTTP_CONNECT_TIMEOUT', 10));
        $caBundle = $cfg['ca_bundle_path'] ?? env('CURL_CA_BUNDLE');
        $allowInsecure = (bool) ($cfg['allow_insecure_ssl'] ?? env('PAYMOB_ALLOW_INSECURE_SSL', false));
        $proxy = $cfg['http_proxy'] ?? env('HTTP_PROXY') ?? env('http_proxy');

        $txArgs = [
            $snapshot,
            $apiBase,
            $apiKey,
            $integrationId,
            $iframeId,
            $currency,
            $gateway,
            $mock,
            $timeoutSec,
            $connectTimeout,
            $caBundle,
            $allowInsecure,
            $proxy,
        ];

        return \Illuminate\Support\Facades\DB::transaction(function () use (
            $snapshot,
            $apiBase,
            $apiKey,
            $integrationId,
            $iframeId,
            $currency,
            $gateway,
            $mock,
            $timeoutSec,
            $connectTimeout,
            $caBundle,
            $allowInsecure,
            $proxy
        ) {
            $payment = Payment::create([
                'order_id' => null,
                'user_id' => $snapshot['user_id'] ?? null,
                'method' => 'weaccept',
                'amount' => $snapshot['total'] ?? 0,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => ['checkout_snapshot' => $snapshot],
            ]);

            // Validate minimal config (unless mock mode)
            if (! $mock) {
                if (empty($apiKey)) {
                    throw new \Exception('Missing PAYMOB_API_KEY in gateway config');
                }
                if (empty($integrationId)) {
                    throw new \Exception('Missing integration_id in gateway config');
                }
                if (empty($iframeId)) {
                    throw new \Exception('Missing iframe_id in gateway config');
                }
            }

            // Paymob Accept flow: 1) get auth token, 2) create order, 3) request payment_key
            // -> get payment_token and redirect
            $amountCents = (int) round(($snapshot['total'] ?? 0) * 100);

            $initLog = [
                'payment_id' => $payment->id,
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'mock' => $mock,
            ];

            try {
                // If mock mode is enabled, skip external requests and return a local redirect to the return route
                if ($mock) {
                    $mockToken = 'mock-'.$payment->id.'-'.time();
                    $payment->payload = array_merge($payment->payload ?? [], [
                        'weaccept_order_id' => 'mock-order-'.$payment->id,
                        'weaccept_payment_token' => $mockToken,
                        'weaccept_integration_id' => $integrationId,
                        'weaccept_mock' => true,
                    ]);
                    $payment->save();
                    $redirectUrl = route('weaccept.return', ['payment' => $payment->id]).'?mock=1';

                    return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => ['mock' => true]];
                }
                // build API-prefixed base (support user setting api_base with or without /api)
                $apiPrefix = str_contains($apiBase, '/api') ? rtrim($apiBase, '/') :
                    rtrim($apiBase, '/').'/api';

                // 1) auth token
                $authUrl = $apiPrefix.'/auth/tokens';
                $details = [
                    'payment_id' => $payment->id,
                    'auth_url' => $authUrl,
                    'api_prefix' => $apiPrefix,
                    'api_key_tail' => substr((string) $apiKey, -4),
                ];
                // Sanitize API key (strip whitespace/newlines) and try several candidate keys
                // if auth fails
                $candidates = [];
                $rawKey = $apiKey;
                if (! empty($rawKey)) {
                    $clean = trim(preg_replace('/\s+/', '', $rawKey));
                    $candidates[] = $clean;
                }
                // Also try common alternate keys from gateway config or env
                if (! empty($cfg['api_key'])) {
                    $candidates[] = trim(preg_replace('/\s+/', '', $cfg['api_key']));
                }
                if (! empty($cfg['weaccept_api_key'])) {
                    $candidates[] = trim(preg_replace('/\s+/', '', $cfg['weaccept_api_key']));
                }
                if (! empty($cfg['paymob_api_key'])) {
                    $candidates[] = trim(preg_replace('/\s+/', '', $cfg['paymob_api_key']));
                }
                $envKey = trim(preg_replace('/\s+/', '', env('PAYMOB_API_KEY') ?? ''));
                $candidates[] = $envKey;
                $candidates = array_values(array_filter(array_unique($candidates)));

                $authResp = null;
                $lastBody = null;
                foreach ($candidates as $try) {
                    try {
                        $attemptInfo = [
                            'payment_id' => $payment->id,
                            'auth_url' => $authUrl,
                            'api_key_tail' => substr($try, -8),
                        ];

                        $http = Http::acceptJson()
                            ->timeout($timeoutSec)
                            ->retry(2, 500)
                            ->withOptions(['connect_timeout' => $connectTimeout]);

                        if (! empty($proxy)) {
                            $http = $http->withOptions(['proxy' => $proxy]);
                        }

                        if (! empty($caBundle)) {
                            $http = $http->withOptions(['verify' => $caBundle]);
                        }

                        $authResp = $http->post($authUrl, ['api_key' => $try]);
                        $lastBody = $authResp->body();
                        $bodyPreview = $lastBody ? substr($lastBody, 0, 1000) : null;
                        $authRespLog = [
                            'payment_id' => $payment->id,
                            'auth_url' => $authUrl,
                            'status' => $authResp->status(),
                            'body' => $bodyPreview,
                        ];
                        if ($authResp->successful()) {
                            $authJson = $authResp->json();
                            break;
                        }
                        // If 403, continue trying other candidates; otherwise stop and throw below
                        if ($authResp->status() != 403) {
                            break;
                        }
                    } catch (\Throwable $inner) {
                        // Optional insecure retry for local environments only
                        if ($allowInsecure) {
                            try {
                                $http = Http::acceptJson()
                                    ->timeout($timeoutSec)
                                    ->retry(1, 500)
                                    ->withOptions(['verify' => false, 'connect_timeout' => $connectTimeout]);

                                if (! empty($proxy)) {
                                    $http = $http->withOptions(['proxy' => $proxy]);
                                }

                                $authResp = $http->post($authUrl, ['api_key' => $try]);
                                $lastBody = $authResp->body();
                                $insecureInfo = ['payment_id' => $payment->id, 'status' => $authResp->status()];
                                if ($authResp->successful()) {
                                    $authJson = $authResp->json();
                                    break;
                                }
                            } catch (\Throwable $inner2) {
                            }
                        }
                    }
                }
                if (! $authResp || ! $authResp->successful()) {
                    $status = $authResp ? $authResp->status() : 'no_response';
                    $bodyPreview = $lastBody ? substr($lastBody, 0, 1000) : null;
                    throw new \Exception('Auth token error: '.$status);
                }
                $authJson = $authResp->json();
                // Extract token if present. PayMob may return a `profile` object alongside a `token`
                // (test accounts do this).
                $authToken = $authJson['token'] ?? ($authJson['access_token'] ?? null);
                if (empty($authToken)) {
                    // If we received profile/user but no token, this is unexpected — raise a helpful error.
                    if (isset($authJson['profile']) || isset($authJson['user'])) {
                        $errMsg = 'Unexpected auth response: profile/user present but token missing. '.
                            'auth_url: '.$authUrl;
                        throw new \Exception($errMsg);
                    }
                    throw new \Exception('Missing auth token from Paymob');
                }

                $client = Http::withToken($authToken)
                    ->acceptJson()
                    ->timeout($timeoutSec)
                    ->retry(2, 500)
                    ->withOptions(['connect_timeout' => $connectTimeout]);

                if (! empty($proxy)) {
                    $client = $client->withOptions(['proxy' => $proxy]);
                }

                if (! empty($caBundle)) {
                    $client = $client->withOptions(['verify' => $caBundle]);
                }

                // 2) create order
                // map snapshot items to PayMob expected item shape
                $rawItems = $snapshot['items'] ?? [];
                $pmItems = [];
                foreach ($rawItems as $it) {
                    $price = $it['amount_cents'] ?? (isset($it['price']) ?
                        (int) round($it['price'] * 100) : $amountCents);
                    $qty = $it['qty'] ?? ($it['quantity'] ?? 1);
                    $pmItems[] = [
                        'name' => $it['name'] ?? ($it['product_name'] ?? 'Item'),
                        'description' => $it['description'] ?? ($it['name'] ?? ''),
                        'amount_cents' => (int) $price,
                        'quantity' => (int) $qty,
                    ];
                }

                $orderPayload = [
                    'merchant_order_id' => (string) $payment->id,
                    'amount_cents' => $amountCents,
                    'currency' => $currency,
                    'items' => $pmItems,
                ];
                try {
                    $orderResp = $client->post($apiPrefix.'/ecommerce/orders', $orderPayload);
                } catch (\Throwable $e) {
                    if ($allowInsecure) {
                        $orderResp = $client->withOptions(['verify' => false])
                            ->post($apiPrefix.'/ecommerce/orders', $orderPayload);
                    } else {
                        throw $e;
                    }
                }
                $orderBodyPreview = substr($orderResp->body(), 0, 500);
                $orderRespLog = [
                    'payment_id' => $payment->id,
                    'status' => $orderResp->status(),
                    'body' => $orderBodyPreview,
                ];
                if (! $orderResp->successful()) {
                    throw new \Exception('Order creation error: '.$orderResp->status());
                }
                $orderJson = $orderResp->json();
                $orderId = $orderJson['id'] ?? ($orderJson['data']['id'] ?? null);
                if (empty($orderId)) {
                    throw new \Exception('Missing order id from Paymob');
                }

                // 3) request payment key

                // Populate required billing fields PayMob expects
                // (do not leave required fields blank)
                $fullName = $snapshot['customer_name'] ?? 'Customer';
                $nameParts = preg_split('/\s+/', trim($fullName));
                $first = $nameParts[0] ?? 'Customer';
                $last = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : 'Customer';
                // Ensure city and country are names/codes, not IDs
                $city = $snapshot['billing_city'] ?? $snapshot['customer_city'] ?? null;
                $country = $snapshot['billing_country'] ?? $snapshot['customer_country'] ?? null;
                // Fallbacks for city/country
                if (is_numeric($city) || empty($city)) {
                    $city = 'Cairo';
                }
                if (is_numeric($country) || empty($country)) {
                    $country = 'EG';
                }
                $billingData = [
                    'apartment' => $snapshot['billing_apartment'] ?? '12A',
                    'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                    'floor' => $snapshot['billing_floor'] ?? '3',
                    'first_name' => $first,
                    'last_name' => $last,
                    'street' => $snapshot['billing_street'] ?? 'Tahrir St',
                    'building' => $snapshot['billing_building'] ?? '15',
                    'phone_number' => $snapshot['customer_phone'] ?? '201000000000',
                    'shipping_method' => 'NO',
                    'postal_code' => $snapshot['billing_postal_code'] ?? '11511',
                    'city' => $city,
                    'country' => $country,
                ];

                // Include amount_cents inside order per PayMob validation rules
                $returnUrl = route('weaccept.return', ['payment' => $payment->id]);
                $billingLog = ['payment_id' => $payment->id, 'billing_data' => $billingData];
                $paymentKeyPayload = [
                    'amount_cents' => $amountCents,
                    'currency' => $currency,
                    'order' => ['id' => $orderId, 'amount_cents' => $amountCents],
                    'billing_data' => $billingData,
                    'expiration' => 3600,
                    'integration_id' => (int) $integrationId,
                    // Ask PayMob to redirect back to our app after payment
                    'redirection_url' => $returnUrl,
                ];
                $pkFullLog = ['payment_id' => $payment->id, 'payload' => $paymentKeyPayload];
                $pkPayloadLog = [
                    'payment_id' => $payment->id,
                    'integration_id' => (int) $integrationId,
                    'iframe_id' => $iframeId,
                    'redirection_url' => $returnUrl,
                ];

                try {
                    $pkResp = $client->post($apiPrefix.'/acceptance/payment_keys', $paymentKeyPayload);
                } catch (\Throwable $e) {
                    if ($allowInsecure) {
                        $pkResp = $client->withOptions(['verify' => false])
                            ->post($apiPrefix.'/acceptance/payment_keys', $paymentKeyPayload);
                    } else {
                        throw $e;
                    }
                }
                $pkBodyPreview = substr($pkResp->body(), 0, 500);
                if (! $pkResp->successful()) {
                    throw new \Exception('Payment key error: '.$pkResp->status());
                }
                $pkJson = $pkResp->json();
                $paymentToken = $pkJson['token'] ?? $pkJson['payment_token'] ?? null;
                if (empty($paymentToken)) {
                    throw new \Exception('Missing payment token from Paymob');
                }

                // build redirect URL using iframe
                $iframeBase = $apiBase.'/api/acceptance/iframes/'.$iframeId;
                $iframeUrl = $iframeBase.'?payment_token='.$paymentToken;

                // standalone order URL (full-page) as a safe fallback when iframe integration
                // is not permitted
                $standaloneUrl = $orderJson['order_url'] ?? $orderJson['url'] ?? null;

                $newPayload = [
                    'weaccept_order_id' => $orderId,
                    'weaccept_payment_token' => $paymentToken,
                    'weaccept_integration_id' => $integrationId,
                    'weaccept_iframe_id' => $iframeId,
                    'weaccept_api_base' => $apiBase,
                    'weaccept_iframe_url' => $iframeUrl,
                    'weaccept_order_url' => $standaloneUrl,
                ];
                $payment->payload = array_merge($payment->payload ?? [], $newPayload);
                $payment->save();

                $result = [
                    'payment' => $payment,
                    'redirect_url' => $iframeUrl,
                    'raw' => ['order' => $orderJson, 'payment_key' => $pkJson],
                ];
                if ($standaloneUrl) {
                    $result['fallback_url'] = $standaloneUrl;
                }
                // If the gateway config explicitly prefers standalone, use it as primary redirect
                $prefStandalone = data_get($gateway->config ?? [], 'weaccept_prefer_standalone');
                if ($prefStandalone) {
                    $result['redirect_url'] = $standaloneUrl ?: $iframeUrl;
                }

                return $result;
            } catch (\Throwable $e) {
                throw $e;
            }
        });
    }

    public function verifyCharge(\App\Models\Payment $payment, PaymentGateway $gateway): array
    {
        $svc = app(\App\Services\Payments\PaymentGatewayService::class);

        return $svc->verifyGenericGatewayCharge($payment, $gateway);
    }
}

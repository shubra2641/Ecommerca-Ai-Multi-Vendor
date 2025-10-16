<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Create a PayPal order and return redirect URL + payment model.
     */
    public function initPayPal(Order $order, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $clientId = $cfg['paypal_client_id'] ?? null;
        $secret = $cfg['paypal_secret'] ?? null;
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $base = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        return DB::transaction(function () use ($order, $mode, $clientId, $secret, $base) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'paypal',
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
                'payload' => [
                    'order_reference' => $order->id,
                    'paypal_mode' => $mode,
                ],
            ]);

            $tokenResp = Http::withBasicAuth($clientId, $secret)
                ->asForm()
                ->timeout(25)
                ->retry(2, 400)
                ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
            if (! $tokenResp->ok()) {
                throw new \Exception('Token error: ' . $tokenResp->status() . ' ' . substr($tokenResp->body(), 0, 150));
            }
            $accessToken = $tokenResp->json('access_token');
            if (! $accessToken) {
                throw new \Exception('Token empty');
            }
            $expiresIn = (int) ($tokenResp->json('expires_in') ?? 0);
            $tokenExpiresAt = now()->addSeconds(max(0, $expiresIn - 60))->toIso8601String();

            $ppOrderPayload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($order->currency ?? 'USD'),
                        'value' => number_format($order->total, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('paypal.return', ['payment' => $payment->id]),
                    'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ];
            $createResp = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(25)
                ->retry(2, 500)
                ->post($base . '/v2/checkout/orders', $ppOrderPayload);
            // PayPal success for create is HTTP 201; treat any 2xx as success
            if ($createResp->status() < 200 || $createResp->status() >= 300) {
                throw new \Exception('Create error: ' . $createResp->status() . ' ' . substr($createResp->body(), 0, 200));
            }
            $ppData = $createResp->json();
            Log::info('paypal.order.created', [
                'paypal_status' => $ppData['status'] ?? null,
                'id' => $ppData['id'] ?? null,
                'links_count' => count($ppData['links'] ?? []),
                'links' => array_map(fn ($l) => ['rel' => $l['rel'] ?? null, 'href' => $l['href'] ?? null], $ppData['links'] ?? []),
            ]);
            $approvalUrl = null;
            foreach (($ppData['links'] ?? []) as $lnk) {
                if (($lnk['rel'] ?? '') === 'approve') {
                    $approvalUrl = $lnk['href'] ?? null;
                    break;
                }
            }
            if (! $approvalUrl) {
                throw new \Exception('Approval link missing');
            }
            $captureLink = null;
            foreach (($ppData['links'] ?? []) as $lnk) {
                if (($lnk['rel'] ?? '') === 'capture') {
                    $captureLink = $lnk['href'] ?? null;
                    break;
                }
            }
            $payment->payload = array_merge($payment->payload ?? [], [
                'paypal_order_id' => $ppData['id'] ?? null,
                'paypal_approval_url' => $approvalUrl,
                'paypal_capture_url' => $captureLink,
                'paypal_access_token' => $accessToken,
                'paypal_token_expires_at' => $tokenExpiresAt,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $ppData];
        });
    }

    /**
     * Create a Tap charge and return redirect URL + payment model.
     */
    public function initTap(Order $order, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;
        $currency = strtoupper($cfg['tap_currency'] ?? ($order->currency ?? 'USD'));

        return DB::transaction(function () use ($order, $secret, $currency) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => 'tap',
                'amount' => $order->total,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => ['order_reference' => $order->id],
            ]);
            $chargePayload = [
                'amount' => (float) number_format($order->total, 2, '.', ''),
                'currency' => $currency,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => 'Order #' . $order->id,
                'statement_descriptor' => 'Order ' . $order->id,
                'metadata' => ['order_id' => $order->id, 'payment_id' => $payment->id],
                'redirect' => ['url' => route('tap.return', ['payment' => $payment->id])],
                'customer' => [
                    'first_name' => $order->user?->name ?? 'Customer',
                    'email' => $order->user?->email ?? 'customer@example.com',
                ],
                'source' => ['id' => 'src_all'],
            ];
            // Diagnostic logging to help debug Tap init issues
            \Log::info('tap.init.request', ['payment_id' => $payment->id, 'payload' => $chargePayload]);
            $resp = Http::withToken($secret)->acceptJson()->post('https://api.tap.company/v2/charges', $chargePayload);
            \Log::info('tap.init.response', ['payment_id' => $payment->id, 'status' => $resp->status(), 'body_snippet' => substr($resp->body(), 0, 500)]);
            if (! $resp->ok()) {
                throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
            }
            $json = $resp->json();
            $redirectUrl = $json['transaction']['url'] ?? null;
            if (! $redirectUrl) {
                throw new \Exception('Missing redirect URL');
            }
            $payment->payload = array_merge($payment->payload ?? [], [
                'tap_charge_id' => $json['id'] ?? null,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $redirectUrl];
        });
    }

    /**
     * Initialize PayPal using a checkout snapshot (no Order persisted yet).
     */
    public function initPayPalFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $clientId = $cfg['paypal_client_id'] ?? null;
        $secret = $cfg['paypal_secret'] ?? null;
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $base = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        return DB::transaction(function () use ($snapshot, $mode, $clientId, $secret, $base) {
            $payment = Payment::create([
                'order_id' => null,
                'user_id' => $snapshot['user_id'] ?? null,
                'method' => 'paypal',
                'amount' => $snapshot['total'] ?? 0,
                'currency' => $snapshot['currency'] ?? 'USD',
                'status' => 'pending',
                'payload' => ['checkout_snapshot' => $snapshot, 'paypal_mode' => $mode],
            ]);

            $tokenResp = Http::withBasicAuth($clientId, $secret)
                ->asForm()
                ->timeout(25)
                ->retry(2, 400)
                ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
            if (! $tokenResp->ok()) {
                throw new \Exception('Token error: ' . $tokenResp->status() . ' ' . substr($tokenResp->body(), 0, 150));
            }
            $accessToken = $tokenResp->json('access_token');
            if (! $accessToken) {
                throw new \Exception('Token empty');
            }
            $expiresIn = (int) ($tokenResp->json('expires_in') ?? 0);
            $tokenExpiresAt = now()->addSeconds(max(0, $expiresIn - 60))->toIso8601String();

            $currencyCode = strtoupper($snapshot['currency'] ?? 'USD');
            $amountValue = number_format($snapshot['total'] ?? 0, 2, '.', '');
            $ppOrderPayload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $currencyCode,
                        'value' => $amountValue,
                    ],
                ]],
                'application_context' => [
                    'return_url' => route('paypal.return', ['payment' => $payment->id]),
                    'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ];
            $createResp = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(25)
                ->retry(2, 500)
                ->post($base . '/v2/checkout/orders', $ppOrderPayload);
            if ($createResp->status() < 200 || $createResp->status() >= 300) {
                throw new \Exception('Create error: ' . $createResp->status() . ' ' . substr($createResp->body(), 0, 200));
            }
            $ppData = $createResp->json();
            $approvalUrl = null;
            $captureLink = null;
            foreach (($ppData['links'] ?? []) as $lnk) {
                if (($lnk['rel'] ?? '') === 'approve') {
                    $approvalUrl = $lnk['href'] ?? null;
                }
                if (($lnk['rel'] ?? '') === 'capture') {
                    $captureLink = $lnk['href'] ?? null;
                }
            }
            if (! $approvalUrl) {
                throw new \Exception('Approval link missing');
            }
            $payment->payload = array_merge($payment->payload ?? [], [
                'paypal_order_id' => $ppData['id'] ?? null,
                'paypal_approval_url' => $approvalUrl,
                'paypal_capture_url' => $captureLink,
                'paypal_access_token' => $accessToken,
                'paypal_token_expires_at' => $tokenExpiresAt,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $ppData];
        });
    }

    /**
     * Initialize Tap using a checkout snapshot (no Order persisted yet).
     */
    public function initTapFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;
        $currency = strtoupper($cfg['tap_currency'] ?? ($snapshot['currency'] ?? 'USD'));

        return DB::transaction(function () use ($snapshot, $secret, $currency) {
            $payment = Payment::create([
                'order_id' => null,
                'user_id' => $snapshot['user_id'] ?? null,
                'method' => 'tap',
                'amount' => $snapshot['total'] ?? 0,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => ['checkout_snapshot' => $snapshot],
            ]);
            $amountVal = (float) number_format($snapshot['total'] ?? 0, 2, '.', '');
            $chargePayload = [
                'amount' => $amountVal,
                'currency' => $currency,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => 'Checkout',
                'statement_descriptor' => 'Checkout',
                'metadata' => ['order_id' => null, 'payment_id' => $payment->id],
                'redirect' => ['url' => route('tap.return', ['payment' => $payment->id])],
                'customer' => [
                    'first_name' => $snapshot['customer_name'] ?? 'Customer',
                    'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                ],
                'source' => ['id' => 'src_all'],
            ];
            $resp = Http::withToken($secret)->acceptJson()->post('https://api.tap.company/v2/charges', $chargePayload);
            if (! $resp->ok()) {
                throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
            }
            $json = $resp->json();
            $redirectUrl = $json['transaction']['url'] ?? null;
            if (! $redirectUrl) {
                throw new \Exception('Missing redirect URL');
            }
            $payment->payload = array_merge($payment->payload ?? [], [
                'tap_charge_id' => $json['id'] ?? null,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $redirectUrl];
        });
    }

    /**
     * Verify a Tap charge status on return (synchronous redirect) and update payment/order.
     * Returns array: [payment=>Payment, status=>'paid'|'failed'|'pending', charge=>raw]
     */
    public function verifyTapCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $gw = new \App\Services\Payments\Gateways\TapGateway();

        return $gw->verifyCharge($payment, $gateway);
    }

    /**
     * Generic pattern: initialize other redirect gateways from a checkout snapshot.
     * These methods use a configurable `api_base` in gateway config (recommended).
     * They attempt common response shapes for redirect URLs and log request/response for debugging.
     */
    public function initPaytabsFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $class = '\\App\\Services\\Payments\\Gateways\\PaytabsGateway';
        if (class_exists($class)) {
            $gw = new $class();

            return $gw->initFromSnapshot($snapshot, $gateway);
        }

        return $this->initGenericRedirectGatewayFromSnapshot($snapshot, $gateway, 'paytabs');
    }

    public function initWeacceptFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $class = '\\App\\Services\\Payments\\Gateways\\WeacceptGateway';
        if (class_exists($class)) {
            $gw = new $class();

            return $gw->initFromSnapshot($snapshot, $gateway);
        }

        return $this->initGenericRedirectGatewayFromSnapshot($snapshot, $gateway, 'weaccept');
    }

    public function initPayeerFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $class = '\\App\\Services\\Payments\\Gateways\\PayeerGateway';
        if (class_exists($class)) {
            $gw = new $class();

            return $gw->initFromSnapshot($snapshot, $gateway);
        }

        return $this->initGenericRedirectGatewayFromSnapshot($snapshot, $gateway, 'payeer');
    }

    // Payrexx removed from deployment

    /**
     * Attempt to verify charge for generic gateways by reading stored charge id from payload
     */
    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);
        $chargeId = $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;
        if (! $secret || ! $chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }
        try {
            $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);
            if (! $resp->ok()) {
                Log::warning($gateway->slug . '.verify.error', ['payment_id' => $payment->id, 'status' => $resp->status()]);

                return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
            }
            $json = $resp->json();
            $status = $json['status'] ?? $json['data']['status'] ?? null;
            $final = null;
            if (in_array(strtoupper($status), ['CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS'], true)) {
                $final = 'paid';
            } elseif (in_array(strtoupper($status), ['FAILED', 'CANCELLED', 'DECLINED'], true)) {
                $final = 'failed';
            } else {
                $final = 'processing';
            }
            $payment->status = $final === 'paid' ? 'paid' : ($final === 'failed' ? 'failed' : 'processing');
            $payment->payload = array_merge($payment->payload ?? [], [$gateway->slug . '_charge_status' => $status]);
            $payment->save();

            if ($payment->status === 'paid') {
                // Create order from snapshot if missing
                $order = $payment->order;
                if (! $order) {
                    $snap = $payment->payload['checkout_snapshot'] ?? null;
                    if ($snap) {
                        try {
                            $order = DB::transaction(function () use ($snap) {
                                $order = Order::create([
                                    'user_id' => $snap['user_id'] ?? null,
                                    'status' => 'completed',
                                    'total' => $snap['total'] ?? 0,
                                    'items_subtotal' => $snap['total'] ?? 0,
                                    'currency' => $snap['currency'] ?? config('app.currency', 'USD'),
                                    'shipping_address' => $snap['shipping_address'] ?? null,
                                    'payment_method' => $payment->method,
                                    'payment_status' => 'paid',
                                ]);
                                foreach ($snap['items'] ?? [] as $it) {
                                    \App\Models\OrderItem::create([
                                        'order_id' => $order->id,
                                        'product_id' => $it['product_id'] ?? null,
                                        'name' => $it['name'] ?? null,
                                        'qty' => $it['qty'] ?? 1,
                                        'price' => $it['price'] ?? 0,
                                    ]);
                                }

                                return $order;
                            });
                            $payment->order_id = $order->id;
                            $payment->save();
                        } catch (\Throwable $e) {
                            Log::error($gateway->slug . '.order.create_from_snapshot_failed', ['error' => $e->getMessage()]);
                        }
                    }
                }
                if ($order && $order->status !== 'paid') {
                    $order->status = 'paid';
                    $order->save();
                }
                try {
                    session()->forget('cart');
                } catch (\Throwable $_) {
                }
            }

            return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
        } catch (\Throwable $e) {
            Log::warning($gateway->slug . '.verify.exception', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);

            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }
    }

    /**
     * Internal helper: initialize a generic redirect gateway using snapshot
     */
    private function initGenericRedirectGatewayFromSnapshot(array $snapshot, PaymentGateway $gateway, string $slug): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $slug . '.com'), '/');
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);
        $currency = strtoupper($cfg[$slug . '_currency'] ?? ($snapshot['currency'] ?? 'USD'));

        return DB::transaction(function () use ($snapshot, $apiBase, $secret, $currency, $slug) {
            $payment = Payment::create([
                'order_id' => null,
                'user_id' => $snapshot['user_id'] ?? null,
                'method' => $slug,
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
                'redirect' => ['url' => route($slug . '.return', ['payment' => $payment->id])],
                'customer' => [
                    'first_name' => $snapshot['customer_name'] ?? 'Customer',
                    'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                ],
            ];

            Log::info($slug . '.init.request', ['payment_id' => $payment->id, 'payload' => $chargePayload]);
            try {
                // Diagnostic: log URL called and whether secret exists (do not log secret value)
                try {
                    Log::info($slug . '.init.call', ['payment_id' => $payment->id, 'url' => $apiBase . '/charges', 'has_secret' => $secret ? true : false]);
                } catch (\Throwable $_) {
                }
                $resp = Http::withToken($secret)->acceptJson()->post($apiBase . '/charges', $chargePayload);
                // Log status, body snippet and response headers to help debug 4xx/5xx rejections from gateway
                $headers = method_exists($resp, 'headers') ? $resp->headers() : (method_exists($resp, 'header') ? $resp->header() : null);
                Log::info($slug . '.init.response', ['payment_id' => $payment->id, 'status' => $resp->status(), 'body_snippet' => substr($resp->body(), 0, 500)]);
                try {
                    Log::info($slug . '.init.response_headers', ['payment_id' => $payment->id, 'headers' => $headers]);
                } catch (\Throwable $_) {
                }
                if (! $resp->ok()) {
                    throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
                }
                $json = $resp->json();
                $redirectUrl = $json['transaction']['url'] ?? $json['redirect_url'] ?? ($json['data']['redirect_url'] ?? null);
                if (! $redirectUrl) {
                    throw new \Exception('Missing redirect URL');
                }
                $payment->payload = array_merge($payment->payload ?? [], [$slug . '_charge_id' => $json['id'] ?? ($json['data']['id'] ?? null)]);
                $payment->save();

                return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $json];
            } catch (\Throwable $e) {
                Log::error($slug . '.init.exception', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
                throw $e;
            }
        });
    }
}

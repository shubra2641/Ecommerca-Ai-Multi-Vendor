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
    public function initPayPal(Order $order, PaymentGateway $gateway): array
    {
        return $this->initPayPalPayment($order, $gateway, $order->id);
    }

    public function initPayPalFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initPayPalPayment(null, $gateway, null, $snapshot);
    }

    public function initTap(Order $order, PaymentGateway $gateway): array
    {
        return $this->initTapPayment($order, $gateway, $order->id);
    }

    public function initTapFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initTapPayment(null, $gateway, null, $snapshot);
    }

    public function initPaytabsFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'paytabs');
    }

    public function initWeacceptFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'weaccept');
    }

    public function initPayeerFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initGenericGateway($snapshot, $gateway, 'payeer');
    }

    public function verifyTapCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $gw = new \App\Services\Payments\Gateways\TapGateway();
        return $gw->verifyCharge($payment, $gateway);
    }

    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);
        $chargeId = $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;

        if (!$secret || !$chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }

        try {
            $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);
            
            if (!$resp->ok()) {
                Log::warning($gateway->slug . '.verify.error', [
                    'payment_id' => $payment->id, 
                    'status' => $resp->status()
                ]);
                return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
            }

            $json = $resp->json();
            $status = $json['status'] ?? $json['data']['status'] ?? null;
            $finalStatus = $this->mapPaymentStatus($status);
            
            $payment->status = $finalStatus;
            $payment->payload = array_merge($payment->payload ?? [], [
                $gateway->slug . '_charge_status' => $status
            ]);
            $payment->save();

            if ($payment->status === 'paid') {
                $this->handleSuccessfulPayment($payment);
            }

            return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
        } catch (\Throwable $e) {
            Log::warning($gateway->slug . '.verify.exception', [
                'payment_id' => $payment->id, 
                'error' => $e->getMessage()
            ]);
            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }
    }

    private function initPayPalPayment(?Order $order, PaymentGateway $gateway, ?int $orderId, ?array $snapshot = null): array
    {
        $cfg = $gateway->config ?? [];
        $clientId = $cfg['paypal_client_id'] ?? null;
        $secret = $cfg['paypal_secret'] ?? null;
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $base = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        return DB::transaction(function () use ($order, $orderId, $snapshot, $clientId, $secret, $base, $mode) {
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
                'method' => 'paypal',
                'amount' => $order?->total ?? $snapshot['total'] ?? 0,
                'currency' => $order?->currency ?? $snapshot['currency'] ?? 'USD',
                'status' => 'pending',
                'payload' => [
                    'order_reference' => $orderId,
                    'checkout_snapshot' => $snapshot,
                    'paypal_mode' => $mode,
                ],
            ]);

            $accessToken = $this->getPayPalToken($clientId, $secret, $base);
            $ppOrderPayload = $this->buildPayPalOrderPayload($order, $snapshot, $payment->id);
            
            $createResp = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(25)
                ->retry(2, 500)
                ->post($base . '/v2/checkout/orders', $ppOrderPayload);

            if ($createResp->status() < 200 || $createResp->status() >= 300) {
                throw new \Exception('Create error: ' . $createResp->status() . ' ' . substr($createResp->body(), 0, 200));
            }

            $ppData = $createResp->json();
            $approvalUrl = $this->extractPayPalApprovalUrl($ppData);
            $captureLink = $this->extractPayPalCaptureUrl($ppData);

            $payment->payload = array_merge($payment->payload ?? [], [
                'paypal_order_id' => $ppData['id'] ?? null,
                'paypal_approval_url' => $approvalUrl,
                'paypal_capture_url' => $captureLink,
                'paypal_access_token' => $accessToken,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $ppData];
        });
    }

    private function initTapPayment(?Order $order, PaymentGateway $gateway, ?int $orderId, ?array $snapshot = null): array
    {
        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;
        $currency = strtoupper($cfg['tap_currency'] ?? ($order?->currency ?? $snapshot['currency'] ?? 'USD'));

        return DB::transaction(function () use ($order, $orderId, $snapshot, $secret, $currency) {
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
                'method' => 'tap',
                'amount' => $order?->total ?? $snapshot['total'] ?? 0,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => [
                    'order_reference' => $orderId,
                    'checkout_snapshot' => $snapshot,
                ],
            ]);

            $chargePayload = $this->buildTapChargePayload($order, $snapshot, $payment->id);
            
            Log::info('tap.init.request', ['payment_id' => $payment->id, 'payload' => $chargePayload]);
            
            $resp = Http::withToken($secret)->acceptJson()->post('https://api.tap.company/v2/charges', $chargePayload);
            
            Log::info('tap.init.response', [
                'payment_id' => $payment->id, 
                'status' => $resp->status(), 
                'body_snippet' => substr($resp->body(), 0, 500)
            ]);

            if (!$resp->ok()) {
                throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
            }

            $json = $resp->json();
            $redirectUrl = $json['transaction']['url'] ?? null;

            if (!$redirectUrl) {
                throw new \Exception('Missing redirect URL');
            }

            $payment->payload = array_merge($payment->payload ?? [], [
                'tap_charge_id' => $json['id'] ?? null,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $redirectUrl];
        });
    }

    private function initGenericGateway(array $snapshot, PaymentGateway $gateway, string $slug): array
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

            $chargePayload = [
                'amount' => (float) number_format($snapshot['total'] ?? 0, 2, '.', ''),
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
                $resp = Http::withToken($secret)->acceptJson()->post($apiBase . '/charges', $chargePayload);
                
                Log::info($slug . '.init.response', [
                    'payment_id' => $payment->id, 
                    'status' => $resp->status(), 
                    'body_snippet' => substr($resp->body(), 0, 500)
                ]);

                if (!$resp->ok()) {
                    throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
                }

                $json = $resp->json();
                $redirectUrl = $json['transaction']['url'] ?? $json['redirect_url'] ?? ($json['data']['redirect_url'] ?? null);

                if (!$redirectUrl) {
                    throw new \Exception('Missing redirect URL');
                }

                $payment->payload = array_merge($payment->payload ?? [], [
                    $slug . '_charge_id' => $json['id'] ?? ($json['data']['id'] ?? null)
                ]);
                $payment->save();

                return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $json];
            } catch (\Throwable $e) {
                Log::error($slug . '.init.exception', [
                    'payment_id' => $payment->id, 
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    private function getPayPalToken(string $clientId, string $secret, string $base): string
    {
        $tokenResp = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->timeout(25)
            ->retry(2, 400)
            ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (!$tokenResp->ok()) {
            throw new \Exception('Token error: ' . $tokenResp->status() . ' ' . substr($tokenResp->body(), 0, 150));
        }

        $accessToken = $tokenResp->json('access_token');
        if (!$accessToken) {
            throw new \Exception('Token empty');
        }

        return $accessToken;
    }

    private function buildPayPalOrderPayload(?Order $order, ?array $snapshot, int $paymentId): array
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
                'return_url' => route('paypal.return', ['payment' => $paymentId]),
                'cancel_url' => route('paypal.cancel', ['payment' => $paymentId]),
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];
    }

    private function buildTapChargePayload(?Order $order, ?array $snapshot, int $paymentId): array
    {
        $amount = $order?->total ?? $snapshot['total'] ?? 0;
        $customerName = $order?->user?->name ?? $snapshot['customer_name'] ?? 'Customer';
        $customerEmail = $order?->user?->email ?? $snapshot['customer_email'] ?? 'customer@example.com';

        return [
            'amount' => (float) number_format($amount, 2, '.', ''),
            'currency' => strtoupper($order?->currency ?? $snapshot['currency'] ?? 'USD'),
            'threeDSecure' => true,
            'save_card' => false,
            'description' => $order ? 'Order #' . $order->id : 'Checkout',
            'statement_descriptor' => $order ? 'Order ' . $order->id : 'Checkout',
            'metadata' => ['order_id' => $order?->id, 'payment_id' => $paymentId],
            'redirect' => ['url' => route('tap.return', ['payment' => $paymentId])],
            'customer' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
            ],
            'source' => ['id' => 'src_all'],
        ];
    }

    private function extractPayPalApprovalUrl(array $ppData): string
    {
        foreach (($ppData['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? '';
            }
        }
        throw new \Exception('Approval link missing');
    }

    private function extractPayPalCaptureUrl(array $ppData): ?string
    {
        foreach (($ppData['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'capture') {
                return $link['href'] ?? null;
            }
        }
        return null;
    }

    private function mapPaymentStatus(?string $status): string
    {
        if (in_array(strtoupper($status), ['CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS'], true)) {
            return 'paid';
        }
        if (in_array(strtoupper($status), ['FAILED', 'CANCELLED', 'DECLINED'], true)) {
            return 'failed';
        }
        return 'processing';
    }

    private function handleSuccessfulPayment(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) {
            $snapshot = $payment->payload['checkout_snapshot'] ?? null;
            if ($snapshot) {
                try {
                    $order = DB::transaction(function () use ($snapshot, $payment) {
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

                        return $order;
                    });
                    $payment->order_id = $order->id;
                    $payment->save();
                } catch (\Throwable $e) {
                    Log::error('order.create_from_snapshot_failed', ['error' => $e->getMessage()]);
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
            // Ignore cart clearing errors
        }
    }
}
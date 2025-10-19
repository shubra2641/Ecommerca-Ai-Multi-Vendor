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
        $chargeId = $this->getChargeId($payment, $gateway);
        $secret = $this->getGatewaySecret($gateway);

        if (!$secret || !$chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }

        $response = $this->makeVerificationRequest($gateway, $secret, $chargeId);

        if (!$response['success']) {
            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }

        $this->updatePaymentStatus($payment, $response['status'], $gateway);

        if ($payment->status === 'paid') {
            $this->handleSuccessfulPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $response['data']];
    }

    private function initPayPalPayment(?Order $order, PaymentGateway $gateway, ?int $orderId, ?array $snapshot = null): array
    {
        $config = $this->getPayPalConfig($gateway);

        return DB::transaction(function () use ($order, $orderId, $snapshot, $config) {
            $payment = $this->createPayment('paypal', $order, $orderId, $snapshot);
            $accessToken = $this->getPayPalToken($config);
            $orderData = $this->createPayPalOrder($order, $snapshot, $payment->id, $accessToken, $config);

            $this->updatePaymentPayload($payment, $orderData);
            return ['payment' => $payment, 'redirect_url' => $orderData['approval_url'], 'paypal_order' => $orderData['raw']];
        });
    }

    private function initTapPayment(?Order $order, PaymentGateway $gateway, ?int $orderId, ?array $snapshot = null): array
    {
        $config = $this->getTapConfig($gateway, $order, $snapshot);

        return DB::transaction(function () use ($order, $orderId, $snapshot, $config) {
            $payment = $this->createPayment('tap', $order, $orderId, $snapshot);
            $chargeData = $this->createTapCharge($order, $snapshot, $payment->id, $config);

            $this->updatePaymentPayload($payment, $chargeData);
            return ['payment' => $payment, 'redirect_url' => $chargeData['redirect_url']];
        });
    }

    private function initGenericGateway(array $snapshot, PaymentGateway $gateway, string $slug): array
    {
        $config = $this->getGenericConfig($gateway, $snapshot, $slug);

        return DB::transaction(function () use ($snapshot, $config, $slug) {
            $payment = $this->createPayment($slug, null, null, $snapshot);
            $chargeData = $this->createGenericCharge($snapshot, $payment->id, $config, $slug);

            $this->updatePaymentPayload($payment, $chargeData);
            return ['payment' => $payment, 'redirect_url' => $chargeData['redirect_url'], 'raw' => $chargeData['raw']];
        });
    }

    private function getPayPalConfig(PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';

        return [
            'client_id' => $cfg['paypal_client_id'] ?? null,
            'secret' => $cfg['paypal_secret'] ?? null,
            'mode' => $mode,
            'base_url' => $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com'
        ];
    }

    private function getTapConfig(PaymentGateway $gateway, ?Order $order, ?array $snapshot): array
    {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper($cfg['tap_currency'] ?? ($order?->currency ?? $snapshot['currency'] ?? 'USD'));

        return [
            'secret' => $cfg['tap_secret_key'] ?? null,
            'currency' => $currency
        ];
    }

    private function getGenericConfig(PaymentGateway $gateway, array $snapshot, string $slug): array
    {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper($cfg[$slug . '_currency'] ?? ($snapshot['currency'] ?? 'USD'));

        return [
            'api_base' => rtrim($cfg['api_base'] ?? ('https://api.' . $slug . '.com'), '/'),
            'secret' => $cfg['secret_key'] ?? ($cfg['api_key'] ?? null),
            'currency' => $currency
        ];
    }

    private function createPayment(string $method, ?Order $order, ?int $orderId, ?array $snapshot): Payment
    {
        return Payment::create([
            'order_id' => $orderId,
            'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
            'method' => $method,
            'amount' => $order?->total ?? $snapshot['total'] ?? 0,
            'currency' => $order?->currency ?? $snapshot['currency'] ?? 'USD',
                'status' => 'pending',
            'payload' => [
                'order_reference' => $orderId,
                'checkout_snapshot' => $snapshot,
            ],
            ]);
    }

    private function getPayPalToken(array $config): string
    {
        $response = Http::withBasicAuth($config['client_id'], $config['secret'])
                ->asForm()
                ->timeout(25)
                ->retry(2, 400)
            ->post($config['base_url'] . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (!$response->ok()) {
            throw new \Exception('Token error: ' . $response->status());
        }

        $token = $response->json('access_token');
        if (!$token) {
                throw new \Exception('Token empty');
            }

        return $token;
    }

    private function createPayPalOrder(?Order $order, ?array $snapshot, int $paymentId, string $token, array $config): array
    {
        $currency = $order?->currency ?? $snapshot['currency'] ?? 'USD';
        $amount = $order?->total ?? $snapshot['total'] ?? 0;

        $payload = [
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

        $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(25)
                ->retry(2, 500)
            ->post($config['base_url'] . '/v2/checkout/orders', $payload);

        if ($response->status() < 200 || $response->status() >= 300) {
            throw new \Exception('Create error: ' . $response->status());
        }

        $data = $response->json();
        return [
            'approval_url' => $this->extractApprovalUrl($data),
            'capture_url' => $this->extractCaptureUrl($data),
            'order_id' => $data['id'] ?? null,
            'access_token' => $token,
            'raw' => $data
        ];
    }

    private function createTapCharge(?Order $order, ?array $snapshot, int $paymentId, array $config): array
    {
        $amount = $order?->total ?? $snapshot['total'] ?? 0;
        $customerName = $order?->user?->name ?? $snapshot['customer_name'] ?? 'Customer';
        $customerEmail = $order?->user?->email ?? $snapshot['customer_email'] ?? 'customer@example.com';

        $payload = [
            'amount' => (float) number_format($amount, 2, '.', ''),
            'currency' => $config['currency'],
                'threeDSecure' => true,
                'save_card' => false,
            'description' => $order ? 'Order #' . $order->id : 'Checkout',
            'statement_descriptor' => $order ? 'Order ' . $order->id : 'Checkout',
            'metadata' => ['order_id' => $order?->id, 'payment_id' => $paymentId],
            'redirect' => ['url' => route('tap.return', ['payment' => $paymentId])],
            'customer' => ['first_name' => $customerName, 'email' => $customerEmail],
            'source' => ['id' => 'src_all'],
        ];

        $response = Http::withToken($config['secret'])->acceptJson()->post('https://api.tap.company/v2/charges', $payload);

        if (!$response->ok()) {
            throw new \Exception('Charge error: ' . $response->status());
        }

        $data = $response->json();
        return [
            'redirect_url' => $data['transaction']['url'] ?? null,
            'charge_id' => $data['id'] ?? null,
            'raw' => $data
        ];
    }

    private function createGenericCharge(array $snapshot, int $paymentId, array $config, string $slug): array
    {
        $payload = [
            'amount' => (float) number_format($snapshot['total'] ?? 0, 2, '.', ''),
            'currency' => $config['currency'],
                'description' => 'Checkout',
            'metadata' => ['order_id' => null, 'payment_id' => $paymentId],
            'redirect' => ['url' => route($slug . '.return', ['payment' => $paymentId])],
                'customer' => [
                    'first_name' => $snapshot['customer_name'] ?? 'Customer',
                    'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                ],
        ];

        $response = Http::withToken($config['secret'])->acceptJson()->post($config['api_base'] . '/charges', $payload);

        if (!$response->ok()) {
            throw new \Exception('Charge error: ' . $response->status());
        }

        $data = $response->json();
        return [
            'redirect_url' => $data['transaction']['url'] ?? $data['redirect_url'] ?? ($data['data']['redirect_url'] ?? null),
            'charge_id' => $data['id'] ?? ($data['data']['id'] ?? null),
            'raw' => $data
        ];
    }

    private function getChargeId(Payment $payment, PaymentGateway $gateway): ?string
    {
        return $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;
    }

    private function getGatewaySecret(PaymentGateway $gateway): ?string
    {
        $cfg = $gateway->config ?? [];
        return $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);
    }

    private function makeVerificationRequest(PaymentGateway $gateway, string $secret, string $chargeId): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');

        try {
            $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);

            if (!$resp->ok()) {
                Log::warning($gateway->slug . '.verify.error', [
                    'payment_id' => $gateway->id,
                    'status' => $resp->status()
                ]);
                return ['success' => false, 'status' => 'pending', 'data' => null];
            }

            $json = $resp->json();
            $status = $json['status'] ?? $json['data']['status'] ?? null;
            $finalStatus = $this->mapPaymentStatus($status);

            return ['success' => true, 'status' => $finalStatus, 'data' => $json];
        } catch (\Throwable $e) {
            Log::warning($gateway->slug . '.verify.exception', [
                'payment_id' => $gateway->id,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'status' => 'pending', 'data' => null];
        }
    }

    private function updatePaymentStatus(Payment $payment, string $status, PaymentGateway $gateway): void
    {
        $payment->status = $status;
        $payment->payload = array_merge($payment->payload ?? [], [
            $gateway->slug . '_charge_status' => $status
        ]);
        $payment->save();
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

    private function updatePaymentPayload(Payment $payment, array $data): void
    {
        $payload = $payment->payload ?? [];

        if (isset($data['order_id'])) {
            $payload['paypal_order_id'] = $data['order_id'];
        }
        if (isset($data['approval_url'])) {
            $payload['paypal_approval_url'] = $data['approval_url'];
        }
        if (isset($data['capture_url'])) {
            $payload['paypal_capture_url'] = $data['capture_url'];
        }
        if (isset($data['access_token'])) {
            $payload['paypal_access_token'] = $data['access_token'];
        }
        if (isset($data['charge_id'])) {
            $payload['tap_charge_id'] = $data['charge_id'];
        }

        $payment->payload = $payload;
        $payment->save();
    }

    private function extractApprovalUrl(array $data): string
    {
        foreach (($data['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? '';
            }
        }
        throw new \Exception('Approval link missing');
    }

    private function extractCaptureUrl(array $data): ?string
    {
        foreach (($data['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === 'capture') {
                return $link['href'] ?? null;
            }
        }
        return null;
    }

    private function handleSuccessfulPayment(Payment $payment): void
    {
                $order = $payment->order;
        if (!$order) {
            $order = $this->createOrderFromSnapshot($payment);
        }

        if ($order && $order->status !== 'paid') {
            $order->status = 'paid';
            $order->save();
        }

        $this->clearCart();
    }

    private function createOrderFromSnapshot(Payment $payment): ?Order
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        if (!$snapshot) {
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
            Log::error('order.create_from_snapshot_failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function clearCart(): void
    {
        try {
            session()->forget('cart');
                } catch (\Throwable $_) {
            // Ignore cart clearing errors
        }
    }
}
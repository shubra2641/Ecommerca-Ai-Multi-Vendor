<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        $gw = new \App\Services\Payments\Gateways\TapGateway;

        return $gw->verifyCharge($payment, $gateway);
    }

    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $chargeId = $payment->payload[$gateway->slug.'_charge_id'] ?? $payment->payload['charge_id'] ?? null;
        $cfg = $gateway->config ?? [];
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);

        if (! $secret || ! $chargeId) {
            throw new \RuntimeException('Missing gateway secret or charge id for verify');
        }

        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.'.$gateway->slug.'.com'), '/');

        try {
            $resp = Http::withToken($secret)->acceptJson()->get($apiBase.'/charges/'.$chargeId);

            if (! $resp->ok()) {
                return ['payment' => $gateway, 'status' => 'pending', 'charge' => null];
            }

            $json = $resp->json();
            $status = $json['status'] ?? $json['data']['status'] ?? null;

            if (in_array(strtoupper($status), ['CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS'], true)) {
                $finalStatus = 'paid';
            } elseif (in_array(strtoupper($status), ['FAILED', 'CANCELLED', 'DECLINED'], true)) {
                $finalStatus = 'failed';
            } else {
                $finalStatus = 'processing';
            }

            $payment->status = $finalStatus;
            $payment->payload = array_merge($payment->payload ?? [], [
                $gateway->slug.'_charge_status' => $finalStatus,
            ]);
            $payment->save();

            if ($payment->status === 'paid') {
                $order = $payment->order;
                if (! $order) {
                    $order = $this->createOrderFromSnapshot($payment);
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

            return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'pending', 'data' => null];
        }
    }

    private function initPayPalPayment(
        ?Order $order,
        PaymentGateway $gateway,
        ?int $orderId,
        ?array $snapshot = null
    ): array {
        $cfg = $gateway->config ?? [];
        $mode = ($cfg['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        return DB::transaction(function () use ($order, $orderId, $snapshot, $cfg, $baseUrl) {
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
                ],
            ]);

            $response = Http::withBasicAuth($cfg['paypal_client_id'], $cfg['paypal_secret'])
                ->asForm()
                ->timeout(25)
                ->retry(2, 400)
                ->post($baseUrl.'/v1/oauth2/token', ['grant_type' => 'client_credentials']);

            if (! $response->ok()) {
                throw new \Exception('Token error: '.$response->status());
            }

            $token = $response->json('access_token');
            if (! $token) {
                throw new \Exception('Token empty');
            }

            $currency = $order?->currency ?? $snapshot['currency'] ?? 'USD';
            $amount = $order?->total ?? $snapshot['total'] ?? 0;

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.return', ['payment' => $payment->id]),
                    'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ];

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(25)
                ->retry(2, 500)
                ->post($baseUrl.'/v2/checkout/orders', $payload);

            if ($response->status() < 200 || $response->status() >= 300) {
                throw new \Exception('Create error: '.$response->status());
            }

            $data = $response->json();
            $approvalUrl = '';
            foreach (($data['links'] ?? []) as $link) {
                if (($link['rel'] ?? '') === 'approve') {
                    $approvalUrl = $link['href'] ?? '';
                    break;
                }
            }

            if (! $approvalUrl) {
                throw new \Exception('Approval link missing');
            }

            $payment->payload = array_merge($payment->payload ?? [], [
                'paypal_order_id' => $data['id'] ?? null,
                'paypal_approval_url' => $approvalUrl,
                'paypal_access_token' => $token,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $data];
        });
    }

    private function initTapPayment(
        ?Order $order,
        PaymentGateway $gateway,
        ?int $orderId,
        ?array $snapshot = null
    ): array {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper(
            $cfg['tap_currency'] ?? ($order?->currency ?? $snapshot['currency'] ?? 'USD')
        );

        return DB::transaction(function () use ($order, $orderId, $snapshot, $cfg, $currency) {
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id' => $order?->user_id ?? $snapshot['user_id'] ?? null,
                'method' => 'tap',
                'amount' => $order?->total ?? $snapshot['total'] ?? 0,
                'currency' => $order?->currency ?? $snapshot['currency'] ?? 'USD',
                'status' => 'pending',
                'payload' => [
                    'order_reference' => $orderId,
                    'checkout_snapshot' => $snapshot,
                ],
            ]);

            $amount = $order?->total ?? $snapshot['total'] ?? 0;
            $customerName = $order?->user?->name ?? $snapshot['customer_name'] ?? 'Customer';
            $customerEmail = $order?->user?->email ?? $snapshot['customer_email'] ?? 'customer@example.com';

            $payload = [
                'amount' => (float) number_format($amount, 2, '.', ''),
                'currency' => $currency,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => $order ? 'Order #'.$order->id : 'Checkout',
                'statement_descriptor' => $order ? 'Order '.$order->id : 'Checkout',
                'metadata' => ['order_id' => $order?->id, 'payment_id' => $payment->id],
                'redirect' => ['url' => route('tap.return', ['payment' => $payment->id])],
                'customer' => ['first_name' => $customerName, 'email' => $customerEmail],
                'source' => ['id' => 'src_all'],
            ];

            $response = Http::withToken($cfg['tap_secret_key'])
                ->acceptJson()
                ->post('https://api.tap.company/v2/charges', $payload);

            if (! $response->ok()) {
                throw new \Exception('Charge error: '.$response->status());
            }

            $data = $response->json();
            $payment->payload = array_merge($payment->payload ?? [], [
                'tap_charge_id' => $data['id'] ?? null,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $data['transaction']['url'] ?? null];
        });
    }

    private function initGenericGateway(array $snapshot, PaymentGateway $gateway, string $slug): array
    {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper($cfg[$slug.'_currency'] ?? ($snapshot['currency'] ?? 'USD'));
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.'.$slug.'.com'), '/');

        return DB::transaction(function () use ($snapshot, $cfg, $currency, $apiBase, $slug) {
            $payment = Payment::create([
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

            $payload = [
                'amount' => (float) number_format($snapshot['total'] ?? 0, 2, '.', ''),
                'currency' => $currency,
                'description' => 'Checkout',
                'metadata' => ['order_id' => null, 'payment_id' => $payment->id],
                'redirect' => [
                    'url' => route($slug.'.return', ['payment' => $payment->id]),
                ],
                'customer' => [
                    'first_name' => $snapshot['customer_name'] ?? 'Customer',
                    'email' => $snapshot['customer_email'] ?? 'customer@example.com',
                ],
            ];

            $response = Http::withToken(
                $cfg['secret_key'] ?? ($cfg['api_key'] ?? null)
            )
                ->acceptJson()
                ->post($apiBase.'/charges', $payload);

            if (! $response->ok()) {
                throw new \Exception('Charge error: '.$response->status());
            }

            $data = $response->json();
            $redirectUrl = $data['transaction']['url'] ??
                $data['redirect_url'] ??
                $data['data']['redirect_url'] ??
                null;
            $chargeId = $data['id'] ?? $data['data']['id'] ?? null;

            $payment->payload = array_merge($payment->payload ?? [], [
                $slug.'_charge_id' => $chargeId,
            ]);
            $payment->save();

            return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $data];
        });
    }

    private function createOrderFromSnapshot(Payment $payment): ?Order
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        if (! $snapshot) {
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
            return null;
        }
    }
}

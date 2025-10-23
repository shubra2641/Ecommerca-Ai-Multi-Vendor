<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TapGateway
{
    /**
     * Initialize a Tap charge from a checkout snapshot and return payment + redirect_url
     */
    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway): array
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

            $resp = Http::withToken($secret)
                ->acceptJson()
                ->post('https://api.tap.company/v2/charges', $chargePayload);
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

            return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $json];
        });
    }

    /**
     * Verify Tap charge status and update payment/order. Returns same shape as previous method.
     */
    public function verifyCharge(Payment $payment, PaymentGateway $gateway): array
    {
        if ($payment->method !== 'tap') {
            throw new \InvalidArgumentException('Payment not Tap');
        }
        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;
        $chargeId = $payment->payload['tap_charge_id'] ?? null;
        if (! $secret || ! $chargeId) {
            throw new \RuntimeException('Missing Tap secret or charge id');
        }
        $resp = Http::withToken($secret)->acceptJson()->get('https://api.tap.company/v2/charges/' . $chargeId);
        if (! $resp->ok()) {
            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }
        $json = $resp->json();
        $tapStatus = $json['status'] ?? null;
        $final = null;
        if (in_array($tapStatus, ['CAPTURED', 'AUTHORIZED'])) {
            $final = 'paid';
        } elseif (in_array($tapStatus, ['FAILED', 'CANCELLED'])) {
            $final = 'failed';
        } else {
            $final = 'processing';
        }
        $payment->status = $final === 'paid' ? 'paid' : ($final === 'failed' ? 'failed' : 'processing');
        $payment->payload = array_merge($payment->payload ?? [], ['tap_charge_status' => $tapStatus]);
        $payment->save();

        if ($payment->status === 'paid') {
            $order = $payment->order;
            if (! $order) {
                $snap = $payment->payload['checkout_snapshot'] ?? null;
                if ($snap) {
                    try {
                        $order = DB::transaction(function () use ($snap) {
                            $order = \App\Models\Order::create([
                                'user_id' => $snap['user_id'] ?? null,
                                'status' => 'completed',
                                'total' => $snap['total'] ?? 0,
                                'items_subtotal' => $snap['total'] ?? 0,
                                'currency' => $snap['currency'] ?? config('app.currency', 'USD'),
                                'shipping_address' => $snap['shipping_address'] ?? null,
                                'payment_method' => 'tap',
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
                    }
                }
            }
            if ($order && $order->status !== 'paid') {
                $order->status = 'paid';
                $order->save();
            }
            try {
                session()->forget('cart');
            } catch (\Throwable $e) {
                logger()->warning('Failed to clear session after payment: ' . $e->getMessage());
            }
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
    }
}

<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayeerGateway
{
    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? 'https://payeer.com/api', '/');
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);
        $currency = strtoupper($cfg['payeer_currency'] ?? ($snapshot['currency'] ?? 'USD'));

        return \Illuminate\Support\Facades\DB::transaction(function () use ($snapshot, $apiBase, $secret, $currency) {
            $payment = Payment::create([
                'order_id' => null,
                'user_id' => $snapshot['user_id'] ?? null,
                'method' => 'payeer',
                'amount' => $snapshot['total'] ?? 0,
                'currency' => $currency,
                'status' => 'pending',
                'payload' => ['checkout_snapshot' => $snapshot],
            ]);

            $chargePayload = [
                'amount' => (float) number_format($snapshot['total'] ?? 0, 2, '.', ''),
                'currency' => $currency,
                'description' => 'Checkout',
                'redirect' => ['url' => route('payeer.return', ['payment' => $payment->id])],
                'customer' => ['first_name' => $snapshot['customer_name'] ?? 'Customer', 'email' => $snapshot['customer_email'] ?? 'customer@example.com'],
            ];

            Log::info('payeer.init.request', ['payment_id' => $payment->id, 'payload' => $chargePayload]);
            try {
                $client = Http::acceptJson();
                if (! empty($secret)) {
                    $client = $client->withToken($secret);
                }
                $resp = $client->post($apiBase . '/charges', $chargePayload);
                Log::info('payeer.init.response', ['payment_id' => $payment->id, 'status' => $resp->status(), 'body_snippet' => substr($resp->body(), 0, 500)]);
                try {
                    Log::info('payeer.init.response_headers', ['payment_id' => $payment->id, 'headers' => method_exists($resp, 'headers') ? $resp->headers() : null]);
                } catch (\Throwable $_) {
                }
                if (! $resp->ok()) {
                    throw new \Exception('Charge error: ' . $resp->status() . ' ' . substr($resp->body(), 0, 200));
                }
                $json = $resp->json();
                $redirectUrl = $json['redirect_url'] ?? $json['data']['redirect_url'] ?? null;
                if (! $redirectUrl) {
                    throw new \Exception('Missing redirect URL');
                }
                $payment->payload = array_merge($payment->payload ?? [], ['payeer_charge_id' => $json['id'] ?? ($json['data']['id'] ?? null)]);
                $payment->save();

                return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $json];
            } catch (\Throwable $e) {
                Log::error('payeer.init.exception', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
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

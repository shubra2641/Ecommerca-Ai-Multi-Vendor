<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

final class PayPalGateway
{
    public function initPayment(Order $order, PaymentGateway $gateway, int $orderId): array
    {
        return $this->initPayPalPayment($order, $gateway, $orderId);
    }

    public function initPaymentFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initPayPalPayment(null, $gateway, null, $snapshot);
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
            $payment = $this->createPayment($order, $orderId, $snapshot);

            $token = $this->getAccessToken($cfg, $baseUrl);
            $payload = $this->buildOrderPayload($order, $snapshot);

            $response = $this->createPayPalOrder($baseUrl, $token, $payload);
            $data = $response->json();
            $approvalUrl = $this->extractApprovalUrl($data);

            $this->updatePaymentPayload($payment, $data, $token);

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $data];
        });
    }

    private function createPayment(?Order $order, ?int $orderId, ?array $snapshot): Payment
    {
        return Payment::create([
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
    }

    private function getAccessToken(array $cfg, string $baseUrl): string
    {
        $response = Http::withBasicAuth($cfg['paypal_client_id'], $cfg['paypal_secret'])
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

    private function buildOrderPayload(?Order $order, ?array $snapshot): array
    {
        $currency = $order?->currency ?? $snapshot['currency'] ?? 'USD';
        $amount = $order?->total ?? $snapshot['total'] ?? 0;

        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('paypal.return', ['payment' => 0]), // Will be updated after payment creation
                'cancel_url' => route('paypal.cancel', ['payment' => 0]), // Will be updated after payment creation
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];
    }

    private function createPayPalOrder(string $baseUrl, string $token, array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(25)
            ->retry(2, 500)
            ->post($baseUrl . '/v2/checkout/orders', $payload);

        if ($response->status() < 200 || $response->status() >= 300) {
            throw new \Exception('Create error: ' . $response->status());
        }

        return $response;
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

    private function updatePaymentPayload(Payment $payment, array $data, string $token): void
    {
        $payment->payload = array_merge($payment->payload ?? [], [
            'paypal_order_id' => $data['id'] ?? null,
            'paypal_approval_url' => $this->extractApprovalUrl($data),
            'paypal_access_token' => $token,
        ]);

        // Update return/cancel URLs with actual payment ID
        $payment->payload['application_context'] = [
            'return_url' => route('paypal.return', ['payment' => $payment->id]),
            'cancel_url' => route('paypal.cancel', ['payment' => $payment->id]),
            'shipping_preference' => 'NO_SHIPPING',
        ];

        $payment->save();
    }
}
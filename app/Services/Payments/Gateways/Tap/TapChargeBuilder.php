<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways\Tap;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

final class TapChargeBuilder
{
    public function buildChargePayload(?Order $order, ?array $snapshot, Payment $payment, string $currency): array
    {
        $amount = $order?->total ?? $snapshot['total'] ?? 0;
        $customerName = $order?->user?->name ?? $snapshot['customer_name'] ?? 'Customer';
        $customerEmail = $order?->user?->email ?? $snapshot['customer_email'] ?? 'customer@example.com';

        return [
            'amount' => (float) number_format($amount, 2, '.', ''),
            'currency' => $currency,
            'threeDSecure' => true,
            'save_card' => false,
            'description' => $order ? 'Order #' . $order->id : 'Checkout',
            'statement_descriptor' => $order ? 'Order ' . $order->id : 'Checkout',
            'metadata' => ['order_id' => $order?->id, 'payment_id' => $payment->id],
            'redirect' => ['url' => route('tap.return', ['payment' => $payment->id])],
            'customer' => ['first_name' => $customerName, 'email' => $customerEmail],
            'source' => ['id' => 'src_all'],
        ];
    }

    public function createCharge(array $config, array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($config['tap_secret_key'])
            ->acceptJson()
            ->post('https://api.tap.company/v2/charges', $payload);

        if (!$response->ok()) {
            throw new \Exception('Charge error: ' . $response->status());
        }

        return $response;
    }
}
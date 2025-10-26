<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Tap\TapChargeBuilder;
use App\Services\Payments\Gateways\Tap\TapPaymentCreator;
use Illuminate\Support\Facades\DB;

final class TapGateway
{
    public function __construct(
        private readonly TapChargeBuilder $chargeBuilder,
        private readonly TapPaymentCreator $paymentCreator,
    ) {
    }

    public function initPayment(Order $order, PaymentGateway $gateway, int $orderId): array
    {
        return $this->initTapPayment($order, $gateway, $orderId);
    }

    public function initPaymentFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->initTapPayment(null, $gateway, null, $snapshot);
    }

    public function verifyCharge(Payment $payment, PaymentGateway $gateway): array
    {
        return $this->verifyTapCharge($payment, $gateway);
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
            $payment = $this->paymentCreator->createPayment($order, $orderId, $snapshot);

            $payload = $this->chargeBuilder->buildChargePayload($order, $snapshot, $payment, $currency);
            $response = $this->chargeBuilder->createCharge($cfg, $payload);

            $data = $response->json();
            $this->paymentCreator->updatePaymentPayload($payment, $data);

            return ['payment' => $payment, 'redirect_url' => $data['transaction']['url'] ?? null];
        });
    }

    private function verifyTapCharge(Payment $payment, PaymentGateway $gateway): array
    {
        if ($payment->method !== 'tap') {
            throw new \InvalidArgumentException('Payment not Tap');
        }

        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;
        $chargeId = $payment->payload['tap_charge_id'] ?? null;

        if (!$secret || !$chargeId) {
            throw new \RuntimeException('Missing Tap secret or charge id');
        }

        $response = \Illuminate\Support\Facades\Http::withToken($secret)
            ->acceptJson()
            ->get('https://api.tap.company/v2/charges/' . $chargeId);

        if (!$response->ok()) {
            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }

        $json = $response->json();
        $tapStatus = $json['status'] ?? null;
        $finalStatus = $this->determineStatus($tapStatus);

        $payment->status = $finalStatus;
        $payment->payload = array_merge($payment->payload ?? [], ['tap_charge_status' => $tapStatus]);
        $payment->save();

        if ($payment->status === 'paid') {
            $this->handlePaidPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
    }

    private function determineStatus(?string $status): string
    {
        if (!$status) {
            return 'processing';
        }

        return match (strtoupper($status)) {
            'CAPTURED', 'AUTHORIZED' => 'paid',
            'FAILED', 'CANCELLED' => 'failed',
            default => 'processing',
        };
    }

    private function handlePaidPayment(Payment $payment): void
    {
        $order = $payment->order;
        if (!$order) {
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
            null;
        }
    }

    private function createOrderFromSnapshot(Payment $payment): ?\App\Models\Order
    {
        $snapshot = $payment->payload['checkout_snapshot'] ?? null;
        if (!$snapshot) {
            return null;
        }

        try {
            return DB::transaction(function () use ($snapshot, $payment) {
                $order = \App\Models\Order::create([
                    'user_id' => $snapshot['user_id'] ?? null,
                    'status' => 'completed',
                    'total' => $snapshot['total'] ?? 0,
                    'items_subtotal' => $snapshot['total'] ?? 0,
                    'currency' => $snapshot['currency'] ?? config('app.currency', 'USD'),
                    'shipping_address' => $snapshot['shipping_address'] ?? null,
                    'payment_method' => 'tap',
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

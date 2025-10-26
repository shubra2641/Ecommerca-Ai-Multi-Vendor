<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Tap\TapChargeBuilder;
use App\Services\Payments\Gateways\Tap\TapPaymentCreator;
use App\Services\Payments\Gateways\Verifier\OrderCreator;
use Illuminate\Support\Facades\DB;

final class TapGateway
{
    public function __construct(
        private readonly TapChargeBuilder $chargeBuilder,
        private readonly TapPaymentCreator $paymentCreator,
        private readonly OrderCreator $orderCreator,
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
        $this->validatePaymentMethod($payment);
        $credentials = $this->getTapCredentials($gateway);
        $chargeId = $this->getChargeId($payment);

        $response = $this->callTapApi($credentials['secret'], $chargeId);

        return $this->processTapResponse($payment, $response);
    }

    private function validatePaymentMethod(Payment $payment): void
    {
        if ($payment->method !== 'tap') {
            throw new \InvalidArgumentException('Payment not Tap');
        }
    }

    private function getTapCredentials(PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $secret = $cfg['tap_secret_key'] ?? null;

        if (!$secret) {
            throw new \RuntimeException('Missing Tap secret key');
        }

        return ['secret' => $secret];
    }

    private function getChargeId(Payment $payment): string
    {
        $chargeId = $payment->payload['tap_charge_id'] ?? null;

        if (!$chargeId) {
            throw new \RuntimeException('Missing Tap charge id');
        }

        return $chargeId;
    }

    private function callTapApi(string $secret, string $chargeId): array
    {
        $response = \Illuminate\Support\Facades\Http::withToken($secret)
            ->acceptJson()
            ->get('https://api.tap.company/v2/charges/' . $chargeId);

        if (!$response->ok()) {
            return ['status' => 'pending', 'data' => null];
        }

        return $response->json();
    }

    private function processTapResponse(Payment $payment, array $response): array
    {
        $tapStatus = $response['status'] ?? null;
        $finalStatus = $this->determineStatus($tapStatus);

        $this->updatePaymentStatus($payment, $finalStatus, $tapStatus);

        if ($payment->status === 'paid') {
            $this->orderCreator->handlePaidPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $response];
    }

    private function updatePaymentStatus(Payment $payment, string $status, ?string $tapStatus): void
    {
        $payment->status = $status;
        $payment->payload = array_merge($payment->payload ?? [], ['tap_charge_status' => $tapStatus]);
        $payment->save();
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
        $this->orderCreator->handlePaidPayment($payment);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Verifier\ApiRequestHandler;
use App\Services\Payments\Gateways\Verifier\StatusProcessor;
use App\Services\Payments\Gateways\Verifier\OrderCreator;

final class PaymentVerifier
{
    public function __construct(
        private readonly ApiRequestHandler $apiRequestHandler,
        private readonly StatusProcessor $statusProcessor,
        private readonly OrderCreator $orderCreator,
    ) {
    }

    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $payment->method . '.com'), '/');
        $chargeId = $payment->payload[$payment->method . '_charge_id'] ?? null;

        if (!$chargeId) {
            throw new \RuntimeException('Missing charge id');
        }

        $response = $this->apiRequestHandler->getChargeStatus($cfg, $apiBase, $chargeId);

        if (!$response->ok()) {
            return ['payment' => $payment, 'status' => 'pending', 'charge' => null];
        }

        $json = $response->json();
        $status = $this->statusProcessor->determineStatus($json['status'] ?? null);

        $payment->status = $status;
        $payment->payload = array_merge($payment->payload ?? [], [
            $payment->method . '_charge_status' => $json['status'] ?? null
        ]);
        $payment->save();

        if ($payment->status === 'paid') {
            $this->orderCreator->handlePaidPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $json];
    }
}
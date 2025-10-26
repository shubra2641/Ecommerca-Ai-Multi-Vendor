<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Verifier\OrderCreator;
use Illuminate\Support\Facades\Http;

final class PaymentVerifier
{
    public function __construct(
        private readonly OrderCreator $orderCreator,
    ) {
    }    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        $credentials = $this->getGatewayCredentials($gateway);
        $chargeId = $this->getChargeId($payment, $gateway);
        $apiBase = $this->getApiBase($gateway);

        $response = $this->makeApiRequest($apiBase, $credentials['secret'], $chargeId);

        return $this->processApiResponse($payment, $gateway, $response);
    }

    private function getGatewayCredentials(PaymentGateway $gateway): array
    {
        $cfg = $gateway->config ?? [];
        $secret = $cfg['secret_key'] ?? ($cfg['api_key'] ?? null);

        if (!$secret) {
            throw new \RuntimeException('Missing gateway secret or API key');
        }

        return ['secret' => $secret];
    }

    private function getChargeId(Payment $payment, PaymentGateway $gateway): string
    {
        $chargeId = $payment->payload[$gateway->slug . '_charge_id'] ?? $payment->payload['charge_id'] ?? null;

        if (!$chargeId) {
            throw new \RuntimeException('Missing charge id');
        }

        return $chargeId;
    }

    private function getApiBase(PaymentGateway $gateway): string
    {
        $cfg = $gateway->config ?? [];
        return rtrim($cfg['api_base'] ?? ('https://api.' . $gateway->slug . '.com'), '/');
    }

    private function makeApiRequest(string $apiBase, string $secret, string $chargeId): array
    {
        $resp = Http::withToken($secret)->acceptJson()->get($apiBase . '/charges/' . $chargeId);

        if (!$resp->ok()) {
            return ['status' => 'pending', 'data' => null];
        }

        return $resp->json();
    }

    private function processApiResponse(Payment $payment, PaymentGateway $gateway, array $response): array
    {
        $status = $response['status'] ?? $response['data']['status'] ?? null;
        $finalStatus = $this->determineStatus($status);

        $payment->status = $finalStatus;
        $payment->payload = array_merge($payment->payload ?? [], [
            $gateway->slug . '_charge_status' => $finalStatus
        ]);
        $payment->save();

        if ($finalStatus === 'paid') {
            $this->orderCreator->handlePaidPayment($payment);
        }

        return ['payment' => $payment, 'status' => $payment->status, 'charge' => $response];
    }

    private function determineStatus(?string $status): string
    {
        if (! $status) {
            return 'processing';
        }

        return match (strtoupper($status)) {
            'CAPTURED', 'AUTHORIZED', 'PAID', 'SUCCESS' => 'paid',
            'FAILED', 'CANCELLED', 'DECLINED' => 'failed',
            default => 'processing',
        };
    }
}

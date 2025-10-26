<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Generic\GenericChargeBuilder;
use App\Services\Payments\Gateways\Generic\GenericPaymentCreator;
use Illuminate\Support\Facades\DB;

final class GenericGateway
{
    public function __construct(
        private readonly GenericChargeBuilder $chargeBuilder,
        private readonly GenericPaymentCreator $paymentCreator,
    ) {
    }

    public function initFromSnapshot(array $snapshot, PaymentGateway $gateway, string $slug): array
    {
        $cfg = $gateway->config ?? [];
        $currency = strtoupper($cfg[$slug . '_currency'] ?? ($snapshot['currency'] ?? 'USD'));
        $apiBase = rtrim($cfg['api_base'] ?? ('https://api.' . $slug . '.com'), '/');

        return DB::transaction(function () use ($snapshot, $cfg, $apiBase, $slug) {
            $payment = $this->paymentCreator->createPayment($snapshot, $slug);

            $payload = $this->chargeBuilder->buildChargePayload($snapshot, $payment);
            $response = $this->chargeBuilder->createCharge($cfg, $apiBase, $payload);

            $data = $response->json();
            $redirectUrl = $this->chargeBuilder->extractRedirectUrl($data);
            $chargeId = $this->chargeBuilder->extractChargeId($data);

            $this->paymentCreator->updatePaymentPayload($payment, $slug, $chargeId);

            return ['payment' => $payment, 'redirect_url' => $redirectUrl, 'raw' => $data];
        });
    }
}

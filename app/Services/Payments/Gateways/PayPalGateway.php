<?php

declare(strict_types=1);

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\PayPal\PayPalOrderBuilder;
use App\Services\Payments\Gateways\PayPal\PayPalPaymentCreator;
use App\Services\Payments\Gateways\PayPal\PayPalTokenManager;
use Illuminate\Support\Facades\DB;

final class PayPalGateway
{
    public function __construct(
        private readonly PayPalTokenManager $tokenManager,
        private readonly PayPalOrderBuilder $orderBuilder,
        private readonly PayPalPaymentCreator $paymentCreator,
    ) {
    }

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
        $baseUrl = $this->orderBuilder->getBaseUrl($cfg);

        return DB::transaction(function () use ($order, $orderId, $snapshot, $cfg, $baseUrl) {
            $payment = $this->paymentCreator->createPayment($order, $orderId, $snapshot);

            $token = $this->tokenManager->getAccessToken($cfg);
            $payload = $this->orderBuilder->buildOrderPayload($order, $snapshot);

            $response = $this->orderBuilder->createPayPalOrder($baseUrl, $token, $payload);
            $data = $response->json();
            $approvalUrl = $this->orderBuilder->extractApprovalUrl($data);

            $this->paymentCreator->updatePaymentPayload($payment, $data, $token);

            return ['payment' => $payment, 'redirect_url' => $approvalUrl, 'paypal_order' => $data];
        });
    }
}

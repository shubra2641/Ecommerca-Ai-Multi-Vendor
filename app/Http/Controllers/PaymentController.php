<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function getGatewayCredentials(PaymentGateway $gateway): array
    {
        return $this->paymentService->getCredentials($gateway);
    }

    public function updateGatewayCredentials(PaymentGateway $gateway, array $credentials): void
    {
        $this->paymentService->updateCredentials($gateway, $credentials);
    }

    public function validateGatewayCredentials(PaymentGateway $gateway): bool
    {
        return $this->paymentService->hasValidCredentials($gateway);
    }

    public function getGatewayConfig(PaymentGateway $gateway, ?string $key = null)
    {
        return $this->paymentService->getGatewayConfig($gateway, $key);
    }

    public function getMaskedCredentials(PaymentGateway $gateway): array
    {
        return $this->paymentService->getMaskedCredentials($gateway);
    }
}

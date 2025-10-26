<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\GenericGateway;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\Gateways\TapGateway;

final class PaymentGatewayService
{
    public function __construct(
        private readonly PayPalGateway $payPalGateway,
        private readonly TapGateway $tapGateway,
        private readonly GenericGateway $genericGateway,
        private readonly PaymentVerifier $paymentVerifier,
    ) {}

    public function initPayPal(Order $order, PaymentGateway $gateway): array
    {
        return $this->payPalGateway->initPayment($order, $gateway, $order->id);
    }

    public function initPayPalFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->payPalGateway->initPaymentFromSnapshot($snapshot, $gateway);
    }

    public function initTap(Order $order, PaymentGateway $gateway): array
    {
        return $this->tapGateway->initPayment($order, $gateway, $order->id);
    }

    public function initTapFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->tapGateway->initPaymentFromSnapshot($snapshot, $gateway);
    }

    public function initPaytabsFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->genericGateway->initFromSnapshot($snapshot, $gateway, 'paytabs');
    }

    public function initWeacceptFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->genericGateway->initFromSnapshot($snapshot, $gateway, 'weaccept');
    }

    public function initPayeerFromSnapshot(array $snapshot, PaymentGateway $gateway): array
    {
        return $this->genericGateway->initFromSnapshot($snapshot, $gateway, 'payeer');
    }

    public function verifyTapCharge(Payment $payment, PaymentGateway $gateway): array
    {
        return $this->tapGateway->verifyCharge($payment, $gateway);
    }

    public function verifyGenericGatewayCharge(Payment $payment, PaymentGateway $gateway): array
    {
        return $this->paymentVerifier->verifyGenericGatewayCharge($payment, $gateway);
    }
}

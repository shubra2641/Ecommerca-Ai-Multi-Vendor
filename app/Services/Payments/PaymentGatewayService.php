<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\Gateways\Generic\GenericChargeBuilder;
use App\Services\Payments\Gateways\Generic\GenericPaymentCreator;
use App\Services\Payments\Gateways\GenericGateway;
use App\Services\Payments\Gateways\PaymentVerifier;
use App\Services\Payments\Gateways\PayPal\PayPalOrderBuilder;
use App\Services\Payments\Gateways\PayPal\PayPalPaymentCreator;
use App\Services\Payments\Gateways\PayPal\PayPalTokenManager;
use App\Services\Payments\Gateways\PayPalGateway;
use App\Services\Payments\Gateways\Tap\TapChargeBuilder;
use App\Services\Payments\Gateways\Tap\TapPaymentCreator;
use App\Services\Payments\Gateways\TapGateway;
use App\Services\Payments\Gateways\Verifier\ApiRequestHandler;
use App\Services\Payments\Gateways\Verifier\OrderCreator;
use App\Services\Payments\Gateways\Verifier\StatusProcessor;

final class PaymentGatewayService
{
    public function __construct(
        private readonly PayPalTokenManager $payPalTokenManager,
        private readonly PayPalOrderBuilder $payPalOrderBuilder,
        private readonly PayPalPaymentCreator $payPalPaymentCreator,
        private readonly PayPalGateway $payPalGateway,
        private readonly TapChargeBuilder $tapChargeBuilder,
        private readonly TapPaymentCreator $tapPaymentCreator,
        private readonly TapGateway $tapGateway,
        private readonly GenericChargeBuilder $genericChargeBuilder,
        private readonly GenericPaymentCreator $genericPaymentCreator,
        private readonly GenericGateway $genericGateway,
        private readonly ApiRequestHandler $apiRequestHandler,
        private readonly StatusProcessor $statusProcessor,
        private readonly OrderCreator $orderCreator,
        private readonly PaymentVerifier $paymentVerifier,
    ) {
    }

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

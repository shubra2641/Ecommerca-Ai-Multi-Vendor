<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

final class PaymentProcessingService
{
    public function __construct(
        private readonly OfflinePaymentHandler $offlinePaymentHandler,
        private readonly StripePaymentHandler $stripePaymentHandler
    ) {}

    /**
     * @return array<string, string>
     */
    public function processPayment(
        Order $order,
        PaymentGateway $gateway,
        Request $request
    ): array {
        if ($gateway->driver === 'offline') {
            return $this->offlinePaymentHandler->handleOfflinePayment(
                $order,
                $request
            );
        }

        if ($gateway->driver === 'stripe') {
            return $this->stripePaymentHandler->handleStripePayment(
                $order,
                $gateway
            );
        }

        throw new \Exception('Unsupported payment gateway');
    }
}

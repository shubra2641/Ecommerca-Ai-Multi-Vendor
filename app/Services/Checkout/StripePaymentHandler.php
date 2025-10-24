<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;

final class StripePaymentHandler
{
    /**
     * @return array<string, string>
     */
    public function handleStripePayment(Order $order, PaymentGateway $gateway): array
    {
        $secret = $this->resolveStripeSecret($gateway);
        $session = $this->createStripeSession($order, $secret);

        $this->recordStripePayment($order, $session);

        return [
            'type' => 'stripe',
            'redirect_url' => $session->url,
        ];
    }

    private function resolveStripeSecret(PaymentGateway $gateway): string
    {
        $stripeCfg = method_exists($gateway, 'getStripeConfig')
            ? $gateway->getStripeConfig()
            : [];
        $secret = $stripeCfg['secret_key'] ?? null;

        if (! $secret || ! class_exists(\Stripe\Stripe::class)) {
            throw new \Exception(__('Stripe not configured'));
        }

        return (string) $secret;
    }

    private function createStripeSession(Order $order, string $secret): \Stripe\Checkout\Session
    {
        \Stripe\Stripe::setApiKey($secret);

        return \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($order->currency ?? 'usd'),
                        'product_data' => [
                            'name' => 'Order #' . $order->id,
                        ],
                        'unit_amount' => (int) round(
                            ($order->total ?? 0) * 100
                        ),
                    ],
                    'quantity' => 1,
                ],
            ],
            'success_url' => url('/checkout/success?order=' . $order->id),
            'cancel_url' => url('/checkout/cancel?order=' . $order->id),
            'metadata' => ['order_id' => $order->id],
        ]);
    }

    private function recordStripePayment(
        Order $order,
        \Stripe\Checkout\Session $session
    ): void {
        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'stripe',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
            'payload' => ['stripe_session_id' => $session->id],
        ]);
    }
}

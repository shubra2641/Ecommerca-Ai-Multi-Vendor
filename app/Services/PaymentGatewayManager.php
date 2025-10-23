<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentGateway;

class PaymentGatewayManager
{
    // Get enabled gateways
    public function enabled()
    {
        return PaymentGateway::where('enabled', true)->get();
    }

    // Find gateway by slug
    public function find(string $slug)
    {
        return PaymentGateway::where('slug', $slug)->first();
    }

    // Create a payment intent / redirect URL (stub).
    // For real providers implement provider-specific logic.
    public function createPaymentUrl($gateway, $order)
    {
        if (! $gateway) {
            return null;
        }

        return match ($gateway->driver) {
            'stripe' => $this->createStripePaymentUrl($gateway, $order),
            default => null,
        };
    }

    private function createStripePaymentUrl($gateway, $order): ?string
    {
        $config = $this->getStripeConfig($gateway);
        $secret = $config['secret'] ?? env('STRIPE_SECRET');

        if (!$secret) {
            return null;
        }

        $lineItems = $this->buildStripeLineItems($order);
        $postData = $this->prepareStripePostData($order, $lineItems);

        return $this->makeStripeApiRequest($secret, $postData);
    }

    private function getStripeConfig($gateway): array
    {
        $config = $gateway->getCredentials();

        return empty($config) ? ($gateway->config ?? []) : $config;
    }

    private function buildStripeLineItems($order): array
    {
        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $order->currency ?? 'usd',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => (int) round($item->price * 100),
                ],
                'quantity' => (int) $item->qty,
            ];
        }

        return $lineItems;
    }

    private function prepareStripePostData($order, array $lineItems): array
    {
        $post = [];
        foreach ($lineItems as $i => $li) {
            $prefix = "line_items[{$i}]";
            $post["{$prefix}[price_data][currency]"] = $li['price_data']['currency'];
            $post["{$prefix}[price_data][product_data][name]"] = $li['price_data']['product_data']['name'];
            $post["{$prefix}[price_data][unit_amount]"] = $li['price_data']['unit_amount'];
            $post["{$prefix}[quantity]"] = $li['quantity'];
        }

        $success = url('/admin/orders/' . $order->id);
        $cancel = url('/order/' . $order->id);

        $post['mode'] = 'payment';
        $post['success_url'] = $success . '?session_id={CHECKOUT_SESSION_ID}';
        $post['cancel_url'] = $cancel;
        $post['payment_method_types[]'] = 'card';
        $post['metadata[order_id]'] = $order->id;

        return $post;
    }

    private function makeStripeApiRequest(string $secret, array $postData): ?string
    {
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $secret,
        ]);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return null;
        }

        $json = json_decode($resp, true);
        return $json['url'] ?? null;
    }
}

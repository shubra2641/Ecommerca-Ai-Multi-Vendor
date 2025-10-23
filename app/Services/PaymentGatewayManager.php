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
        if ($gateway->driver === 'stripe') {
            $config = $gateway->getCredentials();

            // Fallback to legacy config if no credentials found
            if (empty($config)) {
                $config = $gateway->config ?? [];
            }

            $secret = $config['secret'] ?? env('STRIPE_SECRET');
            if (! $secret) {
                return null;
            }

            // Build line items
            $line_items = [];
            foreach ($order->items as $item) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => $order->currency ?? 'usd',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => (int) round($item->price * 100),
                    ],
                    'quantity' => (int) $item->qty,
                ];
            }

            $success = url('/admin/orders/'.$order->id); // admin redirect after success (can be changed)
            $cancel = url('/order/'.$order->id);

            // Prepare POST fields as form-encoded according to Stripe API
            $post = [];
            foreach ($line_items as $i => $li) {
                $prefix = "line_items[{$i}]";
                // price_data
                $post["{$prefix['price_data']}[currency]"] = $li['price_data']['currency'];
                $post["{$prefix['price_data']}[product_data][name]"] = $li['price_data']['product_data']['name'];
                $post["{$prefix['price_data']}[unit_amount]"] = $li['price_data']['unit_amount'];
                $post["{$prefix['quantity']}"] = $li['quantity'];
            }
            $post['mode'] = 'payment';
            $post['success_url'] = $success.'?session_id={CHECKOUT_SESSION_ID}';
            $post['cancel_url'] = $cancel;
            $post['payment_method_types[]'] = 'card';
            // metadata
            $post['metadata[order_id]'] = $order->id;

            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$secret,
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

        return null;
    }
}

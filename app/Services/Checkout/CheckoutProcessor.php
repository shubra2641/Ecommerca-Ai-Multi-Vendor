<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class CheckoutProcessor
{
    /**
     * Process checkout - simple version
     */
    public function processCheckout(Request $request, array $cart, array $validatedData): array
    {
        // Get gateway
        $gateway = PaymentGateway::where('slug', $validatedData['gateway'])->where('enabled', true)->first();
        if (!$gateway) {
            throw new \Exception(__('Selected payment method is not available'));
        }

        // Calculate total
        $total = 0;
        $items = [];
        foreach ($cart as $pid => $row) {
            $product = \App\Models\Product::find($pid);
            if (!$product) {
                continue;
            }

            $qty = $row['qty'];
            $price = $row['price'];
            $total += $price * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'variant' => $row['variant'] ?? null
            ];
        }

        return [
            'gateway' => $gateway,
            'items' => $items,
            'total' => $total,
            'user' => $request->user()
        ];
    }

    /**
     * Create simple order
     */
    public function createOrder(array $checkoutData, Request $request): Order
    {
        $order = Order::create([
            'user_id' => $checkoutData['user']?->id,
            'status' => 'pending',
            'total' => $checkoutData['total'],
            'items_subtotal' => $checkoutData['total'],
            'currency' => config('app.currency', 'USD'),
            'payment_method' => $checkoutData['gateway']->slug,
            'payment_status' => 'pending',
        ]);

        // Create order items
        foreach ($checkoutData['items'] as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'name' => $item['product']->name,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'purchased_at' => now(),
            ]);
        }

        return $order;
    }

    /**
     * Handle payment - simple version
     */
    public function processPayment(Order $order, PaymentGateway $gateway, Request $request): array
    {
        if ($gateway->driver === 'offline') {
            return $this->handleOfflinePayment($order, $request);
        }

        if ($gateway->driver === 'stripe') {
            return $this->handleStripePayment($order, $gateway);
        }

        throw new \Exception('Unsupported payment gateway');
    }

    /**
     * Handle offline payment
     */
    private function handleOfflinePayment(Order $order, Request $request): array
    {
        \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'offline',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return [
            'type' => 'offline',
            'redirect_url' => route('orders.show', $order),
        ];
    }

    /**
     * Handle Stripe payment
     */
    private function handleStripePayment(Order $order, PaymentGateway $gateway): array
    {
        $stripeCfg = method_exists($gateway, 'getStripeConfig') ? $gateway->getStripeConfig() : [];
        $secret = $stripeCfg['secret_key'] ?? null;

        if (!$secret || !class_exists(\Stripe\Stripe::class)) {
            throw new \Exception(__('Stripe not configured'));
        }

        \Stripe\Stripe::setApiKey($secret);

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($order->currency ?? 'usd'),
                    'product_data' => ['name' => 'Order #' . $order->id],
                    'unit_amount' => (int) round(($order->total ?? 0) * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => url('/checkout/success?order=' . $order->id),
            'cancel_url' => url('/checkout/cancel?order=' . $order->id),
            'metadata' => ['order_id' => $order->id],
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'stripe',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
            'payload' => ['stripe_session_id' => $session->id],
        ]);

        return [
            'type' => 'stripe',
            'redirect_url' => $session->url,
        ];
    }
}

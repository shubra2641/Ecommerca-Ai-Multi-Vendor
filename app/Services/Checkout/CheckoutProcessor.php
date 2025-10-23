<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class CheckoutProcessor
{
    /**
     * Process checkout - simple version
     */
    public function processCheckout(Request $request, array $cart, array $validatedData, float $discount = 0): array
    {
        // Get gateway
        $gateway = PaymentGateway::where('slug', $validatedData['gateway'])->where('enabled', true)->first();
        if (! $gateway) {
            throw new \Exception(__('Selected payment method is not available'));
        }

        // Calculate subtotal
        $subtotal = 0;
        $items = [];
        foreach ($cart as $pid => $row) {
            $product = \App\Models\Product::find($pid);
            if (! $product) {
                continue;
            }

            $qty = $row['qty'];
            $price = $row['price'];
            $subtotal += $price * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'variant' => $row['variant'] ?? null,
            ];
        }

        // Apply discount
        $subtotal -= $discount;

        // Add shipping cost if provided
        $shippingPrice = $validatedData['shipping_price'] ?? 0;
        $total = $subtotal + $shippingPrice;

        // Prepare shipping address
        $shippingAddress = [
            'customer_name' => $validatedData['customer_name'],
            'customer_email' => $validatedData['customer_email'],
            'customer_phone' => $validatedData['customer_phone'],
            'customer_address' => $validatedData['customer_address'],
            'country_id' => $validatedData['country'],
            'governorate_id' => $validatedData['governorate'] ?? null,
            'city_id' => $validatedData['city'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
        ];

        return [
            'gateway' => $gateway,
            'items' => $items,
            'total' => $total,
            'items_subtotal' => $subtotal,
            'shipping_price' => $shippingPrice,
            'shipping_zone_id' => $validatedData['shipping_zone_id'] ?? null,
            'shipping_estimated_days' => $validatedData['shipping_estimated_days'] ?? null,
            'shipping_address' => $shippingAddress,
            'selected_address_id' => $validatedData['selected_address_id'] ?? null,
            'user' => $request->user(),
        ];
    }

    /**
     * Create simple order
     */
    public function createOrder(array $checkoutData, Request $request): Order
    {
        // Prepare base order payload
        $payload = [
            'user_id' => $checkoutData['user']?->id,
            'status' => 'pending',
            'total' => $checkoutData['total'],
            'items_subtotal' => $checkoutData['items_subtotal'],
            'currency' => config('app.currency', 'USD'),
            'payment_method' => $checkoutData['gateway']->slug,
            'payment_status' => 'pending',
            'shipping_price' => $checkoutData['shipping_price'] ?? 0,
            'shipping_zone_id' => $checkoutData['shipping_zone_id'] ?? null,
            'shipping_estimated_days' => $checkoutData['shipping_estimated_days'] ?? null,
            'shipping_address' => $checkoutData['shipping_address'] ?? null,
            'shipping_address_id' => null,
        ];

        // If caller provided a selected address id (from saved addresses), attach it
        if (! empty($checkoutData['selected_address_id'])) {
            $payload['shipping_address_id'] = $checkoutData['selected_address_id'];
        }

        // Create the order first so we can attach address if needed
        $order = Order::create($payload);

        // If no selected address id but user is authenticated, create an Address record and link it
        if (empty($payload['shipping_address_id']) && $checkoutData['user']) {
            try {
                $addrData = $checkoutData['shipping_address'] ?? [];
                $addr = \App\Models\Address::create([
                    'user_id' => $checkoutData['user']->id,
                    'name' => $addrData['customer_name'] ?? null,
                    'phone' => $addrData['customer_phone'] ?? null,
                    'country_id' => $addrData['country_id'] ?? null,
                    'governorate_id' => $addrData['governorate_id'] ?? null,
                    'city_id' => $addrData['city_id'] ?? null,
                    'line1' => $addrData['customer_address'] ?? null,
                    'line2' => null,
                    'postal_code' => null,
                    'is_default' => false,
                ]);
                if ($addr && $addr->id) {
                    $order->shipping_address_id = $addr->id;
                    $order->save();
                }
            } catch (\Throwable $e) {
                // swallow address creation errors but log
                logger()->warning('Failed to persist shipping address for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        // Create order items and reserve stock
        foreach ($checkoutData['items'] as $item) {
            $meta = [];
            $variantId = $item['variant'] ?? null;
            $variant = null;

            if ($variantId) {
                // Get variant details
                $variant = \App\Models\ProductVariation::find($variantId);
                if ($variant) {
                    $meta['variant_id'] = $variant->id; // Store variant_id for StockAdjustmentListener
                    $meta['variant'] = [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                    ];
                }
            }

            $name = $item['product']->name;
            if ($variant) {
                $name .= ' - ' . $variant->name;
            }

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'name' => $name,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'meta' => $meta,
                'purchased_at' => now(),
            ]);

            // Reserve stock when order is created
            if ($variant) {
                \App\Services\StockService::reserveVariation($variant, $item['qty']);
            } else {
                \App\Services\StockService::reserve($item['product'], $item['qty']);
            }
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
        $payment = \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'method' => 'offline',
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        // Handle transfer image if uploaded
        if ($request->hasFile('transfer_image')) {
            $file = $request->file('transfer_image');
            $path = $file->store('payments', 'public');
            \App\Models\PaymentAttachment::create([
                'payment_id' => $payment->id,
                'path' => $path,
                'mime' => $file->getMimeType(),
                'user_id' => $order->user_id,
            ]);
        }

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

        if (! $secret || ! class_exists(\Stripe\Stripe::class)) {
            throw new \Exception(__('Stripe not configured'));
        }

        \Stripe\Stripe::setApiKey($secret);

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($order->currency ?? 'usd'),
                        'product_data' => ['name' => 'Order #' . $order->id],
                        'unit_amount' => (int) round(($order->total ?? 0) * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
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

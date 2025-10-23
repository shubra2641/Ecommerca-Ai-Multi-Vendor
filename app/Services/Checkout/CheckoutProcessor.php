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
        $payload = $this->prepareOrderPayload($checkoutData);
        $order = Order::create($payload);

        $this->handleShippingAddress($order, $checkoutData);
        $this->createOrderItems($order, $checkoutData);

        return $order;
    }

    private function prepareOrderPayload(array $checkoutData): array
    {
        return [
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
            'shipping_address_id' => $checkoutData['selected_address_id'] ?? null,
        ];
    }

    private function handleShippingAddress(Order $order, array $checkoutData): void
    {
        if (!empty($checkoutData['selected_address_id']) || !$checkoutData['user']) {
            return;
        }

        try {
            $addr = $this->createShippingAddress($checkoutData);
            if ($addr && $addr->id) {
                $order->shipping_address_id = $addr->id;
                $order->save();
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to persist shipping address for order ' . $order->id . ': ' . $e->getMessage());
        }
    }

    private function createShippingAddress(array $checkoutData): ?\App\Models\Address
    {
        $addrData = $checkoutData['shipping_address'] ?? [];
        return \App\Models\Address::create([
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
    }

    private function createOrderItems(Order $order, array $checkoutData): void
    {
        foreach ($checkoutData['items'] as $item) {
            $this->createOrderItem($order, $item);
        }
    }

    private function createOrderItem(Order $order, array $item): void
    {
        $meta = $this->prepareItemMeta($item);
        $name = $this->prepareItemName($item, $meta['variant'] ?? null);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item['product']->id,
            'name' => $name,
            'qty' => $item['qty'],
            'price' => $item['price'],
            'meta' => $meta,
            'purchased_at' => now(),
        ]);

        $this->reserveStock($item);
    }

    private function prepareItemMeta(array $item): array
    {
        $meta = [];
        $variantId = $item['variant'] ?? null;

        if ($variantId) {
            $variant = \App\Models\ProductVariation::find($variantId);
            if ($variant) {
                $meta['variant_id'] = $variant->id;
                $meta['variant'] = [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                ];
            }
        }

        return $meta;
    }

    private function prepareItemName(array $item, ?array $variant): string
    {
        $name = $item['product']->name;
        if ($variant) {
            $name .= ' - ' . $variant['name'];
        }

        return $name;
    }

    private function reserveStock(array $item): void
    {
        $variantId = $item['variant'] ?? null;

        if ($variantId) {
            $variant = \App\Models\ProductVariation::find($variantId);
            if ($variant) {
                \App\Services\StockService::reserveVariation($variant, $item['qty']);
            }
        } else {
            \App\Services\StockService::reserve($item['product'], $item['qty']);
        }
    }

    /**
     * Handle payment - simple version
     */
    public function processPayment(Order $order, PaymentGateway $gateway, Request $_request): array
    {
        if ($gateway->driver === 'offline') {
            return $this->handleOfflinePayment($order, $_request);
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

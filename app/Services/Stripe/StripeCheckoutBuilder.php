<?php

declare(strict_types=1);

namespace App\Services\Stripe;

use App\Models\Coupon;
use App\Models\Order;

/**
 * Builds an array payload for creating a Stripe Checkout Session without making network calls.
 * This makes it easy to unit test that the session will contain expected line items and metadata.
 */
class StripeCheckoutBuilder
{
    protected Order $order;

    protected ?Coupon $coupon;

    public function __construct(Order $order, ?Coupon $coupon = null)
    {
        $this->order = $order;
        $this->coupon = $coupon;
    }

    /**
     * Build the payload that would be passed to \Stripe\Checkout\Session::create
     */
    public function build(): array
    {
        $currency = strtolower($this->order->currency ?? config('app.currency', 'USD'));
        // items subtotal
        $itemsSubtotalCents = (int) round(($this->order->items_subtotal ?? 0) * 100);
        $lineItems = [];
        $lineItems[] = [
            'price_data' => [
                'currency' => $currency,
                'product_data' => ['name' => 'Items (subtotal)'],
                'unit_amount' => $itemsSubtotalCents,
            ],
            'quantity' => 1,
        ];

        if (! empty($this->order->shipping_price) && $this->order->shipping_price > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'Shipping'],
                    'unit_amount' => (int) round($this->order->shipping_price * 100),
                ],
                'quantity' => 1,
            ];
        }

        $metadata = ['order_id' => $this->order->id];
        if ($this->coupon) {
            $metadata['coupon_code'] = $this->coupon->code;
            if (! empty($this->coupon->stripe_coupon_id)) {
                $metadata['stripe_coupon_id'] = $this->coupon->stripe_coupon_id;
            }
        }

        return [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => url('/checkout/success?order='.$this->order->id),
            'cancel_url' => url('/checkout/cancel?order='.$this->order->id),
            'metadata' => $metadata,
        ];
    }
}

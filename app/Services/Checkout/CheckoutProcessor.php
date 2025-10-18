<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Checkout\AddressService;
use App\Services\Checkout\OrderService;
use App\Services\Checkout\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutProcessor
{
    public function __construct(
        private AddressService $addressService,
        private OrderService $orderService,
        private PaymentService $paymentService
    ) {}

    /**
     * Process complete checkout flow
     */
    public function processCheckout(Request $request, array $cart, array $validatedData): array
    {
        // Handle address selection
        $selectedAddress = $this->addressService->handleAddressSelection($request);
        
        // Prepare shipping data
        $shippingData = $this->prepareShippingData($request);
        
        // Validate gateway
        $gateway = $this->validateGateway($validatedData['gateway']);
        
        // Build cart items
        $cartData = $this->buildCartItems($cart);
        
        // Handle currency conversion
        $currencyData = $this->handleCurrencyConversion($cartData['total']);
        
        // Verify shipping selection
        $verifiedShipping = $this->verifyShippingSelection($shippingData);
        
        // Handle coupon application
        $couponData = $this->handleCouponApplication($cartData['total']);
        
        // Calculate final totals
        $finalShippingPrice = $verifiedShipping['price'] ?? null;
        $orderTotalToStore = $currencyData['total'] + ($finalShippingPrice ?? 0);

        return [
            'gateway' => $gateway,
            'cartData' => $cartData,
            'currencyData' => $currencyData,
            'couponData' => $couponData,
            'shippingData' => [
                'price' => $finalShippingPrice,
                'zone_id' => $verifiedShipping['zone_id'] ?? null,
                'eta' => $verifiedShipping['estimated_days'] ?? null,
            ],
            'selectedAddress' => $selectedAddress,
            'orderTotal' => $orderTotalToStore,
        ];
    }

    /**
     * Create order with items
     */
    public function createOrder(array $checkoutData, Request $request): Order
    {
        $shippingAddressPayload = $this->addressService->buildShippingAddressPayload(
            $checkoutData['selectedAddress'], 
            $request
        );

        $orderData = [
            'user_id' => $request->user()?->id,
            'status' => 'pending',
            'total' => $checkoutData['orderTotal'],
            'items_subtotal' => $checkoutData['cartData']['total'],
            'currency' => $checkoutData['currencyData']['currency'],
            'shipping_address' => $shippingAddressPayload,
            'shipping_address_id' => $checkoutData['selectedAddress']?->id,
            'shipping_price' => $checkoutData['shippingData']['price'],
            'shipping_zone_id' => $checkoutData['shippingData']['zone_id'],
            'shipping_estimated_days' => $checkoutData['shippingData']['eta'],
            'payment_method' => $checkoutData['gateway']->slug,
            'payment_status' => 'pending',
        ];

        return $this->orderService->createOrder($orderData, $checkoutData['cartData']['items'], $shippingAddressPayload);
    }

    /**
     * Handle payment processing based on gateway type
     */
    public function processPayment(Order $order, PaymentGateway $gateway, Request $request, $coupon = null): array
    {
        $redirectDrivers = ['paypal', 'tap', 'paytabs', 'weaccept', 'payeer'];
        $gwKey = $gateway->driver ?? $gateway->slug;

        if (in_array($gwKey, $redirectDrivers, true) || in_array($gateway->slug, $redirectDrivers, true)) {
            return $this->handleRedirectGateway($order, $gateway, $request, $coupon);
        }

        if ($gateway->driver === 'offline') {
            return $this->handleOfflinePayment($order, $request);
        }

        if ($gateway->driver === 'stripe') {
            return $this->handleStripePayment($order, $gateway, $coupon);
        }

        throw new \Exception('Unsupported payment gateway');
    }

    /**
     * Handle redirect-based gateways
     */
    private function handleRedirectGateway(Order $order, PaymentGateway $gateway, Request $request, $coupon): array
    {
        $snapshot = $this->buildPaymentSnapshot($order, $request, $coupon);
        $result = $this->paymentService->createExternalGatewayPayment($order, $gateway, $snapshot);
        
        return [
            'type' => 'redirect',
            'redirect_url' => $this->processRedirectUrl($result, $gateway),
            'payment' => $result['payment'] ?? null,
        ];
    }

    /**
     * Handle offline payment
     */
    private function handleOfflinePayment(Order $order, Request $request): array
    {
        $payment = $this->paymentService->createOfflinePayment($order, $request);
        
        return [
            'type' => 'offline',
            'payment' => $payment,
            'redirect_url' => route('orders.show', $order),
        ];
    }

    /**
     * Handle Stripe payment
     */
    private function handleStripePayment(Order $order, PaymentGateway $gateway, $coupon): array
    {
        $result = $this->paymentService->createStripePayment($order, $gateway, $coupon);
        
        return [
            'type' => 'stripe',
            'redirect_url' => $result['redirect_url'],
            'payment' => $result['payment'],
        ];
    }

    /**
     * Build payment snapshot for external gateways
     */
    private function buildPaymentSnapshot(Order $order, Request $request, $coupon): array
    {
        $user = $request->user();
        $inlineData = $this->extractInlineAddressData($request);

        return [
            'user_id' => $user?->id ?? null,
            'total' => $order->total,
            'currency' => $order->currency,
            'items' => $this->buildSnapshotItems($order),
            'shipping' => $order->shipping_price ? [
                'price' => $order->shipping_price,
                'zone_id' => $order->shipping_zone_id,
                'eta' => $order->shipping_estimated_days,
            ] : null,
            'customer_name' => $inlineData['name'] ?? null,
            'customer_email' => $user?->email ?? null,
            'customer_phone' => $inlineData['phone'] ?? null,
            'billing_city' => $inlineData['city'] ?? null,
            'billing_country' => $inlineData['country'] ?? null,
            'billing_postal_code' => $inlineData['postal_code'] ?? null,
            'billing_street' => $inlineData['line1'] ?? null,
        ];
    }

    /**
     * Build snapshot items from order
     */
    private function buildSnapshotItems(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'product_id' => $item->product_id,
                'name' => $item->name,
                'qty' => $item->qty,
                'price' => $item->price,
                'variant' => $item->meta['variant_id'] ?? null,
            ];
        }
        return $items;
    }

    /**
     * Process redirect URL with iframe support
     */
    private function processRedirectUrl(array $result, PaymentGateway $gateway): string
    {
        $originalRedirect = $result['redirect_url'] ?? null;
        $fallbackUrl = $result['fallback_url'] ?? null;
        
        $useLocalIframe = (bool) data_get($gateway->config ?? [], 'weaccept_use_local_iframe', env('PAYMOB_USE_LOCAL_IFRAME', false));
        
        if ($useLocalIframe && $this->shouldUseIframe($gateway, $originalRedirect)) {
            if (!empty($result['payment']?->id)) {
                return route('payments.iframe.payment', ['payment' => $result['payment']->id]);
            } else {
                $iframeBase = url('/payments/iframe') . '?redirect=' . urlencode($originalRedirect);
                if ($fallbackUrl) {
                    $iframeBase .= '&fallback=' . urlencode($fallbackUrl);
                }
                return $iframeBase;
            }
        }

        return $originalRedirect;
    }

    /**
     * Check if iframe should be used
     */
    private function shouldUseIframe(PaymentGateway $gateway, ?string $redirectUrl): bool
    {
        $isWeacceptDriver = (($gateway->driver ?? '') === 'weaccept');
        $redirectLooksLikePayMob = $redirectUrl ? str_contains($redirectUrl, 'accept.paymob.com') : false;
        
        return $isWeacceptDriver || $redirectLooksLikePayMob;
    }

    // Helper methods (moved from controller)
    private function prepareShippingData(Request $request): array
    {
        $shippingData = $request->only([
            'shipping_zone_id', 'shipping_price', 'shipping_country', 'shipping_governorate', 'shipping_city',
        ]);

        if (empty($shippingData['shipping_country']) && $request->filled('country')) {
            $shippingData['shipping_country'] = $request->input('country');
        }
        if (empty($shippingData['shipping_governorate']) && $request->filled('governorate')) {
            $shippingData['shipping_governorate'] = $request->input('governorate');
        }
        if (empty($shippingData['shipping_city']) && $request->filled('city')) {
            $shippingData['shipping_city'] = $request->input('city');
        }

        return $shippingData;
    }

    private function validateGateway(string $gatewaySlug): PaymentGateway
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->where('enabled', true)->first();
        if (!$gateway) {
            throw new \Exception(__('Selected payment method is not available'));
        }
        return $gateway;
    }

    private function buildCartItems(array $cart): array
    {
        $itemRows = [];
        $total = 0;

        foreach ($cart as $pid => $row) {
            $product = \App\Models\Product::find($pid);
            if (!$product) continue;

            $qty = $row['qty'];
            $price = $row['price'];
            $total += $price * $qty;

            $variant = $row['variant'] ?? null;
            if ($variant && is_int($variant)) {
                $variant = \App\Models\ProductVariation::find($variant);
            }

            $itemRows[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'variant' => $variant
            ];
        }

        return ['items' => $itemRows, 'total' => $total];
    }

    private function handleCurrencyConversion(float $total): array
    {
        $currentCurrency = session('currency_id') ? \App\Models\Currency::find(session('currency_id')) : \App\Models\Currency::getDefault();
        $defaultCurrency = \App\Models\Currency::getDefault();
        $orderTotalToStore = $total;
        $orderCurrencyCode = $defaultCurrency->code ?? config('app.currency', 'USD');

        try {
            if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                $orderTotalToStore = $defaultCurrency->convertTo($total, $currentCurrency, 2);
                $orderCurrencyCode = $currentCurrency->code;
            }
        } catch (\Throwable $e) {
            $orderTotalToStore = $total;
            $orderCurrencyCode = $defaultCurrency->code ?? config('app.currency', 'USD');
        }

        return ['total' => $orderTotalToStore, 'currency' => $orderCurrencyCode];
    }

    private function verifyShippingSelection(array $shippingData): ?array
    {
        $zoneId = $shippingData['shipping_zone_id'] ?? null;
        $country = $shippingData['shipping_country'] ?? null;
        $gov = $shippingData['shipping_governorate'] ?? null;
        $city = $shippingData['shipping_city'] ?? null;

        if (!$country) return null;

        $resolver = new \App\Services\Shipping\ShippingResolver();
        $resolved = $resolver->resolve($country, $gov, $city, $zoneId);

        if (!$resolved) return null;

        return [
            'zone_id' => $resolved['zone_id'],
            'price' => $resolved['price'],
            'estimated_days' => $resolved['estimated_days'],
        ];
    }

    private function handleCouponApplication(float $total): array
    {
        $coupon = null;
        $discount = 0;
        $finalTotal = $total;

        if (session()->has('applied_coupon_id')) {
            $coupon = \App\Models\Coupon::find(session('applied_coupon_id'));
            if ($coupon && $coupon->isValidForTotal($total)) {
                $discountedTotal = $coupon->applyTo($total);
                $discount = round($total - $discountedTotal, 2);
                $finalTotal = $discountedTotal;
            } else {
                session()->forget('applied_coupon_id');
                $coupon = null;
            }
        }

        return ['coupon' => $coupon, 'discount' => $discount, 'total' => $finalTotal];
    }

    private function extractInlineAddressData(Request $request): array
    {
        return [
            'name' => $request->input('customer_name') ?: $request->input('name') ?: $request->input('full_name') ?: $request->input('shipping_name'),
            'line1' => $request->input('customer_address') ?: $request->input('line1') ?: $request->input('address_line1') ?: $request->input('shipping_line1') ?: $request->input('address1'),
            'line2' => $request->input('line2') ?: $request->input('address_line2') ?: $request->input('shipping_line2') ?: $request->input('address2'),
            'phone' => $request->input('customer_phone') ?: $request->input('phone') ?: $request->input('shipping_phone'),
            'postal_code' => $request->input('postal_code') ?: $request->input('zip') ?: $request->input('postcode'),
            'country' => $request->input('shipping_country') ?: $request->input('country'),
            'governorate' => $request->input('shipping_governorate') ?: $request->input('governorate'),
            'city' => $request->input('shipping_city') ?: $request->input('city'),
        ];
    }
}

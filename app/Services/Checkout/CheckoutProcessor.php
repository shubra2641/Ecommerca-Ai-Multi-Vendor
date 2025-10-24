<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use Illuminate\Http\Request;

final class CheckoutProcessor
{
    public function __construct(
        private readonly OrderCreationService $orderCreationService,
        private readonly PaymentProcessingService $paymentProcessingService
    ) {
    }

    /**
     * Process checkout - simple version
     * @param array<int|string, mixed> $cart
     * @param array<string, mixed> $validatedData
     * @return array<string, mixed>
     */
    public function processCheckout(Request $request, array $cart, array $validatedData, float $discount = 0): array
    {
        $gateway = $this->resolveGateway($validatedData['gateway'] ?? null);
        [$items, $subtotal] = $this->mapCartItems($cart);

        $subtotal = $this->applyDiscount($subtotal, $discount);
        $shippingPrice = (float) ($validatedData['shipping_price'] ?? 0);
        $total = $subtotal + $shippingPrice;

        $shippingAddress = $this->buildShippingAddress($validatedData);

        return $this->buildCheckoutPayload(
            $gateway,
            $items,
            $subtotal,
            $shippingPrice,
            $total,
            $shippingAddress,
            $validatedData,
            $request
        );
    }

    /**
     * Create simple order
     * @param array<string, mixed> $checkoutData
     */
    public function createOrder(array $checkoutData): Order
    {
        return $this->orderCreationService->createOrder($checkoutData);
    }

    /**
     * Handle payment - simple version
     * @return array<string, string>
     */
    public function processPayment(Order $order, PaymentGateway $gateway, Request $request): array
    {
        return $this->paymentProcessingService->processPayment($order, $gateway, $request);
    }

    private function resolveGateway(?string $gatewaySlug): PaymentGateway
    {
        $gateway = PaymentGateway::query()
            ->where('slug', $gatewaySlug)
            ->where('enabled', true)
            ->first();

        if (! $gateway) {
            throw new \Exception(__('Selected payment method is not available'));
        }

        return $gateway;
    }

    /**
     * @param array<int|string, mixed> $cart
     * @return array{0: array<int, array<string, mixed>>, 1: float}
     */
    private function mapCartItems(array $cart): array
    {
        $subtotal = 0.0;
        $items = [];

        foreach ($cart as $productId => $row) {
            $product = $this->findProduct((int) $productId);

            if (! $product) {
                continue;
            }

            $qty = max(1, (int) ($row['qty'] ?? 1));
            $price = (float) ($row['price'] ?? 0);
            $subtotal += $price * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'variant' => $row['variant'] ?? null,
            ];
        }

        return [$items, $subtotal];
    }

    private function findProduct(int $productId): ?Product
    {
        return Product::find($productId);
    }

    private function applyDiscount(float $subtotal, float $discount): float
    {
        return max(0.0, $subtotal - max(0.0, $discount));
    }

    /**
     * @param array<string, mixed> $validatedData
     * @return array<string, mixed>
     */
    private function buildShippingAddress(array $validatedData): array
    {
        return [
            'customer_name' => $validatedData['customer_name'] ?? null,
            'customer_email' => $validatedData['customer_email'] ?? null,
            'customer_phone' => $validatedData['customer_phone'] ?? null,
            'customer_address' => $validatedData['customer_address'] ?? null,
            'country_id' => $validatedData['country'] ?? null,
            'governorate_id' => $validatedData['governorate'] ?? null,
            'city_id' => $validatedData['city'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<string, mixed> $shippingAddress
     * @param array<string, mixed> $validatedData
     * @return array<string, mixed>
     */
    private function buildCheckoutPayload(
        PaymentGateway $gateway,
        array $items,
        float $subtotal,
        float $shippingPrice,
        float $total,
        array $shippingAddress,
        array $validatedData,
        Request $request
    ): array {
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
}

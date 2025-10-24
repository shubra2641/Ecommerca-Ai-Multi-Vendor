<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;

final class OrderCreationService
{
    public function __construct(
        private readonly ShippingAddressHandler $shippingAddressHandler,
        private readonly OrderItemsCreator $orderItemsCreator
    ) {}

    /**
     * @param array<string, mixed> $checkoutData
     */
    public function createOrder(array $checkoutData): Order
    {
        $payload = $this->prepareOrderPayload($checkoutData);
        $order = Order::create($payload);

        $this->shippingAddressHandler->handleShippingAddress($order, $checkoutData);
        $this->orderItemsCreator->createOrderItems($order, $checkoutData);

        return $order;
    }

    /**
     * @param array<string, mixed> $checkoutData
     *
     * @return array<string, mixed>
     */
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
            'shipping_estimated_days' =>
            $checkoutData['shipping_estimated_days'] ?? null,
            'shipping_address' => $checkoutData['shipping_address'] ?? null,
            'shipping_address_id' =>
            $checkoutData['selected_address_id'] ?? null,
        ];
    }
}

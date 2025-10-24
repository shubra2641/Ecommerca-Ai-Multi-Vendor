<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Address;
use App\Models\Order;

final class ShippingAddressHandler
{
    /**
     * @param array $checkoutData
     */
    public function handleShippingAddress(Order $order, array $checkoutData): void
    {
        $selectedAddressId = $checkoutData['selected_address_id'] ?? null;

        if ($selectedAddressId === null && $checkoutData['user']) {
            $this->createAndAttachShippingAddress($order, $checkoutData);
        }
    }

    /**
     * @param array $checkoutData
     */
    private function createAndAttachShippingAddress(Order $order, array $checkoutData): void
    {
        try {
            $addr = $this->createShippingAddress($checkoutData);
            if ($addr?->id) {
                $order->shipping_address_id = $addr->id;
                $order->save();
            }
        } catch (\Throwable $e) {
            logger()->warning(
                'Failed to persist shipping address for order ' .
                    $order->id . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * @param array $checkoutData
     */
    private function createShippingAddress(array $checkoutData): ?Address
    {
        $addrData = $checkoutData['shipping_address'] ?? [];
        if (! $checkoutData['user']) {
            return null;
        }

        return Address::create([
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
}

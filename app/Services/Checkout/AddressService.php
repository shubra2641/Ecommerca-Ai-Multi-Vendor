<?php

namespace App\Services\Checkout;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressService
{
    /**
     * Handle address selection and creation for checkout
     */
    public function handleAddressSelection(Request $request): ?Address
    {
        $selectedAddressId = $request->input('selected_address_id');
        $selectedAddress = null;

        // Get selected address if exists
        if ($selectedAddressId) {
            $selectedAddress = Address::where('id', $selectedAddressId)
                ->where('user_id', $request->user()?->id)
                ->first();
        }

        // Create new address if none selected and user is logged in
        if (!$selectedAddress && $request->user()) {
            $selectedAddress = $this->createInlineAddress($request);
        }

        return $selectedAddress;
    }

    /**
     * Create address from inline form data
     */
    private function createInlineAddress(Request $request): ?Address
    {
        $inlineData = $this->extractInlineAddressData($request);
        
        if (!$this->hasRequiredAddressData($inlineData)) {
            return null;
        }

        try {
            $hasDefault = $request->user()->addresses()->where('is_default', true)->exists();
            
            return Address::create([
                'user_id' => $request->user()->id,
                'title' => $request->input('title') ?: ($hasDefault ? 'Address' : 'Default'),
                'name' => $inlineData['name'],
                'phone' => $inlineData['phone'],
                'country_id' => $inlineData['country'],
                'governorate_id' => $inlineData['governorate'],
                'city_id' => $inlineData['city'],
                'line1' => $inlineData['line1'],
                'line2' => $inlineData['line2'],
                'postal_code' => $inlineData['postal_code'],
                'is_default' => !$hasDefault,
            ]);
        } catch (\Throwable $e) {
            logger()->warning('Failed creating inline checkout address: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract address data from request with fallback field names
     */
    private function extractInlineAddressData(Request $request): array
    {
        return [
            'name' => $request->input('customer_name')
                ?: $request->input('name')
                ?: $request->input('full_name')
                ?: $request->input('shipping_name'),
            'line1' => $request->input('customer_address')
                ?: $request->input('line1')
                ?: $request->input('address_line1')
                ?: $request->input('shipping_line1')
                ?: $request->input('address1'),
            'line2' => $request->input('line2')
                ?: $request->input('address_line2')
                ?: $request->input('shipping_line2')
                ?: $request->input('address2'),
            'phone' => $request->input('customer_phone')
                ?: $request->input('phone')
                ?: $request->input('shipping_phone'),
            'postal_code' => $request->input('postal_code')
                ?: $request->input('zip')
                ?: $request->input('postcode'),
            'country' => $request->input('shipping_country') ?: $request->input('country'),
            'governorate' => $request->input('shipping_governorate') ?: $request->input('governorate'),
            'city' => $request->input('shipping_city') ?: $request->input('city'),
        ];
    }

    /**
     * Check if we have minimum required address data
     */
    private function hasRequiredAddressData(array $data): bool
    {
        return !empty($data['name']) || !empty($data['line1']) || !empty($data['city']);
    }

    /**
     * Build shipping address payload for order
     */
    public function buildShippingAddressPayload(?Address $address, Request $request): ?array
    {
        if ($address) {
            return [
                'id' => $address->id,
                'title' => $address->title ?? null,
                'name' => $address->name ?? null,
                'phone' => $address->phone ?? null,
                'country_id' => $address->country_id ?? null,
                'governorate_id' => $address->governorate_id ?? null,
                'city_id' => $address->city_id ?? null,
                'line1' => $address->line1 ?? null,
                'line2' => $address->line2 ?? null,
                'postal_code' => $address->postal_code ?? null,
            ];
        }

        // Fallback to inline data
        $inlineData = $this->extractInlineAddressData($request);
        if ($this->hasRequiredAddressData($inlineData)) {
            return [
                'title' => 'Inline',
                'name' => $inlineData['name'],
                'phone' => $inlineData['phone'],
                'country_id' => $inlineData['country'],
                'governorate_id' => $inlineData['governorate'],
                'city_id' => $inlineData['city'],
                'line1' => $inlineData['line1'],
                'line2' => $inlineData['line2'],
                'postal_code' => $inlineData['postal_code'],
            ];
        }

        return null;
    }
}

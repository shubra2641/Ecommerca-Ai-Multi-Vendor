<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressService
{
    /**
     * Get or create address for checkout
     */
    public function handleAddressSelection(Request $request): ?Address
    {
        $selectedAddressId = $request->input('selected_address_id');

        if ($selectedAddressId) {
            return Address::where('id', $selectedAddressId)
                ->where('user_id', $request->user()?->id)
                ->first();
        }

        return null;
    }
}

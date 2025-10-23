<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->isVendor() && $product->vendor_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isVendor() && $product->vendor_id === $user->id;
    }
}

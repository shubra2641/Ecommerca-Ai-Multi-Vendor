<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;

class PricingService
{
    public static function productEffectivePrice(Product $product): float
    {
        return $product->effectivePrice();
    }

    public static function variationEffectivePrice(ProductVariation $variation): float
    {
        return $variation->effectivePrice();
    }
}

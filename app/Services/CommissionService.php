<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class CommissionService
{
    /** Resolve commission rate for a product (%). */
    public static function rateForProduct(Product $product): float
    {
        if (! $product->vendor_id) {
            return 0.0; // only vendor products incur commission
        }

        $settings = self::getCommissionSettings();
        $mode = $settings->commission_mode ?? 'flat';

        return match ($mode) {
            'category' => self::getCategoryCommissionRate($product),
            default => (float) ($settings->commission_flat_rate ?? 0.0),
        };
    }

    private static function getCommissionSettings()
    {
        static $settings = null;
        if ($settings === null) {
            $settings = Setting::first();
        }

        return $settings;
    }

    private static function getCategoryCommissionRate(Product $product): float
    {
        $cat = $product->category;
        while ($cat) {
            if ($cat->commission_rate !== null) {
                return (float) $cat->commission_rate;
            }
            $cat = $cat->parent;
        }

        return 0.0;
    }

    /** Compute commission + vendor earnings for a line. */
    public static function breakdown(Product $product, int $qty, float $unitPrice): array
    {
        $subtotal = $unitPrice * $qty;
        $rate = self::rateForProduct($product);
        $commission = $rate > 0 ? round($subtotal * $rate / 100, 2) : 0.0;
        $vendorEarnings = round($subtotal - $commission, 2); // shipping & tax excluded (handled at order level)

        return [
            'rate' => $rate,
            'commission' => $commission,
            'vendor_earnings' => $vendorEarnings,
            'subtotal' => $subtotal,
        ];
    }
}

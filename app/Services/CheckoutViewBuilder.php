<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ProductVariation;

class CheckoutViewBuilder
{
    public function build(array $cart, ?int $currencyId, ?int $appliedCouponId, $user): array
    {
        $items = [];
        $total = 0;
        foreach ($cart as $pid => $row) {
            $product = Product::find($pid);
            if (! $product) {
                continue;
            }
            $qty = (int) ($row['qty'] ?? 1);
            $price = (float) ($row['price'] ?? $product->price ?? 0);
            $variant = $row['variant'] ?? null;
            if ($variant && is_int($variant)) {
                $variant = ProductVariation::find($variant);
            }
            $lineTotal = $price * $qty;
            $total += $lineTotal;
            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'lineTotal' => $lineTotal,
                'variant' => $variant,
            ];
        }

        $currentCurrency = $currencyId ? Currency::find($currencyId) : Currency::getDefault();
        $defaultCurrency = Currency::getDefault();
        $currency_symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
        $displayTotal = $total;
        try {
            if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                $displayTotal = $defaultCurrency->convertTo($total, $currentCurrency, 2);
            }
        } catch (\Throwable $e) {
            $displayTotal = $total;
        }

        // Per-line display conversions
        foreach ($items as &$it) {
            $it['display_price'] = $it['price'];
            $it['display_lineTotal'] = $it['lineTotal'];
            try {
                if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                    $it['display_price'] = $defaultCurrency->convertTo($it['price'], $currentCurrency, 2);
                    $it['display_lineTotal'] = $defaultCurrency->convertTo($it['lineTotal'], $currentCurrency, 2);
                }
            } catch (\Throwable $e) { /* leave defaults */
            }
        }

        // Coupon
        $coupon = null;
        $discount = 0;
        $discounted_total = $total;
        $displayDiscountedTotal = $displayTotal;
        if ($appliedCouponId) {
            $coupon = Coupon::find($appliedCouponId);
            if ($coupon && $coupon->isValidForTotal($total)) {
                $discounted_total = $coupon->applyTo($total);
                $discount = round($total - $discounted_total, 2);
                try {
                    if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                        $displayDiscountedTotal = $defaultCurrency->convertTo($discounted_total, $currentCurrency, 2);
                    } else {
                        $displayDiscountedTotal = $discounted_total;
                    }
                } catch (\Throwable $e) {
                    $displayDiscountedTotal = $discounted_total;
                }
            } else {
                $coupon = null; // invalid remove
            }
        }

        // Addresses
        $addresses = collect();
        $defaultAddress = null;
        if ($user) {
            try {
                $addresses = $user->addresses()->get();
                $defaultAddress = $addresses->firstWhere('is_default', true);
            } catch (\Throwable $e) {
0                \Log::warning('Error fetching user addresses', ['error' => $e->getMessage()]);
            }
        }

        $gateways = PaymentGateway::where('enabled', true)->get();

        // Pre-build order items for display (with variant label) & order summary lines
        foreach ($items as &$it) {
            $variantLabel = null;
            $variant = $it['variant'] ?? null;
            if ($variant) {
                if (is_object($variant)) {
                    $variantLabel = $variant->name ?? null;
                    if (! $variantLabel && ! empty($variant->attribute_data)) {
                        $variantLabel = collect($variant->attribute_data)
                            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                            ->values()
                            ->join(', ');
                    }
                } elseif (is_string($variant)) {
                    $parsed = @json_decode($variant, true);
                    if (is_array($parsed) && isset($parsed['attribute_data'])) {
                        $variantLabel = collect($parsed['attribute_data'])
                            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                            ->values()
                            ->join(', ');
                    } else {
                        $variantLabel = $variant;
                    }
                }
            }
            if (! $variantLabel && ! empty($it['attributes']) && is_array($it['attributes'])) {
                $variantLabel = implode(', ', $it['attributes']);
            }
            $it['variant_label'] = $variantLabel;
            // Resolve image
            $img = null;
            try {
                if (! empty($it['product']->image_url)) {
                    $img = $it['product']->image_url;
                } elseif (method_exists($it['product'], 'getFirstMediaUrl')) {
                    $img = $it['product']->getFirstMediaUrl('images');
                }
            } catch (\Throwable $e) {
            }
            $it['image'] = $img ?: asset('images/placeholder.svg');
        }

        // JS config
        $checkoutConfig = [
            'baseTotal' => (float) $displayDiscountedTotal,
            'rawItemsSubtotal' => (float) $total,
            'coupon' => $coupon ? [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount' => (float) $discount
            ] : null,
            'initial' => [
                'country' => $user?->country_id,
                'governorate' => $user?->governorate_id,
                'city' => $user?->city_id,
            ],
            'labels' => [
                'selectGovernorate' => __('Select Governorate'),
                'selectCity' => __('Select City'),
            ],
        ];

        return compact(
            'items',
            'total',
            'displayTotal',
            'gateways',
            'currency_symbol',
            'currentCurrency',
            'coupon',
            'discount',
            'discounted_total',
            'displayDiscountedTotal',
            'defaultAddress',
            'addresses',
            'checkoutConfig'
        );
    }
}

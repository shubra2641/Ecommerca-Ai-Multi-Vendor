<?php

declare(strict_types=1);

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
        [$items, $total] = $this->processCartItems($cart);
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

        if (!$currentCurrency || !$defaultCurrency || $currentCurrency->id === $defaultCurrency->id) {
            // no currency conversion needed
        } else {
            collect($items)->each(function (&$it) use ($defaultCurrency, $currentCurrency) {
                try {
                    $it['display_price'] = $defaultCurrency->convertTo($it['price'], $currentCurrency, 2);
                } catch (\Throwable $e) {
                    $it['display_price'] = $it['price'];
                }
                try {
                    $it['display_lineTotal'] = $defaultCurrency->convertTo($it['lineTotal'], $currentCurrency, 2);
                } catch (\Throwable $e) {
                    $it['display_lineTotal'] = $it['lineTotal'];
                }
            });
        }

        [$coupon, $discount, $discounted_total, $displayDiscountedTotal] = $this->applyCoupon($appliedCouponId, $total, $displayTotal, $currentCurrency, $defaultCurrency);

        $addresses = collect();
        $defaultAddress = null;
        if ($user) {
            try {
                $addresses = $user->addresses()->get();
                $defaultAddress = $addresses->firstWhere('is_default', true);
            } catch (\Throwable $e) {
                // addresses loading failed
            }
        }
        $gateways = PaymentGateway::where('enabled', true)->get();

        collect($items)->each(function (&$it) {
            $variant = $it['variant'] ?? null;
            if (!$variant) {
                $it['variant_label'] = null;
            } else {
                $it['variant_label'] = match (true) {
                    is_object($variant) => !empty($variant->name) ? $variant->name : (!empty($variant->attribute_data) ? collect($variant->attribute_data)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->values()->join(', ') : null),
                    is_string($variant) => (function($v) {
                        $parsed = json_decode($v, true);
                        return json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['attribute_data']) ? collect($parsed['attribute_data'])->map(fn($val, $key) => ucfirst($key) . ': ' . $val)->values()->join(', ') : $v;
                    })($variant),
                    !empty($it['attributes']) && is_array($it['attributes']) => implode(', ', $it['attributes']),
                    default => null,
                };
            }
            try {
                if (!empty($it['product']->image_url)) {
                    $it['image'] = $it['product']->image_url;
                } elseif (method_exists($it['product'], 'getFirstMediaUrl')) {
                    $it['image'] = $it['product']->getFirstMediaUrl('images');
                } else {
                    $it['image'] = asset('images/placeholder.svg');
                }
            } catch (\Throwable $e) {
                $it['image'] = asset('images/placeholder.svg');
            }
        });

        $checkoutConfig = [
            'baseTotal' => (float) $displayDiscountedTotal,
            'rawItemsSubtotal' => (float) $total,
            'coupon' => $coupon ? [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount' => (float) $discount,
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

    private function processCartItems(array $cart): array
    {
        $items = collect($cart)->map(function ($row, $pid) {
            $product = Product::find($pid);
            if (!$product) {
                return null;
            }
            $qty = (int) ($row['qty'] ?? 1);
            $price = (float) ($row['price'] ?? $product->price ?? 0);
            $variant = $row['variant'] ?? null;
            if ($variant && is_int($variant)) {
                $variant = ProductVariation::find($variant);
            }
            $lineTotal = $price * $qty;
            return [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'lineTotal' => $lineTotal,
                'variant' => $variant,
            ];
        })->filter()->values()->all();

        $total = collect($items)->sum('lineTotal');
        return [$items, $total];
    }

    private function applyCoupon(?int $appliedCouponId, float $total, float $displayTotal, $currentCurrency, $defaultCurrency): array
    {
        if (!$appliedCouponId) {
            return [null, 0, $total, $displayTotal];
        }

        $coupon = Coupon::find($appliedCouponId);
        if (!$coupon || !$coupon->isValidForTotal($total)) {
            return [null, 0, $total, $displayTotal];
        }

        $discounted_total = $coupon->applyTo($total);
        $discount = round($total - $discounted_total, 2);
        try {
            $displayDiscountedTotal = $defaultCurrency->convertTo($discounted_total, $currentCurrency, 2);
        } catch (\Throwable $e) {
            $displayDiscountedTotal = $discounted_total;
        }

        return [$coupon, $discount, $discounted_total, $displayDiscountedTotal];
    }




















}

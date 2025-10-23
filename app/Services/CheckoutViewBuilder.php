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
        [$currentCurrency, $defaultCurrency, $currency_symbol, $displayTotal] = $this->prepareCurrenciesAndTotals($currencyId, $total);
        $this->convertCurrency($items, $currentCurrency, $defaultCurrency);
        [$coupon, $discount, $discounted_total, $displayDiscountedTotal] = $this->applyCoupon($appliedCouponId, $total, $displayTotal, $currentCurrency, $defaultCurrency);
        $addresses = collect();
        if ($user) {
            try {
                $addresses = $user->addresses()->get();
            } catch (\Throwable $e) {
            }
        }
        $defaultAddress = $addresses->firstWhere('is_default', true);
        $gateways = PaymentGateway::where('enabled', true)->get();
        $this->buildItemDetails($items);
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
            if (! $product) {
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
        if (! $appliedCouponId) {
            return [null, 0, $total, $displayTotal];
        }

        $coupon = Coupon::find($appliedCouponId);
        if (! $coupon || ! $coupon->isValidForTotal($total)) {
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

    private function convertCurrency(array &$items, $currentCurrency, $defaultCurrency): void
    {
        if (! $currentCurrency || ! $defaultCurrency || $currentCurrency->id === $defaultCurrency->id) {
            return;
        }
        collect($items)->each(function (&$it) use ($defaultCurrency, $currentCurrency): void {
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

    private function buildItemDetails(array &$items): void
    {
        collect($items)->each(function (&$it): void {
            $variant = $it['variant'] ?? null;
            $it['variant_label'] = $this->buildVariantLabel($variant, $it['attributes'] ?? null);
            $it['image'] = $this->resolveItemImage($it['product']);
        });
    }

    private function buildVariantLabel($variant, ?array $attributes): ?string
    {
        if (! $variant) {
            return null;
        }
        return match (true) {
            is_object($variant) => ! empty($variant->name) ? $variant->name : (! empty($variant->attribute_data) ? collect($variant->attribute_data)->map(fn($v, $k) => ucfirst($k) . ': ' . $v)->values()->join(', ') : null),
            is_string($variant) => (function ($v) {
                $parsed = json_decode($v, true);
                return json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['attribute_data']) ? collect($parsed['attribute_data'])->map(fn($val, $key) => ucfirst($key) . ': ' . $val)->values()->join(', ') : $v;
            })($variant),
            ! empty($attributes) => implode(', ', $attributes),
            default => null,
        };
    }

    private function resolveItemImage($product): string
    {
        try {
            if (! empty($product->image_url)) {
                return $product->image_url;
            }
            if (method_exists($product, 'getFirstMediaUrl')) {
                return $product->getFirstMediaUrl('images');
            }
        } catch (\Throwable $e) {
        }
        return asset('images/placeholder.svg');
    }

    private function prepareCurrenciesAndTotals(?int $currencyId, float $total): array
    {
        $currentCurrency = $currencyId ? Currency::find($currencyId) : Currency::getDefault();
        $defaultCurrency = Currency::getDefault();
        $currency_symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
        $displayTotal = $total;

        $status = match (true) {
            ! $currentCurrency => 'no_current',
            ! $defaultCurrency => 'no_default',
            $currentCurrency->id === $defaultCurrency->id => 'same',
            default => 'convert',
        };

        return match ($status) {
            'no_current' => [$currentCurrency, $defaultCurrency, Currency::defaultSymbol(), $total],
            'no_default' => [$currentCurrency, $defaultCurrency, $currency_symbol, $total],
            'same' => [$currentCurrency, $defaultCurrency, $currency_symbol, $displayTotal],
            'convert' => $this->convertTotal($currentCurrency, $defaultCurrency, $currency_symbol, $total),
        };
    }

    private function convertTotal($currentCurrency, $defaultCurrency, string $currency_symbol, float $total): array
    {
        $displayTotal = $total;
        try {
            $displayTotal = $defaultCurrency->convertTo($total, $currentCurrency, 2);
        } catch (\Throwable $e) {
        }
        return [$currentCurrency, $defaultCurrency, $currency_symbol, $displayTotal];
    }
}

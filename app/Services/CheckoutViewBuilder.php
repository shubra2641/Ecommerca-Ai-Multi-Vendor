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
        [$currentCurrency, $defaultCurrency, $currency_symbol] = $this->getCurrencies($currencyId);
        $displayTotal = $this->calculateDisplayTotal($total, $currentCurrency, $defaultCurrency);

        $this->applyCurrencyToItems($items, $currentCurrency, $defaultCurrency);

        [$coupon, $discount, $discounted_total, $displayDiscountedTotal] = $this->applyCoupon($appliedCouponId, $total, $displayTotal, $currentCurrency, $defaultCurrency);

        [$addresses, $defaultAddress] = $this->loadAddresses($user);
        $gateways = $this->loadGateways();

        $this->buildItemDetails($items);

        $checkoutConfig = $this->buildCheckoutConfig($displayDiscountedTotal, $total, $coupon, $discount, $user);

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
        $items = [];
        $total = 0;
        foreach ($cart as $pid => $row) {
            $product = Product::find($pid);
            if (!$product) {
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
        return [$items, $total];
    }

    private function getCurrencies(?int $currencyId): array
    {
        $currentCurrency = $currencyId ? Currency::find($currencyId) : Currency::getDefault();
        $defaultCurrency = Currency::getDefault();
        $currency_symbol = $currentCurrency?->symbol ?? Currency::defaultSymbol();
        return [$currentCurrency, $defaultCurrency, $currency_symbol];
    }

    private function calculateDisplayTotal(float $total, $currentCurrency, $defaultCurrency): float
    {
        $displayTotal = $total;
        try {
            if ($currentCurrency && $defaultCurrency && $currentCurrency->id !== $defaultCurrency->id) {
                $displayTotal = $defaultCurrency->convertTo($total, $currentCurrency, 2);
            }
        } catch (\Throwable $e) {
            $displayTotal = $total;
        }
        return $displayTotal;
    }

    private function applyCurrencyToItems(array &$items, $currentCurrency, $defaultCurrency): void
    {
        if (!$currentCurrency || !$defaultCurrency || $currentCurrency->id === $defaultCurrency->id) {
            return;
        }

        foreach ($items as &$it) {
            $it['display_price'] = $this->convertCurrency($it['price'], $defaultCurrency, $currentCurrency);
            $it['display_lineTotal'] = $this->convertCurrency($it['lineTotal'], $defaultCurrency, $currentCurrency);
        }
    }

    private function convertCurrency(float $amount, $fromCurrency, $toCurrency): float
    {
        try {
            return $fromCurrency->convertTo($amount, $toCurrency, 2);
        } catch (\Throwable $e) {
            return $amount;
        }
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
        $displayDiscountedTotal = $this->convertCurrency($discounted_total, $defaultCurrency, $currentCurrency) ?? $discounted_total;

        return [$coupon, $discount, $discounted_total, $displayDiscountedTotal];
    }

    private function loadAddresses($user): array
    {
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
        return [$addresses, $defaultAddress];
    }

    private function loadGateways()
    {
        return PaymentGateway::where('enabled', true)->get();
    }

    private function buildItemDetails(array &$items): void
    {
        foreach ($items as &$it) {
            $it['variant_label'] = $this->buildVariantLabel($it);
            $it['image'] = $this->resolveItemImage($it);
        }
    }

    private function buildVariantLabel(array $it): ?string
    {
        $variant = $it['variant'] ?? null;
        if (!$variant) {
            return null;
        }

        return match (true) {
            is_object($variant) => $this->buildObjectVariantLabel($variant),
            is_string($variant) => $this->buildStringVariantLabel($variant),
            !empty($it['attributes']) && is_array($it['attributes']) => implode(', ', $it['attributes']),
            default => null,
        };
    }

    private function buildObjectVariantLabel($variant): ?string
    {
        if (!empty($variant->name)) {
            return $variant->name;
        }

        if (!empty($variant->attribute_data)) {
            return collect($variant->attribute_data)
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }

        return null;
    }

    private function buildStringVariantLabel(string $variant): string
    {
        $parsed = json_decode($variant, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['attribute_data'])) {
            return collect($parsed['attribute_data'])
                ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                ->values()
                ->join(', ');
        }

        return $variant;
    }

    private function resolveItemImage(array $it): string
    {
        try {
            if (!empty($it['product']->image_url)) {
                return $it['product']->image_url;
            }
            if (method_exists($it['product'], 'getFirstMediaUrl')) {
                return $it['product']->getFirstMediaUrl('images');
            }
        } catch (\Throwable $e) {
            // fallback to placeholder
        }
        return asset('images/placeholder.svg');
    }

    private function buildCheckoutConfig(float $displayDiscountedTotal, float $total, $coupon, float $discount, $user): array
    {
        return [
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
    }
}

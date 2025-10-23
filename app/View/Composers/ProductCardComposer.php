<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;

final class ProductCardComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        /** @var \App\Models\Product $product */
        $product = $data['product'] ?? null;
        if (! $product) {
            return;
        }

        $view->with($this->buildCardData($product, $data));
    }

    private function buildCardData($product, array $data): array
    {
        $wishlistIds = $data['wishlistIds'] ?? [];
        $compareIds = $data['compareIds'] ?? [];

        return [
            'cardOnSale' => $this->isOnSale($product),
            'cardDiscountPercent' => $this->calculateDiscountPercent($product),
            'cardAvailable' => $this->getAvailableStock($product),
            'cardWishActive' => in_array($product->id, $wishlistIds, true),
            'cardCmpActive' => in_array($product->id, $compareIds, true),
            'cardRating' => $product->reviews_avg_rating ?? 0.0,
            'cardReviewsCount' => $product->reviews_count ?? 0,
            'cardFullStars' => (int) floor($product->reviews_avg_rating ?? 0.0),
            'cardSnippet' => $this->getCardSnippet($product),
            'cardDisplayPrice' => $product->display_price ?? ($this->getEffectivePrice($product)),
            'cardDisplaySalePrice' => $this->getDisplaySalePrice($product),
            'cardImageUrl' => $product->main_image ? asset($product->main_image) : asset('images/placeholder.png'),
        ];
    }

    private function isOnSale($product): bool
    {
        $salePrice = $product->sale_price ?? null;
        $price = $product->price ?? 0;
        return $salePrice && $salePrice < $price;
    }

    private function calculateDiscountPercent($product): ?int
    {
        if (! $this->isOnSale($product)) {
            return null;
        }
        $price = $product->price ?? 0;
        $salePrice = $product->sale_price ?? null;
        return (int) round(($price - $salePrice) / $price * 100);
    }

    private function getAvailableStock($product): ?int
    {
        if (isset($product->list_available)) {
            return $product->list_available;
        }
        if (! $product->manage_stock) {
            return null;
        }
        return max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0));
    }

    private function getDisplaySalePrice($product): ?float
    {
        $salePrice = $product->sale_price ?? null;
        $price = $product->price ?? null;
        return $salePrice && $price && $salePrice < $price ? $salePrice : null;
    }

    private function getEffectivePrice($product): float
    {
        $price = $product->price ?? 0;
        return $price > 0 ? $price : ($product->effectivePrice() ?? 0);
    }

    private function getCardSnippet($product): string
    {
        $desc = trim(strip_tags($product->short_description ?? $product->description ?? ''));
        return $desc ? Str::limit($desc, 50, '...') : '';
    }
}

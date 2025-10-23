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
        return [
            'cardOnSale' => $this->isOnSale($product),
            'cardDiscountPercent' => $this->calculateDiscountPercent($product),
            'cardAvailable' => $this->getAvailableStock($product),
            'cardWishActive' => $this->isInWishlist($product, $data),
            'cardCmpActive' => $this->isInCompare($product, $data),
            'cardRating' => $product->reviews_avg_rating ?? 0.0,
            'cardReviewsCount' => $product->reviews_count ?? 0,
            'cardFullStars' => $this->calculateFullStars($product),
            'cardSnippet' => $this->getDescriptionSnippet($product),
            'cardDisplayPrice' => $this->getDisplayPrice($product),
            'cardDisplaySalePrice' => $this->getDisplaySalePrice($product),
            'cardImageUrl' => $this->getImageUrl($product),
        ];
    }

    private function isOnSale($product): bool
    {
        $price = $product->price ?? null;
        $sale = ($product->sale_price ?? null) && $product->sale_price < $price ? $product->sale_price : null;

        return $sale !== null;
    }

    private function calculateDiscountPercent($product): ?int
    {
        $price = $product->price ?? null;
        $sale = ($product->sale_price ?? null) && $product->sale_price < $price ? $product->sale_price : null;

        if ($sale !== null && $price) {
            return (int) round(($price - $sale) / $price * 100);
        }

        return null;
    }

    private function getAvailableStock($product): ?int
    {
        // If list_available is set, use it directly
        if (isset($product->list_available)) {
            return $product->list_available;
        }

        // If stock management is disabled, return null
        if (! $product->manage_stock) {
            return null;
        }

        // Calculate available stock
        $stockQty = $product->stock_qty ?? 0;
        $reservedQty = $product->reserved_qty ?? 0;

        return max(0, $stockQty - $reservedQty);
    }

    private function isInWishlist($product, array $data): bool
    {
        $wishlistIds = $data['wishlistIds'] ?? [];

        return in_array($product->id, $wishlistIds, true);
    }

    private function isInCompare($product, array $data): bool
    {
        $compareIds = $data['compareIds'] ?? [];

        return in_array($product->id, $compareIds, true);
    }

    private function calculateFullStars($product): int
    {
        $rating = $product->reviews_avg_rating ?? 0.0;

        return (int) floor((float) $rating);
    }

    private function getDescriptionSnippet($product): string
    {
        $plainDesc = trim(strip_tags($product->short_description ?? $product->description ?? ''));
        $snippet = Str::limit($plainDesc, 50, '...');

        return $snippet === '...' ? '' : $snippet;
    }

    private function getDisplayPrice($product): mixed
    {
        // Return display_price if available
        if (isset($product->display_price)) {
            return $product->display_price;
        }

        // Return price if available
        if (isset($product->price)) {
            return $product->price;
        }

        // Return effective price or default to 0
        return $product->effectivePrice() ?? 0;
    }

    private function getDisplaySalePrice($product): mixed
    {
        // Return display_sale_price if available
        if (isset($product->display_sale_price)) {
            return $product->display_sale_price;
        }

        $price = $product->price ?? null;
        $salePrice = $product->sale_price ?? null;

        // Check if sale price is valid and lower than regular price
        if ($salePrice !== null && $price !== null && $salePrice < $price) {
            return $salePrice;
        }

        return null;
    }

    private function getImageUrl($product): string
    {
        $placeholderData = asset('images/placeholder.png');

        return $product->main_image ? asset($product->main_image) : $placeholderData;
    }
}

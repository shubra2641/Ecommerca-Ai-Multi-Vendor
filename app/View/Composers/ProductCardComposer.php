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
        $wishlistAndCompare = $this->getWishlistAndCompareStatus($product, $data);
        $prices = $this->getDisplayPrices($product);

        return [
            'cardOnSale' => $this->isOnSale($product),
            'cardDiscountPercent' => $this->calculateDiscountPercent($product),
            'cardAvailable' => $this->getAvailableStock($product),
            'cardWishActive' => $wishlistAndCompare['wishlist'],
            'cardCmpActive' => $wishlistAndCompare['compare'],
            'cardRating' => $product->reviews_avg_rating ?? 0.0,
            'cardReviewsCount' => $product->reviews_count ?? 0,
            'cardFullStars' => $this->calculateFullStars($product),
            'cardSnippet' => $this->getDescriptionSnippet($product),
            'cardDisplayPrice' => $prices['price'],
            'cardDisplaySalePrice' => $prices['sale_price'],
            'cardImageUrl' => $this->getImageUrl($product),
        ];
    }

    private function getWishlistAndCompareStatus($product, array $data): array
    {
        $wishlistIds = $data['wishlistIds'] ?? [];
        $compareIds = $data['compareIds'] ?? [];

        return [
            'wishlist' => in_array($product->id, $wishlistIds, true),
            'compare' => in_array($product->id, $compareIds, true),
        ];
    }

    private function isOnSale($product): bool
    {
        $price = $product->price ?? 0;
        $sale = $product->sale_price ?? null;

        return $sale !== null && $sale < $price;
    }

    private function calculateDiscountPercent($product): ?int
    {
        $price = $product->price ?? null;
        $sale = $product->sale_price ?? null;

        if ($price && $sale && $sale < $price) {
            return (int) round(($price - $sale) / $price * 100);
        }

        return null;
    }

    private function getAvailableStock($product): ?int
    {
        return match (true) {
            isset($product->list_available) => $product->list_available,
            ! $product->manage_stock => null,
            default => max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0)),
        };
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

    private function getDisplayPrices($product): array
    {
        return [
            'price' => $product->display_price ?? $this->getFallbackPrice($product),
            'sale_price' => $product->display_sale_price ?? $this->getDisplaySalePriceValue($product),
        ];
    }

    private function getFallbackPrice($product): mixed
    {
        return $product->price ?? ($product->effectivePrice() ?? 0);
    }

    private function getDisplaySalePriceValue($product): mixed
    {
        $price = $product->price ?? null;
        $salePrice = $product->sale_price ?? null;

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

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
            'cardFullStars' => $this->calculateFullStars($product),
            'cardSnippet' => $this->getDescriptionSnippet($product),
            'cardDisplayPrice' => $this->getDisplayPrice($product),
            'cardDisplaySalePrice' => $this->getDisplaySalePriceValue($product),
            'cardImageUrl' => $this->getImageUrl($product),
        ];
    }

    private function isOnSale($product): bool
    {
        $productPrice = $product->price ?? 0;
        $productSale = $product->sale_price ?? null;

        return $productSale !== null && $productSale < $productPrice;
    }

    private function calculateDiscountPercent($product): ?int
    {
        $productPrice = $product->price ?? 0;
        $productSale = $product->sale_price ?? null;

        return $productPrice && $productSale && $productSale < $productPrice
            ? (int) round(($productPrice - $productSale) / $productPrice * 100)
            : null;
    }

    private function calculateFullStars($product): int
    {
        return (int) floor((float) ($product->reviews_avg_rating ?? 0.0));
    }

    private function getDisplayPrice($product)
    {
        return $product->display_price ?? ($product->price ?? ($product->effectivePrice() ?? 0));
    }

    private function getDisplaySalePriceValue($product): mixed
    {
        $price = $product->price ?? null;
        $salePrice = $product->sale_price ?? null;

        return match (true) {
            $salePrice && $price && $salePrice < $price => $salePrice,
            default => null,
        };
    }

    private function getImageUrl($product): string
    {
        $placeholderData = asset('images/placeholder.png');

        return $product->main_image ? asset($product->main_image) : $placeholderData;
    }

    private function getAvailableStock($product): ?int
    {
        return match (true) {
            isset($product->list_available) => $product->list_available,
            !$product->manage_stock => null,
            default => max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0)),
        };
    }

    private function getDescriptionSnippet($product): string
    {
        $desc = trim(strip_tags($product->short_description ?? $product->description ?? ''));

        return $desc ? Str::limit($desc, 50, '...') : '';
    }
}

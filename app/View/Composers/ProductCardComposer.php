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

        $price = $product->price ?? 0;
        $salePrice = $product->sale_price ?? null;
        $cardOnSale = $salePrice && $salePrice < $price;
        $cardDiscountPercent = $salePrice && $salePrice < $price ? (int) round(($price - $salePrice) / $price * 100) : null;
        $cardDisplaySalePrice = $cardOnSale ? (float) $salePrice : null;
        $effectivePrice = $price > 0 ? $price : ($product->effectivePrice() ?? 0);
        $cardDisplayPrice = $product->display_price ?? $effectivePrice;

        $cardAvailable = match (true) {
            isset($product->list_available) => (int) $product->list_available,
            ! $product->manage_stock => null,
            default => max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0)),
        };

        $desc = trim(strip_tags($product->short_description ?? $product->description ?? ''));
        $cardSnippet = $desc ? Str::limit($desc, 50, '...') : '';
        return [
            'cardOnSale' => $cardOnSale,
            'cardDiscountPercent' => $cardDiscountPercent,
            'cardAvailable' => $cardAvailable,
            'cardWishActive' => in_array($product->id, $wishlistIds, true),
            'cardCmpActive' => in_array($product->id, $compareIds, true),
            'cardRating' => $product->reviews_avg_rating ?? 0.0,
            'cardReviewsCount' => $product->reviews_count ?? 0,
            'cardFullStars' => (int) floor((float) ($product->reviews_avg_rating ?? 0.0)),
            'cardSnippet' => $cardSnippet,
            'cardDisplayPrice' => $cardDisplayPrice,
            'cardDisplaySalePrice' => $cardDisplaySalePrice,
            'cardImageUrl' => $product->main_image ? asset($product->main_image) : asset('images/placeholder.png'),
        ];
    }
}

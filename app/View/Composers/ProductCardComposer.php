<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Product;

final class ProductCardComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        /** @var Product $product */
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

        $prices = $this->getCardPrices($product);
        $cardAvailable = $this->calculateCardAvailable($product);
        $cardSnippet = $this->generateCardSnippet($product);

        return [
            'cardOnSale' => $prices['cardOnSale'],
            'cardDiscountPercent' => $prices['cardDiscountPercent'],
            'cardAvailable' => $cardAvailable,
            'cardWishActive' => in_array($product->id, $wishlistIds, true),
            'cardCmpActive' => in_array($product->id, $compareIds, true),
            'cardRating' => $product->reviews_avg_rating ?? 0.0,
            'cardReviewsCount' => $product->reviews_count ?? 0,
            'cardFullStars' => (int) floor((float) ($product->reviews_avg_rating ?? 0.0)),
            'cardSnippet' => $cardSnippet,
            'cardDisplayPrice' => $prices['cardDisplayPrice'],
            'cardDisplaySalePrice' => $prices['cardDisplaySalePrice'],
            'cardImageUrl' => $product->main_image ? asset($product->main_image) : asset('images/placeholder.png'),
        ];
    }

    private function calculateCardAvailable($product): ?int
    {
        if (isset($product->list_available)) {
            return (int) $product->list_available;
        }

        if (! $product->manage_stock) {
            return null;
        }

        return max(0, (int) ($product->stock_qty ?? 0) - (int) ($product->reserved_qty ?? 0));
    }

    private function generateCardSnippet($product): string
    {
        $desc = trim(strip_tags($product->short_description ?? $product->description ?? ''));
        return $desc ? Str::limit($desc, 50, '...') : '';
    }

    private function getCardPrices($product): array
    {
        $price = $product->price ?? 0;
        $salePrice = $product->sale_price ?? null;
        $cardOnSale = $salePrice && $salePrice < $price;
        $cardDiscountPercent = $cardOnSale ? (int) round(($price - $salePrice) / $price * 100) : null;
        $cardDisplaySalePrice = $cardOnSale ? (float) $salePrice : null;
        $effectivePrice = $price > 0 ? $price : ($product->effectivePrice() ?? 0);
        $cardDisplayPrice = $product->display_price ?? $effectivePrice;

        return [
            'cardOnSale' => $cardOnSale,
            'cardDiscountPercent' => $cardDiscountPercent,
            'cardDisplaySalePrice' => $cardDisplaySalePrice,
            'cardDisplayPrice' => $cardDisplayPrice,
        ];
    }
}

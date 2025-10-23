<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCardComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        /** @var \App\Models\Product $product */
        $product = $data['product'] ?? null;
        if (! $product) {
            return;
        }

        // On sale logic
        $price = $product->price ?? null;
        $sale = ($product->sale_price ?? null) && $product->sale_price < $price ? $product->sale_price : null;
        $onSale = $sale !== null;
        $discountPercent = null;
        if ($onSale && $price) {
            $discountPercent = (int) round(($price - $sale) / $price * 100);
        }

        // Availability (prefer precomputed list_available set upstream)
        $available = $product->list_available ??
            ($product->manage_stock
                ? max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0))
                : null);

        // Rating aggregates (preloaded or 0)
        $rating = $product->reviews_avg_rating ?? 0.0;
        $reviewsCount = $product->reviews_count ?? 0;
        $fullStars = (int) floor($rating ? $rating : 0);

        // Truncated description
        $plainDesc = trim(strip_tags($product->short_description ?? $product->description ?? ''));
        $snippet = Str::limit($plainDesc, 50, '...');
        if ($snippet === '...') {
            $snippet = '';
        } // avoid meaningless output when original empty

        // Display prices (allow controller to override via display_price/display_sale_price)
        $displayPrice = $product->display_price ?? ($price ?? ($product->effectivePrice() ?? 0));
        $displaySalePrice = $product->display_sale_price ?? $sale;

        // Wishlist / Compare active flags (use arrays passed to view if exist)
        $wishlistIds = $data['wishlistIds'] ?? [];
        $compareIds = $data['compareIds'] ?? [];
        $wishActive = in_array($product->id, $wishlistIds, true);
        $cmpActive = in_array($product->id, $compareIds, true);

        // Placeholder image - using Font Awesome icon
        $placeholderData = asset('images/placeholder.png');
        $imageUrl = $product->main_image ? asset($product->main_image) : $placeholderData;

        $view->with([
            'cardOnSale' => $onSale,
            'cardDiscountPercent' => $discountPercent,
            'cardAvailable' => $available,
            'cardWishActive' => $wishActive,
            'cardCmpActive' => $cmpActive,
            'cardRating' => $rating,
            'cardReviewsCount' => $reviewsCount,
            'cardFullStars' => $fullStars,
            'cardSnippet' => $snippet,
            'cardDisplayPrice' => $displayPrice,
            'cardDisplaySalePrice' => $displaySalePrice,
            'cardImageUrl' => $imageUrl,
        ]);
    }
}

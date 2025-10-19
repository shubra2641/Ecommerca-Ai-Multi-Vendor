<?php

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
        $sale = ($product->sale_price ?? null) && ($product->sale_price < $price ? $product->sale_price : null);
        $onSale = $sale !== null;
        $discountPercent = null;
        if ($onSale && $price) {
            $discountPercent = (int) round((($price - $sale) / $price) * 100);
        }

        // Availability (prefer precomputed list_available set upstream)
        $available = $product->list_available ??
            ($product->manage_stock
                ? max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0))
                : null);

        // Rating aggregates (preloaded or 0)
        $rating = $product->reviews_avg_rating ?? 0.0;
        $reviewsCount = $product->reviews_count ?? 0;
        $fullStars = (int) floor($rating ?: 0);

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

        // Placeholder image (base64 svg) - static cache
        static $placeholderData = null;
        if ($placeholderData === null) {
            $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
    <rect width="200" height="200" fill="#f5f5f5"/>
    <text x="100" y="105" text-anchor="middle" fill="#999" font-size="14">No Image</text>
</svg>
SVG;

            $placeholderData = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
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

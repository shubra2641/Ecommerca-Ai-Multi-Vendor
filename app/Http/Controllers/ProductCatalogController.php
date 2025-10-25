<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class ProductCatalogController extends Controller
{
    /**
     * Color presets for swatch mapping
     */
    private const COLOR_PRESETS = [
        'black' => '#000000',
        'white' => '#ffffff',
        'gray' => '#808080',
        'grey' => '#808080',
        'silver' => '#c0c0c0',
        'charcoal' => '#36454f',
        'graphite' => '#383e42',
        'slate' => '#708090',
        'red' => '#ff0000',
        'crimson' => '#dc143c',
        'maroon' => '#800000',
        'burgundy' => '#800020',
        'brick' => '#b22222',
        'orange' => '#ff8c00',
        'amber' => '#ffbf00',
        'gold' => '#ffd700',
        'yellow' => '#ffd700',
        'mustard' => '#ffdb58',
        'olive' => '#556b2f',
        'green' => '#008000',
        'forest green' => '#228b22',
        'mint' => '#3eb489',
        'emerald' => '#50c878',
        'teal' => '#008080',
        'cyan' => '#00b7eb',
        'aqua' => '#00ffff',
        'turquoise' => '#40e0d0',
        'blue' => '#0052cc',
        'navy' => '#001f3f',
        'navy blue' => '#001f3f',
        'light blue' => '#87cefa',
        'sky blue' => '#87ceeb',
        'royal blue' => '#4169e1',
        'purple' => '#800080',
        'violet' => '#8a2be2',
        'lavender' => '#e6e6fa',
        'magenta' => '#ff00ff',
        'pink' => '#ff69b4',
        'rose' => '#ff007f',
        'peach' => '#ffdab9',
        'coral' => '#ff7f50',
        'brown' => '#8b4513',
        'chocolate' => '#7b3f00',
        'tan' => '#d2b48c',
        'beige' => '#f5f5dc',
        'cream' => '#fffdd0',
        'khaki' => '#c3b091',
        'sand' => '#f4a460',
        'bronze' => '#cd7f32',
        'copper' => '#b87333',
        'transparent' => '#f3f4f6',
    ];

    /**
     * Main catalog index
     */
    public function index(Request $request)
    {
        $query = $this->baseQuery();

        // Category filter
        $cat = $request->get('category');
        if ($cat) {
            $slugMap = Cache::remember(
                'product_category_slug_id_map',
                600,
                fn() => ProductCategory::pluck('id', 'slug')->all()
            );
            $id = $slugMap[$cat] ?? null;
            if ($id) {
                $childIds = $this->getCategoryChildrenIds($id);
                $query->where(function ($qq) use ($id, $childIds): void {
                    $qq->where('product_category_id', $id)
                        ->orWhereIn('product_category_id', $childIds);
                });
            }
        }

        // Tag filter
        $tag = $request->get('tag');
        if ($tag) {
            $query->whereHas('tags', fn($t) => $t->where('slug', $tag));
        }

        $data = $this->handleListing($request, $query, ['selectedBrands' => (array) $request->get('brand', [])]);

        return view('front.products.index', $data);
    }

    /**
     * Category page
     */
    public function category($slug, Request $request)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $childIds = $this->getCategoryChildrenIds($category->id);

        $query = $this->baseQuery()->where(function ($qq) use ($category, $childIds): void {
            $qq->where('product_category_id', $category->id)->orWhereIn('product_category_id', $childIds);
        });

        $data = $this->handleListing($request, $query, compact('category'));

        return view('front.products.category', $data);
    }

    /**
     * Tag page
     */
    public function tag($slug, Request $request)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $query = $this->baseQuery()->whereHas('tags', function ($t) use ($tag): void {
            $t->where('product_tags.id', $tag->id);
        });

        $data = $this->handleListing($request, $query, compact('tag'));

        return view('front.products.tag', $data);
    }

    /**
     * Product detail page
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'tags', 'variations', 'vendor'])
            ->withCount([
                'reviews as approved_reviews_count' => function ($q): void {
                    $q->where('approved', true);
                },
            ])
            ->withAvg([
                'reviews as approved_reviews_avg' => function ($q): void {
                    $q->where('approved', true);
                },
            ], 'rating')
            ->where('slug', $slug)
            ->firstOrFail();

        // Build attribute map for variations
        $attributeMap = $this->buildAttributeMap($product);

        // Related products
        $related = Cache::remember(
            'product_related_' . $product->id,
            300,
            function () use ($product) {
                return Product::active()
                    ->where('product_category_id', $product->product_category_id)
                    ->where('id', '!=', $product->id)
                    ->with('variations')
                    ->limit(6)
                    ->get();
            }
        );

        // Convert price
        $this->convertPrices(collect([$product]));
        if ($product->variations) {
            $this->convertPrices($product->variations);
        }

        // Convert original price if on sale
        if ($product->isOnSale()) {
            $product->display_original_price = GlobalHelper::convertCurrency($product->price);
            $product->display_sale_price = GlobalHelper::convertCurrency($product->sale_price);
        }

        $currentCurrency = $this->resolveCurrentCurrency();

        // Reviews data
        $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
        $rating = $reviewsCount ? (float) ($product->approved_reviews_avg ?? 0) : 0;

        // Gallery images
        ['gallery' => $gallery, 'mainImage' => $mainImage] = $this->buildGallery($product);

        // Pricing
        $onSale = $product->isOnSale();
        $basePrice = GlobalHelper::convertCurrency($product->effectivePrice());
        $origPrice = $onSale ? GlobalHelper::convertCurrency($product->price) : null;
        $discountPercent = $onSale && $origPrice && $origPrice > 0
            ? (int) round(($origPrice - $basePrice) / $origPrice * 100)
            : null;

        // Stock
        $available = $product->availableStock();
        ['stockClass' => $stockClass, 'levelLabel' => $levelLabel] = $this->buildStockData($product, $available);

        // Interest count
        try {
            $interestCount = \App\Models\ProductInterest::countForProduct($product->id);
        } catch (\Throwable $e) {
            $interestCount = 0;
        }

        // Variation price range
        ['minP' => $minP, 'maxP' => $maxP, 'activeVars' => $activeVars] = $this->buildVariationPrices($product);

        // Variation attributes
        $usedAttrs = is_array($product->used_attributes) ? $product->used_attributes : array_keys($attributeMap);
        $variationAttributes = $this->buildVariationAttributes($attributeMap, $usedAttrs);

        // Tags
        $tagsCount = $product->tags->count();
        $tagsFirst = $product->tags->take(6);
        $tagsMore = $tagsCount > 6 ? $product->tags->slice(6) : collect();

        // Dimensions
        $dims = array_filter([$product->length, $product->width, $product->height]);
        $hasDims = count($dims) > 0;

        // Spec count
        $specCount = $this->buildSpecCount($product);

        // Flags
        // For variable products, check if ANY variation has stock
        ['isOut' => $isOut, 'hasDiscount' => $hasDiscount, 'brandName' => $brandName] = $this->buildFlags($product, $available, $onSale, $activeVars);

        // Reviews
        $formattedReviewsCount = $reviewsCount >= 1000 ? round($reviewsCount / 1000, 1) . 'k' : $reviewsCount;
        ['reviews' => $reviews, 'reviewStats' => $reviewStats] = $this->buildReviews($product);

        // Check if user purchased
        $purchased = $this->checkPurchased($product);

        // Stars
        $stars = $this->buildStars($rating);

        // In cart
        $inCart = $this->checkInCart($product);

        return view('front.products.show', compact(
            'product',
            'attributeMap',
            'related',
            'currentCurrency',
            'rating',
            'reviewsCount',
            'gallery',
            'mainImage',
            'onSale',
            'basePrice',
            'origPrice',
            'discountPercent',
            'available',
            'stockClass',
            'levelLabel',
            'interestCount',
            'minP',
            'maxP',
            'variationAttributes',
            'usedAttrs',
            'tagsCount',
            'tagsFirst',
            'tagsMore',
            'hasDims',
            'dims',
            'specCount',
            'isOut',
            'hasDiscount',
            'brandName',
            'formattedReviewsCount',
            'reviews',
            'reviewStats',
            'purchased',
            'stars',
            'inCart',
            'activeVars'
        ));
    }

    /**
     * Get category children IDs with caching
     */
    protected function getCategoryChildrenIds($categoryId): array
    {
        return Cache::remember(
            'category_children_ids_' . $categoryId,
            600,
            fn() => ProductCategory::where('parent_id', $categoryId)->pluck('id')->all()
        );
    }

    /**
     * Common listing logic for index/category/tag pages
     */
    protected function handleListing(Request $request, $query = null, array $extraData = [])
    {
        $query ??= $this->baseQuery();
        $query = $this->applyFilters($query, $request);
        $products = $this->processProducts($query->simplePaginate(24)->withQueryString());

        $commonData = $this->getCommonData($request);

        return array_merge($commonData, compact('products'), $extraData);
    }

    /**
     * Base query for products
     */
    protected function baseQuery()
    {
        $select = [
            'id',
            'name',
            'slug',
            'price',
            'sale_price',
            'product_category_id',
            'manage_stock',
            'stock_qty',
            'reserved_qty',
            'type',
            'main_image',
            'is_featured',
            'active',
            'vendor_id',
        ];

        return Product::query()
            ->select($select)
            ->with(['category', 'brand'])
            ->active();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, Request $request)
    {
        // Search
        $search = $request->get('q');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filters
        if ($request->boolean('featured')) {
            $query->featured();
        }
        if ($request->boolean('best')) {
            $query->bestSeller();
        }
        if ($request->boolean('sale')) {
            $query->onSale();
        }
        $type = $request->get('type');
        if ($type) {
            $query->where('type', $type);
        }

        // Price range
        $min = $request->get('min_price');
        if ($min && is_numeric($min)) {
            $query->where('price', '>=', $min);
        }
        $max = $request->get('max_price');
        if ($max && is_numeric($max)) {
            $query->where('price', '<=', $max);
        }

        // Brand filter
        $brands = $request->get('brand');
        if ($brands) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $query->whereHas('brand', function ($b) use ($brandsArr): void {
                    $b->whereIn('slug', array_map('Str::slug', $brandsArr));
                });
            }
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            default:
                $query->latest();
        }

        return $query;
    }

    /**
     * Process products for display
     */
    protected function processProducts($products)
    {
        // Add availability
        foreach ($products as $p) {
            $p->list_available = $p->manage_stock ? max(0, ($p->stock_qty ?? 0) - ($p->reserved_qty ?? 0)) : null;
        }

        // Convert prices
        $this->convertPrices($products);

        return $products;
    }

    /**
     * Convert product prices for display
     */
    protected function convertPrices($products): void
    {
        foreach ($products as $p) {
            $p->display_price = GlobalHelper::convertCurrency($p->effectivePrice());
        }
    }

    /**
     * Get common data for views
     */
    protected function getCommonData(Request $request)
    {
        $categories = Cache::remember('product_category_tree', 600, function () {
            return ProductCategory::with('children.children')->whereNull('parent_id')->get();
        });

        $brandList = Cache::remember('product_brands_list', 600, function () {
            return Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get();
        });

        $wishlistIds = [];
        if ($request->user()?->id) {
            $wishlistIds = (array) Cache::remember(
                'wishlist_ids_' . $request->user()->id,
                60,
                function () use ($request) {
                    return \App\Models\WishlistItem::where('user_id', $request->user()->id)
                        ->pluck('product_id')
                        ->all();
                }
            );
        } else {
            $wishlistSession = session('wishlist');
            $wishlistIds = is_array($wishlistSession) && $wishlistSession ? $wishlistSession : [];
        }

        $compareIds = session('compare', []);
        $currentCurrency = $this->resolveCurrentCurrency();

        // Price range defaults in current currency
        $priceRangeDefaults = [
            'min_price_default' => 0,
            'max_price_default' => GlobalHelper::convertCurrency(1000), // Convert 1000 from default currency
            'max_price_limit' => GlobalHelper::convertCurrency(10000), // Higher limit for max attribute
        ];

        return compact('categories', 'brandList', 'wishlistIds', 'compareIds', 'currentCurrency') + $priceRangeDefaults;
    }

    /**
     * Build a color swatch lookup for variation attributes.
     */
    protected function buildSwatchMap(array $values): array
    {
        $map = [];
        foreach ($values as $raw) {
            if (! is_string($raw)) {
                continue;
            }

            $value = trim($raw);
            if ($value === '') {
                continue;
            }

            $normalized = strtolower($value);
            $key = $normalized;

            // Check if it's already a hex color
            if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value)) {
                $map[$key] = $value;
                continue;
            }

            // Check direct preset match
            if (isset(self::COLOR_PRESETS[$normalized])) {
                $map[$key] = self::COLOR_PRESETS[$normalized];
                continue;
            }

            // Try without spaces
            $fallback = str_replace(' ', '', $normalized);
            if (isset(self::COLOR_PRESETS[$fallback])) {
                $map[$key] = self::COLOR_PRESETS[$fallback];
                continue;
            }

            // Default neutral gray
            $map[$key] = '#f3f4f6';
        }

        return $map;
    }

    /**
     * Resolve current currency
     */
    protected function resolveCurrentCurrency()
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $currencyContext = GlobalHelper::getCurrencyContext();
        $cached = $currencyContext['currentCurrency'];

        return $cached;
    }

    private function buildAttributeMap($product): array
    {
        $attributeMap = [];
        if ($product->type === 'variable') {
            foreach ($product->variations as $v) {
                if (! $v->active) {
                    continue;
                }
                foreach (($v->attribute_data ?? []) as $attr => $val) {
                    $attributeMap[$attr] = $attributeMap[$attr] ?? [];
                    if (! in_array($val, $attributeMap[$attr])) {
                        $attributeMap[$attr][] = $val;
                    }
                }
            }
        }
        return $attributeMap;
    }

    private function buildGallery($product): array
    {
        $images = collect();
        if (! empty($product->main_image)) {
            $images->push($product->main_image);
        }
        if (! empty($product->gallery) && is_array($product->gallery)) {
            $images = $images->merge(collect($product->gallery)->filter());
        }
        if ($product->type === 'variable' && $product->variations->count()) {
            $variationImages = $product->variations->where('active', true)->pluck('image')->filter()->unique();
            $images = $images->merge($variationImages);
        }
        if ($images->isEmpty()) {
            $images->push('front/images/default-product.png');
        }

        $gallery = $images->unique()->map(fn($p) => ['raw' => $p, 'url' => asset($p)]);
        $mainImage = $gallery->first();

        return compact('gallery', 'mainImage');
    }

    private function buildStockData($product, $available): array
    {
        $stockClass = match (true) {
            $available === 0 => 'out-stock',
            is_null($available) => 'high-stock',
            $available <= 5 => 'low-stock',
            $available <= 20 => 'mid-stock',
            default => 'high-stock',
        };

        $levelLabel = match (true) {
            $available === 0 => __('Out of stock'),
            ! is_numeric($available) => __('In stock'),
            $available <= 5 => __('In stock') . " ({$available}) • Low stock",
            $available <= 20 => __('In stock') . " ({$available}) • Mid stock",
            default => __('In stock') . " ({$available}) • High stock",
        };

        return compact('stockClass', 'levelLabel');
    }

    private function buildVariationPrices($product): array
    {
        $minP = $maxP = null;
        $activeVars = collect();
        if ($product->type === 'variable') {
            $activeVars = $product->variations->where('active', true);
            $prices = $activeVars->map(fn($v) => $v->effectivePrice())->filter();
            if ($prices->count()) {
                $minP = $prices->min();
                $maxP = $prices->max();
            }
            $activeVars = $activeVars->map(function ($v) {
                $v->effective_price = $v->effectivePrice();
                $v->stock_qty = $v->stock_qty ?? 0;
                $v->reserved_qty = $v->reserved_qty ?? 0;
                $v->manage_stock = $v->manage_stock ?? false;

                return $v;
            });
        }

        return compact('minP', 'maxP', 'activeVars');
    }

    private function buildVariationAttributes($attributeMap, $usedAttrs): array
    {
        $variationAttributes = [];
        foreach ($attributeMap as $attrName => $values) {
            if (! in_array($attrName, $usedAttrs)) {
                continue;
            }

            $lower = strtolower($attrName);
            $icon = match (true) {
                in_array($lower, ['color', 'colour', 'color_name', 'colour_name']) => '🎨',
                in_array($lower, ['size', 'sizes']) => '📏',
                in_array($lower, ['material', 'fabric']) => '🧵',
                default => '⚙️',
            };

            $isColor = in_array($lower, ['color', 'colour', 'color_name', 'colour_name']);
            $variationAttributes[] = [
                'name' => $attrName,
                'label' => str_replace('_', ' ', $attrName),
                'icon' => $icon,
                'is_color' => $isColor,
                'values' => $values,
                'swatch_map' => $isColor ? $this->buildSwatchMap($values) : [],
            ];
        }

        return $variationAttributes;
    }

    private function buildSpecCount($product): int
    {
        $specCount = 0;
        if ($product->sku) {
            $specCount++;
        }
        if ($product->weight) {
            $specCount++;
        }
        if ($product->length) {
            $specCount++;
        }
        if ($product->width) {
            $specCount++;
        }
        if ($product->height) {
            $specCount++;
        }
        $specCount++;
        if ($product->refund_days) {
            $specCount++;
        }

        return $specCount;
    }

    private function buildFlags($product, $available, $onSale, $activeVars): array
    {
        $hasAnyStock = $product->type !== 'variable' || $activeVars->isEmpty() || $activeVars->contains(function ($v) {
            return ! $v->manage_stock || (($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0) > 0);
        });
        $isOut = ! $hasAnyStock;
        $hasDiscount = $onSale;
        $brandName = $product->brand->name ?? null;

        return compact('isOut', 'hasDiscount', 'brandName');
    }

    private function buildStars($rating): array
    {
        $fullRating = (int) floor($rating);
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = ['index' => $i, 'filled' => $i <= $fullRating];
        }

        return $stars;
    }

    private function buildReviews($product): array
    {
        try {
            $reviewsPayload = app(\App\Services\ReviewsPresenter::class)->build($product);
            $reviews = $reviewsPayload['reviews'];
            $reviewStats = $reviewsPayload['stats'];
        } catch (\Throwable $e) {
            $reviews = collect();
            $reviewStats = [
                'total' => 0,
                'average' => 0,
                'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'distribution_percent' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'helpful_total' => 0,
            ];
        }

        return compact('reviews', 'reviewStats');
    }

    private function checkPurchased($product): bool
    {
        $purchased = false;
        if (auth()->check()) {
            try {
                $user = auth()->user();
                if (method_exists($user, 'orders')) {
                    $purchased = $user->orders()
                        ->whereIn('status', ['completed', 'paid', 'delivered'])
                        ->whereHas('items', function ($q) use ($product): void {
                            $q->where('product_id', $product->id);
                        })
                        ->exists();
                }
            } catch (\Throwable $e) {
                $purchased = false;
            }
        }

        return $purchased;
    }

    private function checkInCart($product): bool
    {
        try {
            $inCart = (bool) (session('cart') && isset(session('cart')[$product->id]));
        } catch (\Throwable $e) {
            $inCart = false;
        }

        return $inCart;
    }
}

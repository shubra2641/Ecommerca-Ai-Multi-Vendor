<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductCatalogController extends Controller
{
    protected function baseQuery()
    {
        // include category (+ brand if exists) using static cached flag; use denormalized review aggregates if present
        static $hasBrands = true; // assume present after first deployment (remove runtime hasTable cost)
        static $hasDenorm = true; // assume migration applied; adjust config if rolling deploy
        $select = ['id', 'name', 'slug', 'price', 'sale_price', 'product_category_id', 'manage_stock', 'stock_qty', 'reserved_qty', 'type', 'main_image', 'is_featured', 'active', 'vendor_id'];
        if ($hasDenorm) {
            $select[] = 'approved_reviews_count';
            $select[] = 'approved_reviews_avg';
        }
        $q = Product::query()->select($select);
        // Always eager load category and brand to avoid N+1 when rendering product cards and filters
        $with = ['category'];
        if ($hasBrands) {
            $with[] = 'brand';
        }
        $q->with($with);
        if (! $hasDenorm) {
            $q->withAvg(['reviews as reviews_avg_rating' => function ($q) {
                $q->where('approved', true);
            }], 'rating')
                ->withCount(['reviews as reviews_count' => function ($q) {
                    $q->where('approved', true);
                }]);
        }

        return $q->active();
    }

    public function index(Request $r)
    {
        // Catalog index: build base query with optional filters
        $q = $this->baseQuery();
        static $hasWishlist = true; // assume wishlist_items table exists
        if ($search = $r->get('q')) {
            $q->where('name', 'like', '%' . $search . '%');
        }
        if ($r->boolean('featured')) {
            $q->featured();
        }
        if ($r->boolean('best')) {
            $q->bestSeller();
        }
        if ($r->boolean('sale')) {
            $q->onSale();
        }
        if ($type = $r->get('type')) {
            $q->where('type', $type);
        }
        if ($cat = $r->get('category')) {
            $slugMap = Cache::remember('product_category_slug_id_map', 600, fn () => ProductCategory::pluck('id', 'slug')->all());
            $id = $slugMap[$cat] ?? null;
            if ($id) {
                $childIds = Cache::remember('category_children_ids_' . $id, 600, fn () => ProductCategory::where('parent_id', $id)->pluck('id')->all());
                $q->where(function ($qq) use ($id, $childIds) {
                    $qq->where('product_category_id', $id)
                        ->orWhereIn('product_category_id', $childIds);
                });
            }
        }
        if ($tag = $r->get('tag')) {
            $q->whereHas('tags', fn ($t) => $t->where('slug', $tag));
        }
        // price range
        if ($min = $r->get('min_price')) {
            if (is_numeric($min)) {
                $q->where('price', '>=', $min);
            }
        }
        if ($max = $r->get('max_price')) {
            if (is_numeric($max)) {
                $q->where('price', '<=', $max);
            }
        }

        // brand filter (if brands table exists)
        static $hasBrands = true; // assume table exists; remove hasTable() query
        if ($hasBrands && ($brands = $r->get('brand'))) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $q->whereHas('brand', fn ($b) => $b->whereIn('slug', array_map('Str::slug', $brandsArr)));
            }
        }

        switch ($r->get('sort')) {
            case 'price_asc':
                $q->orderBy('price');
                break;
            case 'price_desc':
                $q->orderByDesc('price');
                break;
            default:
                $q->latest();
        }
        $products = $q->simplePaginate(24)->withQueryString();
        // Precompute lightweight availability to reduce template logic & queries
        foreach ($products as $p) {
            $p->list_available = $p->manage_stock ? max(0, ($p->stock_qty ?? 0) - ($p->reserved_qty ?? 0)) : null;
        }
        // Convert product prices for display if a session currency is selected
        try {
            $sessionCurrencyId = session('currency_id');
            if ($sessionCurrencyId) {
                $target = \App\Models\Currency::find($sessionCurrencyId);
                $default = \App\Models\Currency::getDefault();
                if ($target && $default && $target->id !== $default->id) {
                    foreach ($products as $p) {
                        $p->display_price = $default->convertTo($p->price, $target, 2);
                    }
                } else {
                    foreach ($products as $p) {
                        $p->display_price = $p->price;
                    }
                }
            } else {
                foreach ($products as $p) {
                    $p->display_price = $p->price;
                }
            }
        } catch (\Throwable $e) {
            foreach ($products as $p) {
                $p->display_price = $p->price;
            }
        }
        $categories = Cache::remember('product_category_tree', 600, function () {
            return ProductCategory::with('children.children')->whereNull('parent_id')->get();
        });
        $brandList = collect();
        if ($hasBrands) {
            $brandList = Cache::remember('product_brands_list', 600, function () {
                return Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get();
            });
        }
        $wishlistIds = [];
        if ($r->user()?->id && $hasWishlist) {
            $wishlistIds = (array) Cache::remember('wishlist_ids_' . $r->user()->id, 60, function () use ($r) {
                return \App\Models\WishlistItem::where('user_id', $r->user()->id)->pluck('product_id')->all();
            });
        } else {
            // Quick skip: if session wishlist empty or not array, avoid extra operations
            $wishlistSession = session('wishlist');
            $wishlistIds = is_array($wishlistSession) && $wishlistSession ? $wishlistSession : [];
        }
        $compareIds = session('compare', []);
        $currentCurrency = $this->resolveCurrentCurrency();

        // Capture selected brands for sidebar (array of slugs)
        try {
            $selectedBrands = (array) $r->get('brand', []);
        } catch (\Throwable $e) {
            $selectedBrands = [];
        }

        return view('front.products.index', compact('products', 'categories', 'brandList', 'wishlistIds', 'compareIds', 'currentCurrency', 'selectedBrands'));
    }

    public function category($slug, Request $r)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $childIds = Cache::remember('category_children_ids_' . $category->id, 600, function () use ($category) {
            return $category->children()->pluck('id')->all();
        });
        $q = $this->baseQuery()->where(function ($qq) use ($category, $childIds) {
            $qq->where('product_category_id', $category->id)
                ->orWhereIn('product_category_id', $childIds);
        });
        // static flag to avoid repeated schema checks (assume brands table exists in current deployment)
        static $hasBrands = true;
        static $hasWishlist = true;

        if ($search = $r->get('q')) {
            $q->where('name', 'like', '%' . $search . '%');
        }
        if ($r->boolean('featured')) {
            $q->featured();
        }
        if ($r->boolean('best')) {
            $q->bestSeller();
        }
        if ($r->boolean('sale')) {
            $q->onSale();
        }
        if ($type = $r->get('type')) {
            $q->where('type', $type);
        }
        // price range
        if ($min = $r->get('min_price')) {
            if (is_numeric($min)) {
                $q->where('price', '>=', $min);
            }
        }
        if ($max = $r->get('max_price')) {
            if (is_numeric($max)) {
                $q->where('price', '<=', $max);
            }
        }
        // brand filter
        if ($hasBrands && ($brands = $r->get('brand'))) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $q->whereHas('brand', fn ($b) => $b->whereIn('slug', array_map('Str::slug', $brandsArr)));
            }
        }
        switch ($r->get('sort')) {
            case 'price_asc':
                $q->orderBy('price');
                break;
            case 'price_desc':
                $q->orderByDesc('price');
                break;
            default:
                $q->latest();
        }

        $products = $q->simplePaginate(24)->withQueryString();
        foreach ($products as $p) {
            $p->list_available = $p->manage_stock ? max(0, ($p->stock_qty ?? 0) - ($p->reserved_qty ?? 0)) : null;
        }
        try {
            $sessionCurrencyId = session('currency_id');
            $target = $sessionCurrencyId ? \App\Models\Currency::find($sessionCurrencyId) : null;
            $default = \App\Models\Currency::getDefault();
            if ($target && $default && $target->id !== $default->id) {
                foreach ($products as $p) {
                    $p->display_price = $default->convertTo($p->price, $target, 2);
                }
            } else {
                foreach ($products as $p) {
                    $p->display_price = $p->price;
                }
            }
        } catch (\Throwable $e) {
            foreach ($products as $p) {
                $p->display_price = $p->price;
            }
        }
        $categories = Cache::remember('product_category_tree', 600, function () {
            return ProductCategory::with('children.children')->whereNull('parent_id')->get();
        });
        $brandList = collect();
        if ($hasBrands) {
            $brandList = Cache::remember('product_brands_list', 600, function () {
                return Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get();
            });
        }
        $wishlistIds = [];
        if ($r->user()?->id && $hasWishlist) {
            $wishlistIds = (array) Cache::remember('wishlist_ids_' . $r->user()->id, 60, function () use ($r) {
                return \App\Models\WishlistItem::where('user_id', $r->user()->id)->pluck('product_id')->all();
            });
        } else {
            $wishlistSession = session('wishlist');
            $wishlistIds = is_array($wishlistSession) && $wishlistSession ? $wishlistSession : [];
        }
        $compareIds = session('compare', []);
        $currentCurrency = $this->resolveCurrentCurrency();

        return view('front.products.category', compact('category', 'products', 'categories', 'brandList', 'wishlistIds', 'compareIds', 'currentCurrency'));
    }

    public function tag($slug, Request $r)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $q = $this->baseQuery()->whereHas('tags', fn ($t) => $t->where('product_tags.id', $tag->id));
        // static flag similar to index/category
        static $hasBrands = true;
        static $hasWishlist = true;

        if ($search = $r->get('q')) {
            $q->where('name', 'like', '%' . $search . '%');
        }
        if ($r->boolean('featured')) {
            $q->featured();
        }
        if ($r->boolean('best')) {
            $q->bestSeller();
        }
        if ($r->boolean('sale')) {
            $q->onSale();
        }
        if ($type = $r->get('type')) {
            $q->where('type', $type);
        }
        if ($min = $r->get('min_price')) {
            if (is_numeric($min)) {
                $q->where('price', '>=', $min);
            }
        }
        if ($max = $r->get('max_price')) {
            if (is_numeric($max)) {
                $q->where('price', '<=', $max);
            }
        }
        if ($hasBrands && ($brands = $r->get('brand'))) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $q->whereHas('brand', fn ($b) => $b->whereIn('slug', array_map('Str::slug', $brandsArr)));
            }
        }
        switch ($r->get('sort')) {
            case 'price_asc':
                $q->orderBy('price');
                break;
            case 'price_desc':
                $q->orderByDesc('price');
                break;
            default:
                $q->latest();
        }
        $products = $q->simplePaginate(24)->withQueryString();
        foreach ($products as $p) {
            $p->list_available = $p->manage_stock ? max(0, ($p->stock_qty ?? 0) - ($p->reserved_qty ?? 0)) : null;
        }
        try {
            $sessionCurrencyId = session('currency_id');
            $target = $sessionCurrencyId ? \App\Models\Currency::find($sessionCurrencyId) : null;
            $default = \App\Models\Currency::getDefault();
            if ($target && $default && $target->id !== $default->id) {
                foreach ($products as $p) {
                    $p->display_price = $default->convertTo($p->price, $target, 2);
                }
            } else {
                foreach ($products as $p) {
                    $p->display_price = $p->price;
                }
            }
        } catch (\Throwable $e) {
            foreach ($products as $p) {
                $p->display_price = $p->price;
            }
        }
        $categories = Cache::remember('product_category_tree', 600, function () {
            return ProductCategory::with('children.children')->whereNull('parent_id')->get();
        });
        $brandList = collect();
        if ($hasBrands) {
            $brandList = Cache::remember('product_brands_list', 600, function () {
                return Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get();
            });
        }
        $wishlistIds = [];
        if ($r->user()?->id && $hasWishlist) {
            $wishlistIds = (array) Cache::remember('wishlist_ids_' . $r->user()->id, 60, function () use ($r) {
                return \App\Models\WishlistItem::where('user_id', $r->user()->id)->pluck('product_id')->all();
            });
        } else {
            $wishlistSession = session('wishlist');
            $wishlistIds = is_array($wishlistSession) && $wishlistSession ? $wishlistSession : [];
        }
        $compareIds = session('compare', []);
        $currentCurrency = $this->resolveCurrentCurrency();

        return view('front.products.tag', compact('tag', 'products', 'categories', 'brandList', 'wishlistIds', 'compareIds', 'currentCurrency'));
    }

    protected function resolveCurrentCurrency()
    {
        static $cached = null; // per-request static cache
        if ($cached !== null) {
            return $cached;
        }
        try {
            $cid = session('currency_id');
            if ($cid) {
                $cached = \App\Models\Currency::find($cid) ?: \App\Models\Currency::getDefault();
            } else {
                $cached = \App\Models\Currency::getDefault();
            }
        } catch (\Throwable $e) {
            $cached = null;
        }

        return $cached;
    }

    public function show($slug)
    {
        // Eager load related objects + pre-compute approved review aggregates to avoid N+1 queries later.
        $product = Product::with([
            'category', 'tags', 'variations',
        ])
            ->withCount([
                'reviews as approved_reviews_count' => function ($q) {
                    $q->where('approved', true);
                },
            ])
            ->withAvg([
                'reviews as approved_reviews_avg' => function ($q) {
                    $q->where('approved', true);
                },
            ], 'rating')
            ->where('slug', $slug)
            ->firstOrFail();
        // build attribute map from variations
        $attributeMap = [];
        if ($product->type === 'variable') {
            foreach ($product->variations as $v) {
                if (! $v->active) {
                    continue;
                } foreach (($v->attribute_data ?? []) as $attr => $val) {
                    $attributeMap[$attr] = $attributeMap[$attr] ?? [];
                    if (! in_array($val, $attributeMap[$attr])) {
                        $attributeMap[$attr][] = $val;
                    }
                }
            }
        }
        // Cache related products briefly (category based) to reduce repeated queries on popular items
        $related = Cache::remember('product_related_' . $product->id, 300, function () use ($product) {
            return Product::active()
                ->where('product_category_id', $product->product_category_id)
                ->where('id', '!=', $product->id)
                ->with('variations')
                ->limit(6)
                ->get();
        });
        // compute display price for product
        try {
            $sessionCurrencyId = session('currency_id');
            $target = $sessionCurrencyId ? \App\Models\Currency::find($sessionCurrencyId) : null;
            $default = \App\Models\Currency::getDefault();
            if ($target && $default && $target->id !== $default->id) {
                $product->display_price = $default->convertTo($product->price, $target, 2);
            } else {
                $product->display_price = $product->price;
            }
        } catch (\Throwable $e) {
            $product->display_price = $product->price;
        }

        $currentCurrency = session('currency_id') ? \App\Models\Currency::find(session('currency_id')) : \App\Models\Currency::getDefault();
        // compute rating & reviews count (approved reviews only)
        // Use pre-loaded aggregates (falls back if columns not present due to older migration state)
        $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
        $rating = $reviewsCount ? (float) ($product->approved_reviews_avg ?? 0) : 0;

        // Build gallery images (main > gallery > variation images) with fallback
        $images = collect();
        if (! empty($product->main_image)) {
            $images->push($product->main_image);
        }
        if (! empty($product->gallery) && is_array($product->gallery)) {
            foreach ($product->gallery as $img) {
                if ($img) {
                    $images->push($img);
                }
            }
        }
        if ($product->type === 'variable' && $product->variations->count()) {
            foreach ($product->variations->where('active', true) as $v) {
                if (! empty($v->image) && ! $images->contains($v->image)) {
                    $images->push($v->image);
                }
            }
        }
        if ($images->isEmpty()) {
            $images->push('front/images/default-product.png');
        }
        $gallery = $images->map(fn ($p) => [
            'raw' => $p,
            'url' => asset($p),
        ]);
        $mainImage = $gallery->first();

        // Pricing & discount
        $onSale = $product->isOnSale();
        $basePrice = $product->display_price ?? $product->effectivePrice();
        $origPrice = $product->display_price ?? $product->price;
        $discountPercent = ($onSale && $origPrice && $origPrice > 0) ? (int) round((($origPrice - $basePrice) / $origPrice) * 100) : null;

        // Availability
        $available = $product->availableStock();
        $stockClass = 'high-stock';
        if ($available === 0) {
            $stockClass = 'out-stock';
        } elseif (! is_null($available)) {
            if ($available <= 5) {
                $stockClass = 'low-stock';
            } elseif ($available <= 20) {
                $stockClass = 'mid-stock';
            }
        }
        $levelLabel = '';
        if ($available === 0) {
            $levelLabel = __('Out of stock');
        } elseif (is_numeric($available)) {
            if ($available <= 5) {
                $levelLabel = __('In stock') . " ({$available}) • Low stock";
            } elseif ($available <= 20) {
                $levelLabel = __('In stock') . " ({$available}) • Mid stock";
            } else {
                $levelLabel = __('In stock') . " ({$available}) • High stock";
            }
        } else {
            $levelLabel = __('In stock');
        }

        // Interest count
        try {
            $interestCount = \App\Models\ProductInterest::countForProduct($product->id);
        } catch (\Throwable $e) {
            $interestCount = 0;
        }

        // Variation price range (for variable)
        $minP = $maxP = null;
        $activeVars = collect();
        if ($product->type === 'variable') {
            $activeVars = $product->variations->where('active', true);
            $prices = $activeVars->map(fn ($v) => $v->effectivePrice())->filter();
            if ($prices->count()) {
                $minP = $prices->min();
                $maxP = $prices->max();
            }
        }

        // Variation attribute presentation
        $usedAttrs = is_array($product->used_attributes) ? $product->used_attributes : array_keys($attributeMap);
        $variationAttributes = [];
        foreach ($attributeMap as $attrName => $values) {
            if (! in_array($attrName, $usedAttrs)) {
                continue;
            }
            $lower = strtolower($attrName);
            $icon = '⚙️';
            if (in_array($lower, ['color', 'colour', 'color_name', 'colour_name'])) {
                $icon = '🎨';
            } elseif (in_array($lower, ['size', 'sizes'])) {
                $icon = '📏';
            } elseif (in_array($lower, ['material', 'fabric'])) {
                $icon = '🧵';
            }
            $isColor = in_array($lower, ['color', 'colour', 'color_name', 'colour_name']);
            $variationAttributes[] = [
                'name' => $attrName,
                'label' => str_replace('_', ' ', $attrName),
                'icon' => $icon,
                'is_color' => $isColor,
                'values' => $values,
            ];
        }

        // Tags split
        $tagsCount = $product->tags->count();
        $tagsFirst = $product->tags->take(6);
        $tagsMore = $tagsCount > 6 ? $product->tags->slice(6) : collect();

        // Dimensions
        $dims = array_filter([$product->length, $product->width, $product->height]);
        $hasDims = count($dims) > 0;

        // Spec count
        $specCount = 0;
        if ($product->sku) {
            $specCount++;
        } if ($product->weight) {
            $specCount++;
        } if ($product->length) {
            $specCount++;
        } if ($product->width) {
            $specCount++;
        } if ($product->height) {
            $specCount++;
        } $specCount++;
        if ($product->refund_days) {
            $specCount++;
        }

        // Misc flags
        $isOut = ($available === 0);
        $hasDiscount = $onSale;
        $brandName = $product->brand->name ?? null;

        // Formatted review count (k style)
        $formattedReviewsCount = $reviewsCount >= 1000 ? round($reviewsCount / 1000, 1) . 'k' : $reviewsCount;

        try {
            $reviewsPayload = app(\App\Services\ReviewsPresenter::class)->build($product);
            $reviews = $reviewsPayload['reviews'];
            $reviewStats = $reviewsPayload['stats'];
        } catch (\Throwable $e) {
            $reviews = collect();
            $reviewStats = ['total' => 0, 'average' => 0, 'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], 'distribution_percent' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], 'helpful_total' => 0];
        }
        // Determine if current user purchased (for review form visibility)
        $purchased = false;
        if (auth()->check()) {
            try {
                $user = auth()->user();
                if (method_exists($user, 'orders')) {
                    $purchased = $user->orders()
                        ->whereIn('status', ['completed', 'paid', 'delivered'])
                        ->whereHas('items', function ($q) use ($product) {
                            $q->where('product_id', $product->id);
                        })
                        ->exists();
                }
            } catch (\Throwable $e) {
                $purchased = false;
            }
        }

        // Stars representation (boolean filled) for rating display (5 elements)
        $fullRating = (int) floor($rating);
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = ['index' => $i, 'filled' => $i <= $fullRating];
        }
        // In-cart flag for authenticated user/session cart
        try {
            $inCart = (bool) (session('cart') && isset(session('cart')[$product->id]));
        } catch (\Throwable $e) {
            $inCart = false;
        }

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
            'inCart'
        ));
    }
}

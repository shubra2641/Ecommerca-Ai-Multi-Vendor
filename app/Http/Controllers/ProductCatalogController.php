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
        if ($search = $request->get('q')) {
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
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Price range
        if ($min = $request->get('min_price')) {
            if (is_numeric($min)) {
                $query->where('price', '>=', $min);
            }
        }
        if ($max = $request->get('max_price')) {
            if (is_numeric($max)) {
                $query->where('price', '<=', $max);
            }
        }

        // Brand filter
        if ($brands = $request->get('brand')) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $query->whereHas('brand', function ($b) use ($brandsArr) {
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
    protected function convertPrices($products)
    {
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

        return compact('categories', 'brandList', 'wishlistIds', 'compareIds', 'currentCurrency');
    }

    /**
     * Main catalog index
     */
    public function index(Request $request)
    {
        $query = $this->baseQuery();

        // Category filter
        if ($cat = $request->get('category')) {
            $slugMap = Cache::remember(
                'product_category_slug_id_map',
                600,
                fn () => ProductCategory::pluck('id', 'slug')->all()
            );
            $id = $slugMap[$cat] ?? null;
            if ($id) {
                $childIds = Cache::remember(
                    'category_children_ids_' . $id,
                    600,
                    fn () => ProductCategory::where('parent_id', $id)->pluck('id')->all()
                );
                $query->where(function ($qq) use ($id, $childIds) {
                    $qq->where('product_category_id', $id)
                        ->orWhereIn('product_category_id', $childIds);
                });
            }
        }

        // Tag filter
        if ($tag = $request->get('tag')) {
            $query->whereHas('tags', fn ($t) => $t->where('slug', $tag));
        }

        $query = $this->applyFilters($query, $request);
        $products = $this->processProducts($query->simplePaginate(24)->withQueryString());

        $commonData = $this->getCommonData($request);
        $selectedBrands = (array) $request->get('brand', []);

        return view('front.products.index', array_merge($commonData, compact('products', 'selectedBrands')));
    }

    /**
     * Category page
     */
    public function category($slug, Request $request)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $childIds = Cache::remember('category_children_ids_' . $category->id, 600, function () use ($category) {
            return $category->children()->pluck('id')->all();
        });

        $query = $this->baseQuery()->where(function ($qq) use ($category, $childIds) {
            $qq->where('product_category_id', $category->id)->orWhereIn('product_category_id', $childIds);
        });

        $query = $this->applyFilters($query, $request);
        $products = $this->processProducts($query->simplePaginate(24)->withQueryString());

        $commonData = $this->getCommonData($request);

        return view('front.products.category', array_merge($commonData, compact('category', 'products')));
    }

    /**
     * Tag page
     */
    public function tag($slug, Request $request)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $query = $this->baseQuery()->whereHas('tags', function ($t) use ($tag) {
            $t->where('product_tags.id', $tag->id);
        });

        $query = $this->applyFilters($query, $request);
        $products = $this->processProducts($query->simplePaginate(24)->withQueryString());

        $commonData = $this->getCommonData($request);

        return view('front.products.tag', array_merge($commonData, compact('tag', 'products')));
    }

    /**
     * Product detail page
     */
    public function show($slug)
    {
        $product = Product::with(['category', 'tags', 'variations', 'vendor'])
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

        // Build attribute map for variations
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

        $currentCurrency = session('currency_id')
            ? \App\Models\Currency::find(session('currency_id'))
            : \App\Models\Currency::getDefault();

        // Reviews data
        $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
        $rating = $reviewsCount ? (float) ($product->approved_reviews_avg ?? 0) : 0;

        // Gallery images
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

        $gallery = $images->map(fn ($p) => ['raw' => $p, 'url' => asset($p)]);
        $mainImage = $gallery->first();

        // Pricing
        $onSale = $product->isOnSale();
        $basePrice = $product->display_price ?? $product->effectivePrice();
        $origPrice = $product->display_price ?? $product->price;
        $discountPercent = ($onSale && $origPrice && $origPrice > 0)
            ? (int) round((($origPrice - $basePrice) / $origPrice) * 100)
            : null;

        // Stock
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

        // Variation price range
        $minP = $maxP = null;
        $activeVars = collect();
        if ($product->type === 'variable') {
            $activeVars = $product->variations->where('active', true);
            $prices = $activeVars->map(fn ($v) => $v->effectivePrice())->filter();
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

        // Variation attributes
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
                'swatch_map' => $isColor ? $this->buildSwatchMap($values) : [],
            ];
        }

        // Tags
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

        // Flags
        // For variable products, check if ANY variation has stock
        if ($product->type === 'variable' && $activeVars->isNotEmpty()) {
            $hasAnyStock = $activeVars->filter(function ($v) {
                if (! $v->manage_stock) {
                    return true;
                } // Unlimited stock
                $availableStock = ($v->stock_qty ?? 0) - ($v->reserved_qty ?? 0);

                return $availableStock > 0;
            })->isNotEmpty();
            $isOut = ! $hasAnyStock;
        } else {
            $isOut = ($available === 0);
        }
        $hasDiscount = $onSale;
        $brandName = $product->brand->name ?? null;

        // Reviews
        $formattedReviewsCount = $reviewsCount >= 1000 ? round($reviewsCount / 1000, 1) . 'k' : $reviewsCount;

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

        // Check if user purchased
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

        // Stars
        $fullRating = (int) floor($rating);
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = ['index' => $i, 'filled' => $i <= $fullRating];
        }

        // In cart
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
            'inCart',
            'activeVars'
        ));
    }

    /**
     * Build a color swatch lookup for variation attributes.
     */
    protected function buildSwatchMap(array $values): array
    {
        $presets = [
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
            if (isset($presets[$normalized])) {
                $map[$key] = $presets[$normalized];

                continue;
            }

            // Try without spaces
            $fallback = str_replace(' ', '', $normalized);
            if (isset($presets[$fallback])) {
                $map[$key] = $presets[$fallback];

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
}

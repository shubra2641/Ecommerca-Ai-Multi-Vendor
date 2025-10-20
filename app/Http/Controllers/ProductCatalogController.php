<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductCatalogController extends Controller
{
    /**
     * Main catalog index
     */
    public function index(Request $request)
    {
        $query = $this->buildBaseQuery();
        $query = $this->applyFilters($query, $request);
        $products = $this->getProducts($query, $request);
        $data = $this->getViewData($request);

        return view('front.products.index', array_merge($data, compact('products')));
    }

    /**
     * Category page
     */
    public function category($slug, Request $request)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $query = $this->buildBaseQuery();
        $query = $this->applyCategoryFilter($query, $category);
        $query = $this->applyFilters($query, $request);
        $products = $this->getProducts($query, $request);
        $data = $this->getViewData($request);

        return view('front.products.category', array_merge($data, compact('category', 'products')));
    }

    /**
     * Tag page
     */
    public function tag($slug, Request $request)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $query = $this->buildBaseQuery();
        $query->whereHas('tags', function ($t) use ($tag) {
            $t->where('product_tags.id', $tag->id);
        });
        $query = $this->applyFilters($query, $request);
        $products = $this->getProducts($query, $request);
        $data = $this->getViewData($request);

        return view('front.products.tag', array_merge($data, compact('tag', 'products')));
    }

    /**
     * Product detail page
     */
    public function show($slug)
    {
        $product = $this->getProduct($slug);
        $this->convertPrices(collect([$product]));

        $data = [
            'product' => $product,
            'related' => $this->getRelatedProducts($product),
            'currentCurrency' => $this->getCurrentCurrency(),
            'gallery' => $this->getProductGallery($product),
            'pricing' => $this->getPricingData($product),
            'stock' => $this->getStockData($product),
            'reviews' => $this->getReviewsData($product),
            'user' => $this->getUserData($product),
        ];

        return view('front.products.show', $data);
    }

    /**
     * Build base query for products
     */
    protected function buildBaseQuery()
    {
        return Product::query()
            ->select([
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
                'vendor_id'
            ])
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
     * Apply category filter
     */
    protected function applyCategoryFilter($query, $category)
    {
        $childIds = Cache::remember('category_children_ids_' . $category->id, 600, function () use ($category) {
            return $category->children()->pluck('id')->all();
        });

        return $query->where(function ($qq) use ($category, $childIds) {
            $qq->where('product_category_id', $category->id)
                ->orWhereIn('product_category_id', $childIds);
        });
    }

    /**
     * Get products with processing
     */
    protected function getProducts($query, Request $request)
    {
        $products = $query->simplePaginate(24)->withQueryString();
        $this->processProducts($products);
        return $products;
    }

    /**
     * Process products for display
     */
    protected function processProducts($products)
    {
        foreach ($products as $product) {
            $product->list_available = $product->manage_stock
                ? max(0, ($product->stock_qty ?? 0) - ($product->reserved_qty ?? 0))
                : null;
        }
        $this->convertPrices($products);
    }

    /**
     * Convert product prices
     */
    protected function convertPrices($products)
    {
        if ($products->isEmpty()) return;

        try {
            $sessionCurrencyId = session('currency_id');
            if (!$sessionCurrencyId) {
                $this->setDefaultPrices($products);
                return;
            }

            $target = \App\Models\Currency::find($sessionCurrencyId);
            $default = \App\Models\Currency::getDefault();

            if (!$target || !$default || $target->id === $default->id) {
                $this->setDefaultPrices($products);
                return;
            }

            foreach ($products as $product) {
                $product->display_price = $default->convertTo($product->price, $target, 2);
            }
        } catch (\Throwable $e) {
            $this->setDefaultPrices($products);
        }
    }

    /**
     * Set default prices
     */
    protected function setDefaultPrices($products)
    {
        foreach ($products as $product) {
            $product->display_price = $product->price;
        }
    }

    /**
     * Get view data
     */
    protected function getViewData(Request $request)
    {
        return [
            'categories' => Cache::remember('product_category_tree', 600, function () {
                return ProductCategory::with('children.children')->whereNull('parent_id')->get();
            }),
            'brandList' => Cache::remember('product_brands_list', 600, function () {
                return Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get();
            }),
            'wishlistIds' => $this->getWishlistIds($request),
            'compareIds' => session('compare', []),
            'currentCurrency' => $this->getCurrentCurrency(),
            'selectedBrands' => (array) $request->get('brand', []),
        ];
    }

    /**
     * Get wishlist IDs
     */
    protected function getWishlistIds(Request $request)
    {
        if ($request->user()?->id) {
            return (array) Cache::remember(
                'wishlist_ids_' . $request->user()->id,
                60,
                function () use ($request) {
                    return \App\Models\WishlistItem::where('user_id', $request->user()->id)
                        ->pluck('product_id')->all();
                }
            );
        }
        return session('wishlist', []);
    }

    /**
     * Get current currency
     */
    protected function getCurrentCurrency()
    {
        static $cached = null;
        if ($cached !== null) return $cached;

        try {
            $cid = session('currency_id');
            $cached = $cid ? \App\Models\Currency::find($cid) : \App\Models\Currency::getDefault();
            return $cached ?: \App\Models\Currency::getDefault();
        } catch (\Throwable $e) {
            return \App\Models\Currency::getDefault();
        }
    }

    /**
     * Get product with details
     */
    protected function getProduct($slug)
    {
        return Product::with(['category', 'tags', 'variations'])
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('approved', true);
            }])
            ->withAvg(['reviews as approved_reviews_avg' => function ($q) {
                $q->where('approved', true);
            }], 'rating')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Get related products
     */
    protected function getRelatedProducts($product)
    {
        return Cache::remember('product_related_' . $product->id, 300, function () use ($product) {
            return Product::active()
                ->where('product_category_id', $product->product_category_id)
                ->where('id', '!=', $product->id)
                ->with('variations')
                ->limit(6)
                ->get();
        });
    }

    /**
     * Get product gallery
     */
    protected function getProductGallery($product)
    {
        $images = collect();

        if (!empty($product->main_image)) {
            $images->push($product->main_image);
        }

        if (!empty($product->gallery) && is_array($product->gallery)) {
            foreach ($product->gallery as $img) {
                if ($img) $images->push($img);
            }
        }

        if ($product->type === 'variable' && $product->variations->count()) {
            foreach ($product->variations->where('active', true) as $v) {
                if (!empty($v->image) && !$images->contains($v->image)) {
                    $images->push($v->image);
                }
            }
        }

        if ($images->isEmpty()) {
            $images->push('front/images/default-product.png');
        }

        $gallery = $images->map(fn($p) => ['raw' => $p, 'url' => asset($p)]);
        return ['gallery' => $gallery, 'mainImage' => $gallery->first()];
    }

    /**
     * Get pricing data
     */
    protected function getPricingData($product)
    {
        $onSale = $product->isOnSale();
        $basePrice = $product->display_price ?? $product->effectivePrice();
        $origPrice = $product->display_price ?? $product->price;
        $discountPercent = ($onSale && $origPrice && $origPrice > 0)
            ? (int) round((($origPrice - $basePrice) / $origPrice) * 100)
            : null;

        return compact('onSale', 'basePrice', 'origPrice', 'discountPercent');
    }

    /**
     * Get stock data
     */
    protected function getStockData($product)
    {
        $available = $product->availableStock();
        $stockClass = 'high-stock';

        if ($available === 0) {
            $stockClass = 'out-stock';
        } elseif (!is_null($available)) {
            if ($available <= 5) $stockClass = 'low-stock';
            elseif ($available <= 20) $stockClass = 'mid-stock';
        }

        $levelLabel = $this->getStockLabel($available);
        return compact('available', 'stockClass', 'levelLabel');
    }

    /**
     * Get stock label
     */
    protected function getStockLabel($available)
    {
        if ($available === 0) return __('Out of stock');
        if (!is_numeric($available)) return __('In stock');

        $level = $available <= 5 ? 'Low' : ($available <= 20 ? 'Mid' : 'High');
        return __('In stock') . " ({$available}) • {$level} stock";
    }

    /**
     * Get reviews data
     */
    protected function getReviewsData($product)
    {
        $reviewsCount = (int) ($product->approved_reviews_count ?? 0);
        $rating = $reviewsCount ? (float) ($product->approved_reviews_avg ?? 0) : 0;
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
                'helpful_total' => 0
            ];
        }

        $stars = [];
        $fullRating = (int) floor($rating);
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = ['index' => $i, 'filled' => $i <= $fullRating];
        }

        return compact('reviewsCount', 'rating', 'formattedReviewsCount', 'reviews', 'reviewStats', 'stars');
    }

    /**
     * Get user data
     */
    protected function getUserData($product)
    {
        $available = $product->availableStock();
        $onSale = $product->isOnSale();

        return [
            'tagsCount' => $product->tags->count(),
            'tagsFirst' => $product->tags->take(6),
            'tagsMore' => $product->tags->slice(6),
            'hasDims' => count(array_filter([$product->length, $product->width, $product->height])) > 0,
            'dims' => array_filter([$product->length, $product->width, $product->height]),
            'specCount' => $this->calculateSpecCount($product),
            'isOut' => ($available === 0),
            'hasDiscount' => $onSale,
            'brandName' => $product->brand->name ?? null,
            'purchased' => $this->checkUserPurchased($product),
            'inCart' => $this->checkInCart($product),
        ];
    }

    /**
     * Calculate spec count
     */
    protected function calculateSpecCount($product)
    {
        $count = 0;
        if ($product->sku) $count++;
        if ($product->weight) $count++;
        if ($product->length) $count++;
        if ($product->width) $count++;
        if ($product->height) $count++;
        if ($product->refund_days) $count++;
        return $count + 1; // +1 for base spec
    }

    /**
     * Check if user purchased product
     */
    protected function checkUserPurchased($product)
    {
        if (!Auth::check()) return false;

        try {
            $user = Auth::user();
            if (method_exists($user, 'orders')) {
                return $user->orders()
                    ->whereIn('status', ['completed', 'paid', 'delivered'])
                    ->whereHas('items', function ($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })
                    ->exists();
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check if product is in cart
     */
    protected function checkInCart($product)
    {
        try {
            return (bool) (session('cart') && isset(session('cart')[$product->id]));
        } catch (\Throwable $e) {
            return false;
        }
    }
}

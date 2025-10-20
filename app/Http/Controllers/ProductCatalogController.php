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
        $query = $this->getBaseQuery();
        $this->applyFilters($query, $request);
        $products = $this->getProducts($query);
        $data = $this->getCommonData($request);

        return view('front.products.index', array_merge($data, compact('products')));
    }

    /**
     * Category page
     */
    public function category($slug, Request $request)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $query = $this->getBaseQuery();
        $this->applyCategoryFilter($query, $category);
        $this->applyFilters($query, $request);
        $products = $this->getProducts($query);
        $data = $this->getCommonData($request);

        return view('front.products.category', array_merge($data, compact('category', 'products')));
    }

    /**
     * Tag page
     */
    public function tag($slug, Request $request)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $query = $this->getBaseQuery();
        $query->whereHas('tags', fn($t) => $t->where('product_tags.id', $tag->id));
        $this->applyFilters($query, $request);
        $products = $this->getProducts($query);
        $data = $this->getCommonData($request);

        return view('front.products.tag', array_merge($data, compact('tag', 'products')));
    }

    /**
     * Product detail page
     */
    public function show($slug)
    {
        $product = $this->getProduct($slug);
        $this->convertPrices(collect([$product]));

        return view('front.products.show', [
            'product' => $product,
            'related' => $this->getRelatedProducts($product),
            'currentCurrency' => $this->getCurrentCurrency(),
            'gallery' => $this->getGallery($product),
            'pricing' => $this->getPricing($product),
            'stock' => $this->getStock($product),
            'reviews' => $this->getReviews($product),
            'user' => $this->getUserInfo($product),
        ]);
    }

    /**
     * Get base query
     */
    protected function getBaseQuery()
    {
        return Product::query()
            ->select(['id', 'name', 'slug', 'price', 'sale_price', 'product_category_id', 
                     'manage_stock', 'stock_qty', 'reserved_qty', 'type', 'main_image', 
                     'is_featured', 'active', 'vendor_id'])
            ->with(['category', 'brand'])
            ->active();
    }

    /**
     * Apply filters
     */
    protected function applyFilters($query, Request $request)
    {
        // Search
        if ($search = $request->get('q')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filters
        if ($request->boolean('featured')) $query->featured();
        if ($request->boolean('best')) $query->bestSeller();
        if ($request->boolean('sale')) $query->onSale();
        if ($type = $request->get('type')) $query->where('type', $type);

        // Price range
        if ($min = $request->get('min_price')) {
            if (is_numeric($min)) $query->where('price', '>=', $min);
        }
        if ($max = $request->get('max_price')) {
            if (is_numeric($max)) $query->where('price', '<=', $max);
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
        match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest()
        };
    }

    /**
     * Apply category filter
     */
    protected function applyCategoryFilter($query, $category)
    {
        $childIds = Cache::remember('category_children_ids_' . $category->id, 600, 
            fn() => $category->children()->pluck('id')->all());

        $query->where(function ($qq) use ($category, $childIds) {
            $qq->where('product_category_id', $category->id)
                ->orWhereIn('product_category_id', $childIds);
        });
    }

    /**
     * Get products
     */
    protected function getProducts($query)
    {
        $products = $query->simplePaginate(24)->withQueryString();
        $this->processProducts($products);
        return $products;
    }

    /**
     * Process products
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
     * Convert prices
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
     * Get common data
     */
    protected function getCommonData(Request $request)
    {
        return [
            'categories' => Cache::remember('product_category_tree', 600, 
                fn() => ProductCategory::with('children.children')->whereNull('parent_id')->get()),
            'brandList' => Cache::remember('product_brands_list', 600, 
                fn() => Brand::active()->withCount('products')->orderByDesc('products_count')->take(30)->get()),
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
            return (array) Cache::remember('wishlist_ids_' . $request->user()->id, 60, 
                fn() => \App\Models\WishlistItem::where('user_id', $request->user()->id)
                    ->pluck('product_id')->all());
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
     * Get product
     */
    protected function getProduct($slug)
    {
        return Product::with(['category', 'tags', 'variations'])
            ->withCount(['reviews as approved_reviews_count' => fn($q) => $q->where('approved', true)])
            ->withAvg(['reviews as approved_reviews_avg' => fn($q) => $q->where('approved', true)], 'rating')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Get related products
     */
    protected function getRelatedProducts($product)
    {
        return Cache::remember('product_related_' . $product->id, 300, 
            fn() => Product::active()
                ->where('product_category_id', $product->product_category_id)
                ->where('id', '!=', $product->id)
                ->with('variations')
                ->limit(6)
                ->get());
    }

    /**
     * Get gallery
     */
    protected function getGallery($product)
    {
        $images = collect();
        
        if (!empty($product->main_image)) $images->push($product->main_image);
        
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
        
        if ($images->isEmpty()) $images->push('front/images/default-product.png');

        $gallery = $images->map(fn($p) => ['raw' => $p, 'url' => asset($p)]);
        return ['gallery' => $gallery, 'mainImage' => $gallery->first()];
    }

    /**
     * Get pricing
     */
    protected function getPricing($product)
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
     * Get stock
     */
    protected function getStock($product)
    {
        $available = $product->availableStock();
        $stockClass = match (true) {
            $available === 0 => 'out-stock',
            $available <= 5 => 'low-stock',
            $available <= 20 => 'mid-stock',
            default => 'high-stock'
        };

        $levelLabel = match (true) {
            $available === 0 => __('Out of stock'),
            !is_numeric($available) => __('In stock'),
            $available <= 5 => __('In stock') . " ({$available}) • Low stock",
            $available <= 20 => __('In stock') . " ({$available}) • Mid stock",
            default => __('In stock') . " ({$available}) • High stock"
        };

        return compact('available', 'stockClass', 'levelLabel');
    }

    /**
     * Get reviews
     */
    protected function getReviews($product)
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
                'total' => 0, 'average' => 0,
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
     * Get user info
     */
    protected function getUserInfo($product)
    {
        $available = $product->availableStock();
        $onSale = $product->isOnSale();
        
        return [
            'tagsCount' => $product->tags->count(),
            'tagsFirst' => $product->tags->take(6),
            'tagsMore' => $product->tags->slice(6),
            'hasDims' => count(array_filter([$product->length, $product->width, $product->height])) > 0,
            'dims' => array_filter([$product->length, $product->width, $product->height]),
            'specCount' => $this->getSpecCount($product),
            'isOut' => ($available === 0),
            'hasDiscount' => $onSale,
            'brandName' => $product->brand->name ?? null,
            'purchased' => $this->checkPurchased($product),
            'inCart' => $this->checkInCart($product),
        ];
    }

    /**
     * Get spec count
     */
    protected function getSpecCount($product)
    {
        $count = 0;
        if ($product->sku) $count++;
        if ($product->weight) $count++;
        if ($product->length) $count++;
        if ($product->width) $count++;
        if ($product->height) $count++;
        if ($product->refund_days) $count++;
        return $count + 1;
    }

    /**
     * Check if purchased
     */
    protected function checkPurchased($product)
    {
        if (!Auth::check()) return false;
        
        try {
            $user = Auth::user();
            if (method_exists($user, 'orders')) {
                return $user->orders()
                    ->whereIn('status', ['completed', 'paid', 'delivered'])
                    ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                    ->exists();
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check if in cart
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
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
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->active();
        $this->applyFilters($query, $request);
        $products = $query->latest()->paginate(20)->appends($request->all());
        $data = $this->getCommonData($request);

        return view('front.products.index', array_merge($data, compact('products')));
    }

    public function category($slug, Request $request)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();
        $query = Product::with(['category', 'brand'])->active()->where('product_category_id', $category->id);
        $this->applyFilters($query, $request);
        $products = $query->latest()->paginate(20)->appends($request->all());
        $data = $this->getCommonData($request);

        return view('front.products.category', array_merge($data, compact('category', 'products')));
    }

    public function tag($slug, Request $request)
    {
        $tag = ProductTag::where('slug', $slug)->firstOrFail();
        $query = Product::with(['category', 'brand'])->active()->whereHas('tags', fn($t) => $t->where('product_tags.id', $tag->id));
        $this->applyFilters($query, $request);
        $products = $query->latest()->paginate(20)->appends($request->all());
        $data = $this->getCommonData($request);

        return view('front.products.tag', array_merge($data, compact('tag', 'products')));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'brand', 'tags', 'variations', 'reviews.user'])
            ->where('slug', $slug)->active()->firstOrFail();

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

    protected function applyFilters($query, Request $request)
    {
        if ($search = $request->get('q')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', 1);
        }
        if ($request->boolean('best')) {
            $query->where('is_best_seller', 1);
        }
        if ($request->boolean('sale')) {
            $query->whereNotNull('sale_price')->where('sale_price', '>', 0);
        }
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

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

        if ($brands = $request->get('brand')) {
            $brandsArr = array_filter(is_array($brands) ? $brands : explode(',', $brands));
            if ($brandsArr) {
                $query->whereHas('brand', function ($b) use ($brandsArr) {
                    $b->whereIn('slug', array_map('Str::slug', $brandsArr));
                });
            }
        }

        if ($sort = $request->get('sort')) {
            match ($sort) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'name_asc' => $query->orderBy('name', 'asc'),
                'name_desc' => $query->orderBy('name', 'desc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                default => $query->latest(),
            };
        }
    }

    protected function getCommonData(Request $request)
    {
        return [
            'categories' => Cache::remember('categories', 3600, fn() => ProductCategory::orderBy('name')->get()),
            'brands' => Cache::remember('brands', 3600, fn() => Brand::orderBy('name')->get()),
            'wishlistIds' => Auth::check() ? Auth::user()->wishlistItems()->pluck('product_id')->toArray() : [],
            'currentCurrency' => $this->getCurrentCurrency(),
        ];
    }

    protected function getRelatedProducts($product)
    {
        return Product::with(['category', 'brand'])
            ->where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(8)
            ->get();
    }

    protected function getCurrentCurrency()
    {
        return session('currency', 'USD');
    }

    protected function getProductGallery($product)
    {
        if (empty($product->gallery)) {
            return [$product->main_image];
        }
        return array_merge([$product->main_image], $product->gallery);
    }

    protected function getPricingData($product)
    {
        return [
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'currency' => $this->getCurrentCurrency(),
        ];
    }

    protected function getStockData($product)
    {
        return [
            'manage_stock' => $product->manage_stock,
            'stock_qty' => $product->stock_qty,
            'reserved_qty' => $product->reserved_qty,
            'backorder' => $product->backorder,
        ];
    }

    protected function getReviewsData($product)
    {
        return $product->reviews()->with('user')->latest()->limit(10)->get();
    }

    protected function getUserData($product)
    {
        if (!Auth::check()) {
            return ['in_cart' => false, 'purchased' => false];
        }

        $user = Auth::user();
        return [
            'in_cart' => $user->cart()->where('product_id', $product->id)->exists(),
            'purchased' => $user->orders()->whereHas('items', fn($q) => $q->where('product_id', $product->id))->exists(),
        ];
    }

    protected function convertPrices($products)
    {
        $currency = $this->getCurrentCurrency();
        if ($currency === 'USD') {
            return;
        }

        $rate = Cache::remember("currency_rate_{$currency}", 3600, function () use ($currency) {
            return 1.0; // Default rate
        });

        $products->each(function ($product) use ($rate) {
            $product->price = $product->price * $rate;
            if ($product->sale_price) {
                $product->sale_price = $product->sale_price * $rate;
            }
        });
    }
}

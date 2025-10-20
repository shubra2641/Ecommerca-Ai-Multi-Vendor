<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\HomepageBanner;
use App\Models\HomepageSection;
use App\Models\HomepageSlide;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $setting = $this->getSetting();
        $sections = $this->getSections();
        $enabledSections = $sections->where('enabled', true)->values();

        $data = [
            'setting' => $setting,
            'categories' => $this->getCategories($sections),
            'landingCategories' => $this->getCategories($sections), // For blade template
            'brands' => $this->getBrands($sections),
            'parentCategories' => $this->getParentCategories(),
            'latestProducts' => $this->getLatestProducts($sections),
            'flashSaleProducts' => $this->getFlashSaleProducts($sections),
            'latestPosts' => $this->getLatestPosts($sections),
            'slides' => $this->getSlides(),
            'banners' => $this->getBanners(),
            'enabledSections' => $enabledSections,
            'flashSaleEndsAt' => $this->getFlashSaleEndTime(),
            'wishlistIds' => $this->getWishlistIds(),
            'compareIds' => $this->getCompareIds(),
            // Showcase sections
            'showcase_brands' => $this->getShowcaseBrands($sections),
            'showcase_most_rated' => $this->getShowcaseMostRated($sections),
            'showcase_discount' => $this->getShowcaseDiscount($sections),
            'showcase_best_selling' => $this->getShowcaseBestSelling($sections),
            'showcase_latest' => $this->getShowcaseLatest($sections),
            'blog_posts' => $this->getBlogPosts($sections),
        ];

        return view('front.landing', $data);
    }

    private function getSetting()
    {
        return Cache::remember('setting', 3600, fn() => Setting::first() ?? new Setting());
    }

    private function getSections()
    {
        return Cache::remember('homepage_sections', 3600, fn() => HomepageSection::orderBy('id')->get());
    }

    private function getCategories($sections)
    {
        $limit = $sections->where('key', 'categories')->first()->item_limit ?? 6;
        return Cache::remember(
            'home_categories',
            1800,
            fn() =>
            ProductCategory::whereNull('parent_id')->where('active', true)->orderBy('id')->take($limit)->get()
        );
    }

    private function getBrands($sections)
    {
        $limit = $sections->where('key', 'brands')->first()->item_limit ?? 8;
        return Cache::remember(
            'home_brands',
            1800,
            fn() =>
            Brand::where('active', true)->orderBy('id')->take($limit)->get()
        );
    }

    private function getParentCategories()
    {
        return Cache::remember(
            'parent_categories',
            1800,
            fn() =>
            ProductCategory::whereNull('parent_id')->where('active', true)->orderBy('id')->get()
        );
    }

    private function getLatestProducts($sections)
    {
        $limit = $sections->where('key', 'latest_products')->first()->item_limit ?? 8;
        return Cache::remember(
            'landing_latest_products',
            900,
            fn() =>
            Product::active()->with(['category'])->withCount('reviews')->withAvg('reviews', 'rating')->latest('id')->take($limit)->get()
        );
    }

    private function getFlashSaleProducts($sections)
    {
        $limit = $sections->where('key', 'flash_sale')->first()->item_limit ?? 8;
        return Cache::remember(
            'landing_flash_sale_products',
            300,
            fn() =>
            Product::active()->whereNotNull('sale_price')->where('sale_price', '>', 0)->with(['category'])->withCount('reviews')->withAvg('reviews', 'rating')->latest('id')->take($limit)->get()
        );
    }

    private function getLatestPosts($sections)
    {
        $limit = $sections->where('key', 'latest_posts')->first()->item_limit ?? 3;
        return Cache::remember(
            'landing_latest_posts',
            1800,
            fn() =>
            Post::published()->with(['category'])->latest('published_at')->take($limit)->get()
        );
    }

    private function getSlides()
    {
        return Cache::remember(
            'homepage_slides',
            1800,
            fn() =>
            HomepageSlide::orderBy('id')->get()
        );
    }

    private function getBanners()
    {
        return Cache::remember(
            'homepage_banners',
            1800,
            fn() =>
            HomepageBanner::orderBy('id')->get()->groupBy('placement_key')
        );
    }

    private function getFlashSaleEndTime()
    {
        return now()->addHours(24); // Default 24 hours
    }

    private function getWishlistIds()
    {
        if (auth()->check()) {
            return auth()->user()->wishlistItems()->pluck('product_id')->toArray();
        }
        return session('wishlist', []);
    }

    private function getCompareIds()
    {
        return session('compare', []);
    }

    // Showcase sections
    private function getShowcaseBrands($sections)
    {
        $limit = $sections->where('key', 'showcase_brands')->first()->item_limit ?? 8;
        return Cache::remember(
            'showcase_brands',
            1800,
            fn() =>
            Brand::where('active', true)->orderBy('id')->take($limit)->get()
        );
    }

    private function getShowcaseMostRated($sections)
    {
        $limit = $sections->where('key', 'showcase_most_rated')->first()->item_limit ?? 6;
        return Cache::remember(
            'showcase_most_rated',
            900,
            fn() =>
            Product::active()->with(['category'])->withCount('reviews')->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc')->take($limit)->get()
        );
    }

    private function getShowcaseDiscount($sections)
    {
        $limit = $sections->where('key', 'showcase_discount')->first()->item_limit ?? 6;
        return Cache::remember(
            'showcase_discount',
            300,
            fn() =>
            Product::active()->whereNotNull('sale_price')->where('sale_price', '>', 0)->with(['category'])->orderBy('sale_price', 'asc')->take($limit)->get()
        );
    }

    private function getShowcaseBestSelling($sections)
    {
        $limit = $sections->where('key', 'showcase_best_selling')->first()->item_limit ?? 6;
        return Cache::remember(
            'showcase_best_selling',
            900,
            fn() =>
            Product::active()->with(['category'])->withCount('orderItems')->orderBy('order_items_count', 'desc')->take($limit)->get()
        );
    }

    private function getShowcaseLatest($sections)
    {
        $limit = $sections->where('key', 'showcase_latest')->first()->item_limit ?? 6;
        return Cache::remember(
            'showcase_latest',
            900,
            fn() =>
            Product::active()->with(['category'])->latest('id')->take($limit)->get()
        );
    }

    private function getBlogPosts($sections)
    {
        $limit = $sections->where('key', 'blog_posts')->first()->item_limit ?? 3;
        return Cache::remember(
            'blog_posts',
            1800,
            fn() =>
            Post::published()->with(['category'])->latest('published_at')->take($limit)->get()
        );
    }
}

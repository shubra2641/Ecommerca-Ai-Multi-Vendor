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
        // Get basic data
        $setting = $this->getSetting();
        $sections = $this->getSections();
        $enabledSections = $sections->where('enabled', true)->values();

        // Get section titles and meta
        $sectionData = $this->getSectionData($sections);

        // Get main data
        $categories = $this->getCategories($sections);
        $latestProducts = $this->getLatestProducts($sections);
        $flashSaleProducts = $this->getFlashSaleProducts($sections);
        $latestPosts = $this->getLatestPosts($sections);
        $slides = $this->getSlides();
        $banners = $this->getBanners();
        $showcaseSections = $this->getShowcaseSections($sections, $sectionData['titles']);

        // Calculate flash sale end time
        $flashSaleEndsAt = $this->getFlashSaleEndTime($flashSaleProducts);

        // Prepare view data
        $viewData = [
            'setting' => $setting,
            'categories' => $categories,
            'landingMainCategories' => $categories,
            'landingCategories' => $categories,
            'latestProducts' => $latestProducts,
            'flashSaleProducts' => $flashSaleProducts,
            'flashSaleEndsAt' => $flashSaleEndsAt,
            'latestPosts' => $latestPosts,
            'slides' => $slides,
            'banners' => $banners,
            'enabledSections' => $enabledSections,
            'sectionTitles' => $sectionData['titles'],
            'sectionMeta' => $sectionData['meta'],
            'showcaseSections' => $showcaseSections,
            'showcaseSectionsActiveCount' => $showcaseSections->where('type', '!=', 'brands')->count(),
            'brandSec' => $showcaseSections->firstWhere('type', 'brands'),
            'categoryImagePlaceholder' => asset('images/product-placeholder.svg'),
            'wishlistIds' => [],
            'compareIds' => [],
        ];

        return view('front.landing', $viewData);
    }

    private function getSetting()
    {
        return Cache::remember('site_settings', 3600, fn() => Setting::first());
    }

    private function getSections()
    {
        return Cache::remember(
            'homepage_sections_conf',
            600,
            fn() =>
            HomepageSection::orderBy('sort_order')->get()
        );
    }

    private function getSectionData($sections)
    {
        $locale = app()->getLocale();
        $defaultLocale = config('app.locale');
        $titles = [];
        $meta = [];

        foreach ($sections as $sec) {
            $titles[$sec->key] = $this->getTranslatedText($sec->title_i18n, $locale, $defaultLocale, $sec->key);
            $meta[$sec->key] = [
                'cta_enabled' => $sec->cta_enabled,
                'cta_url' => $sec->cta_url,
                'cta_label' => $this->getTranslatedText($sec->cta_label_i18n, $locale, $defaultLocale),
            ];
        }

        return ['titles' => $titles, 'meta' => $meta];
    }

    private function getTranslatedText($i18nArray, $locale, $defaultLocale, $fallback = null)
    {
        if (!is_array($i18nArray)) return $fallback ? ucfirst(str_replace('_', ' ', $fallback)) : '';

        return $i18nArray[$locale] ??
            $i18nArray[$defaultLocale] ??
            (array_values($i18nArray)[0] ?? ($fallback ? ucfirst(str_replace('_', ' ', $fallback)) : ''));
    }

    private function getCategories($sections)
    {
        $sectionsIndex = $sections->keyBy('key');
        $limit = optional($sectionsIndex->get('categories'))->item_limit ?? 6;

        return Cache::remember(
            'home_categories',
            1800,
            fn() =>
            ProductCategory::whereNull('parent_id')
                ->where('active', true)
                ->orderBy('position')
                ->take($limit)
                ->get()
        );
    }

    private function getLatestProducts($sections)
    {
        $sectionsIndex = $sections->keyBy('key');
        $limit = optional($sectionsIndex->get('latest_products'))->item_limit ?? 8;

        return Cache::remember(
            'landing_latest_products',
            900,
            fn() =>
            Product::active()
                ->with(['category'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->latest('id')
                ->take($limit)
                ->get()
        );
    }

    private function getFlashSaleProducts($sections)
    {
        $sectionsIndex = $sections->keyBy('key');
        $limit = optional($sectionsIndex->get('flash_sale'))->item_limit ?? 8;
        $now = now();

        return Cache::remember(
            'landing_flash_products',
            300,
            fn() =>
            Product::active()
                ->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price')
                ->where(function ($q) use ($now) {
                    $q->whereNull('sale_start')->orWhere('sale_start', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('sale_end')->orWhere('sale_end', '>=', $now);
                })
                ->with('category')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByRaw('(price - sale_price)/price DESC')
                ->take($limit)
                ->get()
        );
    }

    private function getLatestPosts($sections)
    {
        $sectionsIndex = $sections->keyBy('key');
        $limit = optional($sectionsIndex->get('blog_posts'))->item_limit ?? 3;

        return Cache::remember(
            'home_latest_posts',
            900,
            fn() =>
            Post::where('published', true)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->take($limit)
                ->get()
        );
    }

    private function getSlides()
    {
        return Cache::remember(
            'homepage_slides_enabled',
            600,
            fn() =>
            HomepageSlide::where('enabled', true)
                ->orderBy('sort_order')
                ->get()
        )->map(fn($slide) => $this->applyTranslations($slide));
    }

    private function getBanners()
    {
        return Cache::remember(
            'homepage_banners_enabled',
            600,
            fn() =>
            HomepageBanner::where('enabled', true)
                ->orderBy('sort_order')
                ->get()
        )->map(fn($banner) => $this->applyTranslations($banner))
            ->groupBy(fn($b) => $b->placement_key ?: 'default');
    }

    private function applyTranslations($item)
    {
        $locale = app()->getLocale();

        if (is_array($item->title_i18n ?? null) && isset($item->title_i18n[$locale])) {
            $item->title = $item->title_i18n[$locale];
        }
        if (is_array($item->subtitle_i18n ?? null) && isset($item->subtitle_i18n[$locale])) {
            $item->subtitle = $item->subtitle_i18n[$locale];
        }
        if (is_array($item->button_text_i18n ?? null) && isset($item->button_text_i18n[$locale])) {
            $item->button_text = $item->button_text_i18n[$locale];
        }
        if (is_array($item->alt_text_i18n ?? null) && isset($item->alt_text_i18n[$locale])) {
            $item->alt_text = $item->alt_text_i18n[$locale];
        }

        return $item;
    }

    private function getShowcaseSections($sections, $sectionTitles)
    {
        $sectionsIndex = $sections->keyBy('key');
        $showcaseSections = collect();

        $sectionKeys = [
            'showcase_latest',
            'showcase_best_selling',
            'showcase_discount',
            'showcase_most_rated',
            'showcase_brands'
        ];

        foreach ($sectionKeys as $key) {
            $secCfg = $sectionsIndex->get($key);
            if (!$secCfg || !$secCfg->enabled) continue;

            $limit = $secCfg->item_limit ?? 4;
            $items = $this->getShowcaseItems($key, $limit);

            if ($items->count()) {
                $showcaseSections->push([
                    'key' => $key,
                    'title' => $sectionTitles[$key]['title'] ?? ucfirst(str_replace('_', ' ', $key)),
                    'items' => $items,
                    'type' => $key === 'showcase_brands' ? 'brands' : 'products',
                ]);
            }
        }

        return $showcaseSections->sortBy(
            fn($s) =>
            optional($sectionsIndex->get($s['key']))->sort_order ?? 9999
        )->values();
    }

    private function getShowcaseItems($key, $limit)
    {
        $cacheKey = "showcase_{$key}_data_v1";

        return Cache::remember($cacheKey, 600, fn() => match ($key) {
            'showcase_latest' => Product::active()->latest()->take($limit)->get(),
            'showcase_best_selling' => Product::active()->bestSeller()->latest('id')->take($limit)->get(),
            'showcase_discount' => Product::active()->onSale()->latest('sale_start')->take($limit)->get(),
            'showcase_most_rated' => Product::active()
                ->orderByDesc('approved_reviews_avg')
                ->orderByDesc('approved_reviews_count')
                ->take($limit)->get(),
            'showcase_brands' => Brand::active()
                ->withCount(['products' => fn($q) => $q->active()])
                ->orderByDesc('products_count')
                ->take($limit)->get(),
            default => collect(),
        });
    }

    private function getFlashSaleEndTime($flashSaleProducts)
    {
        if (!$flashSaleProducts->count()) return null;

        $futureEnds = $flashSaleProducts->filter(
            fn($p) =>
            $p->sale_end && $p->sale_end->isFuture()
        )->pluck('sale_end');

        return $futureEnds->count() ? $futureEnds->min() : null;
    }
}

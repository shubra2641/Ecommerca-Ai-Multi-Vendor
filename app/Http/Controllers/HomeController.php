<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
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

final class HomeController extends Controller
{
    /**
     * Display the landing page.
     *
     * Data Pipeline (all heavy lifting done server-side → zero inline PHP in Blade):
     * - $setting                : Site settings (cached 1h)
     * - $sections/$enabledSections: HomepageSections records (ordering, enabling, limits)
     * - $sectionTitles/$sectionMeta: i18n + CTA metadata resolved with locale fallback chain
     * - $categories             : Root categories (legacy limited list for header / quick access)
     * - $landingMainCategories / $landingCategories (alias):
     *   Main circular category list (limit derived from sections config)
     * - $latestProducts         : Recent active products with review aggregates (cached 15m)
     * - $flashSaleProducts      : Active discount window products ordered by discount ratio (cached 5m)
     * - $flashSaleEndsAt        : Earliest future sale_end for countdown (nullable)
     * - $latestPosts            : Recent published blog posts (cached 15m)
     * - $slides                 : Enabled HomepageSlide models with i18n text (cached 10m)
     * - $banners                : HomepageBanner models grouped by placement_key (cached 10m)
     * - $showcaseSections       : Aggregated mini-sections (latest, best_selling, discount, most_rated, brands)
     * - $brandSec               : Extracted brand showcase meta
     * - $wishlistIds/$compareIds: Preloaded arrays (future: populate from session/user) to avoid inline lookups
     *
     * Caching Approach: Conservative TTLs; manual invalidation occurs in admin CRUD for slides/banners.
     * Progressive Enhancement: Slider & countdown gracefully degrade when JS disabled.
     */
    public function index(): View
    {
        // Cache site settings for better performance
        $setting = Cache::remember('site_settings', 3600, function () {
            return Setting::first();
        });

        ['sectionsIndex' => $sectionsIndex, 'enabledSections' => $enabledSections, 'sectionTitles' => $sectionTitles, 'sectionMeta' => $sectionMeta] = $this->buildSectionsData();

        ['categories' => $categories, 'landingMainCategories' => $landingMainCategories] = $this->buildCategoriesData($sectionsIndex);

        ['latestProducts' => $latestProducts, 'flashSaleProducts' => $flashSaleProducts, 'flashSaleEndsAt' => $flashSaleEndsAt] = $this->buildProductsData($sectionsIndex);

        $categoryImagePlaceholder = asset('images/product-placeholder.svg');

        $latestPosts = $this->buildPostsData($sectionsIndex);

        ['slides' => $slides, 'banners' => $banners] = $this->buildSlidesAndBannersData();

        ['showcaseSections' => $showcaseSections, 'brandSec' => $brandSec, 'showcaseSectionsActiveCount' => $showcaseSectionsActiveCount] = $this->buildShowcaseSections($sectionsIndex, $sectionTitles);

        // Provide wishlist & compare arrays default to avoid @php in Blade
        $wishlistIds = [];
        $compareIds = [];

        // Backwards compatibility: some Blade templates expect $landingCategories
        $landingCategories = $landingMainCategories; // alias without duplicating query / cache

        return view('front.landing', compact(
            'setting',
            'categories',
            'latestPosts',
            'landingMainCategories',
            'landingCategories',
            'latestProducts',
            'flashSaleProducts',
            'flashSaleEndsAt',
            'categoryImagePlaceholder',
            'enabledSections',
            'sectionTitles',
            'slides',
            'banners',
            'sectionMeta',
            'showcaseSections',
            'showcaseSectionsActiveCount',
            'brandSec',
            'wishlistIds',
            'compareIds'
        ));
    }

    private function buildSectionsData(): array
    {
        $sections = Cache::remember('homepage_sections_conf', 600, function () {
            return HomepageSection::orderBy('sort_order')->get();
        });
        $enabledSections = $sections->where('enabled', true)->values();
        $locale = app()->getLocale();
        $sectionTitles = [];
        $defaultLocale = config('app.locale');
        $sectionMeta = [];
        foreach ($sections as $sec) {
            $titlesArr = is_array($sec->title_i18n) ? $sec->title_i18n : [];
            $subArr = is_array($sec->subtitle_i18n) ? $sec->subtitle_i18n : [];
            $ctaArr = is_array($sec->cta_label_i18n) ? $sec->cta_label_i18n : [];
            $fallbackTitle = $titlesArr ? (array_values($titlesArr)[0] ?? null) : null;
            $fallbackSub = $subArr ? (array_values($subArr)[0] ?? null) : null;
            $fallbackCta = $ctaArr ? (array_values($ctaArr)[0] ?? null) : null;
            $computedTitle = $titlesArr[$locale] ??
                ($titlesArr[$defaultLocale] ??
                    ($fallbackTitle ? $fallbackTitle : ucfirst(str_replace('_', ' ', $sec->key))));
            $computedSub = $subArr[$locale] ?? ($subArr[$defaultLocale] ?? ($fallbackSub ? $fallbackSub : ''));
            $computedCta = $ctaArr[$locale] ?? ($ctaArr[$defaultLocale] ?? ($fallbackCta ? $fallbackCta : null));
            $sectionTitles[$sec->key] = ['title' => $computedTitle, 'subtitle' => $computedSub];
            $sectionMeta[$sec->key] = [
                'cta_enabled' => $sec->cta_enabled,
                'cta_url' => $sec->cta_url,
                'cta_label' => $computedCta,
            ];
        }
        $sectionsIndex = $sections->keyBy('key');

        return compact('sectionsIndex', 'enabledSections', 'sectionTitles', 'sectionMeta');
    }

    private function buildCategoriesData($sectionsIndex): array
    {
        $categories = Cache::remember('home_categories', 1800, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('categories'))->item_limit ?? 6;

            return ProductCategory::whereNull('parent_id')
                ->where('active', true)
                ->orderBy('position')
                ->take($limit)
                ->get();
        });

        $landingMainCategories = Cache::remember('landing_main_categories', 1800, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('categories'))->item_limit ?? 12;

            return ProductCategory::whereNull('parent_id')
                ->where('active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->take($limit)
                ->get();
        });

        return compact('categories', 'landingMainCategories');
    }

    private function buildProductsData($sectionsIndex): array
    {
        $latestProducts = Cache::remember('landing_latest_products', 900, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('latest_products'))->item_limit ?? 8;

            return Product::active()
                ->with(['category'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->latest('id')
                ->take($limit)
                ->get()
                ->map(function ($p) {
                    $p->reviews_count = $p->reviews_count ?? 0;
                    $p->reviews_avg_rating = $p->reviews_avg_rating ?? ($p->reviews_avg_rating ?? 0);

                    return $p;
                });
        });

        $flashSaleProducts = Cache::remember('landing_flash_products', 300, function () use ($sectionsIndex) {
            $now = now();
            $limit = optional($sectionsIndex->get('flash_sale'))->item_limit ?? 8;

            return Product::active()
                ->whereNotNull('sale_price')
                ->whereColumn('sale_price', '<', 'price')
                ->where(function ($q) use ($now): void {
                    $q->whereNull('sale_start')->orWhere('sale_start', '<=', $now);
                })
                ->where(function ($q) use ($now): void {
                    $q->whereNull('sale_end')->orWhere('sale_end', '>=', $now);
                })
                ->with('category')
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByRaw('(price - sale_price)/price DESC')
                ->take($limit)
                ->get()
                ->map(function ($p) {
                    $p->reviews_count = $p->reviews_count ?? 0;
                    $p->reviews_avg_rating = $p->reviews_avg_rating ?? ($p->reviews_avg_rating ?? 0);

                    return $p;
                });
        });

        $flashSaleEndsAt = null;
        if ($flashSaleProducts->count()) {
            $futureEnds = $flashSaleProducts->filter(function ($p) {
                return $p->sale_end && $p->sale_end->isFuture();
            })->pluck('sale_end');
            if ($futureEnds->count()) {
                $flashSaleEndsAt = $futureEnds->min();
            }
        }

        return compact('latestProducts', 'flashSaleProducts', 'flashSaleEndsAt');
    }

    private function buildPostsData($sectionsIndex)
    {
        return Cache::remember('home_latest_posts', 900, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('blog_posts'))->item_limit ?? 3;

            return Post::where('published', true)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->take($limit)
                ->get();
        });
    }

    private function buildSlidesAndBannersData(): array
    {
        $slides = Cache::remember('homepage_slides_enabled', 600, function () {
            return HomepageSlide::where('enabled', true)->orderBy('sort_order')->get();
        })->map(function ($sl) {
            $loc = app()->getLocale();
            if (is_array($sl->title_i18n) && isset($sl->title_i18n[$loc])) {
                $sl->title = $sl->title_i18n[$loc];
            }
            if (is_array($sl->subtitle_i18n) && isset($sl->subtitle_i18n[$loc])) {
                $sl->subtitle = $sl->subtitle_i18n[$loc];
            }
            if (is_array($sl->button_text_i18n) && isset($sl->button_text_i18n[$loc])) {
                $sl->button_text = $sl->button_text_i18n[$loc];
            }

            return $sl;
        });

        $bannersCollection = Cache::remember('homepage_banners_enabled', 600, function () {
            return HomepageBanner::where('enabled', true)->orderBy('sort_order')->get();
        })->map(function ($bn) {
            $loc = app()->getLocale();
            if (is_array($bn->alt_text_i18n) && isset($bn->alt_text_i18n[$loc])) {
                $bn->alt_text = $bn->alt_text_i18n[$loc];
            }

            return $bn;
        });
        $banners = $bannersCollection->groupBy(function ($b) {
            return $b->placement_key ? $b->placement_key : 'default';
        });

        return compact('slides', 'banners');
    }

    private function buildShowcaseSections($sectionsIndex, $sectionTitles): array
    {
        $showcaseSections = collect();
        foreach (
            [
                'showcase_latest',
                'showcase_best_selling',
                'showcase_discount',
                'showcase_most_rated',
                'showcase_brands',
            ] as $sk
        ) {
            $secCfg = $sectionsIndex->get($sk);
            if (! $secCfg || ! $secCfg->enabled) {
                continue;
            }
            $limit = $secCfg->item_limit ?? 4;
            $cacheKey = 'showcase_' . $sk . '_data_v1';
            $items = Cache::remember($cacheKey, 600, function () use ($sk, $limit) {
                return match ($sk) {
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
                };
            });
            if (! $items->count()) {
                continue;
            }
            $type = $sk === 'showcase_brands' ? 'brands' : 'products';
            if ($type === 'products') {
                $items = $this->mapProductItems($items, $sk);
            }
            $showcaseSections->push([
                'key' => $sk,
                'title' => $sectionTitles[$sk]['title'] ?? ucfirst(str_replace('_', ' ', $sk)),
                'items' => $items,
                'type' => $type,
            ]);
        }
        $showcaseSections = $showcaseSections->sortBy(
            fn($s) => optional($sectionsIndex->get($s['key']))->sort_order ?? 9999
        )->values();
        $brandSec = $showcaseSections->firstWhere('type', 'brands');
        $showcaseSectionsActiveCount = $showcaseSections->where('type', '!=', 'brands')->count();

        return compact('showcaseSections', 'brandSec', 'showcaseSectionsActiveCount');
    }

    private function mapProductItems($items, $sk)
    {
        return $items->map(function ($p) use ($sk) {
            $image = $p->main_image
                ? asset('storage/' . $p->main_image)
                : asset('images/placeholder.svg');
            $p->mini_image_url = $image;
            $p->mini_image_is_placeholder = ! $p->main_image;
            $name = $p->name;
            if (strlen($name) > 40) {
                $name = mb_substr($name, 0, 40) . '…';
            }
            $p->mini_trunc_name = $name;
            $priceHtml = GlobalHelper::currencyFormat($p->effectivePrice());
            $p->mini_price_html = $priceHtml;
            $extra = '';
            if ($sk === 'showcase_discount' && $p->effectivePrice() < $p->price) {
                $extra .= '<span class="mini-old">' .
                    e(GlobalHelper::currencyFormat($p->price)) . '</span>';
            }
            if ($sk === 'showcase_most_rated' && $p->approved_reviews_avg) {
                $extra .= '<span class="mini-rating">★ ' .
                    number_format((float) $p->approved_reviews_avg, 1) . '</span>';
            }
            $p->mini_extra_html = $extra;
            $flags = [];
            if ($sk === 'showcase_discount') {
                $flags[] = 'on-sale';
            } elseif ($sk === 'showcase_most_rated') {
                $flags[] = 'rated';
            }
            $p->mini_flags = implode(' ', $flags);

            return $p;
        });
    }
}

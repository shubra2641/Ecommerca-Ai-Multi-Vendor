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

        // Homepage configurable sections
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
                    ($fallbackTitle ?: ucfirst(str_replace('_', ' ', $sec->key))));
            $computedSub = $subArr[$locale] ?? ($subArr[$defaultLocale] ?? ($fallbackSub ?: ''));
            $computedCta = $ctaArr[$locale] ?? ($ctaArr[$defaultLocale] ?? ($fallbackCta ?: null));
            $sectionTitles[$sec->key] = ['title' => $computedTitle, 'subtitle' => $computedSub];
            $sectionMeta[$sec->key] = [
                'cta_enabled' => $sec->cta_enabled,
                'cta_url' => $sec->cta_url,
                'cta_label' => $computedCta,
            ];
        }
        $sectionLimit = function (string $key, int $fallback) {
            $sec = $thisSection = $GLOBALS['__home_sections_cache'] ?? null; // placeholder not used

            return optional($GLOBALS['__sections_index'][$key] ?? null)->item_limit ?? $fallback;
        };
        // index sections by key for quick lookup for limits
        $sectionsIndex = $sections->keyBy('key');
        $GLOBALS['__sections_index'] = $sectionsIndex; // used inside closure above if needed

        // Cache categories for better performance (legacy var name) limited by section config if exists
        $categories = Cache::remember('home_categories', 1800, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('categories'))->item_limit ?? 6;

            return ProductCategory::whereNull('parent_id')
                ->where('active', true)
                ->orderBy('position')
                ->take($limit)
                ->get();
        });

        // Landing main categories (12) for circular list
        $landingMainCategories = Cache::remember('landing_main_categories', 1800, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('categories'))->item_limit ?? 12; // reuse same limit concept

            return ProductCategory::whereNull('parent_id')
                ->where('active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->take($limit)
                ->get();
        });

        // Latest products (8) with relations & aggregates
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

        // Flash sale (discounted) products: assumption -> active discount window
        // using sale_start/end & sale_price < price
        // If a dedicated flash sale flag/relationship exists later, adjust this query accordingly.
        $flashSaleProducts = Cache::remember('landing_flash_products', 300, function () use ($sectionsIndex) {
            $now = now();
            $limit = optional($sectionsIndex->get('flash_sale'))->item_limit ?? 8;

            return Product::active()
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
                ->orderByRaw('(price - sale_price)/price DESC') // highest discount first
                ->take($limit)
                ->get()
                ->map(function ($p) {
                    $p->reviews_count = $p->reviews_count ?? 0;
                    $p->reviews_avg_rating = $p->reviews_avg_rating ?? ($p->reviews_avg_rating ?? 0);

                    return $p;
                });
        });

        // Determine earliest sale_end among flash sale products for countdown (ignore null / past)
        $flashSaleEndsAt = null;
        if ($flashSaleProducts->count()) {
            $futureEnds = $flashSaleProducts->filter(function ($p) {
                return $p->sale_end && $p->sale_end->isFuture();
            })->pluck('sale_end');
            if ($futureEnds->count()) {
                $flashSaleEndsAt = $futureEnds->min();
            }
        }

        $categoryImagePlaceholder = asset('images/product-placeholder.svg');

        // Cache latest posts for better performance
        $latestPosts = Cache::remember('home_latest_posts', 900, function () use ($sectionsIndex) {
            $limit = optional($sectionsIndex->get('blog_posts'))->item_limit ?? 3;

            return Post::where('published', true)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->take($limit)
                ->get();
        });

        // Slides & Banners
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
            return $b->placement_key ?: 'default';
        });

        // Showcase mini sections aggregate (no logic left in Blade)
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
                        ->withCount(['products' => fn ($q) => $q->active()])
                        ->orderByDesc('products_count')
                        ->take($limit)->get(),
                    default => collect(),
                };
            });
            if (! $items->count()) {
                continue;
            }
            $type = $sk === 'showcase_brands' ? 'brands' : 'products';
            // For product sections, map presentation attributes to avoid Blade logic
            if ($type === 'products') {
                $items = $items->map(function ($p) use ($sk) {
                    try {
                        $image = $p->main_image
                            ? asset('storage/' . $p->main_image)
                            : asset('images/placeholder.svg');
                    } catch (\Throwable $e) {
                        $image = asset('images/placeholder.svg');
                    }
                    $p->mini_image_url = $image;
                    $p->mini_image_is_placeholder = empty($p->main_image);
                    $name = $p->name;
                    if (strlen($name) > 40) {
                        $name = mb_substr($name, 0, 40) . '…';
                    }
                    $p->mini_trunc_name = $name;
                    // price html
                    try {
                        $priceHtml = currency_format($p->effectivePrice());
                    } catch (\Throwable $e) {
                        $priceHtml = number_format($p->price, 2);
                    }
                    $p->mini_price_html = $priceHtml;
                    $extra = '';
                    if ($sk === 'showcase_discount' && $p->effectivePrice() < $p->price) {
                        try {
                            $extra .= '<span class="mini-old">' .
                                e(currency_format($p->price)) . '</span>';
                        } catch (\Throwable $e) {
                        }
                    }
                    if ($sk === 'showcase_most_rated' && $p->approved_reviews_avg) {
                        $extra .= '<span class="mini-rating">★ ' .
                            number_format($p->approved_reviews_avg, 1) . '</span>';
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
            $showcaseSections->push([
                'key' => $sk,
                'title' => $sectionTitles[$sk]['title'] ?? ucfirst(str_replace('_', ' ', $sk)),
                'items' => $items,
                'type' => $type,
            ]);
        }
        $showcaseSections = $showcaseSections->sortBy(
            fn ($s) => optional($sectionsIndex->get($s['key']))->sort_order ?? 9999
        )->values();
        // Extract brand section separately & compute grid column count excluding brands
        $brandSec = $showcaseSections->firstWhere('type', 'brands');
        $showcaseSectionsActiveCount = $showcaseSections->where('type', '!=', 'brands')->count();

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
}

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ShippingZone;
use App\Models\VendorWithdrawal;
use App\Observers\ProductObserver;
use App\Observers\ProductReviewObserver;
use App\Policies\ShippingZonePolicy;
use App\Policies\VendorWithdrawalPolicy;
use App\View\Composers\HeaderComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('access-vendor', function ($user) {
            return $user->role === 'vendor';
        });

        Gate::define('access-user', function ($user) {
            return $user->role === 'user';
        });

        // Model policies
        Gate::policy(ShippingZone::class, ShippingZonePolicy::class);
        // Product policy (vendor ownership checks)
        Gate::policy(\App\Models\Product::class, \App\Policies\ProductPolicy::class);
        // Vendor withdrawal ownership/visibility
        Gate::policy(VendorWithdrawal::class, VendorWithdrawalPolicy::class);

        // Shared active languages (for switchers in multiple layouts)
        View::composer(
            ['components.language-switcher', 'vendor.partials.vendor-top', 'layouts.guest'],
            function ($view): void {
                $languages = \Illuminate\Support\Facades\Cache::remember(
                    'languages_all',
                    3600,
                    function () {
                        try {
                            return Language::where('is_active', 1)
                                ->orderByDesc('is_default')
                                ->orderBy('name')
                                ->get();
                        } catch (\Throwable $e) {
                            return collect();
                        }
                    }
                );
                $view->with('languages', $languages);
            }
        );

        // Header specific heavy data
        View::composer('front.partials.header', HeaderComposer::class);
        View::composer('front.account._sidebar', \App\View\Composers\AccountSidebarComposer::class);
        // Product card composer (removes inline @php logic from partial)
        View::composer('front.products.partials.product-card', \App\View\Composers\ProductCardComposer::class);
        View::composer('front.products.partials.sidebar', \App\View\Composers\CatalogSidebarComposer::class);
        View::composer('front.products.partials.reviews', \App\View\Composers\ReviewsComposer::class);
        View::composer(['front.account.orders', 'front.account.order_show'], \App\View\Composers\OrdersComposer::class);
        View::composer('front.checkout.index', \App\View\Composers\CheckoutComposer::class);
        View::composer('front.cart.index', \App\View\Composers\CartComposer::class);
        View::composer('front.account.addresses', \App\View\Composers\AddressesComposer::class);

        // Admin specific composers (removing inline @php from admin views)
        View::composer('admin.orders.show', \App\View\Composers\AdminOrderComposer::class);
        View::composer(
            'admin.products.products.index',
            \App\View\Composers\AdminProductsIndexComposer::class
        );
        View::composer('admin.products.categories.index', \App\View\Composers\AdminCategoriesIndexComposer::class);
        View::composer('admin.products.products._form', \App\View\Composers\AdminProductFormComposer::class);
        View::composer('admin.shipping.index', \App\View\Composers\AdminShippingComposer::class);
        View::composer('admin.reports.system', \App\View\Composers\AdminSystemReportComposer::class);
        View::composer('admin.products.products.show', \App\View\Composers\AdminProductShowComposer::class);
        View::composer('admin.users.form', \App\View\Composers\AdminUsersFormComposer::class);
        View::composer('admin.social.form', \App\View\Composers\AdminSocialFormComposer::class);
        View::composer('admin.profile.settings', \App\View\Composers\AdminProfileSettingsComposer::class);
        View::composer(
            'admin.products.products._script',
            \App\View\Composers\AdminProductVariationsDataComposer::class
        );
        View::composer('admin.notify.top-products', \App\View\Composers\AdminNotifyTopProductsComposer::class);
        View::composer('admin.orders.index', \App\View\Composers\AdminOrdersIndexComposer::class);
        View::composer('admin.gallery.index', \App\View\Composers\AdminGalleryIndexComposer::class);
        // Theme tokens (available to all admin + front views for future theming / docs examples)
        View::composer(['front.*', 'admin.*'], function ($view): void {
            static $theme = null;
            if ($theme === null) {
                $theme = config('theme');
            }
            $view->with('themeTokens', $theme);
        });
        View::composer('admin.footer.settings', \App\View\Composers\AdminFooterSettingsComposer::class);
        View::composer('admin.dashboard-admin', \App\View\Composers\AdminDashboardAdminComposer::class);
        View::composer('admin.top-header', \App\View\Composers\AdminDashboardAdminComposer::class);
        View::composer('admin.blog.posts.create', \App\View\Composers\AdminBlogPostCreateComposer::class);
        View::composer('admin.blog.posts.edit', \App\View\Composers\AdminBlogPostEditComposer::class);

        // Footer extended composer (extracts footer_extended inline logic)
        View::composer('front.partials.footer_extended', \App\View\Composers\FooterComposer::class);

        // Vendor area composers (products index specific)
        View::composer('vendor.layout', \App\View\Composers\SiteBrandingComposer::class);
        View::composer('vendor.partials.vendor-top', function ($view): void {
            $flash = [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
                'info' => session('info'),
            ];
            // avoid very long chained call on a single line
            $view->with('flashPayload', $flash);
            $view->with(
                'notificationsPollIntervalMs',
                (int) config('notifications.poll_interval_ms', 30000)
            );
        });
        View::composer('vendor.products.index', \App\View\Composers\VendorProductsIndexComposer::class);

        // Error role specific composer (removes inline @php for dashboard resolution)
        View::composer('errors.404-role', \App\View\Composers\ErrorRoleComposer::class);
        // Site branding (settings/font/logo) for multiple layouts & logo component
        View::composer(
            ['components.application-logo', 'layouts.admin', 'layouts.guest'],
            \App\View\Composers\SiteBrandingComposer::class
        );

        // Provide site setting & selected font globally for front views (remove inline @php in layout)
        View::composer(['front.*'], function ($view): void {
            static $cached = null;
            if ($cached === null) {
                try {
                    $cached = [
                        'setting' => \Illuminate\Support\Facades\Cache::remember(
                            'site_settings',
                            3600,
                            fn() => \App\Models\Setting::first()
                        ),
                    ];
                } catch (\Throwable $e) {
                    $cached = ['setting' => null];
                }
                $font = null;
                try {
                    $font = cache()->get(
                        'settings.font_family',
                        $cached['setting']->font_family ?? 'Inter'
                    );
                } catch (\Throwable $e) {
                    $font = 'Inter';
                }
                $cached['selectedFont'] = $font;
            }
            $view->with('setting', $cached['setting']);
            $view->with('selectedFont', $cached['selectedFont']);
        });

        // Provide selected font for guest layout
        View::composer(['layouts.guest'], function ($view): void {
            static $cached = null;
            if ($cached === null) {
                try {
                    $setting = \Illuminate\Support\Facades\Cache::remember(
                        'site_settings',
                        3600,
                        fn() => \App\Models\Setting::first()
                    );
                    $font = cache()->get(
                        'settings.font_family',
                        $setting->font_family ?? 'Inter'
                    );
                } catch (\Throwable $e) {
                    $font = 'Inter';
                }
                $cached = $font;
            }
            $view->with('selectedFont', $cached);
        });

        // Share default/current currency lightweight for all views.
        // HeaderComposer may override currentCurrency/currency_symbol.
        View::composer('*', function ($view): void {
            static $cache = null;
            if ($cache === null) {
                $cache = ['default' => null, 'symbol' => '$'];
                try {
                    $cache['default'] = \App\Helpers\GlobalHelper::getCurrencyContext()['defaultCurrency'];
                } catch (\Throwable $e) {
                    logger()->warning('Failed to get default currency: ' . $e->getMessage());
                }
                try {
                    $cache['symbol'] = Currency::defaultSymbol() ?? $cache['symbol'];
                } catch (\Throwable $e) {
                    logger()->warning('Failed to get currency symbol: ' . $e->getMessage());
                }
            }
            $symbol = $cache['symbol'];
            if (session()->has('currency_id')) {
                static $sessionCurrency = null;
                static $sessionCurrencyId = null;
                $cid = session('currency_id');
                if ($cid && $cid !== $sessionCurrencyId) {
                    try {
                        $sessionCurrency = Currency::find($cid);
                        $sessionCurrencyId = $cid;
                    } catch (\Throwable $e) {
                        $sessionCurrency = null;
                    }
                }
                if ($sessionCurrency) {
                    $symbol = $sessionCurrency->symbol ?? $symbol;
                }
            }
            $view->with('defaultCurrency', $cache['default']);
            $view->with('currency_symbol', $symbol);
        });

        // Provide pending returns count to admin navigation via view composer
        View::composer('layouts.navigation', function ($view): void {
            $pending = \Illuminate\Support\Facades\Cache::remember(
                'pending_returns_count',
                300,
                function () {
                    try {
                        return \App\Models\OrderItem::where('return_requested', true)->count();
                    } catch (\Throwable $e) {
                        return 0;
                    }
                }
            );

            $pendingVendorProducts = \Illuminate\Support\Facades\Cache::remember(
                'pending_vendor_products_count',
                300,
                function () {
                    try {
                        return \App\Models\Product::whereNotNull('vendor_id')->where('active', false)->count();
                    } catch (\Throwable $e) {
                        return 0;
                    }
                }
            );

            $view->with('pendingReturnsCount', $pending)
                ->with('pendingVendorProductsCount', $pendingVendorProducts);
        });

        // Observe product changes (stock refills) for notification triggers
        Product::observe(ProductObserver::class);
        // Observe product reviews for denormalized aggregates maintenance
        ProductReview::observe(ProductReviewObserver::class);

        // Register a convenient alias for role middleware if router is available
        try {
            if ($this->app->bound('router')) {
                $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);
            }
        } catch (\Throwable $e) {
            // ignore if router not yet available during certain artisan commands
            null;
        }

        // Blade directive for rendering stored sanitized HTML safely.
        // Usage: @clean($html) — will echo sanitized HTML string.
        Blade::directive('clean', function ($expression) {
            // expression is the variable to clean
            return "<?php echo app('App\\\\Services\\\\HtmlSanitizer')->clean({$expression}); ?>";
        });
    }
}

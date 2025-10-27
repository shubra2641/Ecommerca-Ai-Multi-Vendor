<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FooterSettingsController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\HomepageBannerController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\HomepageSlideController;
use App\Http\Controllers\Admin\InterestReportController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaintenanceSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\NotifyController as AdminNotifyController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductApprovalController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\ProductSerialController;
use App\Http\Controllers\Admin\ProductTagController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ReturnsController;
use App\Http\Controllers\Admin\SendNotificationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorWithdrawalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth',
    'role:admin',
    'can:access-admin',
    \App\Http\Middleware\EnsureEmailActivated::class,
])->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Reports routes
    Route::prefix('reports')->name('admin.reports.')->group(function (): void {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/vendors', [ReportsController::class, 'vendorsReport'])->name('vendors');
        Route::get('/financial', [ReportsController::class, 'financialReport'])->name('financial');
        Route::get('/system', [ReportsController::class, 'systemReport'])->name('system');
        Route::get('/inventory', [ReportsController::class, 'inventoryReport'])->name('inventory');
    });

    // Notify interests management
    Route::prefix('notify')->name('admin.notify.')->group(function (): void {
        Route::get('/', [AdminNotifyController::class, 'index'])->name('index');
        Route::put('/{interest}/mark', [AdminNotifyController::class, 'markNotified'])
            ->name('mark');
        Route::delete('/{interest}', [AdminNotifyController::class, 'destroy'])
            ->name('delete');
    });
    // Interest reports
    Route::get('notify/top-products', [InterestReportController::class, 'topProducts'])
        ->name('admin.notify.topProducts');
    Route::get('notify/{product}/price-chart', [InterestReportController::class, 'priceChart'])
        ->name('admin.notify.priceChart');

    // Quick admin notifications
    // API endpoint used by header widget
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])
        ->name('admin.notifications.latest');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('admin.notifications.unreadCount');
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])
        ->name('admin.notifications.latest');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('admin.notifications.unreadCount');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])
        ->name('admin.notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAll'])
        ->name('admin.notifications.markAll');
    // Index page to view all notifications (header links to this)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    // Send notifications (admin)
    Route::get('/notifications/send', [SendNotificationController::class, 'create'])
        ->name('admin.notifications.send.create');
    Route::post('/notifications/send', [SendNotificationController::class, 'store'])
        ->name('admin.notifications.send.store');

    // Profile management routes
    Route::prefix('profile')->name('admin.profile.')->group(function (): void {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // Users management routes
    Route::prefix('users')->name('admin.users.')->group(function (): void {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/pending', [UserController::class, 'pending'])->name('pending');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/approve', [UserController::class, 'approve'])->name('approve');
        Route::get('/{user}/balance', [UserController::class, 'balance'])->name('balance');
        Route::post('/{user}/add-balance', [UserController::class, 'addBalance'])
            ->middleware('throttle:10,1')
            ->name('add-balance');
        Route::post('/{user}/deduct-balance', [UserController::class, 'deductBalance'])
            ->middleware('throttle:10,1')
            ->name('deduct-balance');
        Route::get('/{user}/balance/stats', [UserController::class, 'getBalanceStats'])->name('balance.stats');
        Route::get('/{user}/balance/history', [UserController::class, 'getBalanceHistory'])->name('balance.history');
    });

    // Languages management
    Route::prefix('languages')->name('admin.languages.')->group(function (): void {
        Route::get('/', [AdminLanguageController::class, 'index'])->name('index');
        Route::get('/create', [AdminLanguageController::class, 'create'])->name('create');
        Route::post('/', [AdminLanguageController::class, 'store'])->name('store');
        Route::get('/{language}/edit', [AdminLanguageController::class, 'edit'])->name('edit');
        Route::put('/{language}', [AdminLanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [AdminLanguageController::class, 'destroy'])->name('destroy');
        Route::patch('/{language}/activate', [AdminLanguageController::class, 'activate'])->name('activate');
        Route::patch('/{language}/deactivate', [AdminLanguageController::class, 'deactivate'])->name('deactivate');
        Route::get('/{language}/translations', [AdminLanguageController::class, 'translations'])->name('translations');
        Route::put(
            '/{language}/translations',
            [AdminLanguageController::class, 'updateTranslations']
        )->name('translations.update');
        Route::post(
            '/{language}/translations/add',
            [AdminLanguageController::class, 'addTranslation']
        )->name('translations.add');
        Route::delete('/{language}/translations/delete', [AdminLanguageController::class, 'deleteTranslation'])
            ->name('translations.delete');
        Route::post('/{language}/set-default', [AdminLanguageController::class, 'setDefault'])->name('set-default');
    });

    // Currencies management
    Route::prefix('currencies')->name('admin.currencies.')->group(function (): void {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::get('/{currency}', [CurrencyController::class, 'show'])->name('show');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
        Route::post('/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('set-default');
        Route::post('/{currency}/toggle-status', [CurrencyController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-activate', [CurrencyController::class, 'bulkActivate'])->name('bulk-activate');
        Route::post('/bulk-deactivate', [CurrencyController::class, 'bulkDeactivate'])->name('bulk-deactivate');
        Route::delete('/bulk-delete', [CurrencyController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Social links management
    Route::prefix('social-links')->name('admin.social.')->group(function (): void {
        Route::get('/', [SocialLinkController::class, 'index'])->name('index');
        Route::get('/create', [SocialLinkController::class, 'create'])->name('create');
        Route::post('/', [SocialLinkController::class, 'store'])->name('store');
        Route::get('/{social}/edit', [SocialLinkController::class, 'edit'])->name('edit');
        Route::put('/{social}', [SocialLinkController::class, 'update'])->name('update');
        Route::delete('/{social}', [SocialLinkController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [SocialLinkController::class, 'reorder'])->name('reorder');
    });

    // Gallery management routes
    Route::prefix('gallery')->name('admin.gallery.')->group(function (): void {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/upload', [GalleryController::class, 'create'])->name('create');
        Route::post('/', [GalleryController::class, 'store'])->name('store');
        Route::post('/quick-upload', [GalleryController::class, 'quickStore'])->name('quick-upload');
        Route::get('/{image}/edit', [GalleryController::class, 'edit'])->name('edit');
        Route::put('/{image}', [GalleryController::class, 'update'])->name('update');
        Route::delete('/{image}', [GalleryController::class, 'destroy'])->name('destroy');
        Route::post('/{image}/use-as-logo', [GalleryController::class, 'useAsLogo'])
            ->name('use-as-logo');
    });

    // Blog management routes
    Route::prefix('blog')->name('admin.blog.')->group(function (): void {
        // Posts
        Route::prefix('posts')->name('posts.')->group(function (): void {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
            Route::post('/{post}/publish', [PostController::class, 'publishToggle'])->name('publish');
            // AI assist for post content (title/excerpt/body/seo)
            Route::match(['get', 'post'], '/ai/suggest', [PostController::class, 'aiSuggest'])
                ->name('ai.suggest');
        });
        // Categories
        Route::prefix('categories')->name('categories.')->group(function (): void {
            Route::get('/', [PostCategoryController::class, 'index'])->name('index');
            Route::get('/create', [PostCategoryController::class, 'create'])->name('create');
            Route::post('/', [PostCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [PostCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [PostCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [PostCategoryController::class, 'destroy'])->name('destroy');
            // AI assist for blog category description & SEO
            Route::match(['get', 'post'], '/ai/suggest', [PostCategoryController::class, 'aiSuggest'])
                ->name('ai.suggest');
        });

        // AI Suggestions
        Route::prefix('ai')->name('ai.')->group(function (): void {
            Route::post('/blog-post', [\App\Http\Controllers\AI\SuggestionController::class, 'blogPost'])
                ->name('blog-post');
            Route::post('/product', [\App\Http\Controllers\AI\SuggestionController::class, 'product'])
                ->name('product');
            Route::post('/category', [\App\Http\Controllers\AI\SuggestionController::class, 'category'])
                ->name('category');
        });
        // Tags
        Route::prefix('tags')->name('tags.')->group(function (): void {
            Route::get('/', [TagController::class, 'index'])->name('index');
            Route::post('/', [TagController::class, 'store'])->name('store');
            Route::put('/{tag}', [TagController::class, 'update'])->name('update');
            Route::delete('/{tag}', [TagController::class, 'destroy'])->name('destroy');
        });
    });

    // Dashboard AJAX routes
    Route::prefix('dashboard')->name('admin.dashboard.')->group(function (): void {
        Route::get('/chart-data', [AdminDashboardController::class, 'getChartData'])->name('chart-data');
        Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
    });

    // Settings routes
    Route::prefix('settings')->name('admin.settings.')->group(function (): void {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });

    // Footer settings routes
    Route::prefix('footer-settings')->name('admin.footer-settings.')->group(function (): void {
        Route::get('/', [FooterSettingsController::class, 'edit'])->name('edit');
        Route::put('/', [FooterSettingsController::class, 'update'])->name('update');
    });

    // Maintenance settings routes
    Route::prefix('maintenance-settings')->name('admin.maintenance-settings.')->group(function (): void {
        Route::get('/', [MaintenanceSettingsController::class, 'edit'])->name('edit');
        Route::put('/', [MaintenanceSettingsController::class, 'update'])->name('update');
        Route::get('/preview', [MaintenanceSettingsController::class, 'preview'])->name('preview');
    });
    Route::prefix('homepage')->name('admin.homepage.')->group(function (): void {
        Route::get('sections', [HomepageSectionController::class, 'index'])->name('sections.index');
        Route::post('sections', [HomepageSectionController::class, 'updateBulk'])->name('sections.update');
        Route::get('slides', [HomepageSlideController::class, 'index'])->name('slides.index');
        Route::post('slides', [HomepageSlideController::class, 'store'])->name('slides.store');
        Route::put('slides/{slide}', [HomepageSlideController::class, 'update'])->name('slides.update');
        Route::delete('slides/{slide}', [HomepageSlideController::class, 'destroy'])->name('slides.destroy');
        Route::get('banners', [HomepageBannerController::class, 'index'])->name('banners.index');
        Route::post('banners', [HomepageBannerController::class, 'store'])->name('banners.store');
        Route::put('banners/{banner}', [HomepageBannerController::class, 'update'])->name('banners.update');
        Route::delete('banners/{banner}', [HomepageBannerController::class, 'destroy'])->name('banners.destroy');
    });

    // Cache management routes
    Route::prefix('cache')->name('admin.cache.')->group(function (): void {
        Route::post('/clear', [AdminDashboardController::class, 'clearCache'])->name('clear');
    });

    // System management routes
    Route::prefix('system')->name('admin.')->group(function (): void {
        Route::post('/logs/clear', [AdminDashboardController::class, 'clearLogs'])->name('logs.clear');
        Route::post('/optimize', [AdminDashboardController::class, 'optimize'])->name('optimize');
    });

    // Language routes additions
    Route::prefix('languages')->name('admin.languages.')->group(function (): void {
        Route::post('/bulk-activate', [AdminLanguageController::class, 'bulkActivate'])->name('bulk-activate');
        Route::post('/bulk-deactivate', [AdminLanguageController::class, 'bulkDeactivate'])->name('bulk-deactivate');
        Route::delete('/bulk-delete', [AdminLanguageController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // User routes additions
    Route::prefix('users')->name('admin.users.')->group(function (): void {
        Route::post('/bulk-approve', [UserController::class, 'bulkApprove'])->name('bulk-approve');
        Route::delete('/bulk-delete', [UserController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Payment Gateways (rebuilt system)
    Route::resource('payment-gateways', PaymentGatewayController::class, [
        'parameters' => ['payment-gateways' => 'paymentGateway'],
    ])->names('admin.payment-gateways');
    Route::post('payment-gateways/{paymentGateway}/toggle', [PaymentGatewayController::class, 'toggle'])
        ->name('admin.payment-gateways.toggle');
    Route::post('payment-gateways/{paymentGateway}/test-webhook', [PaymentGatewayController::class, 'testWebhook'])
        ->name('admin.payment-gateways.test-webhook');

    // Coupons management (moved here so path is /admin/coupons)
    Route::prefix('coupons')->name('admin.coupons.')->group(function (): void {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
    });

    // Shipping Zones (new system)
    Route::resource('shipping-zones', ShippingZoneController::class)->names('admin.shipping-zones');
    // Vendor withdrawals management
    Route::prefix('vendor-withdrawals')->name('admin.vendor.withdrawals.')->group(function (): void {
        Route::get('/', [VendorWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [VendorWithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/approve', [VendorWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{withdrawal}/reject', [VendorWithdrawalController::class, 'reject'])->name('reject');
        // payouts execution
        Route::post('/payouts/{payout}/execute', [VendorWithdrawalController::class, 'execute'])
            ->name('payouts.execute');
    });
    Route::get('payouts', [VendorWithdrawalController::class, 'payoutsIndex'])
        ->name('admin.vendor.payouts.index');
    Route::get('payouts/{payout}', [VendorWithdrawalController::class, 'payoutsShow'])
        ->name('admin.vendor.payouts.show');
    // Locations management
    Route::resource('countries', CountryController::class)->except(['show'])->names('admin.countries');
    Route::resource('governorates', GovernorateController::class)->except(['show'])->names('admin.governorates');
    Route::resource('cities', CityController::class)->except(['show'])->names('admin.cities');

    // Location API endpoints for frontend
    Route::get('products/locations/governorates', [LocationController::class, 'governorates']);
    Route::get('products/locations/cities', [LocationController::class, 'cities']);
});

// Language switching (support both GET and POST for backward compatibility)
Route::match(['get', 'post'], '/vendor/language', [\App\Http\Controllers\LanguageController::class, 'switch'])
    ->name('admin.language.switch');

// Admin logout
Route::post('/admin/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();

    return redirect()->route('admin.login');
})->name('admin.logout')->middleware(['auth', 'role:admin', 'can:access-admin']);

// Product admin routes

Route::middleware(['web', 'auth', 'role:admin', 'can:access-admin'])
    ->prefix('products')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('product-categories', ProductCategoryController::class);
        // AI assist endpoint for product categories (generate description + SEO)
        Route::match(['get', 'post', 'put'], 'product-categories/ai/suggest', [ProductCategoryController::class, 'aiSuggest'])
            ->name('product-categories.ai.suggest');
        Route::resource('product-tags', ProductTagController::class)->except(['show']);
        Route::resource('product-attributes', ProductAttributeController::class);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::post(
            'product-attributes/{productAttribute}/values',
            [ProductAttributeController::class, 'storeValue']
        )->name('product-attributes.values.store');
        Route::put(
            'product-attributes/{productAttribute}/values/{value}',
            [ProductAttributeController::class, 'updateValue']
        )->name('product-attributes.values.update');
        Route::delete(
            'product-attributes/{productAttribute}/values/{value}',
            [ProductAttributeController::class, 'deleteValue']
        )->name('product-attributes.values.destroy');
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
        Route::match(['get', 'post'], 'ai/suggest', [ProductController::class, 'aiSuggest'])
            ->name('products.ai.suggest');
        // vendor product approvals (path: /admin/products/pending-review)
        Route::get('pending-review', [ProductApprovalController::class, 'pending'])->name('products.pending');
        Route::post('products/{product}/approve', [ProductApprovalController::class, 'approve'])
            ->name('products.approve');
        Route::post('products/{product}/reject', [ProductApprovalController::class, 'reject'])
            ->name('products.reject');
        // Product serials management

        Route::get('serials', [ProductSerialController::class, 'allSerials'])->name('products.serials.all');
        Route::get('products/{product}/serials', [ProductSerialController::class, 'index'])
            ->name('products.serials.index');

        Route::post('products/{product}/serials/import', [ProductSerialController::class, 'import'])
            ->name('products.serials.import');

        Route::post(
            'products/{product}/serials/{serial}/mark-sold',
            [ProductSerialController::class, 'markSold']
        )->name('products.serials.markSold');
        // product reviews moderation
        Route::get('reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{review}', [ProductReviewController::class, 'show'])->name('reviews.show');
        Route::post('reviews/{review}/approve', [ProductReviewController::class, 'approve'])
            ->name('reviews.approve');
        Route::post('reviews/{review}/unapprove', [ProductReviewController::class, 'unapprove'])
            ->name('reviews.unapprove');
        Route::delete('reviews/{review}', [ProductReviewController::class, 'destroy'])
            ->name('reviews.destroy');

        // Payment gateways removed

        // Orders admin
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');
        Route::get('payments', [OrderController::class, 'payments'])
            ->name('orders.payments');
        Route::post('payments/{payment}/accept', [OrderController::class, 'acceptPayment'])
            ->name('orders.payments.accept');
        Route::post('payments/{payment}/reject', [OrderController::class, 'rejectPayment'])
            ->name('orders.payments.reject');
        Route::post('orders/{order}/retry-assign', [OrderController::class, 'retryAssignSerials'])
            ->name('orders.retry-assign');
        Route::post(
            'orders/{order}/items/{item}/cancel-backorder',
            [OrderController::class, 'cancelBackorderItem']
        )->name('orders.cancelBackorderItem');
        // Returns management
        Route::get('returns', [ReturnsController::class, 'index'])->name('returns.index');
        Route::get('returns/{item}', [ReturnsController::class, 'show'])->name('returns.show');
        Route::post('returns/{item}', [ReturnsController::class, 'update'])
            ->name('returns.update');

        // Enhanced Notification Routes
        Route::prefix('notifications')->group(function (): void {
            Route::get('stats', [NotificationController::class, 'getStats'])->name('notifications.stats');
            Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::delete('{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
            Route::delete('clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
        });

        // Form Validation Routes
        Route::prefix('forms')->group(function (): void {
            Route::post('validate', [FormController::class, 'validateForm'])->name('forms.validate');
            Route::post('auto-save', [FormController::class, 'autoSave'])->name('forms.autoSave');
        });
    });

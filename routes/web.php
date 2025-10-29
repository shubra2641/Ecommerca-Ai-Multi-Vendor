<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Ajax\CurrencyController;
use App\Http\Controllers\Api\NewShippingController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\Files\ManifestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\OrderViewController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\ProductNotificationController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\User\AddressesController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\InvoicePdfController;
use App\Http\Controllers\User\InvoicesController;
use App\Http\Controllers\User\OrdersController;
use App\Http\Controllers\User\ReturnsController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Public routes wrapped in maintenance middleware. Admin routes and admin login are defined later
Route::middleware(\App\Http\Middleware\CheckMaintenanceMode::class)->group(function (): void {
    Route::post('/language', [LanguageController::class, 'switchLang'])->name('language.switch');
    Route::post('/notify/product', [NotifyController::class, 'store'])->name('notify.product');
    Route::get('/notify/check', [NotifyController::class, 'check'])->name('notify.check');
    Route::get('/notify/unsubscribe/{token}', [NotifyController::class, 'unsubscribe'])->name('notify.unsubscribe');

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::view('/offline', 'offline')->name('offline');
    // Fallback manifest route (serves file) if server misconfigured for root reference
    Route::get('/manifest.webmanifest', [ManifestController::class, 'show'])->name('manifest');

    Route::get('/api/shipping/options', [ShippingController::class, 'options']);
    Route::get('/api/new-shipping/quote', [NewShippingController::class, 'quote']);
    Route::post('/api/push/subscribe', [PushSubscriptionController::class, 'store'])
        ->name('push.subscribe');
    Route::post('/api/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])
        ->name('push.unsubscribe');

    // Public location endpoints for frontend checkout
    Route::get('/api/locations/governorates', [AdminLocationController::class, 'governorates']);
    Route::get('/api/locations/cities', [AdminLocationController::class, 'cities']);
    Route::get('/api/locations/shipping', [AdminLocationController::class, 'shipping']);

    // Blog front routes
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    Route::get('/shop', [ProductCatalogController::class, 'index'])->name('products.index');
    Route::get('/shop/category/{slug}', [ProductCatalogController::class, 'category'])
        ->name('products.category');
    Route::get('/shop/tag/{slug}', [ProductCatalogController::class, 'tag'])
        ->name('products.tag');
    Route::get('/product/{slug}', [ProductCatalogController::class, 'show'])->name('products.show');
    // Cart & Checkout (server rendered)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/move-to-wishlist', [CartController::class, 'moveToWishlist'])
        ->name('cart.moveToWishlist');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])
        ->name('cart.applyCoupon');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])
        ->name('cart.removeCoupon');
    Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CheckoutController::class, 'showForm'])
        ->name('checkout.form')
        ->middleware('auth');
    Route::post('/checkout/submit', [CheckoutController::class, 'submitForm'])
        ->name('checkout.submit')
        ->middleware('auth');

    // product reviews
    Route::post('/product/{product}/reviews', [ProductReviewController::class, 'store'])
        ->name('reviews.store')
        ->middleware('auth');

    // Checkout endpoints
    Route::post(
        '/checkout',
        [CheckoutController::class, 'create']
    )->name('checkout.create')->middleware('auth');

    // Wishlist & Compare (AJAX capable)
    Route::post(
        '/wishlist/toggle',
        [WishlistController::class, 'toggle']
    )->middleware('throttle:30,1')->name('wishlist.toggle');

    Route::post(
        '/compare/toggle',
        [CompareController::class, 'toggle']
    )->middleware('throttle:30,1')->name('compare.toggle');

    Route::post(
        '/product/notify',
        [ProductNotificationController::class, 'store']
    )->middleware('throttle:30,1')->name('product.notify');
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.page');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.page');

    // Currency switch endpoint (AJAX)
    Route::post('/currency/switch', [CurrencyController::class, 'switch'])->name('currency.switch');
});

Route::middleware('guest')->group(function (): void {
    Route::get('admin/login', [AdminAuthenticatedSessionController::class, 'create'])
        ->name('admin.login');

    Route::post('admin/login', [AdminAuthenticatedSessionController::class, 'store'])
        ->name('admin.login.store');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', function () {
        if (Gate::allows('access-admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (Gate::allows('access-vendor')) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('user.dashboard');
    })->name('dashboard');
});

// User account area
Route::middleware('auth')->prefix('account')->name('user.')->group(function (): void {
    Route::get('/', [UserDashboardController::class, 'index'])->name('home');
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
    // Returns
    Route::get('/returns', [ReturnsController::class, 'index'])->name('returns.index');
    Route::post('/returns/{item}/request', [ReturnsController::class, 'request'])->name('returns.request');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/invoices', [InvoicesController::class, 'index'])->name('invoices');
    Route::get('/addresses', [AddressesController::class, 'index'])->name('addresses');
    Route::post('/addresses', [AddressesController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressesController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressesController::class, 'destroy'])->name('addresses.destroy');
    Route::get('/addresses/{address}/edit', [AddressesController::class, 'edit'])->name('addresses.edit');
    Route::get('/orders/{order}/invoice.pdf', InvoicePdfController::class)->name('orders.invoice.pdf');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Frontend order view (view own order)
Route::middleware('auth')->get('/order/{order}', [OrderViewController::class, 'show'])->name('orders.show');

Route::middleware(['auth', 'can:access-user'])->prefix('user')->name('user.')->group(function (): void {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});
// auth routes
require __DIR__ . '/auth.php';

// Admin routes
Route::prefix('admin')->group(function (): void {
    require __DIR__ . '/admin.php';
});

// Installer routes (one-time installer)
require __DIR__ . '/install.php';

// Append admin test webhook route to admin routes file by editing admin.php

// Vendor routes
Route::prefix('vendor')->name('vendor.')->group(function (): void {
    require __DIR__ . '/vendor.php';
});


// Payment routes
Route::get('/payments/verify/{payment?}', [CheckoutController::class, 'verifyPayment'])->name('verify-payment');
Route::post('/payments/webhook', [CheckoutController::class, 'paymentWebhook'])->name('payment.webhook');

// Simulated payment redirect routes for local/testing (accept POST for gateways that POST JSON)

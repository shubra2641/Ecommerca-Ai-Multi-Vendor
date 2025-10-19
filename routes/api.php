<?php

use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\SystemController as ApiSystemController;
use App\Http\Controllers\Api\Vendor\AuthController as ApiVendorAuthController;
use App\Http\Controllers\Api\Vendor\DashboardController as ApiVendorDashboardController;
use App\Http\Controllers\Api\Vendor\LanguagesController as ApiVendorLanguagesController;
use App\Http\Controllers\Api\Vendor\NotificationsController as ApiVendorNotificationsController;
use App\Http\Controllers\Api\Vendor\OrdersController as ApiVendorOrdersController;
use App\Http\Controllers\Api\Vendor\ProductAttributesController as ApiVendorProductAttributesController;
use App\Http\Controllers\Api\Vendor\ProductCategoriesController as ApiVendorProductCategoriesController;
use App\Http\Controllers\Api\Vendor\ProductsController as ApiVendorProductsController;
use App\Http\Controllers\Api\Vendor\ProductTagsController as ApiVendorProductTagsController;
use App\Http\Controllers\Api\Vendor\ProductVariationsController as ApiVendorProductVariationsController;
use App\Http\Controllers\Api\Vendor\UploadController as ApiVendorUploadController;
use App\Http\Controllers\Api\Vendor\WithdrawalsController as ApiVendorWithdrawalsController;
use Illuminate\Support\Facades\Route;

// Vendor API routes (loaded through RouteServiceProvider api group -> '/api' prefix)
Route::prefix('vendor')->group(function () {
    Route::post('/login', [ApiVendorAuthController::class, 'login']);
    Route::post('/logout', [ApiVendorAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [ApiVendorAuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::put('/profile', [ApiVendorAuthController::class, 'updateProfile'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [ApiVendorDashboardController::class, 'index']);

        Route::get('/orders', [ApiVendorOrdersController::class, 'index']);
        Route::get('/orders/{id}', [ApiVendorOrdersController::class, 'show']);
        Route::put('/orders/{id}/status', [ApiVendorOrdersController::class, 'updateOrderStatus']);
        Route::post('/orders/export', [ApiVendorOrdersController::class, 'requestExport']);

        Route::get('/products', [ApiVendorProductsController::class, 'index']);
        Route::post('/products', [ApiVendorProductsController::class, 'store']);
        Route::get('/products/{id}', [ApiVendorProductsController::class, 'show']);
        Route::put('/products/{id}', [ApiVendorProductsController::class, 'update']);
        Route::delete('/products/{id}', [ApiVendorProductsController::class, 'destroy']);
        // variation management
        Route::put(
            '/products/{productId}/variations/{variationId}',
            [ApiVendorProductVariationsController::class, 'update']
        );
        Route::delete(
            '/products/{productId}/variations/{variationId}',
            [ApiVendorProductVariationsController::class, 'destroy']
        );
        Route::post('/products/{productId}/variations', [ApiVendorProductVariationsController::class, 'store']);

        // Active languages list for multilingual fields
        Route::get('/languages', [ApiVendorLanguagesController::class, 'index']);

        Route::get('/product-categories', [ApiVendorProductCategoriesController::class, 'index']);
        Route::get('/product-tags', [ApiVendorProductTagsController::class, 'index']);
        Route::get('/product-attributes', [ApiVendorProductAttributesController::class, 'index']);
        Route::post('/upload/image', [ApiVendorUploadController::class, 'image']);

        Route::get('/withdrawals', [ApiVendorWithdrawalsController::class, 'index']);
        Route::post('/withdrawals', [ApiVendorWithdrawalsController::class, 'requestWithdrawal']);
        Route::delete('/withdrawals/{withdrawalId}', [ApiVendorWithdrawalsController::class, 'cancelWithdrawal']);

        // Balance endpoint
        Route::get('/balance', [ApiVendorDashboardController::class, 'balance']);

        // Notifications endpoints
        Route::get('/notifications', [ApiVendorNotificationsController::class, 'index']);
        Route::patch('/notifications/{id}/read', [ApiVendorNotificationsController::class, 'markAsRead']);
        Route::patch('/notifications/read-all', [ApiVendorNotificationsController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [ApiVendorNotificationsController::class, 'destroy']);
    });
});

// Payment API routes
Route::prefix('payments')->group(function () {
    // Public routes
    Route::get('/gateways', [PaymentApiController::class, 'getGateways']);
    Route::any('/webhook', [PaymentApiController::class, 'webhook'])->name('payment.webhook');

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/initialize', [PaymentApiController::class, 'initializePayment']);
        Route::get('/{paymentId}/status', [PaymentApiController::class, 'getPaymentStatus']);
        Route::post('/{paymentId}/verify', [PaymentApiController::class, 'verifyPayment']);
        Route::post('/{paymentId}/cancel', [PaymentApiController::class, 'cancelPayment']);
        Route::get('/analytics', [PaymentApiController::class, 'getAnalytics']);
    });
});

// System settings endpoint (public access)
Route::get('/system/settings', [ApiSystemController::class, 'settings']);

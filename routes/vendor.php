<?php

use App\Http\Controllers\Api\Vendor\LanguagesController as VendorLanguagesController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\NotificationController as VendorNotificationController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
|
| Here is where you can register vendor routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'can:access-vendor', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    // Vendor product management
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('products');
    // Vendor media quick upload (reuse admin gallery controller for uploads)
    Route::post('gallery/quick-upload', [\App\Http\Controllers\Admin\GalleryController::class, 'quickStore'])
        ->name('gallery.quick-upload');
    // Vendor withdrawals
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('withdrawals/{withdrawal}/receipt', [WithdrawalController::class, 'receipt'])
        ->name('withdrawals.receipt');
    // Vendor orders
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'vendorIndex'])->name('orders.index');
    Route::get('orders/{orderItem}', [\App\Http\Controllers\Admin\OrderController::class, 'vendorShow'])->name('orders.show');

    // Vendor notifications (mirroring admin endpoints)
    Route::get('notifications/latest', [VendorNotificationController::class, 'latest'])
        ->name('notifications.latest');
    Route::get('notifications/unread-count', [VendorNotificationController::class, 'unreadCount'])
        ->name('notifications.unreadCount');
    Route::post('notifications/{id}/read', [VendorNotificationController::class, 'markRead'])
        ->name('notifications.read');
    Route::post('notifications/mark-all-read', [VendorNotificationController::class, 'markAll'])
        ->name('notifications.markAll');
    Route::get('notifications', [VendorNotificationController::class, 'index'])->name('notifications.index');
    // Active languages for multilingual product fields
    Route::get('languages', [VendorLanguagesController::class, 'index']);
});

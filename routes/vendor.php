<?php

use App\Http\Controllers\Api\Vendor\LanguagesController as VendorLanguagesController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\NotificationController as VendorNotificationController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ProductController;
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
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
    // Vendor product management
    Route::resource('products', ProductController::class)->names('vendor.products');
    // Vendor withdrawals
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('vendor.withdrawals.index');
    Route::get('withdrawals/create', [WithdrawalController::class, 'create'])->name('vendor.withdrawals.create');
    Route::post('withdrawals', [WithdrawalController::class, 'store'])->name('vendor.withdrawals.store');
    Route::get('withdrawals/{withdrawal}/receipt', [WithdrawalController::class, 'receipt'])
        ->name('vendor.withdrawals.receipt');
    // Vendor orders
    Route::get('orders', [VendorOrderController::class, 'index'])->name('vendor.orders.index');
    Route::get('orders/{id}', [VendorOrderController::class, 'show'])->name('vendor.orders.show');

    // Vendor notifications (mirroring admin endpoints)
    Route::get('notifications/latest', [VendorNotificationController::class, 'latest'])
        ->name('vendor.notifications.latest');
    Route::get('notifications/unread-count', [VendorNotificationController::class, 'unreadCount'])
        ->name('vendor.notifications.unreadCount');
    Route::post('notifications/{id}/read', [VendorNotificationController::class, 'markRead'])
        ->name('vendor.notifications.read');
    Route::post('notifications/mark-all-read', [VendorNotificationController::class, 'markAll'])
        ->name('vendor.notifications.markAll');
    Route::get('notifications', [VendorNotificationController::class, 'index'])->name('vendor.notifications.index');
    // Active languages for multilingual product fields
    Route::get('languages', [VendorLanguagesController::class, 'index']);
});

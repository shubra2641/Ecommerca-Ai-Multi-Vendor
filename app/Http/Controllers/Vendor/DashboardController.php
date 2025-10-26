<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VendorDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();
        $vendor = User::find($vendorId);

        $dashboardService = new VendorDashboardService();
        $dashboardData = $dashboardService->getDashboardData($vendorId);

        // Update vendor balance in database if different
        if ($vendor && abs($vendor->balance - $dashboardData['actual_balance']) > 0.01) {
            $vendor->update(['balance' => $dashboardData['actual_balance']]);
        }

        // Get recent notifications for the header
        $recentNotifications = $vendor->notifications()->latest()->limit(5)->get();
        $unreadCount = $vendor->unreadNotifications()->count();

        return view('vendor.dashboard', [
            'totalSales' => $dashboardData['total_sales'],
            'ordersCount' => $dashboardData['total_orders'],
            'pendingWithdrawals' => $dashboardData['pending_withdrawals'],
            'productsCount' => $dashboardData['total_products'],
            'activeProductsCount' => $dashboardData['active_products'],
            'pendingProductsCount' => $dashboardData['pending_products'],
            'actualBalance' => $vendor->balance ?? 0,
            'recentOrders' => $dashboardData['recent_orders'],
            'totalWithdrawn' => $dashboardData['total_withdrawn'],
            'monthlyApproved' => $dashboardData['monthly_approved'],
            'recentNotifications' => $recentNotifications,
            'unreadNotificationsCount' => $unreadCount,
        ]);
    }
}

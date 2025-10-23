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

        return view('vendor.dashboard', [
            'totalSales' => $dashboardData['total_sales'],
            'ordersCount' => $dashboardData['total_orders'],
            'pendingWithdrawals' => $dashboardData['pending_withdrawals'],
            'productsCount' => $dashboardData['total_products'],
            'activeProductsCount' => $dashboardData['active_products'],
            'pendingProductsCount' => $dashboardData['pending_products'],
            'actualBalance' => $dashboardData['actual_balance'],
            'recentOrders' => $dashboardData['recent_orders'],
        ]);
    }
}

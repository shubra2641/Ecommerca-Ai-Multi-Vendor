<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorWithdrawal;
use Illuminate\Support\Facades\DB;

class VendorDashboardService
{
    public function getDashboardData(int $vendorId): array
    {
        // Calculate total sales from completed orders
        $totalSales = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        // Count unique orders
        $ordersCount = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->distinct('order_id')
            ->count('order_id');

        // Pending withdrawals
        $pendingWithdrawals = VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');

        // Total products count
        $productsCount = Product::where('vendor_id', $vendorId)->count();

        // Active products count (approved)
        $activeProductsCount = Product::where('vendor_id', $vendorId)
            ->where('active', true)
            ->count();

        // Pending products count (under review)
        $pendingProductsCount = Product::where('vendor_id', $vendorId)
            ->where('active', false)
            ->whereNull('rejection_reason')
            ->count();

        // Calculate vendor's actual balance
        $totalWithdrawals = VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'completed')
            ->sum('amount');

        $actualBalance = $totalSales - $totalWithdrawals;

        // Recent orders (last 5)
        $recentOrders = Order::whereHas('items.product', fn($q) => $q->where('vendor_id', $vendorId))
            ->latest('created_at')
            ->limit(5)
            ->get();

        return [
            'total_sales' => (float) $totalSales,
            'total_orders' => (int) $ordersCount,
            'pending_withdrawals' => (float) $pendingWithdrawals,
            'total_products' => (int) $productsCount,
            'active_products' => (int) $activeProductsCount,
            'pending_products' => (int) $pendingProductsCount,
            'actual_balance' => (float) $actualBalance,
            'recent_orders' => $recentOrders,
        ];
    }
}

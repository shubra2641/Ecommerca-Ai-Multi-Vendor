<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorWithdrawal;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $vendorId = auth()->id();
        $vendor = User::find($vendorId);

        // Calculate total sales from completed orders
        $totalSales = OrderItem::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn ($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        // Count unique orders
        $ordersCount = OrderItem::whereHas('product', fn ($q) => $q->where('vendor_id', $vendorId))
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

        // Update vendor balance in database if different
        if ($vendor && abs($vendor->balance - $actualBalance) > 0.01) {
            $vendor->update(['balance' => $actualBalance]);
        }

        // Recent orders (last 5)
        $recentOrders = Order::whereHas('items.product', fn ($q) => $q->where('vendor_id', $vendorId))
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('vendor.dashboard', compact(
            'totalSales',
            'ordersCount',
            'pendingWithdrawals',
            'productsCount',
            'activeProductsCount',
            'pendingProductsCount',
            'actualBalance',
            'recentOrders'
        ));
    }
}

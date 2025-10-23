<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorWithdrawal;
use App\Services\VendorDashboardService;
use App\Services\WithdrawalSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $vendorId = $r->user()->id;
        $vendor = User::find($vendorId);

        $dashboardService = new VendorDashboardService();
        $dashboardData = $dashboardService->getDashboardData($vendorId);

        // Previously this endpoint updated the stored vendor balance on each read.
        // That caused unexpected writes when the mobile app fetches the dashboard after login.
        // To avoid side-effects on read, do NOT persist the computed balance here.
        // Instead log the stored vs computed balance for diagnosis.
        if ($vendor && abs($vendor->balance - $dashboardData['actual_balance']) > 0.01) {
            // If you want to re-enable persistence after fixing root cause, restore the update call:
            // $vendor->update(['balance' => $actualBalance]);
            null;
        }

        // Generate sales chart data for last 12 months
        $salesChartData = $this->generateSalesChartData($vendorId);
        $ordersChartData = $this->generateOrdersChartData($vendorId);

        // Calculate growth percentages
        $salesGrowth = $this->calculateSalesGrowth($vendorId);
        $ordersGrowth = $this->calculateOrdersGrowth($vendorId);

        return response()->json([
            'total_sales' => $dashboardData['total_sales'],
            'total_orders' => $dashboardData['total_orders'],
            'pending_withdrawals' => $dashboardData['pending_withdrawals'],
            'total_products' => $dashboardData['total_products'],
            'active_products' => $dashboardData['active_products'],
            'pending_products' => $dashboardData['pending_products'],
            'actual_balance' => $dashboardData['actual_balance'],
            'recent_orders' => $dashboardData['recent_orders'],
            'sales_chart' => $salesChartData,
            'orders_chart' => $ordersChartData,
            'orders_growth' => (float) $ordersGrowth,
            'sales_growth' => (float) $salesGrowth,
        ]);
    }

    public function balance(Request $r)
    {
        $user = $r->user();
        $vendorId = $user->id;

        // Get user's current balance from the balance column
        $availableBalance = (float) ($user->balance ?? 0);

        // Calculate total sales from completed orders only
        $totalSales = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        // Calculate total withdrawals
        $totalWithdrawals = VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'completed')
            ->sum('amount');

        // Calculate pending withdrawals
        $pendingWithdrawals = VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');

        // Get recent withdrawals (last 10)
        $recentWithdrawals = VendorWithdrawal::where('user_id', $vendorId)
            ->latest('created_at')
            ->limit(10)
            ->get([
                'id',
                'amount',
                'currency',
                'status',
                'payment_method',
                'created_at',
                'approved_at',
                'rejected_at',
                'notes',
            ])
            ->map(function ($withdrawal) {
                return [
                    'id' => $withdrawal->id,
                    'amount' => (float) $withdrawal->amount,
                    'currency' => $withdrawal->currency ?? 'USD',
                    'status' => $withdrawal->status,
                    'payment_method' => $withdrawal->payment_method,
                    'created_at' => $withdrawal->created_at?->toISOString(),
                    'approved_at' => $withdrawal->approved_at?->toISOString(),
                    'rejected_at' => $withdrawal->rejected_at?->toISOString(),
                    'notes' => $withdrawal->notes,
                ];
            });

        // Load withdrawal settings for client
        $withdrawalSettingsService = new WithdrawalSettingsService();
        $withdrawalSettings = $withdrawalSettingsService->getWithdrawalSettings();

        // Standardize response shape to match other API endpoints (success + data)
        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => [
                    'total_sales' => (float) $totalSales,
                    'total_withdrawals' => (float) $totalWithdrawals,
                    'pending_withdrawals' => (float) $pendingWithdrawals,
                    'available_balance' => $availableBalance,
                    'currency' => $user->currency ?? 'USD',
                ],
                'recent_withdrawals' => $recentWithdrawals,
                'settings' => $withdrawalSettings,
            ],
        ]);
    }

    /**
     * Generate sales chart data for the last 12 months
     */
    private function generateSalesChartData($vendorId)
    {
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('M Y');

            $monthlySales = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
                ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum(DB::raw('(price * COALESCE(qty, 1))'));

            $chartData[$monthKey] = (float) $monthlySales;
        }

        return $chartData;
    }

    /**
     * Generate orders chart data for the last 12 months
     */
    private function generateOrdersChartData($vendorId)
    {
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('M Y');

            $monthlyOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
                ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->distinct('order_id')
                ->count('order_id');

            $chartData[$monthKey] = (int) $monthlyOrders;
        }

        return $chartData;
    }

    /**
     * Calculate sales growth percentage compared to previous month
     */
    private function calculateSalesGrowth($vendorId)
    {
        $currentMonth = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        $previousMonth = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        if ($previousMonth === 0) {
            return $currentMonth > 0 ? 100.0 : 0.0;
        }

        return ($currentMonth - $previousMonth) / $previousMonth * 100;
    }

    /**
     * Calculate orders growth percentage compared to previous month
     */
    private function calculateOrdersGrowth($vendorId)
    {
        $currentMonthOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->distinct('order_id')
            ->count('order_id');

        $previousMonthOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->distinct('order_id')
            ->count('order_id');

        if ($previousMonthOrders === 0) {
            return $currentMonthOrders > 0 ? 100.0 : 0.0;
        }

        return ($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders * 100;
    }
}

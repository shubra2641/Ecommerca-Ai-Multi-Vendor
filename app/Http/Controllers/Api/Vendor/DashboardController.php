<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $vendorId = $r->user()->id;
        $vendor = User::find($vendorId);

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

        // Previously this endpoint updated the stored vendor balance on each read.
        // That caused unexpected writes when the mobile app fetches the dashboard after login.
        // To avoid side-effects on read, do NOT persist the computed balance here.
        // Instead log the stored vs computed balance for diagnosis.
        if ($vendor && abs($vendor->balance - $actualBalance) > 0.01) {
            // If you want to re-enable persistence after fixing root cause, restore the update call:
            // $vendor->update(['balance' => $actualBalance]);
            null;
        }

        $recentOrders = Order::whereHas('items.product', fn($q) => $q->where('vendor_id', $vendorId))
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Generate sales chart data for last 12 months
        $salesChartData = $this->generateSalesChartData($vendorId);
        $ordersChartData = $this->generateOrdersChartData($vendorId);

        // Calculate growth percentages
        $salesGrowth = $this->calculateSalesGrowth($vendorId);
        $ordersGrowth = $this->calculateOrdersGrowth($vendorId);

        return response()->json([
            'total_sales' => (float) $totalSales,
            'total_orders' => (int) $ordersCount,
            'pending_withdrawals' => (float) $pendingWithdrawals,
            'total_products' => (int) $productsCount,
            'active_products' => (int) $activeProductsCount,
            'pending_products' => (int) $pendingProductsCount,
            'actual_balance' => (float) $actualBalance,
            'recent_orders' => $recentOrders,
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
        $setting = \App\Models\Setting::first();
        $minimumAmount = isset($setting->min_withdrawal_amount) ? (float) $setting->min_withdrawal_amount : 10.0;
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        if (is_string($rawGateways)) {
            $decoded = json_decode($rawGateways, true);
            if (is_array($decoded)) {
                $rawGateways = $decoded;
            } else {
                $rawGateways = array_filter(array_map('trim', preg_split('/\r?\n/', $rawGateways)));
            }
        }
        if (! is_array($rawGateways)) {
            $rawGateways = (array) $rawGateways;
        }
        $gateways = [];
        foreach ($rawGateways as $g) {
            // If the gateway is already an array with label/name, use it
            if (is_array($g)) {
                $label = $g['label'] ?? ($g['name'] ?? null);
                if ($label) {
                    $slug = \Illuminate\Support\Str::slug($label);
                    $gateways[] = ['slug' => $slug, 'label' => $label];
                }

                continue;
            }

            // If gateway is numeric, try to find by id in payment_gateways
            if (is_numeric($g)) {
                $pg = \App\Models\PaymentGateway::find((int) $g);
                if ($pg) {
                    $gateways[] = [
                        'slug' => $pg->slug ?? \Illuminate\Support\Str::slug($pg->name ?? (string) $pg->id),
                        'label' => $pg->name ?? $pg->slug,
                    ];

                    continue;
                }
            }

            // If gateway is a string, try to match slug in payment_gateways
            if (is_string($g) && $g !== '') {
                $pg = \App\Models\PaymentGateway::where('slug', $g)->first();
                if ($pg) {
                    $gateways[] = ['slug' => $pg->slug, 'label' => $pg->name ?? $pg->slug];

                    continue;
                }

                // Fallback: return stored text as label and slugify it
                $slug = \Illuminate\Support\Str::slug($g);
                $gateways[] = ['slug' => $slug, 'label' => $g];
            }
        }

        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);

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
                'settings' => [
                    'minimum_withdrawal' => $minimumAmount,
                    'withdrawal_gateways' => $gateways,
                    'withdrawal_commission_enabled' => $commissionEnabled,
                    'withdrawal_commission_rate' => $commissionRate,
                ],
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '6m');
        
        if ($request->get('refresh', false)) {
            Cache::forget('dashboard_stats');
        }

        $stats = Cache::remember('dashboard_stats', 60, function () {
            return [
                'totalUsers' => User::count(),
                'totalVendors' => User::where('role', 'vendor')->count(),
                'pendingUsers' => User::whereNull('approved_at')->count(),
                'totalBalance' => User::sum('balance'),
                'activeUsers' => User::whereNotNull('approved_at')->count(),
                'newUsersToday' => User::whereDate('created_at', today())->count(),
                'totalOrders' => Order::count(),
                'revenueTotal' => Order::where('payment_status', 'paid')->sum('total'),
                'totalProducts' => Product::count(),
                'lowStockProducts' => Product::where('manage_stock', 1)->get()->filter(fn($p) => ($p->availableStock() ?? 0) <= 5)->count(),
            ];
        });

        $chartData = $this->buildChartData($period);
        $salesData = $this->getSalesData();
        $orderStatusData = $this->getOrderStatusData();

        return view('admin.dashboard', compact('stats', 'chartData', 'salesData', 'orderStatusData', 'period'));
    }

    private function buildChartData($period)
    {
        $months = [];
        $data = [];
        $count = $period === '1y' ? 12 : ($period === 'all' ? 24 : 6);

        for ($i = $count - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $data[] = User::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
        }

        return ['labels' => $months, 'data' => $data];
    }

    private function getSalesData()
    {
        $from = now()->subDays(29)->startOfDay();
        $raw = Order::selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as revenue')
            ->where('created_at', '>=', $from)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $orders = [];
        $revenue = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayKey = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $orders[] = (int) ($raw[$dayKey]->orders ?? 0);
            $revenue[] = (float) ($raw[$dayKey]->revenue ?? 0);
        }

        return ['labels' => $labels, 'orders' => $orders, 'revenue' => $revenue];
    }

    private function getOrderStatusData()
    {
        $statuses = Order::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
        
        if (empty($statuses)) {
            return [
                'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered'],
                'data' => [5, 3, 2, 8],
                'colors' => ['#ffc107', '#17a2b8', '#28a745', '#6f42c1']
            ];
        }

        $labels = [];
        $data = [];
        foreach ($statuses as $status => $count) {
            $labels[] = ucfirst($status);
            $data[] = $count;
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => ['#ffc107', '#17a2b8', '#28a745', '#6f42c1']];
    }

    public function refresh()
    {
        Cache::forget('dashboard_stats');
        return response()->json(['success' => true, 'message' => __('Dashboard refreshed successfully')]);
    }

    public function getChartData(Request $request)
    {
        $period = $request->get('period', '6m');
        $chartData = $this->buildChartData($period);
        return response()->json(['success' => true, 'chartData' => $chartData]);
    }

    public function getStats()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalVendors' => User::where('role', 'vendor')->count(),
            'pendingUsers' => User::whereNull('approved_at')->count(),
            'totalBalance' => User::sum('balance'),
            'activeUsers' => User::whereNotNull('approved_at')->count(),
        ];
        return response()->json(['success' => true, 'stats' => $stats]);
    }

    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            return redirect()->back()->with('success', __('Cache cleared successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to clear cache: ') . $e->getMessage());
        }
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }
            return redirect()->back()->with('success', __('Logs cleared successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to clear logs: ') . $e->getMessage());
        }
    }

    public function optimize()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize');
            \Illuminate\Support\Facades\Artisan::call('config:cache');
            return redirect()->back()->with('success', __('System optimized successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to optimize system: ') . $e->getMessage());
        }
    }
}
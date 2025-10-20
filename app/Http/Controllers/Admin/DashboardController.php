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
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '6m');
        
        // Clear cache if refresh requested
        if ($request->get('refresh', false)) {
            Cache::forget('dashboard_stats');
        }

        // Get cached stats
        $stats = Cache::remember('dashboard_stats', 60, fn() => $this->getDashboardStats());
        
        // Get chart data
        $chartData = $this->getRegistrationChartDataByPeriod($period);
        $salesChartData = $this->getSalesChartData();
        $orderStatusChartData = $this->getOrderStatusChartData();

        // Get additional data
        $topStats = $this->getTopStatistics();
        $topUsers = $this->getTopActiveUsers();
        $systemHealth = $this->getSystemHealth();

        return view('admin.dashboard', compact(
            'stats', 'chartData', 'salesChartData', 'orderStatusChartData',
            'topStats', 'topUsers', 'systemHealth', 'period'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $dbInfo = $this->getDatabaseInfo();
        $userStats = $this->getUserStats();
        $orderStats = $this->getOrderAggregates();
        
        return array_merge($dbInfo, $userStats, $orderStats);
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbSizeRow = DB::select(
                'SELECT SUM(data_length + index_length) / 1024 / 1024 AS db_size_mb ' .
                'FROM information_schema.tables WHERE table_schema = DATABASE()'
            );
            
            return [
                'tables_count' => count($tables),
                'size_mb' => (float) ($dbSizeRow[0]->db_size_mb ?? 0),
                'connection' => 'active',
            ];
        } catch (\Exception $e) {
            logger()->error('Failed retrieving database info: ' . $e->getMessage());
            return ['tables_count' => 0, 'size_mb' => 0, 'connection' => 'error'];
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        return [
            'totalUsers' => User::count(),
            'totalVendors' => User::where('role', 'vendor')->count(),
            'pendingUsers' => User::whereNull('approved_at')->count(),
            'totalBalance' => User::sum('balance'),
            'activeUsers' => User::whereNotNull('approved_at')->count(),
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'newUsersThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'newUsersThisMonth' => User::whereMonth('created_at', now()->month)->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalCustomers' => User::where('role', 'user')->count(),
            'approvedUsers' => User::whereNotNull('approved_at')->count(),
            'systemHealth' => $this->getSystemHealth(),
        ];
    }

    /**
     * Get registration chart data based on period
     */
    private function getRegistrationChartDataByPeriod($period)
    {
        return $this->getChartDataByPeriod($period);
    }


    /**
     * Get system health status
     */
    private function getSystemHealth()
    {
        $health = [];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $health['database'] = ['status' => 'healthy'];
        } catch (\Exception $e) {
            $health['database'] = ['status' => 'error'];
        }

        // Check cache
        try {
            Cache::put('health_check', 'ok', 60);
            $health['cache'] = ['status' => Cache::get('health_check') === 'ok' ? 'healthy' : 'warning'];
        } catch (\Exception $e) {
            $health['cache'] = ['status' => 'error'];
        }

        // Check storage
        $health['storage'] = ['status' => is_writable(storage_path()) ? 'healthy' : 'warning'];

        return $health;
    }

    /**
     * Get order aggregates
     */
    private function getOrderAggregates(): array
    {
        try {
            $orderStats = $this->getOrderStats();
            $productStats = $this->getProductStats();
            $paymentStats = $this->getPaymentStats();
            
            return array_merge($orderStats, $productStats, $paymentStats);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get order statistics
     */
    private function getOrderStats()
    {
        $todayRange = [now()->startOfDay(), now()->endOfDay()];
        $weekRange = [now()->startOfWeek(), now()->endOfWeek()];
        $monthStart = now()->startOfMonth();
        
        $paidOrders = Order::where('payment_status', 'paid');
        
        return [
            'totalOrders' => Order::count(),
            'ordersToday' => Order::whereBetween('created_at', $todayRange)->count(),
            'ordersThisWeek' => Order::whereBetween('created_at', $weekRange)->count(),
            'ordersThisMonth' => Order::where('created_at', '>=', $monthStart)->count(),
            'revenueTotal' => (float) $paidOrders->sum('total'),
            'revenueToday' => (float) $paidOrders->whereBetween('created_at', $todayRange)->sum('total'),
            'revenueThisWeek' => (float) $paidOrders->whereBetween('created_at', $weekRange)->sum('total'),
            'revenueThisMonth' => (float) $paidOrders->where('created_at', '>=', $monthStart)->sum('total'),
            'averageOrderValue' => (float) $paidOrders->avg('total'),
            'ordersStatusCounts' => Order::selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')->pluck('aggregate', 'status')->toArray(),
        ];
    }

    /**
     * Get product statistics
     */
    private function getProductStats()
    {
        $products = Product::query();
        $stockProducts = $products->where('manage_stock', 1)->get();
        
        return [
            'totalProductsAll' => $products->count(),
            'lowStockProducts' => $stockProducts->filter(fn($p) => ($p->availableStock() ?? 0) > 0 && ($p->availableStock() ?? 0) <= 5)->count(),
            'outOfStockProducts' => $stockProducts->filter(fn($p) => ($p->availableStock() ?? 0) <= 0)->count(),
            'onSaleProducts' => Product::whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count(),
        ];
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStats()
    {
        $payments = Payment::query();
        
        return [
            'paymentsTotal' => $payments->count(),
            'paymentsSuccess' => $payments->where('status', 'completed')->count(),
            'paymentsFailed' => $payments->whereIn('status', ['failed', 'rejected', 'cancelled'])->count(),
        ];
    }

    /**
     * Get sales chart data
     */
    private function getSalesChartData(): array
    {
        try {
            $from = now()->subDays(29)->startOfDay();
            $raw = Order::selectRaw(
                'DATE(created_at) as day, COUNT(*) as orders, ' .
                'SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as revenue'
            )
                ->where('created_at', '>=', $from)
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy('day');

            return $this->buildChartData($raw);
        } catch (\Exception $e) {
            return $this->getDefaultChartData();
        }
    }

    /**
     * Build chart data from raw results
     */
    private function buildChartData($raw)
    {
        $labels = [];
        $ordersData = [];
        $revenueData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayKey = $date->format('Y-m-d');
            
            $labels[] = $date->format('d M');
            $ordersData[] = (int) ($raw[$dayKey]->orders ?? 0);
            $revenueData[] = (float) ($raw[$dayKey]->revenue ?? 0);
        }

        return ['labels' => $labels, 'orders' => $ordersData, 'revenue' => $revenueData];
    }

    /**
     * Get default chart data
     */
    private function getDefaultChartData()
    {
        $labels = [];
        $ordersData = [];
        $revenueData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d M');
            $ordersData[] = rand(1, 10);
            $revenueData[] = rand(100, 1000);
        }
        
        return ['labels' => $labels, 'orders' => $ordersData, 'revenue' => $revenueData];
    }

    /**
     * Get order status chart data
     */
    private function getOrderStatusChartData(): array
    {
        try {
            $orderStatuses = Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return empty($orderStatuses) 
                ? $this->getDefaultOrderStatusData()
                : $this->buildOrderStatusData($orderStatuses);
        } catch (\Exception $e) {
            return $this->getDefaultOrderStatusData();
        }
    }

    /**
     * Build order status data
     */
    private function buildOrderStatusData($orderStatuses)
    {
        $colors = [
            'pending' => '#ffc107', 'processing' => '#17a2b8', 'shipped' => '#28a745',
            'delivered' => '#6f42c1', 'cancelled' => '#dc3545', 'refunded' => '#fd7e14'
        ];

        $labels = [];
        $data = [];
        
        foreach ($orderStatuses as $status => $count) {
            $labels[] = ucfirst($status);
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors)
        ];
    }

    /**
     * Get default order status data
     */
    private function getDefaultOrderStatusData()
    {
        return [
            'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered'],
            'data' => [5, 3, 2, 8],
            'colors' => ['#ffc107', '#17a2b8', '#28a745', '#6f42c1']
        ];
    }

    /**
     * Get top statistics for dashboard
     */
    private function getTopStatistics()
    {
        return [
            'growth_rate' => $this->calculateGrowthRate(),
            'approval_rate' => $this->calculateApprovalRate(),
        ];
    }

    /**
     * Get top active users
     */
    private function getTopActiveUsers()
    {
        return User::whereNotNull('approved_at')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'updated_at', 'created_at']);
    }

    /**
     * Calculate user growth rate
     */
    private function calculateGrowthRate()
    {
        $thisMonth = User::whereMonth('created_at', now()->month)->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth == 0) {
            return 100;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate()
    {
        $totalUsers = User::count();
        $approvedUsers = User::whereNotNull('approved_at')->count();

        if ($totalUsers == 0) {
            return 0;
        }

        return round(($approvedUsers / $totalUsers) * 100, 1);
    }




    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

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
            \Illuminate\Support\Facades\Artisan::call('route:cache');

            return redirect()->back()->with('success', __('System optimized successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to optimize system: ') . $e->getMessage());
        }
    }

    /**
     * Refresh dashboard data (AJAX endpoint)
     */
    public function refresh()
    {
        try {
            // Clear dashboard cache
            Cache::forget('dashboard_stats');

            // Build fresh payload so frontend can update without a full page reload
            $stats = $this->buildFreshStats();
            $chartData = $this->getRegistrationChartData();
            $salesChartData = $this->getSalesChartData();

            return response()->json([
                'success' => true,
                'message' => __('Dashboard refreshed successfully'),
                'data' => [
                    'stats' => $stats,
                    'charts' => $chartData,
                    'salesChart' => $salesChartData,
                    'activities' => [],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to refresh dashboard'),
            ], 500);
        }
    }

    /**
     * Get chart data for different periods (AJAX endpoint)
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', '6m');

        try {
            $chartData = $this->getChartDataByPeriod($period);

            return response()->json([
                'success' => true,
                'chartData' => $chartData,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to get chart data'),
            ], 500);
        }
    }

    /**
     * Get chart data based on period
     */
    private function getChartDataByPeriod($period)
    {
        try {
            $periodCount = $this->getPeriodCount($period);
            $months = [];
            $data = [];
            $vendorData = [];
            $adminData = [];

            for ($i = $periodCount - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');

                $userCounts = $this->getUserCountsForMonth($date);
                $data[] = $userCounts['total'];
                $vendorData[] = $userCounts['vendor'];
                $adminData[] = $userCounts['admin'];
            }

            return [
                'labels' => $months,
                'data' => $data,
                'vendorData' => $vendorData,
                'adminData' => $adminData,
            ];
        } catch (\Exception $e) {
            // Return default data if there's an error
            $months = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');
                $data[] = rand(5, 25); // Random data for demo
            }
            return [
                'labels' => $months,
                'data' => $data,
                'vendorData' => array_map(fn($x) => $x - 2, $data), // Slightly lower for vendors
                'adminData' => array_map(fn($x) => max(1, $x - 5), $data), // Much lower for admins
            ];
        }
    }

    /**
     * Get period count based on period string
     */
    private function getPeriodCount($period)
    {
        return match ($period) {
            '6m' => 6,
            '1y' => 12,
            'all' => 24,
            default => 6,
        };
    }

    /**
     * Get user counts for a specific month
     */
    private function getUserCountsForMonth($date)
    {
        $baseQuery = User::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month);

        return [
            'total' => $baseQuery->count(),
            'vendor' => (clone $baseQuery)->where('role', 'vendor')->count(),
            'admin' => (clone $baseQuery)->where('role', 'admin')->count(),
        ];
    }

    /**
     * Get system statistics (AJAX endpoint)
     */
    public function getStats()
    {
        try {
            $stats = $this->buildFreshStats();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to get statistics'),
            ], 500);
        }
    }

    /**
     * Build the stats array used by both getStats() and refresh()
     */
    private function buildFreshStats()
    {
        return [
            'totalUsers' => User::count(),
            'totalVendors' => User::where('role', 'vendor')->count(),
            'pendingUsers' => User::whereNull('approved_at')->count(),
            'totalBalance' => User::sum('balance'),
            'activeUsers' => User::whereNotNull('approved_at')->count(),
            'activeToday' => User::whereDate('created_at', today())->count(),
            'newUsersToday' => User::whereDate('created_at', today())->count(),
            'newUsersThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'newUsersThisMonth' => User::whereMonth('created_at', now()->month)->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalCustomers' => User::where('role', 'user')->count(),
            'approvedUsers' => User::whereNotNull('approved_at')->count(),
        ];
    }
}

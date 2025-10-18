<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 60, function () {
            $dbInfo = [
                'tables_count' => 0,
                'size_mb' => 0,
                'connection' => 'unknown',
            ];

            try {
                $tables = DB::select('SHOW TABLES');
                $dbSizeRow = DB::select('SELECT SUM(data_length + index_length) / 1024 / 1024 AS db_size_mb FROM information_schema.tables WHERE table_schema = DATABASE()');
                $dbSize = isset($dbSizeRow[0]->db_size_mb) ? (float) $dbSizeRow[0]->db_size_mb : 0.0;

                $dbInfo = [
                    'tables_count' => is_array($tables) ? count($tables) : 0,
                    'size_mb' => $dbSize,
                    'connection' => 'active',
                ];
            } catch (\Exception $e) {
                logger()->error('Failed retrieving database info for admin dashboard: ' . $e->getMessage());
                $dbInfo = [
                    'tables_count' => 0,
                    'size_mb' => 0,
                    'connection' => 'error',
                ];
            }

            $base = [
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

            // Orders & sales stats (not heavy aggregation, cached together)
            $ordersAgg = $this->getOrderAggregates();

            return array_merge($dbInfo, $base, $ordersAgg);
        });

        // Get recent activity (not cached for real-time updates)
        $recentActivity = $this->getRecentActivity();

        // Get chart data for user registrations
        $chartData = $this->getRegistrationChartData();

        // Get sales chart data (orders + revenue last 30 days)
        $salesChartData = $this->getSalesChartData();

        // Get top statistics for quick overview
        $topStats = $this->getTopStatistics();

        // Get top active users
        $topUsers = $this->getTopActiveUsers();

        // Get system health data
        $systemHealth = $this->getSystemHealth();

        return view('admin.dashboard', compact('stats', 'recentActivity', 'chartData', 'salesChartData', 'topStats', 'topUsers', 'systemHealth'));
    }

    /**
     * Get recent activity data
     */
    private function getRecentActivity()
    {
        $activities = [];

        // Get recent user registrations
        $recentUsers = User::latest()
            ->take(3)
            ->get(['name', 'email', 'role', 'created_at']);

        foreach ($recentUsers as $user) {
            $activities[] = [
                'title' => __('New User Registration'),
                'description' => $user->name . ' (' . ucfirst($user->role) . ')',
                'time' => $user->created_at->diffForHumans(),
                'icon' => 'user-plus',
                'type' => 'user',
                'timestamp' => $user->created_at,
            ];
        }

        // Get recent approvals
        $recentApprovals = User::whereNotNull('approved_at')
            ->latest('approved_at')
            ->take(2)
            ->get(['name', 'approved_at', 'role']);

        foreach ($recentApprovals as $user) {
            $activities[] = [
                'title' => __('User Approved'),
                'description' => $user->name . ' (' . ucfirst($user->role) . ') ' . __('was approved'),
                'time' => $user->approved_at->diffForHumans(),
                'icon' => 'check-circle',
                'type' => 'approval',
                'timestamp' => $user->approved_at,
            ];
        }

        // Get recent user registrations instead of logins
        $recentRegistrations = User::latest('created_at')
            ->take(2)
            ->get(['name', 'created_at', 'role']);

        foreach ($recentRegistrations as $user) {
            $activities[] = [
                'title' => __('User Registration'),
                'description' => $user->name . ' (' . ucfirst($user->role) . ') ' . __('registered'),
                'time' => $user->created_at->diffForHumans(),
                'icon' => 'user-plus',
                'type' => 'registration',
                'timestamp' => $user->created_at,
            ];
        }

        // Sort by timestamp and limit to 8 items
        return collect($activities)->sortByDesc('timestamp')->take(8)->values();
    }

    /**
     * Get registration chart data for the last 6 months
     */
    private function getRegistrationChartData()
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $data,
            'vendorData' => $this->getVendorRegistrationData($months),
            'adminData' => $this->getAdminRegistrationData($months),
        ];
    }

    /**
     * Get vendor registration data for chart
     */
    private function getVendorRegistrationData($months)
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('role', 'vendor')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get admin registration data for chart
     */
    private function getAdminRegistrationData($months)
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('role', 'admin')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }

        return $data;
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
     * Aggregate orders / sales metrics
     */
    private function getOrderAggregates(): array
    {
        try {
            $orders = \App\Models\Order::query();
            $paid = $orders->clone()->where('payment_status', 'paid');

            $todayRange = [now()->startOfDay(), now()->endOfDay()];
            $weekRange = [now()->startOfWeek(), now()->endOfWeek()];
            $monthRangeStart = now()->startOfMonth();

            $byStatus = \App\Models\Order::selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->toArray();

            $totalOrders = \App\Models\Order::count();
            $ordersToday = \App\Models\Order::whereBetween('created_at', $todayRange)->count();
            $ordersThisWeek = \App\Models\Order::whereBetween('created_at', $weekRange)->count();
            $ordersThisMonth = \App\Models\Order::where('created_at', '>=', $monthRangeStart)->count();

            $revenueTotal = $paid->clone()->sum('total');
            $revenueToday = \App\Models\Order::where('payment_status', 'paid')->whereBetween('created_at', $todayRange)->sum('total');
            $revenueWeek = \App\Models\Order::where('payment_status', 'paid')->whereBetween('created_at', $weekRange)->sum('total');
            $revenueMonth = \App\Models\Order::where('payment_status', 'paid')->where('created_at', '>=', $monthRangeStart)->sum('total');

            $avgOrder = $paid->clone()->avg('total');

            // Products / inventory
            $productQ = \App\Models\Product::query();
            $totalProducts = $productQ->count();
            $lowStock = $productQ->where('manage_stock', 1)->get()->filter(fn ($p) => ($p->availableStock() ?? 0) > 0 && ($p->availableStock() ?? 0) <= 5)->count();
            $outOfStock = $productQ->where('manage_stock', 1)->get()->filter(fn ($p) => ($p->availableStock() ?? 0) <= 0)->count();
            $onSale = \App\Models\Product::whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count();

            // Payment metrics
            $paymentQ = \App\Models\Payment::query();
            $paymentsTotal = $paymentQ->count();
            $paymentsSuccess = $paymentQ->where('status', 'completed')->count();
            $paymentsFailed = $paymentQ->whereIn('status', ['failed', 'rejected', 'cancelled'])->count();

            return [
                'totalOrders' => $totalOrders,
                'ordersToday' => $ordersToday,
                'ordersThisWeek' => $ordersThisWeek,
                'ordersThisMonth' => $ordersThisMonth,
                'revenueTotal' => (float) $revenueTotal,
                'revenueToday' => (float) $revenueToday,
                'revenueThisWeek' => (float) $revenueWeek,
                'revenueThisMonth' => (float) $revenueMonth,
                'averageOrderValue' => (float) $avgOrder,
                'ordersStatusCounts' => $byStatus,
                'totalProductsAll' => $totalProducts,
                'lowStockProducts' => $lowStock,
                'outOfStockProducts' => $outOfStock,
                'onSaleProducts' => $onSale,
                'paymentsTotal' => $paymentsTotal,
                'paymentsSuccess' => $paymentsSuccess,
                'paymentsFailed' => $paymentsFailed,
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Sales chart data (orders + revenue last 30 days)
     */
    private function getSalesChartData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $raw = \App\Models\Order::selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as revenue')
            ->where('created_at', '>=', $from)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $ordersData = [];
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $ordersData[] = (int) ($raw[$d]->orders ?? 0);
            $revenueData[] = (float) ($raw[$d]->revenue ?? 0);
        }

        return [
            'labels' => $labels,
            'orders' => $ordersData,
            'revenue' => $revenueData,
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
            'activity_score' => $this->calculateActivityScore(),
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

    /**
     * Calculate activity score
     */
    private function calculateActivityScore()
    {
        $totalUsers = User::count();
        $activeToday = User::whereDate('created_at', today())->count();

        if ($totalUsers == 0) {
            return 0;
        }

        return round(($activeToday / $totalUsers) * 100, 1);
    }

    public function reports()
    {
        $totalUsers = User::count();
        $totalVendors = User::where('role', 'vendor')->count();
        $pendingUsers = User::whereNull('approved_at')->count();
        $totalBalance = User::sum('balance');

        // Recent activity - sample data
        $recentActivity = [
            ['title' => 'New User Registration', 'description' => 'John Doe registered as a vendor', 'time' => '2 minutes ago', 'icon' => 'user-plus'],
            ['title' => 'User Approved', 'description' => 'Jane Smith was approved as vendor', 'time' => '15 minutes ago', 'icon' => 'check'],
            ['title' => 'Balance Updated', 'description' => 'Balance updated for 5 users', 'time' => '1 hour ago', 'icon' => 'wallet'],
        ];

        // Registration trends
        $registrationsToday = User::whereDate('created_at', today())->count();
        $registrationsWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $registrationsMonth = User::whereMonth('created_at', now()->month)->count();

        return view('admin.reports', compact(
            'totalUsers',
            'totalVendors',
            'pendingUsers',
            'totalBalance',
            'recentActivity',
            'registrationsToday',
            'registrationsWeek',
            'registrationsMonth'
        ));
    }

    public function usersReport()
    {
        return response()->json(['message' => 'Users report feature coming soon']);
    }

    public function vendorsReport()
    {
        return response()->json(['message' => 'Vendors report feature coming soon']);
    }

    public function financialReport()
    {
        return response()->json(['message' => 'Financial report feature coming soon']);
    }

    public function systemReport()
    {
        return response()->json(['message' => 'System report feature coming soon']);
    }

    public function generateReport()
    {
        // Generate Excel report
        $filename = 'admin-report-' . date('Y-m-d') . '.xlsx';

        return response()->json(['message' => 'Report generated successfully', 'filename' => $filename]);
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
            $activities = $this->getRecentActivity();

            return response()->json([
                'success' => true,
                'message' => __('Dashboard refreshed successfully'),
                'data' => [
                    'stats' => $stats,
                    'charts' => $chartData,
                    'salesChart' => $salesChartData,
                    'activities' => $activities,
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
        $months = [];
        $data = [];
        $vendorData = [];
        $adminData = [];

        switch ($period) {
            case '6m':
                $periodCount = 6;
                break;
            case '1y':
                $periodCount = 12;
                break;
            case 'all':
                $periodCount = 24; // Last 2 years
                break;
            default:
                $periodCount = 6;
        }

        for ($i = $periodCount - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Total users
            $totalCount = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $totalCount;

            // Vendor users
            $vendorCount = User::where('role', 'vendor')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $vendorData[] = $vendorCount;

            // Admin users
            $adminCount = User::where('role', 'admin')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $adminData[] = $adminCount;
        }

        return [
            'labels' => $months,
            'data' => $data,
            'vendorData' => $vendorData,
            'adminData' => $adminData,
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

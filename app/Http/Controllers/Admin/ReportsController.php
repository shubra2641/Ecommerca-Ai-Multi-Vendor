<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    /**
     * Display the main reports page
     */
    public function index()
    {
        // Cache reports data for 10 minutes
        $stats = Cache::remember('reports_stats', 600, function () {
            return [
                'totalUsers' => User::count(),
                'totalVendors' => User::where('role', 'vendor')->count(),
                'totalAdmins' => User::where('role', 'admin')->count(),
                'totalCustomers' => User::where('role', 'user')->count(),
                'pendingUsers' => User::whereNull('approved_at')->count(),
                'approvedUsers' => User::whereNotNull('approved_at')->count(),
                'activeUsers' => User::whereNotNull('approved_at')->count(),
                'inactiveUsers' => User::whereNull('approved_at')->count(),
                'totalBalance' => User::sum('balance'),
                'averageBalance' => User::avg('balance'),
                'maxBalance' => User::max('balance'),
                'minBalance' => User::min('balance'),
                'usersToday' => User::whereDate('created_at', today())->count(),
                'usersThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'usersThisMonth' => User::whereMonth('created_at', now()->month)->count(),
                'usersThisYear' => User::whereYear('created_at', now()->year)->count(),
                'activeToday' => User::whereDate('created_at', today())->count(),
                'activeThisWeek' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ];
        });

        // Get chart data for user registrations
        $chartData = $this->getRegistrationChartData();

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        // Get system health
        $systemHealth = $this->getSystemHealth();

        return view('admin.reports', compact('stats', 'chartData', 'recentActivity', 'systemHealth'));
    }

    /**
     * Generate users report
     */
    public function usersReport()
    {
        $users = User::select('id', 'name', 'email', 'role', 'balance', 'created_at', 'approved_at')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total' => User::count(),
            'active' => User::whereNotNull('approved_at')->count(),
            'pending' => User::whereNull('approved_at')->count(),
            'approved' => User::whereNotNull('approved_at')->count(),
        ];

        return view('admin.reports.users', compact('users', 'stats'));
    }

    /**
     * Generate vendors report
     */
    public function vendorsReport()
    {
        $vendors = User::where('role', 'vendor')
            ->select('id', 'name', 'email', 'balance', 'created_at', 'approved_at')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total' => User::where('role', 'vendor')->count(),
            'active' => User::where('role', 'vendor')->whereNotNull('approved_at')->count(),
            'pending' => User::where('role', 'vendor')->whereNull('approved_at')->count(),
            'totalBalance' => User::where('role', 'vendor')->sum('balance'),
        ];

        return view('admin.reports.vendors', compact('vendors', 'stats'));
    }

    /**
     * Generate financial report
     */
    public function financialReport()
    {
        $financialData = [
            'totalBalance' => User::sum('balance'),
            'vendorBalance' => User::where('role', 'vendor')->sum('balance'),
            'customerBalance' => User::where('role', 'user')->sum('balance'),
            'averageBalance' => User::avg('balance'),
            'maxBalance' => User::max('balance'),
            'minBalance' => User::min('balance'),
            'balanceDistribution' => $this->getBalanceDistribution(),
            'monthlyTrends' => $this->getMonthlyFinancialTrends(),
        ];

        return view('admin.reports.financial', compact('financialData'));
    }

    /**
     * Generate system report
     */
    public function systemReport()
    {
        $systemData = [
            'health' => $this->getSystemHealth(),
            'performance' => $this->getPerformanceMetrics(),
            'storage' => $this->getStorageInfo(),
            'database' => $this->getDatabaseInfo(),
        ];

        return view('admin.reports.system', compact('systemData'));
    }

    /**
     * Inventory report for products
     */
    public function inventoryReport(Request $request)
    {
        $query = \App\Models\Product::query();

        $products = $query->with(['variations'])
            ->withCount(['serials as unsold_serials_count' => function ($q) {
                $q->whereNull('sold_at');
            }])
            ->get()
            ->map(function ($p) {
                $variations = $p->variations->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'name' => $v->name ?? ($v->attribute_data ? json_encode($v->attribute_data) : ''),
                        'manage_stock' => (bool) $v->manage_stock,
                        'available_stock' => $v->manage_stock ? ($v->stock_qty - ($v->reserved_qty ?? 0)) : null,
                    ];
                });

                return [
                    'id' => $p->id,
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'manage_stock' => (bool) $p->manage_stock,
                    'available_stock' => $p->availableStock(),
                    'has_serials' => (bool) $p->has_serials,
                    'unsold_serials' => $p->unsold_serials_count,
                    'variations' => $variations,
                ];
            });

        $totals = [
            'total_products' => \App\Models\Product::count(),
            'manage_stock_count' => \App\Models\Product::where('manage_stock', 1)->count(),
            'out_of_stock' => \App\Models\Product::where('manage_stock', 1)->get()->filter(fn ($x) => ($x->availableStock() ?? 0) <= 0)->count(),
            'serials_low' => \App\Models\Product::where('has_serials', 1)->get()->filter(fn ($x) => $x->serials()->whereNull('sold_at')->count() <= 5)->count(),
            'average_stock' => (int) round(\App\Models\Product::where('manage_stock', 1)->get()->map(fn ($x) => $x->availableStock() ?? 0)->avg()),
        ];

        return view('admin.reports.inventory', compact('products', 'totals'));
    }

    /**
     * Refresh reports data
     */
    public function refresh()
    {
        try {
            // Clear reports cache
            Cache::forget('reports_stats');
            Cache::forget('dashboard_stats');

            return response()->json([
                'success' => true,
                'message' => __('Reports data refreshed successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to refresh reports data'),
            ], 500);
        }
    }

    /**
     * Export data to Excel/PDF
     */
    public function exportData(Request $request)
    {
        $request->validate([
            'type' => ['nullable', 'in:excel,pdf'],
            'report' => ['nullable', 'in:users,vendors,financial,system'],
        ]);

        $type = $request->get('type', 'excel'); // excel or pdf
        $report = $request->get('report', 'users'); // users, vendors, financial, system

        try {
            $filename = 'admin-' . $report . '-report-' . date('Y-m-d-H-i-s');

            if ($type === 'pdf') {
                return $this->exportToPdf($report, $filename);
            } else {
                return $this->exportToExcel($report, $filename);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to export data: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get registration chart data
     */
    private function getRegistrationChartData()
    {
        $months = [];
        $userData = [];
        $vendorData = [];
        $adminData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $userData[] = User::where('role', 'user')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $vendorData[] = User::where('role', 'vendor')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $adminData[] = User::where('role', 'admin')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'userData' => $userData,
            'vendorData' => $vendorData,
            'adminData' => $adminData,
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        $activities = [];

        // Recent registrations
        $recentUsers = User::latest()->take(5)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'title' => __('New User Registration'),
                'description' => $user->name . ' (' . ucfirst($user->role) . ')',
                'time' => $user->created_at->diffForHumans(),
                'icon' => 'user-plus',
                'type' => 'registration',
                'timestamp' => $user->created_at,
            ];
        }

        // Recent approvals
        $recentApprovals = User::whereNotNull('approved_at')
            ->latest('approved_at')
            ->take(3)
            ->get();

        foreach ($recentApprovals as $user) {
            $activities[] = [
                'title' => __('User Approved'),
                'description' => $user->name . ' was approved',
                'time' => $user->approved_at->diffForHumans(),
                'icon' => 'check-circle',
                'type' => 'approval',
                'timestamp' => $user->approved_at,
            ];
        }

        return collect($activities)->sortByDesc('timestamp')->take(10)->values();
    }

    /**
     * Get system health status
     */
    private function getSystemHealth()
    {
        $health = [];

        // Database health
        try {
            DB::connection()->getPdo();
            $health['database'] = ['status' => 'healthy'];
        } catch (\Exception $e) {
            $health['database'] = ['status' => 'error'];
        }

        // Cache health
        try {
            Cache::put('health_check', 'ok', 60);
            $health['cache'] = ['status' => Cache::get('health_check') === 'ok' ? 'healthy' : 'warning'];
        } catch (\Exception $e) {
            $health['cache'] = ['status' => 'error'];
        }

        // Storage health
        $health['storage'] = ['status' => is_writable(storage_path()) ? 'healthy' : 'warning'];

        // Memory usage
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        $health['memory'] = ['status' => $memoryUsage < 128 ? 'healthy' : ($memoryUsage < 256 ? 'warning' : 'error')];

        return $health;
    }

    /**
     * Get balance distribution
     */
    private function getBalanceDistribution()
    {
        return [
            'zero' => User::where('balance', 0)->count(),
            'low' => User::whereBetween('balance', [0.01, 100])->count(),
            'medium' => User::whereBetween('balance', [100.01, 1000])->count(),
            'high' => User::where('balance', '>', 1000)->count(),
        ];
    }

    /**
     * Get monthly financial trends
     */
    private function getMonthlyFinancialTrends()
    {
        $trends = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trends[] = [
                'month' => $date->format('M Y'),
                'total_balance' => User::whereYear('created_at', '<=', $date->year)
                    ->whereMonth('created_at', '<=', $date->month)
                    ->sum('balance'),
                'new_users_balance' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('balance'),
            ];
        }

        return $trends;
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
            'database_queries' => DB::getQueryLog(),
        ];
    }

    /**
     * Get storage information
     */
    private function getStorageInfo()
    {
        $storagePath = storage_path();

        return [
            'total_space' => disk_total_space($storagePath),
            'free_space' => disk_free_space($storagePath),
            'used_space' => disk_total_space($storagePath) - disk_free_space($storagePath),
        ];
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbSize = DB::select('SELECT SUM(data_length + index_length) / 1024 / 1024 AS "DB Size in MB" FROM information_schema.tables WHERE table_schema = DATABASE()')[0];

            return [
                'tables_count' => count($tables),
                'size_mb' => $dbSize->{'DB Size in MB'} ?? 0,
                'connection' => 'active',
            ];
        } catch (\Exception $e) {
            return [
                'tables_count' => 0,
                'size_mb' => 0,
                'connection' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($report, $filename)
    {
        // Implementation for Excel export
        return response()->json([
            'success' => true,
            'message' => __('Excel export feature will be implemented'),
            'filename' => $filename . '.xlsx',
        ]);
    }

    /**
     * Export to PDF
     */
    private function exportToPdf($report, $filename)
    {
        // Implementation for PDF export
        return response()->json([
            'success' => true,
            'message' => __('PDF export feature will be implemented'),
            'filename' => $filename . '.pdf',
        ]);
    }
}

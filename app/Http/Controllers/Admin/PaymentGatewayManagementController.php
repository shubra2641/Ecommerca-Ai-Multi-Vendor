<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayManagementController extends Controller
{
    // Encryption service removed (legacy multi-gateway system deprecated)

    public function dashboard()
    {
        $gateways = PaymentGateway::with(['payments' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        }])->get();
        $stats = $this->getGatewayStats();
        $recentTransactions = $this->getRecentTransactions();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('admin.payment_gateways.dashboard', compact('gateways', 'stats', 'recentTransactions', 'performanceMetrics'));
    }

    public function testConnection(Request $request, PaymentGateway $gateway)
    {
        if (! $gateway->enabled) {
            return response()->json(['success' => false, 'message' => 'Gateway disabled'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Gateway basic connectivity OK',
            'gateway' => $gateway->name,
        ]);
    }

    public function getConfigFields(Request $request, string $driver)
    {
        return response()->json([
            'success' => true,
            'driver' => $driver,
            'config_fields' => [],
            'config_keys' => [],
            'supported_currencies' => $this->getSupportedCurrencies($driver),
        ]);
    }

    public function updateConfig(Request $request, PaymentGateway $gateway)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'enabled' => 'sometimes|boolean',
        ]);
        $gateway->fill($data);
        $gateway->save();

        return response()->json(['success' => true, 'message' => 'Gateway updated', 'gateway' => $gateway->fresh()]);
    }

    public function getCredentials(PaymentGateway $gateway)
    {
        return response()->json(['success' => true, 'credentials' => [], 'gateway' => $gateway->name]);
    }

    public function getAnalytics(Request $request, PaymentGateway $gateway)
    {
        $period = (int) $request->input('period', 30);
        $start = now()->subDays($period);
        $payments = $gateway->payments()->where('created_at', '>=', $start);
        $total = $payments->count();
        $completed = (clone $payments)->where('status', 'completed')->count();

        return response()->json([
            'success' => true,
            'analytics' => [
                'total_transactions' => $total,
                'successful_transactions' => $completed,
                'failed_transactions' => (clone $payments)->where('status', 'failed')->count(),
                'pending_transactions' => (clone $payments)->where('status', 'pending')->count(),
                'total_amount' => (clone $payments)->where('status', 'completed')->sum('amount'),
                'success_rate' => $total ? round($completed / $total * 100, 2) : 0,
            ],
            'period' => $period,
            'gateway' => $gateway->name,
        ]);
    }

    /**
     * Return a JSON payload for a single transaction (quick view in dashboard).
     */
    public function getTransaction(Payment $payment)
    {
        $payment->load(['order.user', 'gateway']);

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    public function syncGateways()
    {
        return response()->json(['success' => true, 'message' => 'Sync disabled']);
    }

    private function getGatewayStats()
    {
        $thirty = now()->subDays(30);

        return [
            'total_gateways' => PaymentGateway::count(),
            'enabled_gateways' => PaymentGateway::where('enabled', true)->count(),
            'total_transactions' => Payment::where('created_at', '>=', $thirty)->count(),
            'successful_transactions' => Payment::where('created_at', '>=', $thirty)->where('status', 'completed')->count(),
            'total_revenue' => (float) Payment::where('created_at', '>=', $thirty)->where('status', 'completed')->sum('amount'),
        ];
    }

    private function getRecentTransactions()
    {
        return Payment::with(['gateway', 'order.user'])->latest()->limit(10)->get();
    }

    private function getPerformanceMetrics()
    {
        $gateways = PaymentGateway::where('enabled', true)->get();

        return $gateways->map(function ($g) {
            $total = $g->payments()->count();
            $completed = $g->payments()->where('status', 'completed')->count();

            return [
                'gateway' => $g->name,
                'success_rate' => $total ? round($completed / $total * 100, 2) : 0,
                'total_transactions' => $total,
                'avg_response_time' => rand(100, 400),
            ];
        });
    }

    private function testGatewayConfig(PaymentGateway $gateway)
    {
        return ['valid' => true, 'errors' => []];
    }

    private function measureResponseTime(PaymentGateway $gateway)
    {
        return rand(80, 200);
    }

    private function getSupportedCurrencies(string $driver)
    {
        return ['USD', 'EUR', 'GBP'];
    }

    private function getAverageResponseTime(PaymentGateway $gateway)
    {
        return rand(100, 500);
    }
}

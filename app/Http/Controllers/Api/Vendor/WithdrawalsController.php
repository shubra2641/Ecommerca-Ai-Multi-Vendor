<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use App\Services\VendorWithdrawalService;
use App\Services\WithdrawalSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class WithdrawalsController extends Controller
{
    public function index(Request $r)
    {
        $userId = $r->user()->id;
        $query = VendorWithdrawal::where('user_id', $userId)->latest();

        $page = $r->get('page', 1);
        $perPage = min($r->get('per_page', 20), 50); // Limit max per page

        $withdrawals = $query->paginate($perPage, ['*'], 'page', $page);

        // Add statistics
        $totalWithdrawn = VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingWithdrawals = VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount');

        $user = $r->user();
        $availableBalance = $user->balance ?? 0;

        // Load settings for withdrawal UI (gateways, minimum, commission)
        $withdrawalSettingsService = new WithdrawalSettingsService();
        $withdrawalSettings = $withdrawalSettingsService->getWithdrawalSettings();

        return response()->json([
            'success' => true,
            'data' => [
                'withdrawals' => array_map(function ($w) {
                    return [
                        'id' => $w->id,
                        'amount' => (float) $w->amount,
                        'gross_amount' => isset($w->gross_amount) ? (float) $w->gross_amount : null,
                        'commission_amount' => isset($w->commission_amount) ? (float) $w->commission_amount : null,
                        'currency' => $w->currency ?? 'USD',
                        'status' => $w->status,
                        'payment_method' => $w->payment_method,
                        'created_at' => $w->created_at?->toISOString(),
                        'approved_at' => $w->approved_at?->toISOString() ?? null,
                        'rejected_at' => $w->rejected_at?->toISOString() ?? null,
                        'notes' => $w->notes,
                    ];
                }, $withdrawals->items()),
                'pagination' => [
                    'current_page' => $withdrawals->currentPage(),
                    'last_page' => $withdrawals->lastPage(),
                    'per_page' => $withdrawals->perPage(),
                    'total' => $withdrawals->total(),
                ],
                'statistics' => [
                    'available_balance' => (float) $availableBalance,
                    'total_withdrawals' => (float) $totalWithdrawn,
                    'pending_withdrawals' => (float) $pendingWithdrawals,
                    'currency' => $user->currency ?? 'USD',
                ],
                'settings' => $withdrawalSettings,
            ],
        ]);
    }

    public function requestWithdrawal(Request $r)
    {
        $withdrawalSettingsService = new WithdrawalSettingsService();
        $withdrawalService = new VendorWithdrawalService();

        return $withdrawalService->requestWithdrawal($r, $withdrawalSettingsService);
    }

    public function cancelWithdrawal(Request $r, $withdrawalId)
    {
        $withdrawalService = new VendorWithdrawalService();

        return $withdrawalService->cancelWithdrawal($r, $withdrawalId);
    }
}

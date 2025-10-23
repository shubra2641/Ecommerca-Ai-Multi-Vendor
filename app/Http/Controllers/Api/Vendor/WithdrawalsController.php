<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use App\Services\WithdrawalSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalsController extends Controller
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
        // Get settings for validation
        $withdrawalSettingsService = new WithdrawalSettingsService();
        $setting = \App\Models\Setting::first();
        $min = $setting->min_withdrawal_amount ?? 1;
        $gatewaySlugs = $withdrawalSettingsService->getWithdrawalGatewaySlugs();

        // Validate request
        $data = $r->validate([
            'amount' => ['required', 'numeric', 'min:' . $min],
            'currency' => 'required|string',
            'payment_method' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($gatewaySlugs): void {
                    if (! in_array($value, $gatewaySlugs)) {
                        $fail('Invalid payment method');
                    }
                },
            ],
            'notes' => 'nullable|string|max:500',
            'transfer' => 'nullable|array',
        ]);

        $user = $r->user();

        // Check balance
        if ((float) $data['amount'] > (float) $user->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
            ], 400);
        }

        // Calculate commission
        $commissionSettings = $withdrawalSettingsService->getCommissionSettings();
        $commissionEnabled = $commissionSettings['enabled'];
        $commissionRate = $commissionSettings['rate'];
        $gross = (float) $data['amount'];
        $commissionExact = $commissionEnabled && $commissionRate > 0 ? $gross * $commissionRate / 100 : 0.0;
        $commissionAmount = $commissionEnabled && $commissionRate > 0 ? round($commissionExact, 2) : 0.0;
        $netAmount = max(0, $gross - $commissionAmount);

        // Update transfer details if provided
        if (isset($data['transfer']) && is_array($data['transfer'])) {
            $user->update(['transfer_details' => $data['transfer']]);
        }

        // Process withdrawal with transaction
        DB::beginTransaction();
        try {
            $previousBalance = (float) $user->balance;
            $newBalance = $previousBalance - $netAmount;
            $user->update(['balance' => $newBalance]);

            $w = VendorWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $netAmount,
                'gross_amount' => $gross,
                'commission_amount' => $commissionAmount,
                'commission_amount_exact' => $commissionExact,
                'currency' => $data['currency'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'],
                'reference' => strtoupper(bin2hex(random_bytes(4))),
                'admin_note' => $commissionAmount > 0
                    ? "Commission {$commissionRate}% potential ({$commissionAmount})"
                    : null,
                'held_at' => now(),
            ]);

            // Record balance history
            try {
                BalanceHistory::createTransaction(
                    $user,
                    BalanceHistory::TYPE_DEBIT,
                    $netAmount,
                    $previousBalance,
                    $newBalance,
                    "Withdrawal hold #{$w->id} (net {$netAmount} {$w->currency})",
                    $user->id,
                    $w
                );
            } catch (\Throwable $e) {
                logger()->warning('Failed logging withdrawal hold: ' . $e->getMessage());
            }

            DB::commit();

            // Notify admins
            try {
                $admins = \App\Models\User::admins()->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\AdminVendorWithdrawalCreated($w));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin withdrawal notification failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => $w,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create withdrawal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cancelWithdrawal(Request $r, $withdrawalId)
    {
        $withdrawal = VendorWithdrawal::where('user_id', $r->user()->id)
            ->where('id', $withdrawalId)
            ->where('status', 'pending')
            ->first();

        if (! $withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal not found or cannot be cancelled',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user = $r->user();
            $previousBalance = (float) $user->balance;
            $newBalance = $previousBalance + $withdrawal->amount;

            // Restore balance
            $user->update(['balance' => $newBalance]);

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'cancelled',
                'held_at' => null,
            ]);

            // Record balance history
            try {
                BalanceHistory::createTransaction(
                    $user,
                    BalanceHistory::TYPE_CREDIT,
                    $withdrawal->amount,
                    $previousBalance,
                    $newBalance,
                    "Withdrawal cancellation #{$withdrawal->id}",
                    $user->id,
                    $withdrawal
                );
            } catch (\Throwable $e) {
                logger()->warning('Failed logging withdrawal cancellation: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal cancelled successfully',
                'data' => $withdrawal,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel withdrawal: ' . $e->getMessage(),
            ], 500);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class VendorWithdrawalService
{
    public function requestWithdrawal(Request $r, WithdrawalSettingsService $settingsService)
    {
        // Get settings for validation
        $setting = \App\Models\Setting::first();
        $min = $setting->min_withdrawal_amount ?? 1;
        $gatewaySlugs = $settingsService->getWithdrawalGatewaySlugs();

        // Validate request
        $data = $r->validate([
            'amount' => ['required', 'numeric', 'min:' . $min],
            'currency' => 'required|string',
            'payment_method' => [
                'required',
                'string',
                function ($_attribute, $value, $fail) use ($gatewaySlugs): void {
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
        $commission = $this->calculateCommission($data['amount'], $settingsService);

        // Update transfer details if provided
        if (isset($data['transfer']) && is_array($data['transfer'])) {
            $user->update(['transfer_details' => $data['transfer']]);
        }

        // Process withdrawal
        return $this->processWithdrawal($user, $data, $commission);
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

    private function calculateCommission(float $gross, WithdrawalSettingsService $service): array
    {
        $commissionSettings = $service->getCommissionSettings();
        $commissionEnabled = $commissionSettings['enabled'];
        $commissionRate = $commissionSettings['rate'];
        $commissionExact = $commissionEnabled && $commissionRate > 0 ? $gross * $commissionRate / 100 : 0.0;
        $commissionAmount = $commissionEnabled && $commissionRate > 0 ? round($commissionExact, 2) : 0.0;
        $netAmount = max(0, $gross - $commissionAmount);

        return [
            'gross' => $gross,
            'net' => $netAmount,
            'commission_amount' => $commissionAmount,
            'commission_exact' => $commissionExact,
            'rate' => $commissionRate,
        ];
    }

    private function processWithdrawal($user, array $data, array $commission)
    {
        // Process withdrawal with transaction
        DB::beginTransaction();
        try {
            $previousBalance = (float) $user->balance;
            $newBalance = $previousBalance - $commission['net'];
            $user->update(['balance' => $newBalance]);

            $w = $this->createWithdrawalRecord($user, $data, $commission);

            $this->recordBalanceHistory($user, $commission['net'], $previousBalance, $newBalance, $w);

            DB::commit();

            $this->notifyAdmins($w);

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

    private function createWithdrawalRecord($user, array $data, array $commission): VendorWithdrawal
    {
        return VendorWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $commission['net'],
            'gross_amount' => $commission['gross'],
            'commission_amount' => $commission['commission_amount'],
            'commission_amount_exact' => $commission['commission_exact'],
            'currency' => $data['currency'],
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
            'payment_method' => $data['payment_method'],
            'reference' => strtoupper(bin2hex(random_bytes(4))),
            'admin_note' => $commission['commission_amount'] > 0
                ? "Commission {$commission['rate']}% potential ({$commission['commission_amount']})"
                : null,
            'held_at' => now(),
        ]);
    }

    private function recordBalanceHistory($user, float $netAmount, float $previousBalance, float $newBalance, VendorWithdrawal $w): void
    {
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
    }

    private function notifyAdmins(VendorWithdrawal $w): void
    {
        try {
            $admins = \App\Models\User::admins()->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminVendorWithdrawalCreated($w));
            }
        } catch (\Throwable $e) {
            logger()->warning('Admin withdrawal notification failed: ' . $e->getMessage());
        }
    }
}

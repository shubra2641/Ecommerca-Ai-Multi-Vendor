<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\PayoutExecuted;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalRejected;
use App\Models\BalanceHistory;
use App\Models\Payout;
use App\Models\User;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVendorWithdrawalService
{
    public function canProcessWithdrawal(VendorWithdrawal $withdrawal): bool
    {
        return $withdrawal->user->balance >= (float) $withdrawal->gross_amount;
    }

    public function approveWithdrawal(VendorWithdrawal $withdrawal, Request $request): void
    {
        $user = $withdrawal->user;

        // Deduct balance
        $this->deductVendorBalance($user, (float) $withdrawal->gross_amount);

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_note' => $request->input('admin_note'),
        ]);

        // Process commission
        $this->processCommission($withdrawal);

        // Create transaction record
        $this->createBalanceTransaction($user, $withdrawal, 'withdrawal_approved');

        // Fire event
        event(new WithdrawalApproved($withdrawal));
    }

    public function rejectWithdrawal(VendorWithdrawal $withdrawal, Request $request): void
    {
        // Update withdrawal status
        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
        ]);

        // Log the rejection
        $this->createBalanceTransaction($withdrawal->user, $withdrawal, 'withdrawal_rejected');

        // Fire event
        event(new WithdrawalRejected($withdrawal));
    }

    public function executePayout(Payout $payout, Request $request): void
    {
        $user = $payout->user;

        // Update payout status
        $payout->update([
            'status' => 'executed',
            'executed_at' => now(),
            'admin_note' => $request->input('admin_note'),
        ]);

        // Store proof if provided
        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('withdrawals/proofs', 'public');
            $payout->update(['proof_path' => $path]);
        }

        // Create transaction record
        $this->createBalanceTransaction($user, $payout->withdrawal, 'withdrawal_executed');

        // Update withdrawal status
        $this->completeWithdrawal($payout);

        // Fire event
        event(new PayoutExecuted($payout));
    }

    private function deductVendorBalance(User $user, float $amount): void
    {
        $user->decrement('balance', $amount);
    }

    private function processCommission(VendorWithdrawal $withdrawal): void
    {
        if ($withdrawal->commission_amount <= 0) {
            return;
        }

        $admin = User::find(1);
        if (! $admin) {
            return;
        }

        $commissionAmount = (float) ($withdrawal->commission_amount_exact ?? $withdrawal->commission_amount);
        $admin->increment('balance', $commissionAmount);

        BalanceHistory::createTransaction(
            $admin,
            'credit',
            $commissionAmount,
            (float) $admin->balance - $commissionAmount,
            (float) $admin->balance,
            __('Commission from withdrawal #:id', ['id' => $withdrawal->id]),
            Auth::id(),
            $withdrawal
        );
    }

    private function createBalanceTransaction(User $user, VendorWithdrawal $withdrawal, string $type): void
    {
        $description = "Withdrawal #{$withdrawal->id} {$type} - Amount: {$withdrawal->gross_amount} {$withdrawal->currency}";

        BalanceHistory::createTransaction(
            $user,
            $type,
            (float) $withdrawal->gross_amount,
            (float) $user->balance + (float) $withdrawal->gross_amount,
            (float) $user->balance,
            $description,
            Auth::id()
        );
    }

    private function completeWithdrawal(Payout $payout): void
    {
        $withdrawal = $payout->withdrawal;
        if (! $withdrawal) {
            return;
        }

        $withdrawal->update(['status' => 'completed']);

        // Copy proof path if not set
        if ($payout->proof_path && ! $withdrawal->proof_path) {
            $withdrawal->update(['proof_path' => $payout->proof_path]);
        }
    }
}

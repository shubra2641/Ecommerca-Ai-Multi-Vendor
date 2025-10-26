<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BalanceHistory;
use App\Models\User;

class BalanceService
{
    public function getStats(User $user): array
    {
        $totalAdded = $user->balanceHistories()->where('type', 'credit')->sum('amount');
        $totalDeducted = $user->balanceHistories()->where('type', 'debit')->sum('amount');

        return [
            'balance' => $user->balance,
            'total_added' => $totalAdded,
            'total_deducted' => $totalDeducted,
            'net_balance_change' => $totalAdded - $totalDeducted,
        ];
    }

    public function addBalance(User $user, float $amount, ?string $note = null, ?int $adminId = null): bool
    {
        $oldBalance = (float) $user->balance;
        $user->balance = $oldBalance + $amount;
        $user->save();

        BalanceHistory::createTransaction($user, 'credit', $amount, $oldBalance, (float) $user->balance, $note, $adminId);

        return true;
    }

    public function deductBalance(User $user, float $amount, ?string $note = null, ?int $adminId = null): bool
    {
        if ($amount > (float) $user->balance) {
            return false;
        }

        $oldBalance = (float) $user->balance;
        $user->balance = $oldBalance - $amount;
        $user->save();

        BalanceHistory::createTransaction($user, 'debit', $amount, $oldBalance, (float) $user->balance, $note, $adminId);

        return true;
    }

    public function getHistory(User $user, int $perPage = 20, int $page = 1)
    {
        return $user->balanceHistories()
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}

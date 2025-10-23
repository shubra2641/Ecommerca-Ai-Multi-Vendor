<?php

namespace App\Services;

use App\Models\BalanceHistory;
use App\Models\User;

class BalanceService
{
    /**
     * Get comprehensive balance statistics
     */
    public function getStats(User $user): array
    {
        $totalAdded = $user->balanceHistories()->whereIn('type', ['credit', 'bonus', 'refund'])->sum('amount');
        $totalDeducted = $user->balanceHistories()->whereIn('type', ['debit', 'penalty'])->sum('amount');
        $transactionCount = $user->balanceHistories()->count();
        $lastTransaction = $user->balanceHistories()->latest()->first();

        // Format values with currency symbol
        $defaultCurrency = \App\Models\Currency::getDefault();
        $symbol = $defaultCurrency ? $defaultCurrency->symbol : '$';

        return [
            'balance' => $user->balance,
            'total_added' => $totalAdded,
            'total_deducted' => $totalDeducted,
            'net_balance_change' => $totalAdded - $totalDeducted,
            'transaction_count' => $transactionCount,
            'last_transaction' => $lastTransaction ? $lastTransaction->created_at->format('Y-m-d H:i:s') : null,
            'formatted' => [
                'balance' => number_format($user->balance, 2).' '.$symbol,
                'total_added' => number_format($totalAdded, 2).' '.$symbol,
                'total_deducted' => number_format($totalDeducted, 2).' '.$symbol,
                'net_change' => number_format($totalAdded - $totalDeducted, 2).' '.$symbol,
            ],
        ];
    }

    /**
     * Add balance to user
     */
    public function addBalance(User $user, float $amount, ?string $note = null, ?int $adminId = null): array
    {
        $oldBalance = (float) $user->balance;
        $user->increment('balance', $amount);
        $user->refresh();

        // Log the transaction
        BalanceHistory::createTransaction(
            $user,
            'credit',
            $amount,
            $oldBalance,
            (float) $user->balance,
            $note,
            $adminId
        );

        return [
            'success' => true,
            'new_balance' => $user->balance,
            'formatted_balance' => number_format($user->balance, 2),
            'transaction' => [
                'type' => 'credit',
                'amount' => $amount,
                'note' => $note,
                'date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Deduct balance from user
     */
    public function deductBalance(User $user, float $amount, ?string $note = null, ?int $adminId = null): array
    {
        if ($amount > (float) $user->balance) {
            return [
                'success' => false,
                'message' => __('Amount exceeds current balance'),
            ];
        }

        $oldBalance = (float) $user->balance;
        $user->decrement('balance', $amount);
        $user->refresh();

        // Log the transaction
        BalanceHistory::createTransaction(
            $user,
            'debit',
            $amount,
            $oldBalance,
            (float) $user->balance,
            $note,
            $adminId
        );

        return [
            'success' => true,
            'new_balance' => $user->balance,
            'formatted_balance' => number_format($user->balance, 2),
            'transaction' => [
                'type' => 'debit',
                'amount' => $amount,
                'note' => $note,
                'date' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Get balance history with pagination
     */
    public function getHistory(User $user, int $perPage = 20, int $page = 1)
    {
        return $user->balanceHistories()
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Handle bulk balance operations
     */
    public function handleBulkOperation(array $userIds, string $operation, float $amount, ?string $note = null, ?int $adminId = null): array
    {
        $successCount = 0;
        $errorCount = 0;

        foreach ($userIds as $userId) {
            try {
                $user = User::findOrFail($userId);
                if ($operation === 'add') {
                    $this->addBalance($user, $amount, $note, $adminId);
                } else {
                    $result = $this->deductBalance($user, $amount, $note, $adminId);
                    if (! $result['success']) {
                        $errorCount++;

                        continue;
                    }
                }
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        return [
            'success' => $errorCount === 0,
            'summary' => [
                'total' => count($userIds),
                'success' => $successCount,
                'errors' => $errorCount,
            ],
        ];
    }
}

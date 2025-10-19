<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
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
        $setting = \App\Models\Setting::first();
        $minimumAmount = isset($setting->min_withdrawal_amount) ? (float) $setting->min_withdrawal_amount : 10.0;
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        if (is_string($rawGateways)) {
            $decoded = json_decode($rawGateways, true);
            if (is_array($decoded)) {
                $rawGateways = $decoded;
            } else {
                $rawGateways = array_filter(array_map('trim', preg_split('/\r?\n/', $rawGateways)));
            }
        }
        if (! is_array($rawGateways)) {
            $rawGateways = (array) $rawGateways;
        }
        $gateways = [];
        foreach ($rawGateways as $g) {
            if (is_array($g)) {
                $label = $g['label'] ?? ($g['name'] ?? null);
                if ($label) {
                    $slug = \Illuminate\Support\Str::slug($label);
                    $gateways[] = ['slug' => $slug, 'label' => $label];
                }

                continue;
            }

            if (is_numeric($g)) {
                $pg = \App\Models\PaymentGateway::find((int) $g);
                if ($pg) {
                    $gateways[] = [
                        'slug' => $pg->slug ?? \Illuminate\Support\Str::slug($pg->name ?? (string) $pg->id),
                        'label' => $pg->name ?? $pg->slug
                    ];

                    continue;
                }
            }

            if (is_string($g) && $g !== '') {
                $pg = \App\Models\PaymentGateway::where('slug', $g)->first();
                if ($pg) {
                    $gateways[] = ['slug' => $pg->slug, 'label' => $pg->name ?? $pg->slug];

                    continue;
                }

                $slug = \Illuminate\Support\Str::slug($g);
                $gateways[] = ['slug' => $slug, 'label' => $g];
            }
        }

        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);

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
                'settings' => [
                    'minimum_withdrawal' => $minimumAmount,
                    'withdrawal_gateways' => $gateways,
                    'withdrawal_commission_enabled' => $commissionEnabled,
                    'withdrawal_commission_rate' => $commissionRate,
                ],
            ],
        ]);
    }

    public function requestWithdrawal(Request $r)
    {
        // Get settings for validation
        $setting = \App\Models\Setting::first();
        $min = $setting->min_withdrawal_amount ?? 1;
        $allowedGateways = $setting->withdrawal_gateways ?? ['Bank Transfer', 'PayPal'];

        // Normalize gateways
        if (is_string($allowedGateways)) {
            $decoded = json_decode($allowedGateways, true);
            if (is_array($decoded)) {
                $allowedGateways = $decoded;
            } else {
                $allowedGateways = array_filter(array_map('trim', preg_split('/\r?\n/', $allowedGateways)));
            }
        }
        if (! is_array($allowedGateways)) {
            $allowedGateways = (array) $allowedGateways;
        }

        // Build gateway slugs for validation
        $gatewaySlugs = [];
        foreach ($allowedGateways as $gw) {
            if (is_array($gw)) {
                $label = $gw['label'] ?? ($gw['name'] ?? null);
                if ($label) {
                    $gatewaySlugs[] = \Illuminate\Support\Str::slug($label);
                }
            } elseif (is_string($gw) && $gw !== '') {
                $gatewaySlugs[] = \Illuminate\Support\Str::slug($gw);
            }
        }

        // Validate request
        $data = $r->validate([
            'amount' => ['required', 'numeric', 'min:' . $min],
            'currency' => 'required|string',
            'payment_method' => ['required', 'string', function ($attribute, $value, $fail) use ($gatewaySlugs) {
                if (! in_array($value, $gatewaySlugs)) {
                    $fail('Invalid payment method');
                }
            }],
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
        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);
        $gross = (float) $data['amount'];
        $commissionExact = $commissionEnabled && $commissionRate > 0 ? ($gross * ($commissionRate / 100)) : 0.0;
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
                'admin_note' => $commissionAmount > 0 ? "Commission {$commissionRate}% potential ({$commissionAmount})" : null,
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\Setting;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $withdrawals = VendorWithdrawal::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        $stats = $this->getStats($user);

        return view('vendor.withdrawals.index', array_merge($stats, [
            'withdrawals' => $withdrawals,
            'currentBalance' => $user->balance ?? 0,
            'currency' => $user->currency ?? 'USD',
        ]));
    }

    public function create()
    {
        $user = Auth::user();
        $setting = Setting::first();
        $gateways = $this->getWithdrawalGateways($setting);
        $stats = $this->getStats($user);

        $availableBalance = ($user->balance ?? 0) - ($stats['pendingAmount'] ?? 0);

        return view('vendor.withdrawals.create', array_merge($stats, [
            'user' => $user,
            'gateways' => $gateways,
            'availableBalance' => max(0, $availableBalance), // Ensure it doesn't go negative
            'currency' => $user->currency ?? 'USD',
            'minimumAmount' => $setting->min_withdrawal ?? 10,
            'commissionRate' => $setting->withdrawal_commission_rate ?? 0,
            'commissionEnabled' => ($setting->withdrawal_commission_rate ?? 0) > 0,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string'],
            'payment_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'transfer' => ['nullable', 'array'],
        ]);

        $user = Auth::user();
        $setting = Setting::first();

        $amount = (float) $request->input('amount');
        $commissionRate = $setting->withdrawal_commission_rate ?? 0;
        $commissionAmount = $amount * $commissionRate / 100;
        $netAmount = $amount - $commissionAmount;

        if ($user->balance < $amount) {
            return redirect()->back()->with('error', __('Insufficient balance.'));
        }

        DB::beginTransaction();
        try {
            $user->update(['balance' => $user->balance - $amount]);

            $withdrawal = VendorWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $netAmount,
                'gross_amount' => $amount,
                'commission_amount' => $commissionAmount,
                'currency' => $request->input('currency'),
                'status' => 'pending',
                'notes' => $request->input('notes'),
                'payment_method' => $request->input('payment_method'),
                'reference' => strtoupper(bin2hex(random_bytes(4))),
                'held_at' => now(),
            ]);

            $this->logTransaction($user, $withdrawal, $netAmount);

            if ($request->has('transfer') && is_array($request->input('transfer'))) {
                $user->update(['transfer_details' => json_encode($request->input('transfer'))]);
            }

            DB::commit();

            return redirect()->route('vendor.withdrawals.index')->with('success', __('Withdrawal request submitted successfully.'));
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', __('An error occurred. Please try again.'));
        }
    }

    public function show(VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('vendor.withdrawals.show', compact('withdrawal'));
    }

    private function getStats($user)
    {
        return [
            'totalWithdrawn' => VendorWithdrawal::where('user_id', $user->id)->where('status', 'completed')->sum('amount'),
            'pendingWithdrawals' => VendorWithdrawal::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'pendingAmount' => VendorWithdrawal::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'approvedThisMonth' => VendorWithdrawal::where('user_id', $user->id)->where('status', 'completed')
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'totalRequests' => VendorWithdrawal::where('user_id', $user->id)->count(),
        ];
    }

    private function getWithdrawalGateways($setting)
    {
        $gateways = $setting->withdrawal_gateways ?? ['bank-transfer' => ['label' => 'Bank Transfer', 'description' => 'Direct bank transfer to your account']];
        if (is_string($gateways)) {
            $gateways = json_decode($gateways, true) ?? ['bank-transfer' => ['label' => 'Bank Transfer', 'description' => 'Direct bank transfer to your account']];
        }

        // Ensure proper format for the view
        $formattedGateways = [];
        foreach ($gateways as $key => $value) {
            if (is_string($value)) {
                $slug = $key;
                $label = $value;
                $description = '';
            } elseif (is_array($value)) {
                $slug = $key;
                $label = $value['label'] ?? ucfirst(str_replace('-', ' ', $key));
                $description = $value['description'] ?? '';
            } else {
                continue;
            }
            $formattedGateways[$slug] = [
                'label' => $label,
                'description' => $description,
            ];
        }

        return $formattedGateways;
    }

    private function logTransaction($user, $withdrawal, $netAmount): void
    {
        try {
            BalanceHistory::createTransaction(
                $user,
                'debit',
                $netAmount,
                $user->balance + $netAmount,
                $user->balance,
                __('Withdrawal hold #:id (net :amount :currency)', [
                    'id' => $withdrawal->id,
                    'amount' => number_format($netAmount, 2),
                    'currency' => $withdrawal->currency,
                ]),
                Auth::id(),
                $withdrawal
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed logging withdrawal hold: ' . $e->getMessage());
        }
    }
}

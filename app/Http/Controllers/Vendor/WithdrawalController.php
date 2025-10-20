<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\WithdrawalRequest;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $heldOnly = request('held') === '1';
        
        $withdrawals = VendorWithdrawal::where('user_id', $userId)
            ->when($heldOnly, fn($q) => $q->whereNotNull('held_at'))
            ->latest()
            ->paginate(20)
            ->appends(['held' => $heldOnly ? '1' : null]);

        $stats = [
            'totalWithdrawn' => VendorWithdrawal::where('user_id', $userId)->where('status', 'completed')->sum('amount'),
            'pendingWithdrawals' => VendorWithdrawal::where('user_id', $userId)->where('status', 'pending')->sum('amount'),
            'pendingAmount' => VendorWithdrawal::where('user_id', $userId)->where('status', 'pending')->sum('amount'),
            'approvedThisMonth' => VendorWithdrawal::where('user_id', $userId)->where('status', 'approved')
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'totalRequests' => VendorWithdrawal::where('user_id', $userId)->count(),
        ];

        return view('vendor.withdrawals.index', array_merge($stats, [
            'withdrawals' => $withdrawals,
            'heldOnly' => $heldOnly,
            'currentBalance' => $user->balance ?? 0,
            'currency' => $user->currency ?? 'USD',
        ]));
    }

    public function create()
    {
        $user = Auth::user();
        $setting = Setting::first();
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        
        // Simplify gateway processing
        $gateways = [];
        if (is_string($rawGateways)) {
            $decoded = json_decode($rawGateways, true);
            $rawGateways = is_array($decoded) ? $decoded : array_filter(explode("\n", $rawGateways));
        }
        
        foreach ((array)$rawGateways as $gateway) {
            $label = is_array($gateway) ? ($gateway['label'] ?? $gateway['name'] ?? '') : $gateway;
            if ($label) {
                $gateways[\Illuminate\Support\Str::slug($label)] = is_array($gateway) ? $gateway + ['label' => $label] : ['label' => $label];
            }
        }

        return view('vendor.withdrawals.create', [
            'availableBalance' => (float) ($user->balance ?? 0),
            'currency' => $user->currency ?? 'USD',
            'pendingAmount' => VendorWithdrawal::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'minimumAmount' => (float) ($setting->min_withdrawal_amount ?? 10.0),
            'gateways' => $gateways,
            'commissionEnabled' => (bool) ($setting->withdrawal_commission_enabled ?? false),
            'commissionRate' => (float) ($setting->withdrawal_commission_rate ?? 0),
        ]);
    }

    public function store(WithdrawalRequest $request)
    {
        $user = Auth::user();
        $setting = Setting::first();
        
        // Simple validation
        $min = $setting->min_withdrawal_amount ?? 1;
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $min],
            'currency' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        // Check balance
        if ((float) $request->input('amount') > (float) $user->balance) {
            return back()->withErrors(['amount' => __('Insufficient balance')])->withInput();
        }

        // Calculate amounts
        $gross = (float) $request->input('amount');
        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);
        $commissionAmount = $commissionEnabled && $commissionRate > 0 ? round($gross * ($commissionRate / 100), 2) : 0.0;
        $netAmount = max(0, $gross - $commissionAmount);

        // Process withdrawal
        DB::beginTransaction();
        try {
            // Update balance
            DB::table('users')->where('id', $user->id)->update(['balance' => (float) $user->balance - $netAmount]);
            
            // Create withdrawal
            $withdrawal = VendorWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $netAmount,
                'gross_amount' => $gross,
                'commission_amount' => $commissionAmount,
                'commission_amount_exact' => $gross * ($commissionRate / 100),
                'currency' => $request->input('currency'),
                'status' => 'pending',
                'notes' => $request->input('notes'),
                'payment_method' => $request->input('payment_method'),
                'reference' => strtoupper(bin2hex(random_bytes(4))),
                'admin_note' => $commissionAmount > 0 ? __('Commission :rate% potential (:fee)', [
                    'rate' => $commissionRate,
                    'fee' => number_format($commissionAmount, 2)
                ]) : null,
                'held_at' => now(),
            ]);

            // Log transaction
            try {
                BalanceHistory::createTransaction(
                    $user,
                    BalanceHistory::TYPE_DEBIT,
                    $netAmount,
                    (float) $user->balance + $netAmount,
                    (float) $user->balance,
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

            // Update transfer details
            if ($request->has('transfer') && is_array($request->input('transfer'))) {
                DB::table('users')->where('id', $user->id)->update(['transfer_details' => json_encode($request->input('transfer'))]);
            }

            DB::commit();

            // Notify admins
            try {
                foreach (User::admins()->get() as $admin) {
                    $admin->notify(new \App\Notifications\AdminVendorWithdrawalCreated($withdrawal));
                }
            } catch (\Throwable $e) {
                logger()->warning('Admin withdrawal notification failed: ' . $e->getMessage());
            }

            return redirect()->route('vendor.withdrawals.index')->with('success', __('Withdrawal request submitted'));
            
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['amount' => __('Failed to create withdrawal: :msg', ['msg' => $e->getMessage()])])->withInput();
        }
    }

    public function receipt(VendorWithdrawal $withdrawal)
    {
        $this->authorize('view', $withdrawal);
        
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($withdrawal->status !== 'completed') {
            return redirect()->route('vendor.withdrawals.index')->with('error', __('Receipt available after completion'));
        }

        return view('vendor.withdrawals.receipt', [
            'withdrawal' => $withdrawal,
            'user' => Auth::user(),
        ]);
    }
}
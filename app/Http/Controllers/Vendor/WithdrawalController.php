<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\WithdrawalRequest;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $query = VendorWithdrawal::where('user_id', $userId)->latest();
        $heldOnly = request('held') === '1';
        if ($heldOnly) {
            $query->whereNotNull('held_at');
        }
        $withdrawals = $query->paginate(20)->appends(['held' => $heldOnly ? '1' : null]);

        // Calculate statistics for the dashboard cards
        $totalWithdrawn = VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingWithdrawals = VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingAmount = $pendingWithdrawals; // Alias for template consistency

        $approvedThisMonth = VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalRequests = VendorWithdrawal::where('user_id', $userId)->count();

        // Get user's current balance and currency
        $user = auth()->user();
        $currentBalance = $user->balance ?? 0;
        $currency = $user->currency ?? 'USD';

        return view('vendor.withdrawals.index', compact(
            'withdrawals',
            'totalWithdrawn',
            'pendingWithdrawals',
            'pendingAmount',
            'approvedThisMonth',
            'totalRequests',
            'currentBalance',
            'currency',
            'heldOnly'
        ));
    }

    public function create()
    {
        // Get user's current balance and currency (coerce to proper types)
        $user = auth()->user();
        $availableBalance = (float) ($user->balance ?? 0);
        $currency = $user->currency ?? 'USD';
        // Pending amount for sidebar summary
        $pendingAmount = \App\Models\VendorWithdrawal::where('user_id', $user->id)->where('status', 'pending')->sum('amount');

        // Load settings and ensure numeric minimum and normalized gateways
        $setting = \App\Models\Setting::first();
        $minimumAmount = isset($setting->min_withdrawal_amount) ? (float) $setting->min_withdrawal_amount : 10.0;
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        // If it's a JSON string from older saves, decode; else if plain string treat as newline list
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
        // Normalize into associative [slug => ['label'=>original]]
        $gateways = [];
        foreach ($rawGateways as $g) {
            if (is_array($g)) {
                // already structured: expect ['label'=>..., 'description'=>...] or similar
                $label = $g['label'] ?? (is_string($g['name'] ?? null) ? $g['name'] : '');
                if ($label) {
                    $slug = \Illuminate\Support\Str::slug($label);
                    $gateways[$slug] = $g + ['label' => $label];
                }
            } elseif (is_string($g) && $g !== '') {
                $slug = \Illuminate\Support\Str::slug($g);
                $gateways[$slug] = ['label' => $g];
            }
        }

        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0); // percentage e.g. 2.5

        return view('vendor.withdrawals.create', compact(
            'availableBalance',
            'currency',
            'minimumAmount',
            'gateways',
            'pendingAmount',
            'commissionEnabled',
            'commissionRate'
        ));
    }

    public function store(WithdrawalRequest $r)
    {
        $setting = \App\Models\Setting::first();
        $min = $setting->min_withdrawal_amount ?? 1;
        $allowedGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
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
        // Build slug whitelist
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

        $r->validate([
            'amount' => ['required', 'numeric', 'min:'.$min],
            'currency' => 'required|string',
            'payment_method' => ['required', 'string', function ($attribute, $value, $fail) use ($gatewaySlugs) {
                if (! in_array($value, $gatewaySlugs)) {
                    $fail(__('Invalid payment method'));
                }
            }],
        ]);
        $user = auth()->user();
        // basic balance check
        if ((float) $r->input('amount') > (float) $user->balance) {
            return back()->withErrors(['amount' => __('Insufficient balance')])->withInput();
        }
        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);
        $gross = (float) $r->input('amount');
        $commissionExact = $commissionEnabled && $commissionRate > 0 ? ($gross * ($commissionRate / 100)) : 0.0; // full precision
        $commissionAmount = $commissionEnabled && $commissionRate > 0 ? round($commissionExact, 2) : 0.0; // legacy 2dp
        $netAmount = max(0, $gross - $commissionAmount);
        // Persist transfer details to user account if provided
        if ($r->has('transfer') && is_array($r->input('transfer'))) {
            $user->update(['transfer_details' => $r->input('transfer')]);
        }

        // Hold (deduct) the net amount immediately so vendor can't reuse balance
        \DB::beginTransaction();
        try {
            $previousBalance = (float) $user->balance;
            $newBalance = $previousBalance - $netAmount;
            $user->update(['balance' => $newBalance]);

            $w = VendorWithdrawal::create([
                'user_id' => $user->id,
                'amount' => $netAmount, // stored net (held)
                'gross_amount' => $gross,
                'commission_amount' => $commissionAmount,
                'commission_amount_exact' => $commissionExact,
                'currency' => $r->input('currency'),
                'status' => 'pending',
                'notes' => $r->input('notes'),
                'payment_method' => $r->input('payment_method'),
                'reference' => strtoupper(bin2hex(random_bytes(4))),
                'admin_note' => $commissionAmount > 0 ? __('Commission :rate% potential (:fee)', ['rate' => $commissionRate, 'fee' => number_format($commissionAmount, 2)]) : null,
                'held_at' => now(),
            ]);

            // Record debit (hold)
            try {
                BalanceHistory::createTransaction(
                    $user,
                    BalanceHistory::TYPE_DEBIT,
                    $netAmount,
                    $previousBalance,
                    $newBalance,
                    __('Withdrawal hold #:id (net :amount :currency)', [
                        'id' => $w->id,
                        'amount' => number_format($netAmount, 2),
                        'currency' => $w->currency,
                    ]),
                    auth()->id(),
                    $w
                );
            } catch (\Throwable $e) {
                logger()->warning('Failed logging withdrawal hold: '.$e->getMessage());
            }


            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();

            return back()->withErrors(['amount' => __('Failed to create withdrawal: :msg', ['msg' => $e->getMessage()])])->withInput();
        }

        // Notify admins about new withdrawal request
        try {
            $admins = \App\Models\User::admins()->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminVendorWithdrawalCreated($w));
            }
        } catch (\Throwable $e) {
            logger()->warning('Admin withdrawal notification failed: '.$e->getMessage());
        }

        return redirect()->route('vendor.withdrawals.index')->with('success', __('Withdrawal request submitted'));
    }

    public function receipt(VendorWithdrawal $withdrawal)
    {
        $this->authorize('view', $withdrawal); // ensure policy exists; if not, simple owner check below
        if ($withdrawal->user_id !== auth()->id()) {
            abort(403);
        }
        if ($withdrawal->status !== 'completed') {
            return redirect()->route('vendor.withdrawals.index')->with('error', __('Receipt available after completion'));
        }

        return view('vendor.withdrawals.receipt', [
            'withdrawal' => $withdrawal,
            'user' => auth()->user(),
        ]);
    }
}

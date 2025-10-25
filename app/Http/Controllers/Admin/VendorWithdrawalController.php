<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\Payout;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;

class VendorWithdrawalController extends Controller
{
    public function index()
    {
        $query = VendorWithdrawal::with('user')->latest();
        $heldOnly = request('held') === '1';
        if ($heldOnly) {
            $query->whereNotNull('held_at');
        }
        $withdrawals = $query->paginate(30)->appends(['held' => $heldOnly ? '1' : null]);

        return view('admin.vendors.withdrawals.index', compact('withdrawals', 'heldOnly'));
    }

    public function show(VendorWithdrawal $withdrawal)
    {
        return view('admin.vendors.withdrawals.show', compact('withdrawal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        VendorWithdrawal::create($data);

        return back()->with('success', __('Withdrawal created'));
    }

    public function approve(Request $request, VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', __('Already processed'));
        }
        $user = $withdrawal->user;
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
            'proof' => 'nullable|image|max:5120',
        ]);
        // create payout record; actual balance deduction happens on execute
        $payout = Payout::create([
            'vendor_withdrawal_id' => $withdrawal->id,
            'user_id' => $user->id,
            'amount' => $withdrawal->amount,
            'currency' => $withdrawal->currency,
            'status' => 'pending',
            'admin_note' => $request->input('admin_note'),
        ]);
        // store proof if provided
        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('withdrawals/proofs', 'public');
            $payout->update(['proof_path' => $path]);
        }

        $withdrawal->update(['status' => 'approved', 'approved_at' => now(), 'admin_note' => $request->input('admin_note')]);

        // Credit commission immediately to admin (user id 1) if commission exists
        if ($withdrawal->commission_amount > 0) {
            $admin = \App\Models\User::find(1);
            if ($admin) {
                $prev = (float) $admin->balance;
                $commissionCredit = (float) ($withdrawal->commission_amount_exact ?? $withdrawal->commission_amount);
                $new = $prev + $commissionCredit;
                $admin->update(['balance' => $new]);
                try {
                    BalanceHistory::createTransaction(
                        $admin,
                        BalanceHistory::TYPE_CREDIT,
                        $commissionCredit,
                        $prev,
                        $new,
                        __('Commission from withdrawal #:id', ['id' => $withdrawal->id]),
                        auth()->id(),
                        $withdrawal
                    );
                } catch (\Throwable $e) {
                    logger()->warning('Failed to credit commission history: ' . $e->getMessage());
                }
            }
        }

        // Record an adjustment transaction (no balance change yet) for Recent Transactions
        try {
            BalanceHistory::createTransaction(
                $user,
                BalanceHistory::TYPE_ADJUSTMENT,
                0.00,
                (float) $user->balance,
                (float) $user->balance,
                __('Withdrawal #:id approved (amount :amount :currency)', [
                    'id' => $withdrawal->id,
                    'amount' => number_format($withdrawal->amount, 2),
                    'currency' => $withdrawal->currency,
                ]),
                auth()->id(),
                $withdrawal
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed to log approval transaction: ' . $e->getMessage());
        }

        // Notify vendor via DB notification
        try {
            $withdrawal->user->notify(new \App\Notifications\VendorWithdrawalStatusUpdated($withdrawal, 'approved'));
        } catch (\Throwable $e) {
            logger()->warning('Vendor notification failed: ' . $e->getMessage());
        }

        // Also notify admins (audit) if needed
        return back()->with('success', __('Withdrawal approved and payout created'));
    }

    public function reject(Request $request, VendorWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', __('Already processed'));
        }
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
            'proof' => 'nullable|image|max:5120',
        ]);
        $withdrawal->update(['status' => 'rejected', 'admin_note' => $request->input('admin_note')]);
        // Refund held net amount back to vendor balance
        $vendor = $withdrawal->user;
        if ($vendor) {
            $prev = (float) $vendor->balance;
            $new = $prev + (float) $withdrawal->amount; // amount is net stored
            $vendor->update(['balance' => $new]);
            try {
                BalanceHistory::createTransaction(
                    $withdrawal->user,
                    BalanceHistory::TYPE_ADJUSTMENT,
                    0.00,
                    (float) $withdrawal->user->balance,
                    (float) $withdrawal->user->balance,
                    __('Withdrawal #:id rejected', ['id' => $withdrawal->id]),
                    auth()->id(),
                    $withdrawal
                );
            } catch (\Throwable $e) {
                logger()->warning('Failed to log rejection transaction: ' . $e->getMessage());
            }
        }

        return back()->with('success', __('Withdrawal rejected'));
    }

    public function execute(Request $request, Payout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', __('Payout already processed'));
        }
        $user = $payout->user;
        // At execution, we assume funds held; just validate that the withdrawal still exists
        $previous = (float) $user->balance; // current balance shouldn't decrease now
        $new = $previous; // unchanged
        $payout->update(['status' => 'executed', 'executed_at' => now(), 'admin_note' => $request->input('admin_note')]);
        $request->validate(['admin_note' => 'nullable|string|max:1000', 'proof' => 'nullable|image|max:5120']);

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('withdrawals/proofs', 'public');
            $payout->update(['proof_path' => $path]);
        }
        try {
            BalanceHistory::createTransaction(
                $user,
                BalanceHistory::TYPE_ADJUSTMENT,
                0.00,
                $previous,
                $new,
                __(
                    'Payout executed for withdrawal #:id (funds were previously held)',
                    ['id' => $payout->withdrawal?->id ?? $payout->id]
                ),
                auth()->id(),
                $payout
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed to record balance history for payout ' . $payout->id . ': ' . $e->getMessage());
        }
        // mark withdrawal completed
        $withdrawal = $payout->withdrawal;
        if ($withdrawal) {
            $withdrawal->update(['status' => 'completed']);
            // copy proof path to withdrawal if payout has it
            if (! empty($payout->proof_path) && empty($withdrawal->proof_path)) {
                $withdrawal->update(['proof_path' => $payout->proof_path]);
            }
            try {
                $withdrawal->user->notify(
                    new \App\Notifications\VendorWithdrawalStatusUpdated(
                        $withdrawal,
                        'executed'
                    )
                );
            } catch (\Throwable $e) {
                logger()->warning('Vendor notification failed: ' . $e->getMessage());
            }
        }
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\PayoutExecuted($payout));
        } catch (\Throwable $e) {
            logger()->warning('Failed to queue payout executed mail: ' . $e->getMessage());
        }

        return back()->with('success', __('Payout executed'));
    }

    public function payoutsShow(Payout $payout)
    {
        return view('admin.vendors.payouts.show', compact('payout'));
    }

    public function payoutsIndex()
    {
        $payouts = Payout::with('user')->latest()->paginate(30);

        return view('admin.vendors.payouts.index', compact('payouts'));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceHistory;
use App\Models\Payout;
use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
            'proof' => 'nullable|image|max:5120',
        ]);

        // Check balance and process withdrawal
        if (!$this->canProcessWithdrawal($withdrawal)) {
            return back()->with('error', __('Vendor has insufficient balance'));
        }

        $this->processWithdrawalApproval($withdrawal, $request);

        return back()->with('success', __('Withdrawal approved and payout created'));
    }

    private function canProcessWithdrawal(VendorWithdrawal $withdrawal): bool
    {
        return $withdrawal->user->balance >= $withdrawal->gross_amount;
    }

    private function processWithdrawalApproval(VendorWithdrawal $withdrawal, Request $request): void
    {
        $user = $withdrawal->user;

        // Deduct balance
        $this->deductVendorBalance($user, $withdrawal->gross_amount);

        // Create payout
        $payout = $this->createPayoutRecord($withdrawal, $request);

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_note' => $request->input('admin_note')
        ]);

        // Process commission
        $this->processCommission($withdrawal);

        // Create transaction record
        $this->createBalanceTransaction($user, $withdrawal, 'withdrawal_approved');

        // Send notifications
        $this->notifyVendor($withdrawal, 'approved');
    }

    private function deductVendorBalance($user, float $amount): void
    {
        $user->decrement('balance', $amount);
    }

    private function createPayoutRecord(VendorWithdrawal $withdrawal, Request $request): Payout
    {
        $payout = Payout::create([
            'vendor_withdrawal_id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'amount' => $withdrawal->amount,
            'currency' => $withdrawal->currency,
            'status' => 'pending',
            'admin_note' => $request->input('admin_note'),
        ]);

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('withdrawals/proofs', 'public');
            $payout->update(['proof_path' => $path]);
        }

        return $payout;
    }

    private function processCommission(VendorWithdrawal $withdrawal): void
    {
        if ($withdrawal->commission_amount <= 0) {
            return;
        }

        $admin = \App\Models\User::find(1);
        if (!$admin) {
            return;
        }

        $commissionAmount = (float) ($withdrawal->commission_amount_exact ?? $withdrawal->commission_amount);
        $admin->increment('balance', $commissionAmount);

        try {
            BalanceHistory::createTransaction(
                $admin,
                'credit',
                $commissionAmount,
                $admin->balance - $commissionAmount,
                $admin->balance,
                __('Commission from withdrawal #:id', ['id' => $withdrawal->id]),
                Auth::id(),
                $withdrawal
            );
        } catch (\Throwable $e) {
            logger()->warning('Failed to credit commission history: ' . $e->getMessage());
        }
    }

    private function createBalanceTransaction($user, VendorWithdrawal $withdrawal, string $type): void
    {
        BalanceHistory::createTransaction(
            $user,
            $type,
            (float) $withdrawal->gross_amount,
            $user->balance + $withdrawal->gross_amount,
            $user->balance,
            "Withdrawal #{$withdrawal->id} {$type} - Amount: {$withdrawal->gross_amount} {$withdrawal->currency}",
            Auth::id()
        );
    }

    private function notifyVendor(VendorWithdrawal $withdrawal, string $status): void
    {
        try {
            $withdrawal->user->notify(new \App\Notifications\VendorWithdrawalStatusUpdated($withdrawal, $status));
        } catch (\Throwable $e) {
            logger()->warning('Vendor notification failed: ' . $e->getMessage());
        }
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

        $this->processWithdrawalRejection($withdrawal, $request);

        return back()->with('success', __('Withdrawal rejected'));
    }

    private function processWithdrawalRejection(VendorWithdrawal $withdrawal, Request $request): void
    {
        // Update withdrawal status
        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note')
        ]);

        // Log the rejection
        $this->createBalanceTransaction($withdrawal->user, $withdrawal, 'withdrawal_rejected');
    }

    public function execute(Request $request, Payout $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', __('Payout already processed'));
        }

        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
            'proof' => 'nullable|image|max:5120'
        ]);

        $this->processPayoutExecution($payout, $request);

        return back()->with('success', __('Payout executed'));
    }

    private function processPayoutExecution(Payout $payout, Request $request): void
    {
        $user = $payout->user;

        // Update payout status
        $payout->update([
            'status' => 'executed',
            'executed_at' => now(),
            'admin_note' => $request->input('admin_note')
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

        // Send notifications
        $this->sendExecutionNotifications($payout);
    }

    private function completeWithdrawal(Payout $payout): void
    {
        $withdrawal = $payout->withdrawal;
        if (!$withdrawal) {
            return;
        }

        $withdrawal->update(['status' => 'completed']);

        // Copy proof path if not set
        if (!empty($payout->proof_path) && empty($withdrawal->proof_path)) {
            $withdrawal->update(['proof_path' => $payout->proof_path]);
        }
    }

    private function sendExecutionNotifications(Payout $payout): void
    {
        $withdrawal = $payout->withdrawal;
        $user = $payout->user;

        // Notify vendor
        if ($withdrawal) {
            $this->notifyVendor($withdrawal, 'executed');
        }

        // Send email notification
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\PayoutExecuted($payout));
        } catch (\Throwable $e) {
            logger()->warning('Failed to queue payout executed mail: ' . $e->getMessage());
        }
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

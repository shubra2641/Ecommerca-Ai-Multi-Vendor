<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Events\PayoutExecuted;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalRejected;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveWithdrawalRequest;
use App\Http\Requests\Admin\ExecutePayoutRequest;
use App\Http\Requests\Admin\RejectWithdrawalRequest;
use App\Models\Payout;
use App\Models\VendorWithdrawal;
use App\Services\AdminVendorWithdrawalService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class VendorWithdrawalController extends Controller
{
    public function __construct(
        private readonly AdminVendorWithdrawalService $withdrawalService
    ) {
    }

    public function index(): View
    {
        $query = VendorWithdrawal::with('user')->latest();
        $heldOnly = request('held') === '1';
        if ($heldOnly) {
            $query->whereNotNull('held_at');
        }
        $withdrawals = $query->paginate(30)->appends(['held' => $heldOnly ? '1' : null]);

        return view('admin.vendors.withdrawals.index', compact('withdrawals', 'heldOnly'));
    }

    public function show(VendorWithdrawal $withdrawal): View
    {
        return view('admin.vendors.withdrawals.show', compact('withdrawal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        VendorWithdrawal::create($data);

        return back()->with('success', __('Withdrawal created'));
    }

    public function approve(ApproveWithdrawalRequest $request, VendorWithdrawal $withdrawal): RedirectResponse
    {
        if (! $this->withdrawalService->canProcessWithdrawal($withdrawal)) {
            return back()->with('error', __('Vendor has insufficient balance'));
        }

        $this->withdrawalService->approveWithdrawal($withdrawal, $request->validated());
        WithdrawalApproved::dispatch($withdrawal);

        return back()->with('success', __('Withdrawal approved and payout created'));
    }

    public function reject(RejectWithdrawalRequest $request, VendorWithdrawal $withdrawal): RedirectResponse
    {
        $this->withdrawalService->rejectWithdrawal($withdrawal, $request->validated());
        WithdrawalRejected::dispatch($withdrawal);

        return back()->with('success', __('Withdrawal rejected'));
    }

    public function execute(ExecutePayoutRequest $request, Payout $payout): RedirectResponse
    {
        $this->withdrawalService->executePayout($payout, $request->validated());
        PayoutExecuted::dispatch($payout);

        return back()->with('success', __('Payout executed'));
    }

    public function payoutsShow(Payout $payout): View
    {
        return view('admin.vendors.payouts.show', compact('payout'));
    }

    public function payoutsIndex(): View
    {
        $payouts = Payout::with('user')
            ->latest()
            ->paginate(30);

        return view('admin.vendors.payouts.index', compact('payouts'));
    }
}

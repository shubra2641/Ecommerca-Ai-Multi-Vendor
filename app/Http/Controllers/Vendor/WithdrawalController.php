<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\WithdrawalRequest;
use App\Models\BalanceHistory;
use App\Models\VendorWithdrawal;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $withdrawals = $this->getWithdrawals($user->id);
        $stats = $this->getWithdrawalStats($user->id);
        
        return view('vendor.withdrawals.index', array_merge($withdrawals, $stats, [
            'user' => $user,
            'currentBalance' => $user->balance ?? 0,
            'currency' => $user->currency ?? 'USD',
        ]));
    }

    public function create()
    {
        $user = Auth::user();
        $setting = $this->getSettings();
        
        return view('vendor.withdrawals.create', [
            'user' => $user,
            'availableBalance' => (float) ($user->balance ?? 0),
            'currency' => $user->currency ?? 'USD',
            'pendingAmount' => $this->getPendingAmount($user->id),
            'minimumAmount' => (float) ($setting->min_withdrawal_amount ?? 10.0),
            'gateways' => $this->getGateways($setting),
            'commissionEnabled' => (bool) ($setting->withdrawal_commission_enabled ?? false),
            'commissionRate' => (float) ($setting->withdrawal_commission_rate ?? 0),
        ]);
    }

    public function store(WithdrawalRequest $request)
    {
        $user = Auth::user();
        $setting = $this->getSettings();
        
        // Validate request
        $this->validateWithdrawal($request, $setting);
        
        // Check balance
        if (!$this->checkBalance($user, $request->input('amount'))) {
            return back()->withErrors(['amount' => __('Insufficient balance')])->withInput();
        }

        // Calculate amounts
        $amounts = $this->calculateAmounts($request->input('amount'), $setting);
        
        // Process withdrawal
        return $this->processWithdrawal($user, $request, $amounts);
    }

    public function receipt(VendorWithdrawal $withdrawal)
    {
        $this->authorize('view', $withdrawal);
        
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($withdrawal->status !== 'completed') {
            return redirect()->route('vendor.withdrawals.index')
                ->with('error', __('Receipt available after completion'));
        }

        return view('vendor.withdrawals.receipt', [
            'withdrawal' => $withdrawal,
            'user' => Auth::user(),
        ]);
    }

    private function getWithdrawals($userId)
    {
        $heldOnly = request('held') === '1';
        $query = VendorWithdrawal::where('user_id', $userId)->latest();
        
        if ($heldOnly) {
            $query->whereNotNull('held_at');
        }
        
        return [
            'withdrawals' => $query->paginate(20)->appends(['held' => $heldOnly ? '1' : null]),
            'heldOnly' => $heldOnly,
        ];
    }

    private function getWithdrawalStats($userId)
    {
        return [
            'totalWithdrawn' => VendorWithdrawal::where('user_id', $userId)
                ->where('status', 'completed')->sum('amount'),
            'pendingWithdrawals' => VendorWithdrawal::where('user_id', $userId)
                ->where('status', 'pending')->sum('amount'),
            'pendingAmount' => VendorWithdrawal::where('user_id', $userId)
                ->where('status', 'pending')->sum('amount'),
            'approvedThisMonth' => VendorWithdrawal::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'totalRequests' => VendorWithdrawal::where('user_id', $userId)->count(),
        ];
    }

    private function getSettings()
    {
        return Setting::first();
    }

    private function getPendingAmount($userId)
    {
        return VendorWithdrawal::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount');
    }

    private function getGateways($setting)
    {
        $rawGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        $gateways = $this->normalizeGateways($rawGateways);
        
        $result = [];
        foreach ($gateways as $gateway) {
            $label = $this->getGatewayLabel($gateway);
            if ($label) {
                $slug = \Illuminate\Support\Str::slug($label);
                $result[$slug] = is_array($gateway) ? $gateway + ['label' => $label] : ['label' => $label];
            }
        }
        
        return $result;
    }

    private function normalizeGateways($rawGateways)
    {
        if (is_string($rawGateways)) {
            $decoded = json_decode($rawGateways, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return array_filter(array_map('trim', preg_split('/\r?\n/', $rawGateways)));
        }
        
        return is_array($rawGateways) ? $rawGateways : [$rawGateways];
    }

    private function getGatewayLabel($gateway)
    {
        if (is_array($gateway)) {
            return $gateway['label'] ?? $gateway['name'] ?? '';
        }
        return is_string($gateway) && $gateway !== '' ? $gateway : '';
    }

    private function validateWithdrawal($request, $setting)
    {
        $min = $setting->min_withdrawal_amount ?? 1;
        $allowedGateways = $this->getAllowedGateways($setting);
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $min],
            'currency' => 'required|string',
            'payment_method' => ['required', 'string', function ($attribute, $value, $fail) use ($allowedGateways) {
                if (!in_array($value, $allowedGateways)) {
                    $fail(__('Invalid payment method'));
                }
            }],
        ]);
    }

    private function getAllowedGateways($setting)
    {
        $allowedGateways = $setting->withdrawal_gateways ?? ['Bank Transfer'];
        $gateways = $this->normalizeGateways($allowedGateways);
        
        $slugs = [];
        foreach ($gateways as $gateway) {
            $label = $this->getGatewayLabel($gateway);
            if ($label) {
                $slugs[] = \Illuminate\Support\Str::slug($label);
            }
        }
        
        return $slugs;
    }

    private function checkBalance($user, $amount)
    {
        return (float) $amount <= (float) $user->balance;
    }

    private function calculateAmounts($gross, $setting)
    {
        $commissionEnabled = (bool) ($setting->withdrawal_commission_enabled ?? false);
        $commissionRate = (float) ($setting->withdrawal_commission_rate ?? 0);
        
        $commissionExact = $commissionEnabled && $commissionRate > 0 
            ? ($gross * ($commissionRate / 100)) 
            : 0.0;
        
        $commissionAmount = $commissionEnabled && $commissionRate > 0 
            ? round($commissionExact, 2) 
            : 0.0;
        
        $netAmount = max(0, $gross - $commissionAmount);
        
        return [
            'gross' => (float) $gross,
            'commission_exact' => $commissionExact,
            'commission_amount' => $commissionAmount,
            'net_amount' => $netAmount,
            'commission_rate' => $commissionRate,
        ];
    }

    private function processWithdrawal($user, $request, $amounts)
    {
        DB::beginTransaction();
        
        try {
            // Update user balance
            $this->updateUserBalance($user, $amounts['net_amount']);
            
            // Create withdrawal record
            $withdrawal = $this->createWithdrawal($user, $request, $amounts);
            
            // Log transaction
            $this->logTransaction($user, $withdrawal, $amounts['net_amount']);
            
            // Update transfer details if provided
            $this->updateTransferDetails($user, $request);
            
            DB::commit();
            
            // Notify admins
            $this->notifyAdmins($withdrawal);
            
            return redirect()->route('vendor.withdrawals.index')
                ->with('success', __('Withdrawal request submitted'));
                
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'amount' => __('Failed to create withdrawal: :msg', ['msg' => $e->getMessage()])
            ])->withInput();
        }
    }

    private function updateUserBalance($user, $netAmount)
    {
        $previousBalance = (float) $user->balance;
        $newBalance = $previousBalance - $netAmount;
        $user->update(['balance' => $newBalance]);
    }

    private function createWithdrawal($user, $request, $amounts)
    {
        return VendorWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $amounts['net_amount'],
            'gross_amount' => $amounts['gross'],
            'commission_amount' => $amounts['commission_amount'],
            'commission_amount_exact' => $amounts['commission_exact'],
            'currency' => $request->input('currency'),
            'status' => 'pending',
            'notes' => $request->input('notes'),
            'payment_method' => $request->input('payment_method'),
            'reference' => strtoupper(bin2hex(random_bytes(4))),
            'admin_note' => $amounts['commission_amount'] > 0
                ? __('Commission :rate% potential (:fee)', [
                    'rate' => $amounts['commission_rate'],
                    'fee' => number_format($amounts['commission_amount'], 2)
                ])
                : null,
            'held_at' => now(),
        ]);
    }

    private function logTransaction($user, $withdrawal, $netAmount)
    {
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
    }

    private function updateTransferDetails($user, $request)
    {
        if ($request->has('transfer') && is_array($request->input('transfer'))) {
            $user->update(['transfer_details' => $request->input('transfer')]);
        }
    }

    private function notifyAdmins($withdrawal)
    {
        try {
            $admins = User::admins()->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminVendorWithdrawalCreated($withdrawal));
            }
        } catch (\Throwable $e) {
            logger()->warning('Admin withdrawal notification failed: ' . $e->getMessage());
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdjustBalanceRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseAdminController
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function index(Request $request)
    {
        return view('admin.users.index', [
            'users' => $this->getUsers(),
            'userStats' => $this->getUserStats(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(StoreUserRequest $request)
    {
        User::create($this->prepareUserData($request->validated()));

        return redirect()->route('admin.users.index')->with('success', __('User created successfully.'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($this->prepareUserData($request->validated(), $user));

        return redirect()->route('admin.users.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', __('You cannot delete your own account.'));
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted successfully.'));
    }

    public function approve(User $user)
    {
        $user->update(['approved_at' => now()]);

        return redirect()->back()->with('success', __('User approved successfully.'));
    }

    public function pending()
    {
        return view('admin.users.pending', [
            'users' => User::whereNull('approved_at')->latest()->paginate(15),
        ]);
    }

    public function status($status, $role = null)
    {
        return view('admin.users.index', [
            'users' => $this->getUsersByStatus($status, $role),
            'title' => ucfirst($status) . ($role ? ' ' . ucfirst($role) . 's' : ' Users'),
        ]);
    }

    public function balances()
    {
        return view('admin.balances.index', [
            'users' => User::select('name', 'email', 'role', 'balance')->paginate(20),
        ]);
    }

    public function bulkApprove(Request $request)
    {
        User::whereIn('id', $request->input('ids', []))
            ->whereNull('approved_at')
            ->update(['approved_at' => now()]);

        return redirect()->back()->with('success', __('Users approved successfully'));
    }

    public function bulkDelete(Request $request)
    {
        User::whereIn('id', $request->input('ids', []))->delete();

        return redirect()->back()->with('success', __('Users deleted successfully'));
    }

    public function balance(User $user)
    {
        return view('admin.users.balance', [
            'user' => $user,
            'defaultCurrency' => \App\Helpers\GlobalHelper::getCurrencyContext()['defaultCurrency'],
            'balanceStats' => $this->balanceService->getStats($user),
            'recentTransactions' => $user->balanceHistories()->with('admin')->latest()->take(3)->get(),
        ]);
    }

    public function addBalance(AdjustBalanceRequest $request, User $user)
    {
        $this->balanceService->addBalance(
            $user,
            (float) $request->validated()['amount'],
            $request->validated()['note'] ?? null,
            Auth::id()
        );

        return redirect()->back()->with('success', __('Balance added successfully'));
    }

    public function deductBalance(AdjustBalanceRequest $request, User $user)
    {
        $result = $this->balanceService->deductBalance(
            $user,
            (float) $request->validated()['amount'],
            $request->validated()['note'] ?? null,
            Auth::id()
        );

        if (! $result) {
            return redirect()->back()->with('error', __('Amount exceeds current balance'));
        }

        return redirect()->back()->with('success', __('Balance deducted successfully'));
    }

    public function getBalanceStats(User $user)
    {
        return $this->successResponse(__('Balance statistics retrieved'), $this->balanceService->getStats($user));
    }

    public function getBalanceHistory(User $user, Request $request)
    {
        $params = $this->getPaginationParams($request);
        $balanceHistories = $this->balanceService->getHistory($user, $params['per_page'], $params['page']);

        return view('admin.users.balance-history', compact('user', 'balanceHistories'));
    }

    protected function getUsers()
    {
        return User::latest()->paginate(15);
    }

    protected function getUserStats()
    {
        return Cache::remember('user_stats', 600, function () {
            return [
                'total_users' => User::count(),
                'total_vendors' => User::where('role', 'vendor')->count(),
                'total_customers' => User::where('role', 'user')->count(),
                'pending_approvals' => User::whereNull('approved_at')->count(),
            ];
        });
    }

    protected function getUsersByStatus($status, $role = null)
    {
        $query = User::query();

        if ($status === 'approved') {
            $query->whereNotNull('approved_at');
        } elseif ($status === 'pending') {
            $query->whereNull('approved_at');
        }

        if ($role) {
            $query->where('role', $role);
        }

        return $query->paginate(15);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareUserData(array $validated, ?User $_user = null)
    {
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'role' => $validated['role'],
            'balance' => $validated['balance'] ?? 0,
            'approved_at' => isset($validated['approved']) ? now() : null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        return $data;
    }
}

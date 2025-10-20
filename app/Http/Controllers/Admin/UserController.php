<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersBalanceExport;
use App\Http\Requests\Admin\AdjustBalanceRequest;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\HtmlSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends BaseAdminController
{
    protected BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function index(Request $request)
    {
        $users = $this->getUsers($request);
        $userStats = $this->getUserStats();
        return view('admin.users.index', compact('users', 'userStats'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(\App\Http\Requests\Admin\StoreUserRequest $request, HtmlSanitizer $sanitizer)
    {
        $data = $this->prepareUserData($request->validated(), $sanitizer);
        User::create($data);
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

    public function update(\App\Http\Requests\Admin\UpdateUserRequest $request, User $user, HtmlSanitizer $sanitizer)
    {
        $data = $this->prepareUserData($request->validated(), $sanitizer, $user);
        $user->update($data);
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
        $users = User::whereNull('approved_at')->latest()->paginate(15);
        return view('admin.users.pending', compact('users'));
    }

    public function status($status, $role = null)
    {
        $users = $this->getUsersByStatus($status, $role);
        $title = ucfirst($status) . ($role ? ' ' . ucfirst($role) . 's' : ' Users');
        return view('admin.users.index', compact('users', 'title'));
    }

    public function balances()
    {
        $users = User::select('name', 'email', 'role', 'balance')->paginate(20);
        return view('admin.balances.index', compact('users'));
    }

    public function export(Request $request)
    {
        $users = User::select('name', 'email', 'role', 'balance')->get();
        $format = $request->query('format', 'xlsx');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.balances', compact('users'));
            return $pdf->download('user_balances.pdf');
        }

        return Excel::download(new UsersBalanceExport($users), 'user_balances.xlsx');
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
            'defaultCurrency' => \App\Models\Currency::getDefault()
        ]);
    }

    public function addBalance(AdjustBalanceRequest $request, User $user)
    {
        $result = $this->balanceService->addBalance(
            $user,
            (float) $request->validated()['amount'],
            $request->validated()['note'] ?? null,
            Auth::id()
        );

        return $this->successResponse(__('Balance added successfully'), [
            'new_balance' => $result['new_balance'],
            'formatted_balance' => $result['formatted_balance'],
            'transaction' => array_merge($result['transaction'], ['admin' => Auth::user()->name]),
        ]);
    }

    public function deductBalance(AdjustBalanceRequest $request, User $user)
    {
        $result = $this->balanceService->deductBalance(
            $user,
            (float) $request->validated()['amount'],
            $request->validated()['note'] ?? null,
            Auth::id()
        );

        if (!$result['success']) {
            return $this->errorResponse($result['message'], null, 422);
        }

        return $this->successResponse(__('Balance deducted successfully'), [
            'new_balance' => $result['new_balance'],
            'formatted_balance' => $result['formatted_balance'],
            'transaction' => array_merge($result['transaction'], ['admin' => Auth::user()->name]),
        ]);
    }

    public function refreshBalance(User $user)
    {
        $user->refresh();
        return response()->json([
            'success' => true,
            'message' => __('Balance refreshed successfully'),
            'balance' => ['current' => $user->balance, 'formatted' => number_format($user->balance, 2)],
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_updated' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function getBalanceStats(User $user)
    {
        return $this->successResponse(__('Balance statistics retrieved'), $this->balanceService->getStats($user));
    }

    public function getBalanceHistory(User $user, Request $request)
    {
        $params = $this->getPaginationParams($request);
        $balanceHistories = $this->balanceService->getHistory($user, $params['per_page'], $params['page']);

        if ($request->ajax()) {
            return $this->successResponse(__('Balance history retrieved'), [
                'data' => $balanceHistories->items(),
                'pagination' => [
                    'current_page' => $balanceHistories->currentPage(),
                    'last_page' => $balanceHistories->lastPage(),
                    'per_page' => $balanceHistories->perPage(),
                    'total' => $balanceHistories->total(),
                ]
            ]);
        }

        return view('admin.users.balance-history', compact('user', 'balanceHistories'));
    }

    public function bulkBalanceOperation(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'operation' => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255'
        ]);

        $result = $this->balanceService->handleBulkOperation(
            $request->user_ids,
            $request->operation,
            (float) $request->amount,
            $request->note,
            Auth::id()
        );

        return $this->jsonResponse(
            $result['success'],
            __('Bulk operation completed'),
            $result,
            $result['success'] ? 200 : 400
        );
    }

    protected function getUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        return $query->latest()->paginate(15)->appends($request->all());
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

    protected function prepareUserData(array $validated, HtmlSanitizer $sanitizer, ?User $user = null)
    {
        $this->sanitizeUserData($validated, $sanitizer);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'role' => $validated['role'],
            'balance' => $validated['balance'] ?? 0,
            'approved_at' => isset($validated['approved']) ? now() : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        return $data;
    }

    protected function sanitizeUserData(array &$data, HtmlSanitizer $sanitizer)
    {
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = $sanitizer->clean($data['email']);
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersBalanceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustBalanceRequest;
use App\Models\User;
use App\Services\HtmlSanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        // Role filter
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            if ($request->status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        $users = $query->latest()->paginate(15)->appends($request->all());

        // Cache user statistics for dashboard
        $userStats = Cache::remember('user_stats', 600, function () {
            return [
                'total_users' => User::count(),
                'total_vendors' => User::where('role', 'vendor')->count(),
                'total_customers' => User::where('role', 'user')->count(),
                'pending_approvals' => User::whereNull('approved_at')->count(),
            ];
        });

        return view('admin.users.index', compact('users', 'userStats'));
    }

    public function create()
    {
        $user = new User();

        return view('admin.users.form', compact('user'));
    }

    public function store(\App\Http\Requests\Admin\StoreUserRequest $request, HtmlSanitizer $sanitizer)
    {
        $validated = $request->validated();

        // sanitize basic string fields
        if (isset($validated['name']) && is_string($validated['name'])) {
            $validated['name'] = $sanitizer->clean($validated['name']);
        }
        if (isset($validated['email']) && is_string($validated['email'])) {
            $validated['email'] = $sanitizer->clean($validated['email']);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'balance' => $validated['balance'] ?? 0,
            'approved_at' => isset($validated['approved']) ? now() : null,
        ]);

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
        $validated = $request->validated();

        // sanitize
        if (isset($validated['name']) && is_string($validated['name'])) {
            $validated['name'] = $sanitizer->clean($validated['name']);
        }
        if (isset($validated['email']) && is_string($validated['email'])) {
            $validated['email'] = $sanitizer->clean($validated['email']);
        }

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

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        // Prevent deletion of current admin user
        if ($user->id === auth()->id()) {
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
        $query = User::query();

        if ($status === 'approved') {
            $query->whereNotNull('approved_at');
        } elseif ($status === 'pending') {
            $query->whereNull('approved_at');
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->paginate(15);
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
        $format = $request->query('format', 'xlsx');
        $users = User::select('name', 'email', 'role', 'balance')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.balances', compact('users'));

            return $pdf->download('user_balances.pdf');
        }

        return Excel::download(new UsersBalanceExport($users), 'user_balances.xlsx');
    }

    protected function getValidationRules(?User $user = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($user ? $user->id : ''),
            'phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'password' => ($user ? 'nullable' : 'required') . '|string|min:8',
            'role' => 'required|in:admin,vendor,user',
            'balance' => 'nullable|numeric|min:0',
            'approved' => 'nullable|boolean',
        ];

        return $rules;
    }

    public function exportExcel()
    {
        $users = User::select('name', 'email', 'role', 'balance')->get();

        return Excel::download(new UsersBalanceExport($users), 'users_export.xlsx');
    }

    public function exportPdf()
    {
        $users = User::select('name', 'email', 'role', 'balance')->get();
        $pdf = Pdf::loadView('exports.balances', compact('users'));

        return $pdf->download('users_export.pdf');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        User::whereIn('id', $ids)->whereNull('approved_at')->update([
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', __('Users approved successfully'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        User::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', __('Users deleted successfully'));
    }

    public function balance(User $user)
    {
        $defaultCurrency = \App\Models\Currency::getDefault();

        return view('admin.users.balance', compact('user', 'defaultCurrency'));
    }

    public function activity(User $user)
    {
        $activities = \App\Models\Activity::where('user_id', $user->id)
            ->latest()
            ->paginate(25);

        // Prepare human-friendly data rendering to avoid raw JSON in the UI
        $activities->getCollection()->transform(function ($act) {
            $html = null;
            $data = $act->data;
            if (is_array($data) && count($data)) {
                $lines = [];
                foreach ($data as $k => $v) {
                    // determine a friendly label for the key
                    if (str_ends_with($k, '_id')) {
                        $base = substr($k, 0, -3); // remove trailing _id
                        $label = ucfirst(str_replace(['_', '-'], ' ', $base)) . ' id';
                    } elseif ($k === 'id') {
                        $label = 'id';
                    } else {
                        $label = ucfirst(str_replace(['_', '-'], ' ', $k));
                    }

                    // resolve common foreign keys for nicer values
                    if (str_contains($k, 'product')) {
                        $p = \App\Models\Product::find($v);
                        $val = $p ? e($p->name) : e($v);
                    } elseif (str_contains($k, 'order')) {
                        $o = \App\Models\Order::find($v);
                        if ($o) {
                            $val = '<a href="' . route('admin.orders.show', $o->id) . '">#' . e($o->id) . '</a>';
                        } else {
                            $val = e($v);
                        }
                    } elseif (str_contains($k, 'country')) {
                        $c = \App\Models\Country::find($v);
                        $val = $c ? e($c->name) : e($v);
                    } elseif (str_contains($k, 'governorate')) {
                        $g = \App\Models\Governorate::find($v);
                        $val = $g ? e($g->name) : e($v);
                    } elseif (str_contains($k, 'city')) {
                        $ci = \App\Models\City::find($v);
                        $val = $ci ? e($ci->name) : e($v);
                    } else {
                        $val = e($v);
                    }

                    $lines[] = '<li><strong>' . e($label) . ':</strong> ' . $val . '</li>';
                }
                $html = '<ul class="mb-0">' . implode('', $lines) . '</ul>';
            }
            $act->data_html = $html;

            return $act;
        });

        return view('admin.users.activity', compact('user', 'activities'));
    }

    public function addBalance(AdjustBalanceRequest $request, User $user)
    {
        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $note = $validated['note'] ?? null;

        $oldBalance = (float) $user->balance;
        $user->increment('balance', $amount);
        $user->refresh();

        // Log the transaction
        \App\Models\BalanceHistory::createTransaction(
            $user,
            'credit',
            $amount,
            (float) $oldBalance,
            (float) $user->balance,
            $note,
            auth()->id()
        );

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => __('Balance added successfully'),
            'new_balance' => $user->balance,
            'formatted_balance' => number_format($user->balance, 2),
            'transaction' => [
                'type' => 'credit',
                'amount' => $amount,
                'note' => $note,
                'date' => now()->format('Y-m-d H:i:s'),
                'admin' => auth()->user()->name,
            ],
        ]);
    }

    public function deductBalance(AdjustBalanceRequest $request, User $user)
    {
        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $note = $validated['note'] ?? null;

        if ($amount > (float) $user->balance) {
            return response()->json([
                'success' => false,
                'message' => __('Amount exceeds current balance'),
            ], 422);
        }

        $oldBalance = (float) $user->balance;
        $user->decrement('balance', $amount);
        $user->refresh();

        // Log the transaction
        \App\Models\BalanceHistory::createTransaction(
            $user,
            'debit',
            $amount,
            (float) $oldBalance,
            (float) $user->balance,
            $note,
            auth()->id()
        );

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => __('Balance deducted successfully'),
            'new_balance' => $user->balance,
            'formatted_balance' => number_format($user->balance, 2),
            'transaction' => [
                'type' => 'debit',
                'amount' => $amount,
                'note' => $note,
                'date' => now()->format('Y-m-d H:i:s'),
                'admin' => auth()->user()->name,
            ],
        ]);
    }

    /**
     * Get user balance statistics via AJAX
     */
    public function getBalanceStats(User $user)
    {
        $totalAdded = $user->balanceHistories()->whereIn('type', ['credit', 'bonus', 'refund'])->sum('amount');
        $totalDeducted = $user->balanceHistories()->whereIn('type', ['debit', 'penalty'])->sum('amount');
        $transactionCount = $user->balanceHistories()->count();
        $lastTransaction = $user->balanceHistories()->latest()->first();

        // Format values with currency symbol
        $defaultCurrency = \App\Models\Currency::getDefault();
        $symbol = $defaultCurrency ? $defaultCurrency->symbol : '';
        $stats = [
            'available_balance' => number_format($user->balance, 2) . ' ' . $symbol,
            'total_added' => number_format($totalAdded, 2) . ' ' . $symbol,
            'total_deducted' => number_format($totalDeducted, 2) . ' ' . $symbol,
            'last_updated' => $lastTransaction ? $lastTransaction->created_at->format('Y-m-d H:i:s') : $user->updated_at->format('Y-m-d H:i:s'),
            'transaction_count' => $transactionCount,
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get user balance history via AJAX
     */
    public function getBalanceHistory(User $user)
    {
        $balanceHistories = $user->balanceHistories()
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $history = $balanceHistories->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'previous_balance' => $transaction->previous_balance,
                'new_balance' => $transaction->new_balance,
                'note' => $transaction->note,
                'admin' => $transaction->admin ? $transaction->admin->name : 'System',
                'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'formatted_amount' => number_format($transaction->amount, 2),
                'formatted_date' => $transaction->created_at->format('M d, Y H:i'),
                'type_label' => $transaction->getTypeLabel(),
                'type_icon' => $transaction->getTypeIcon(),
                // Use existing color class helper
                'type_color' => method_exists($transaction, 'getTypeColorClass') ? $transaction->getTypeColorClass() : 'secondary',
            ];
        });

        return response()->json([
            'success' => true,
            'history' => $history->values()->all(),
            'total' => $history->count(),
        ]);
    }

    /**
     * Refresh user balance data via AJAX
     */
    public function refreshBalance(User $user)
    {
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => __('Balance refreshed successfully'),
            'balance' => [
                'current' => $user->balance,
                'formatted' => number_format($user->balance, 2),
            ],
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_updated' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}

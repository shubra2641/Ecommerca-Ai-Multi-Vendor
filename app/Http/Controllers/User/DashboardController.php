<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ordersQuery = Order::where('user_id', $user->id);
        $paymentsQuery = Payment::where('user_id', $user->id);
        $recentOrders = (clone $ordersQuery)->latest()->with('items')->limit(5)->get();
        $recentPayments = (clone $paymentsQuery)->latest()->limit(5)->get();

        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currencySymbol = $currencyContext['currencySymbol'];

        // Convert order totals and payment amounts to current currency
        foreach ($recentOrders as $order) {
            $order->display_total = GlobalHelper::convertCurrency($order->total, $defaultCurrency, $currentCurrency, 2);
        }
        foreach ($recentPayments as $payment) {
            $payment->display_amount = GlobalHelper::convertCurrency($payment->amount, $defaultCurrency, $currentCurrency, 2);
        }

        $stats = [
            'orders_total' => (clone $ordersQuery)->count(),
            'orders_completed' => (clone $ordersQuery)->where('status', 'completed')->count(),
            'orders_pending' => (clone $ordersQuery)->whereIn('status', ['pending', 'processing'])->count(),
            'payments_total' => (clone $paymentsQuery)->count(),
            'payments_completed' => GlobalHelper::convertCurrency(
                (clone $paymentsQuery)->where('status', 'completed')->sum('amount'),
                $defaultCurrency,
                $currentCurrency,
                2
            ),
            'profile_completion' => $user->profile_completion,
            'last_order_date' => optional($recentOrders->first())->created_at,
        ];
        $addresses = $user->addresses()->get();

        return view('front.account.dashboard', compact('stats', 'recentOrders', 'recentPayments', 'addresses', 'user', 'currencySymbol'));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->where('user_id', Auth::id())->latest()->paginate(12);

        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currencySymbol = $currencyContext['currencySymbol'];

        // Convert order totals to current currency
        foreach ($orders as $order) {
            $order->display_total = GlobalHelper::convertCurrency($order->total, $defaultCurrency, $currentCurrency, 2);
        }

        return view('front.account.orders', compact('orders', 'currencySymbol'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items');
        $vm = app(\App\Services\AccountOrderViewBuilder::class)->build($order);

        return view('front.account.order_show', array_merge(compact('order'), $vm));
    }
}

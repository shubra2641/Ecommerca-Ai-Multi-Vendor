<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->where('user_id', Auth::id())->latest()->paginate(12);

        return view('front.account.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items');
        $vm = app(\App\Services\AccountOrderViewBuilder::class)->build($order);

        return view('front.account.order_show', array_merge(compact('order'), $vm));
    }
}

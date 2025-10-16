<?php

namespace App\Http\Controllers;

class OrderViewController extends Controller
{
    public function show(\App\Models\Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }
        $vm = app(\App\Services\OrderViewBuilder::class)->build($order);

        return view('front.orders.show', [
            'order' => $order,
            'orderItems' => $vm['items'],
            'orderSubtotal' => $vm['subtotal'],
            'orderShipping' => $vm['shipping'],
            'orderTotal' => $vm['total'],
            'orderAttachments' => $vm['attachments'],
        ]);
    }
}

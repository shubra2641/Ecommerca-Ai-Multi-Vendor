<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrderViewBuilder;
use App\Models\ShippingZone;

class OrderViewController extends Controller
{
    public function show(\App\Models\Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }
        $vm = app(OrderViewBuilder::class)->build($order);

        return view('front.orders.show', [
            'order' => $order,
            'orderItems' => $vm['items'],
            'orderSubtotal' => $vm['subtotal'],
            'orderShipping' => $vm['shipping'],
            'orderTotal' => $vm['total'],
            'orderAttachments' => $vm['attachments'],
            'shippingZone' => $order->shipping_zone_id ? ShippingZone::find($order->shipping_zone_id) : null,
        ]);
    }
}

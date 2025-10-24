<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $r)
    {
        $q = OrderItem::with(['order.user', 'product'])
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', $r->user()->id));

        if ($r->filled('q')) {
            $q->whereHas('order', fn ($qo) => $qo->where('id', 'like', '%'.$r->input('q').'%'));
        }
        if ($r->filled('status')) {
            $q->whereHas('order', fn ($qo) => $qo->where('status', $r->input('status')));
        }

        $items = $q->latest()->paginate(30);

        // Transform the data to match frontend expectations
        $items->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'order_id' => $item->order->id,
                'status' => $item->order->status,
                'total' => $item->order->total,
                'customer_name' => $item->order->user ? $item->order->user->name : 'Guest',
                'created_at' => $item->order->created_at,
                'product_name' => $item->product ? $item->product->name : 'Unknown Product',
                'quantity' => $item->qty,
                'price' => $item->price,
                'order' => $item->order,
                'product' => $item->product,
                'user' => $item->order->user,
            ];
        });

        return response()->json($items);
    }

    public function show(Request $r, $id)
    {
        $item = OrderItem::with(['order.user', 'product'])
            ->where('id', $id)
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', $r->user()->id))
            ->firstOrFail();

        // Transform the data to match frontend expectations
        $transformedItem = [
            'id' => $item->id,
            'order_id' => $item->order->id,
            'status' => $item->order->status,
            'total' => $item->order->total,
            'customer_name' => $item->order->user ? $item->order->user->name : 'Guest',
            'customer_email' => $item->order->user ? $item->order->user->email : null,
            'created_at' => $item->order->created_at,
            'product_name' => $item->product ? $item->product->name : 'Unknown Product',
            'quantity' => $item->qty,
            'price' => $item->price,
            'shipping_address' => $item->order->shipping_address,
            'items' => [[
                'product_name' => $item->product ? $item->product->name : 'Unknown Product',
                'quantity' => $item->qty,
                'price' => $item->price,
                'total' => $item->qty * $item->price,
                'product' => $item->product,
            ],
            ],
            'order' => $item->order,
            'product' => $item->product,
            'user' => $item->order->user,
        ];

        return response()->json($transformedItem);
    }

    public function updateOrderStatus(Request $r, $id)
    {
        $r->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,completed,cancelled',
        ]);

        $item = OrderItem::with(['order', 'product'])
            ->where('id', $id)
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', $r->user()->id))
            ->firstOrFail();

        $order = $item->order;
        $order->status = $r->input('status');
        $order->save();

        // Create status history record
        $order->statusHistory()->create([
            'status' => $r->input('status'),
            'note' => 'Updated by vendor',
        ]);

        // Notify customer about status change
        try {
            if ($order->user) {
                $order->user->notify(new \App\Notifications\UserOrderStatusUpdated($order, $r->input('status')));
            }
        } catch (\Throwable $e) {
            logger()->warning('Order status notification failed: '.$e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Order status updated successfully']);
    }
}

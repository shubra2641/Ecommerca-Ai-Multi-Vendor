<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\OrderFilterRequest;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index(OrderFilterRequest $request)
    {
        $items = OrderItem::with('order', 'product')
            ->whereHas('product', fn($qq) => $qq->where('vendor_id', auth()->id()))
            ->when($request->filled('q'), fn($q) => $q->whereHas('order', fn($qo) => $qo->where('id', 'like', '%' . $request->input('q') . '%')))
            ->when($request->filled('status'), fn($q) => $q->whereHas('order', fn($qo) => $qo->where('status', $request->input('status'))))
            ->when($request->filled('start_date'), fn($q) => $q->whereHas('order', fn($qo) => $qo->whereDate('created_at', '>=', $request->input('start_date'))))
            ->when($request->filled('end_date'), fn($q) => $q->whereHas('order', fn($qo) => $qo->whereDate('created_at', '<=', $request->input('end_date'))))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('vendor.orders.index', compact('items'));
    }

    public function show($orderId)
    {
        $item = OrderItem::with('order', 'product')
            ->where('id', $orderId)
            ->whereHas('product', fn($qq) => $qq->where('vendor_id', auth()->id()))
            ->firstOrFail();

        return view('vendor.orders.show', compact('item'));
    }
}

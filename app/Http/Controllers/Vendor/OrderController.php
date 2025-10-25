<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\OrderFilterRequest;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index(OrderFilterRequest $r)
    {
        $q = OrderItem::with('order', 'product')
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', auth()->id()));

        if ($r->filled('q')) {
            $q->whereHas('order', fn ($qo) => $qo->where('id', 'like', '%' . $r->input('q') . '%'));
        }
        if ($r->filled('status')) {
            $q->whereHas('order', fn ($qo) => $qo->where('status', $r->input('status')));
        }
        if ($r->filled('start_date')) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '>=', $r->input('start_date')));
        }
        if ($r->filled('end_date')) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '<=', $r->input('end_date')));
        }

        $items = $q->latest()->paginate(30)->withQueryString();

        return view('vendor.orders.index', compact('items'));
    }

    public function show($id)
    {
        $item = OrderItem::with('order', 'product')
            ->where('id', $id)
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', auth()->id()))
            ->firstOrFail();

        return view('vendor.orders.show', compact('item'));
    }
}

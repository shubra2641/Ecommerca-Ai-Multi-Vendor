<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\OrderFilterRequest;
use App\Jobs\GenerateVendorOrdersCsv;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function export(OrderFilterRequest $r)
    {
        $q = OrderItem::with('order', 'product')
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', auth()->id()));
        if ($r->filled('status')) {
            $q->whereHas('order', fn ($qo) => $qo->where('status', $r->input('status')));
        }
        if ($r->filled('start_date')) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '>=', $r->input('start_date')));
        }
        if ($r->filled('end_date')) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '<=', $r->input('end_date')));
        }

        $filename = 'vendor_orders_' . date('Ymd_His') . '.csv';
        $response = new StreamedResponse(function () use ($q) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['order_id', 'order_date', 'product', 'quantity', 'total_price', 'status']);
            $q->chunk(200, function ($items) use ($handle) {
                foreach ($items as $it) {
                    fputcsv($handle, [
                        $it->order_id,
                        $it->order?->created_at?->format('Y-m-d H:i'),
                        Str::limit($it->product?->name ?? '', 120),
                        $it->qty ?? $it->quantity ?? 1,
                        number_format((float) (($it->price ?? 0) * ($it->qty ?? $it->quantity ?? 1)), 2),
                        $it->order?->status ?? '',
                    ]);
                }
            });
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    public function requestExport(OrderFilterRequest $r)
    {
        // create a DB record to track the export
        $filters = $r->only(['status', 'start_date', 'end_date', 'q']);
        $export = \App\Models\VendorExport::create([
            'vendor_id' => auth()->id(),
            'status' => 'pending',
            'filters' => $filters,
        ]);

        GenerateVendorOrdersCsv::dispatch($export->id, $filters);

        return back()->with('success', 'Export requested. You will receive an email when it is ready.');
    }

    public function downloadExport(Request $r, $filename)
    {
        // validate signed URL
        if (! $r->hasValidSignature()) {
            abort(403);
        }
        $path = storage_path('app/vendor_exports/' . $filename);
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [], 'inline');
    }

    public function show($id)
    {
        $item = OrderItem::with('order', 'product')->where('id', $id)->whereHas('product', fn ($qq) => $qq->where('vendor_id', auth()->id()))->firstOrFail();

        return view('vendor.orders.show', compact('item'));
    }
}

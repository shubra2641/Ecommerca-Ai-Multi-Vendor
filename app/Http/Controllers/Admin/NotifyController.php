<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductInterest;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'product' => ['nullable', 'integer', 'exists:products,id'],
            'email' => ['nullable', 'string', 'max:255'],
        ]);

        $query = ProductInterest::with(['product', 'user'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('product'), fn ($q) => $q->where('product_id', $request->product))
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%' . $request->email . '%'));

        $interests = $query->latest()->paginate(30)->withQueryString();
        // Breakdown summary (cached briefly to avoid heavy queries)
        $breakdown = cache()->remember('notify_breakdown', 60, function () {
            $types = ProductInterest::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')->pluck('total', 'type');
            $pending = ProductInterest::selectRaw('type, COUNT(*) as c')
                ->where('status', 'pending')->groupBy('type')->pluck('c', 'type');
            $notified = ProductInterest::selectRaw('type, COUNT(*) as c')
                ->where('status', 'notified')->groupBy('type')->pluck('c', 'type');
            $active = ProductInterest::selectRaw('type, COUNT(*) as c')
                ->whereNull('unsubscribed_at')
                ->whereNotIn('status', [ProductInterest::STATUS_CANCELLED])
                ->groupBy('type')->pluck('c', 'type');
            $rows = [];
            foreach (ProductInterest::allowedTypes() as $t) {
                $rows[] = [
                    'type' => $t,
                    'total' => $types[$t] ?? 0,
                    'pending' => $pending[$t] ?? 0,
                    'notified' => $notified[$t] ?? 0,
                    'active' => $active[$t] ?? 0,
                ];
            }

            return $rows;
        });

        return view('admin.notify.index', compact('interests', 'breakdown'));
    }

    public function markNotified(ProductInterest $interest)
    {
        $interest->update(['status' => 'notified', 'notified_at' => now()]);

        return back()->with('status', __('Marked as notified'));
    }

    public function destroy(ProductInterest $interest)
    {
        $interest->delete();

        return back()->with('status', __('Deleted'));
    }
}

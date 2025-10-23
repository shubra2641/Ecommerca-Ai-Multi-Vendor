<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ReturnsController extends Controller
{
    public function index()
    {
        $paginator = OrderItem::whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->with('product', 'order')
            ->orderByDesc('created_at')
            ->paginate(20);
        $vm = app(\App\Services\ReturnsViewBuilder::class)->build($paginator);

        return view('front.returns.index', [
            'items' => $vm['items'],
            'paginator' => $vm['paginator'],
        ]);
    }

    public function request(Request $request, OrderItem $item)
    {
        if ($item->order->user_id !== auth()->id()) {
            abort(403);
        }
        if (! $item->isWithinReturnWindow()) {
            return back()->with('error', __('Return window expired for this item'));
        }
        $data = $request->validate([
            'reason' => 'required|string|max:2000',
            'type' => 'required|in:return,exchange',
            'image' => 'nullable|image|max:5120',
        ]);
        $meta = $item->meta ?: [];
        // handle optional user image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('warranty', 'public');
            $meta['user_images'] = $meta['user_images'] ?? [];
            $meta['user_images'][] = $path;
        }
        $meta['history'] = $meta['history'] ?? [];
        $meta['history'][] = [
            'when' => now()->toDateTimeString(),
            'actor' => 'user',
            'action' => 'requested',
            'note' => $data['reason'] ?? null,
        ];

        $item->meta = $meta;
        $item->return_requested = true;
        $item->return_status = 'pending';
        $item->return_reason = $data['reason'];
        $item->save();
        // Notify admins (send now so DB notification is created immediately)
        try {
            $admins = \App\Models\User::admins()->get();
            if ($admins && $admins->count()) {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $admins,
                    new \App\Notifications\AdminNewReturnRequest($item)
                );
            }
        } catch (\Throwable $e) {
            // swallow notify errors to avoid breaking UX
            null;
        }

        try {
            session()->flash('refresh_admin_notifications', true);
        } catch (\Throwable $e) {
            // Ignore session flash errors
            null;
        }

        return back()->with('success', __('Return request submitted'));
    }
}

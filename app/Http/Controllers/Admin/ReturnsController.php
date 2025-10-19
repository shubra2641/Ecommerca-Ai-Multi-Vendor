<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class ReturnsController extends Controller
{
    public function index()
    {
        $items = OrderItem::where('return_requested', true)
            ->with('product', 'order')
            ->orderByDesc('updated_at')
            ->paginate(50);

        return view('admin.returns.index', compact('items'));
    }

    public function show(OrderItem $item)
    {
        return view('admin.returns.show', compact('item'));
    }

    public function update(
        Request $request,
        OrderItem $item,
        HtmlSanitizer $sanitizer
    ) {
        $data = $request->validate([
            'return_status' => 'required|string|in:received,in_repair,shipped_back,delivered,completed,cancelled,pending,rejected,approved',
            'admin_note' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);
        $meta = $item->meta ?: [];
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('warranty_admin', 'public');
            $meta['admin_images'] = $meta['admin_images'] ?? [];
            $meta['admin_images'][] = $path;
        }
        $meta['history'] = $meta['history'] ?? [];
        $note = $data['admin_note'] ?? null;
        if (is_string($note) && $note !== '') {
            $note = $sanitizer->clean($note);
        }

        $meta['history'][] = [
            'when' => now()->toDateTimeString(),
            'actor' => 'admin',
            'action' => $data['return_status'],
            'note' => $note,
        ];

        if (! empty($note)) {
            $meta['admin_note'] = $note;
        }
        $item->meta = $meta;
        $item->return_status = $data['return_status'];
        $item->save();
        // Notify the customer about the status update
        try {
            $user = $item->order?->user;
            if ($user) {
                $user->notify(new \App\Notifications\UserReturnStatusUpdated($item, $data['return_status']));
            }
        } catch (\Throwable $e) {
            // ignore notification errors
        }

        return back()->with('success', __('Updated'));
    }
}

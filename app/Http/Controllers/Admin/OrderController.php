<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariation;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['user', 'items'])->latest();
        $orders = $query->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'payments.attachments', 'user', 'shippingAddress', 'shippingZone');
        $customerStats = null;
        if ($order->user) {
            $customerStats = [
                'orders_count' => Order::where('user_id', $order->user_id)->count(),
                'orders_total' => Order::where('user_id', $order->user_id)->sum('total'),
                'first_order_at' => Order::where('user_id', $order->user_id)->orderBy('id')->value('created_at'),
            ];
        }

        // Compute address text
        $aovAddressText = '';
        if ($order->shippingAddress) {
            $addr = $order->shippingAddress;
            $aovAddressText = ($addr->line1 ? $addr->line1 . ', ' : '') .
                ($addr->line2 ? $addr->line2 . ', ' : '') .
                ($addr->city ? $addr->city->name . ', ' : '') .
                ($addr->governorate ? $addr->governorate->name . ', ' : '') .
                ($addr->country ? $addr->country->name : '');
        } elseif ($order->shipping_address && is_array($order->shipping_address)) {
            $addr = $order->shipping_address;
            $aovAddressText = ($addr['customer_address'] ?? '') . ', ' .
                ($addr['city'] ?? '') . ', ' .
                ($addr['governorate'] ?? '') . ', ' .
                ($addr['country'] ?? '');
        }

        // Offline payments for actions
        $aovOfflinePayments = [];
        foreach ($order->payments as $payment) {
            if ($payment->method === 'offline') {
                $aovOfflinePayments[$payment->id] = true;
            }
        }

        // First payment note
        $aovFirstPaymentNote = $order->payments->first()?->note ?? null;

        return view('admin.orders.show', compact('order', 'customerStats', 'aovAddressText', 'aovOfflinePayments', 'aovFirstPaymentNote'));
    }

    // Mark a payment as accepted (admin verifies transfer)
    public function acceptPayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->status = 'completed';
        $payment->save();

        $order = $payment->order;
        $order->payment_status = 'paid';
        // Set to processing (not completed yet) allowing fulfillment workflow
        $order->status = 'processing';
        $order->save();

        // Dispatch domain event (listener will commit stock idempotently)
        event(new \App\Events\OrderPaid($order));

        // try serial assignment
        try {
            app(\App\Services\SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());
        } catch (\Exception $e) {
            logger()->error('Serial assignment failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // Notify user
        try {
            if ($order && $order->user) {
                $order->user->notify(new \App\Notifications\UserPaymentStatusNotification($payment, 'accepted'));
                $order->user->notify(new \App\Notifications\UserOrderStatusUpdated($order, $order->status));
            }
        } catch (\Throwable $e) {
            logger()->warning('Payment accept notification failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Payment accepted'));
    }

    public function rejectPayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->status = 'rejected';
        $payment->save();
        // Auto-cancel order if payment rejected and not already completed/refunded
        $order = $payment->order;
        if ($order) {
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'cancelled';
            }
            if (! in_array($order->status, ['completed', 'refunded'])) {
                $order->status = 'cancelled';
                $order->save();
                event(new \App\Events\OrderCancelled($order));
            } else {
                $order->save();
            }
        }
        try {
            if ($order && $order->user) {
                $order->user->notify(new \App\Notifications\UserPaymentStatusNotification($payment, 'rejected'));
                $order->user->notify(new \App\Notifications\UserOrderStatusUpdated($order, $order->status));
            }
        } catch (\Throwable $e) {
            logger()->warning('Payment reject notification failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Payment rejected and order cancelled'));
    }

    public function retryAssignSerials(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        try {
            app(\App\Services\SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());

            return redirect()->back()->with('success', __('Serials assigned or queued'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function payments(Request $request)
    {
        $payments = Payment::with('order', 'user')->latest()->paginate(25);

        return view('admin.orders.payments', compact('payments'));
    }

    public function updateStatus(
        \App\Http\Requests\Admin\UpdateOrderStatusRequest $request,
        $orderId,
        \App\Services\HtmlSanitizer $sanitizer
    ) {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        $order->status = $data['status'];
        $order->save();

        $note = $data['note'] ?? null;
        if (is_string($note) && $note !== '') {
            $note = $sanitizer->clean($note);
        }
        $order->statusHistory()->create([
            'status' => $data['status'],
            'note' => $note,
        ]);

        // Automatic stock operations depending on status transitions
        if (in_array($data['status'], ['processing', 'completed']) && $order->payment_status === 'paid') {
            event(new \App\Events\OrderPaid($order));
        }
        if ($data['status'] === 'cancelled') {
            event(new \App\Events\OrderCancelled($order));
        }
        if ($data['status'] === 'refunded') {
            event(new \App\Events\OrderRefunded($order));
        }

        // Notify customer about status change
        try {
            if ($order->user) {
                $tracking = null;
                if ($data['status'] === 'shipped') {
                    $tracking = [
                        'tracking_number' => $data['tracking_number'] ?? null,
                        'tracking_url' => $data['tracking_url'] ?? null,
                        'carrier' => $data['carrier'] ?? null,
                    ];
                }
                $order->user->notify(new \App\Notifications\UserOrderStatusUpdated($order, $data['status'], $tracking));
            }
        } catch (\Throwable $e) {
            logger()->warning('Order status notification failed: ' . $e->getMessage());
        }

        // Notify vendors who have items in this order about the status change
        try {
            $order->load('items.product');
            $itemsByVendor = [];
            foreach ($order->items as $it) {
                $vendorId = $it->product?->vendor_id;
                if (! $vendorId) {
                    continue;
                }
                $itemsByVendor[$vendorId][] = $it;
            }
            foreach ($itemsByVendor as $vendorId => $items) {
                $vendor = User::find($vendorId);
                if ($vendor) {
                    $vendor->notify(new \App\Notifications\VendorOrderStatusUpdated($order, $data['status']));
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Vendor order status notifications failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('Order status updated'));
    }

    // Admin: cancel a backorder for a specific order item (release reserved stock)
    public function cancelBackorderItem(Request $request, $orderId, $itemId)
    {
        $order = Order::findOrFail($orderId);
        $item = OrderItem::where('order_id', $order->id)->where('id', $itemId)->firstOrFail();

        if (! $item->is_backorder) {
            return redirect()->back()->with('info', __('Item is not a backorder'));
        }

        // Release reserved qty
        try {
            $product = $item->product;
            $qty = (int) $item->qty;
            if ($item->meta && is_array($item->meta) && ! empty($item->meta['variant_id'])) {
                $variation = ProductVariation::find($item->meta['variant_id']);
                if ($variation) {
                    StockService::releaseVariation($variation, $qty);
                }
            } else {
                StockService::release($product, $qty);
            }

            // mark item as no longer backorder and update order flag
            $item->is_backorder = false;
            $item->save();

            // If no more items with backorder, clear order flag
            if (! $order->items()->where('is_backorder', true)->exists()) {
                $order->has_backorder = false;
                $order->save();
            }

            return redirect()->back()->with('success', __('Backorder cancelled and stock released'));
        } catch (\Exception $e) {
            logger()->error('Failed cancelling backorder for item ' . $item->id . ': ' . $e->getMessage());

            return redirect()->back()->with('error', __('Failed to cancel backorder: ') . $e->getMessage());
        }
    }
}

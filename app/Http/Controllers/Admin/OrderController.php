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
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::query()->with(['user', 'items'])->latest();
        $orders = $query->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'payments.attachments', 'user', 'shippingAddress', 'shippingZone');

        $customerStats = $this->getCustomerStats($order);
        $addressText = $this->getAddressText($order);
        $offlinePayments = $this->getOfflinePayments($order);
        $firstPaymentNote = $order->payments->first()?->note ?? null;

        return view('admin.orders.show', [
            'order' => $order,
            'customerStats' => $customerStats,
            'aovAddressText' => $addressText,
            'aovOfflinePayments' => $offlinePayments,
            'aovFirstPaymentNote' => $firstPaymentNote,
        ]);
    }

    // Mark a payment as accepted (admin verifies transfer)
    public function acceptPayment($paymentId)
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

    public function rejectPayment($paymentId)
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

    public function retryAssignSerials($orderId)
    {
        $order = Order::findOrFail($orderId);
        try {
            app(\App\Services\SerialAssignmentService::class)->assignForOrder($order->id, $order->items->toArray());

            return redirect()->back()->with('success', __('Serials assigned or queued'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function payments()
    {
        $payments = Payment::with('order', 'user')->latest()->paginate(25);

        return view('admin.orders.payments', compact('payments'));
    }

    public function vendorIndex()
    {
        $query = OrderItem::query()
            ->with(['order.user', 'product'])
            ->whereHas('product', function ($q) {
                $q->where('vendor_id', Auth::id());
            })
            ->latest();

        $orderItems = $query->paginate(25);

        return view('vendor.orders.index', ['items' => $orderItems]);
    }

    public function vendorShow(OrderItem $orderItem)
    {
        // Ensure the vendor owns the product
        if (!$orderItem->product || $orderItem->product->vendor_id !== Auth::id()) {
            abort(403);
        }

        $orderItem->load('order.user', 'product', 'order.payments.attachments', 'order.shippingAddress', 'order.shippingZone');

        $customerStats = $this->getCustomerStats($orderItem->order);
        $addressText = $this->getAddressText($orderItem->order);
        $offlinePayments = $this->getOfflinePayments($orderItem->order);
        $firstPaymentNote = $orderItem->order->payments->first()?->note ?? null;

        return view('vendor.orders.show', [
            'item' => $orderItem,
            'customerStats' => $customerStats,
            'aovAddressText' => $addressText,
            'aovOfflinePayments' => $offlinePayments,
            'aovFirstPaymentNote' => $firstPaymentNote,
        ]);
    }

    public function updateStatus(\App\Http\Requests\Admin\UpdateOrderStatusRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $data = $request->validated();

        $order->status = $data['status'];
        $order->save();

        $note = $data['note'] ?? null;
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
            foreach ($order->items as $orderItem) {
                $vendorId = $orderItem->product?->vendor_id;
                if (! $vendorId) {
                    continue;
                }
                $itemsByVendor[$vendorId][] = $orderItem;
            }
            foreach (array_keys($itemsByVendor) as $vendorId) {
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

    private function getCustomerStats(Order $order): ?array
    {
        if (! $order->user) {
            return null;
        }

        return [
            'orders_count' => Order::where('user_id', $order->user_id)->count(),
            'orders_total' => Order::where('user_id', $order->user_id)->sum('total'),
            'first_order_at' => Order::where('user_id', $order->user_id)->orderBy('id')->value('created_at'),
        ];
    }

    private function getAddressText(Order $order): string
    {
        if ($order->shippingAddress) {
            return $this->formatShippingAddress($order->shippingAddress);
        }

        if ($order->shipping_address && is_array($order->shipping_address)) {
            return $this->formatLegacyAddress($order->shipping_address);
        }

        return '';
    }

    private function formatShippingAddress($address): string
    {
        $parts = [];
        if ($address->line1) {
            $parts[] = $address->line1;
        }
        if ($address->line2) {
            $parts[] = $address->line2;
        }
        if ($address->city) {
            $parts[] = $address->city->name;
        }
        if ($address->governorate) {
            $parts[] = $address->governorate->name;
        }
        if ($address->country) {
            $parts[] = $address->country->name;
        }

        return implode(', ', $parts);
    }

    private function formatLegacyAddress(array $address): string
    {
        $parts = [];
        if (! empty($address['customer_address'])) {
            $parts[] = $address['customer_address'];
        }
        if (! empty($address['city'])) {
            $parts[] = $address['city'];
        }
        if (! empty($address['governorate'])) {
            $parts[] = $address['governorate'];
        }
        if (! empty($address['country'])) {
            $parts[] = $address['country'];
        }

        return implode(', ', $parts);
    }

    private function getOfflinePayments(Order $order): array
    {
        $offlinePayments = [];
        foreach ($order->payments as $payment) {
            if ($payment->method === 'offline') {
                $offlinePayments[$payment->id] = true;
            }
        }

        return $offlinePayments;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductInterest;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public function store(Request $request)
    {
        $allowed = \App\Models\ProductInterest::allowedTypes();
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'type' => ['sometimes', 'in:' . implode(',', $allowed)],
        ]);
        if (! $data['email'] && auth()->check()) {
            $data['email'] = auth()->user()->email;
        }
        if (! $data['email'] && empty($data['phone'])) {
            return response()->json(['ok' => false, 'message' => __('Email or phone required')], 422);
        }
        $data['type'] = $data['type'] ?? 'stock';
        $data['user_id'] = auth()->id();
        $data['ip_address'] = $request->ip();

        $interestQuery = ProductInterest::where('product_id', $data['product_id'])->where('type', $data['type']);
        if (! empty($data['email'])) {
            $interestQuery->where('email', $data['email']);
        } elseif (! empty($data['phone'])) {
            $interestQuery->where('phone', $data['phone']);
        }
        $interest = $interestQuery->first();

        $now = now();
        if ($interest) {
            $updatedDiff = $now->diffInSeconds($interest->updated_at);
            $minInterval = (int) config('interest.min_repeat_seconds', 180);
            if ($updatedDiff < $minInterval) {
                return response()->json(['ok' => true, 'message' => __('Already subscribed recently')], 200);
            }
            $interest->update(array_filter([
                'updated_at' => $now,
                'email' => $data['email'] ?? $interest->email,
                'phone' => $data['phone'] ?? $interest->phone,
            ]));
        } else {
            $data['unsubscribe_token'] = bin2hex(random_bytes(20));
            $interest = ProductInterest::create($data);

            // Notify admins a new product interest was created
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins && $admins->count()) {
                    \Illuminate\Support\Facades\Notification::sendNow($admins, new \App\Notifications\AdminProductInterestNotification($interest));
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed sending product interest admin notification: ' . $e->getMessage());
            }
        }

        \App\Models\Activity::log('interest', 'User subscribed for product interest', [
            'product_id' => $data['product_id'], 'type' => $data['type'],
        ]);

        // Queue confirmation email
        dispatch(new \App\Jobs\SendInterestConfirmationJob($interest->id))->afterResponse();

        // If the request came from an admin browsing session (rare) we also set a session flag.
        try {
            session()->flash('refresh_admin_notifications', true);
        } catch (\Throwable $e) { /* ignore when session not writable (API) */
        }

        return response()->json(['ok' => true, 'message' => __('Subscription saved')]);
    }

    // API: check if an email or phone is subscribed for a product and type
    public function check(Request $request)
    {
        $productId = $request->query('product_id');
        $type = $request->query('type', 'back_in_stock');
        $email = $request->query('email') ?? auth()->user()->email ?? null;
        $phone = $request->query('phone') ?? null;
        if (! $productId) {
            return response()->json(['ok' => false, 'message' => 'product_id required'], 400);
        }
        $q = ProductInterest::where('product_id', $productId)->where('type', $type)->whereNull('unsubscribed_at')->whereNotIn('status', [ProductInterest::STATUS_CANCELLED]);
        if ($email) {
            $q->where('email', $email);
        }
        if ($phone) {
            $q->orWhere('phone', $phone);
        }
        $exists = $q->exists();

        return response()->json(['ok' => true, 'subscribed' => $exists]);
    }

    public function unsubscribe(string $token)
    {
        $interest = ProductInterest::where('unsubscribe_token', $token)->first();
        if (! $interest) {
            return response()->view('errors.404', [], 404);
        }
        if ($interest->unsubscribed_at) {
            return view('emails.unsubscribe-result', ['status' => 'already']);
        }
        $interest->update(['unsubscribed_at' => now(), 'status' => ProductInterest::STATUS_CANCELLED]);

        return view('emails.unsubscribe-result', ['status' => 'success']);
    }
}

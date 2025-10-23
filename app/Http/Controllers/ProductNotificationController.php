<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductNotificationRequest;
use App\Models\Product;
use App\Models\ProductNotification;
use Illuminate\Support\Facades\Auth;

class ProductNotificationController extends Controller
{
    public function store(ProductNotificationRequest $request)
    {
        $data = $request->validated();

        $product = Product::find($data['product_id']);
        if (! $product) {
            return response()->json(['status' => 'error'], 404);
        }

        $userId = Auth::check() ? Auth::id() : null;
        $email = $data['email'] ?? ($userId ? Auth::user()->email : null);
        if (! $email) {
            return response()->json(['status' => 'error', 'message' => 'Email required for guests'], 422);
        }

        // Avoid duplicate subscriptions for same product & email/user
        $exists = ProductNotification::where('product_id', $product->id)
            ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->where('email', $email))
            ->exists();
        if ($exists) {
            return response()->json(['status' => 'ok', 'message' => 'Already subscribed']);
        }

        ProductNotification::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'email' => $email,
            'notified' => false,
        ]);

        return response()->json(['status' => 'ok']);
    }
}

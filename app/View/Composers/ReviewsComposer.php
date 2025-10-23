<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class ReviewsComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $product = $data['product'] ?? null;
        $user = Auth::user();
        $canReview = false;
        if ($user && $product && method_exists($user, 'orders')) {
            $canReview = $user->orders()
                ->whereIn('status', ['completed', 'paid', 'delivered'])
                ->whereHas('items', function ($q) use ($product): void {
                    $q->where('product_id', $product->id);
                })
                ->exists();
        }
        $view->with([
            'reviewUser' => $user,
            'reviewCanSubmit' => $canReview,
        ]);
    }
}

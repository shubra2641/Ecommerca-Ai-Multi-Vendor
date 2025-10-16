<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AccountSidebarComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $completion = $user ? ($user->profile_completion ?? 5) : 0;
        // Precompute compare & wishlist counts
        $compareCount = 0;
        $wishlistCount = 0;
        try {
            $compare = session('compare');
            $compareCount = is_array($compare) ? count($compare) : 0;
        } catch (\Throwable $e) {
        }
        try {
            if (auth()->check() && Schema::hasTable('wishlist_items')) {
                $wishlistCount = \App\Models\WishlistItem::where('user_id', auth()->id())->count();
            } else {
                $wishlist = session('wishlist');
                $wishlistCount = is_array($wishlist) ? count($wishlist) : 0;
            }
        } catch (\Throwable $e) {
            $wishlist = session('wishlist');
            $wishlistCount = is_array($wishlist) ? count($wishlist) : 0;
        }
        $view->with(compact('user', 'completion', 'compareCount', 'wishlistCount'));
    }
}

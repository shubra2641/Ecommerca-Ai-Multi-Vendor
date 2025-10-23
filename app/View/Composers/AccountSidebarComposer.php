<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class AccountSidebarComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $completion = $user ? ($user->profile_completion ?? 5) : 0;

        $compareCount = $this->getCompareCount();
        $wishlistCount = $this->getWishlistCount();

        $view->with(compact('user', 'completion', 'compareCount', 'wishlistCount'));
    }

    private function getCompareCount(): int
    {
        try {
            $compare = session('compare');
            return is_array($compare) ? count($compare) : 0;
        } catch (\Throwable $e) {
            logger()->warning(
                'Failed to get compare count: '.$e->getMessage()
            );
            return 0;
        }
    }

    private function getWishlistCount(): int
    {
        try {
            if (auth()->check() && Schema::hasTable('wishlist_items')) {
                return \App\Models\WishlistItem::where('user_id', auth()->id())
                    ->count();
            }

            $wishlist = session('wishlist');
            return is_array($wishlist) ? count($wishlist) : 0;
        } catch (\Throwable $e) {
            logger()->warning(
                'Failed to get wishlist count from database: '.
                $e->getMessage()
            );
            $wishlist = session('wishlist');
            return is_array($wishlist) ? count($wishlist) : 0;
        }
    }
}

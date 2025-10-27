<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardAdminComposer
{
    public function compose(View $view): void
    {
        // Languages
        $languages = Language::where('is_active', 1)->get();

        // Notifications - only for authenticated admin users
        $notifications = collect();
        $unreadCount = 0;

        if (Auth::check() && Auth::user()->role === 'admin') {
            $user = Auth::user();
            $notifications = $user->notifications()->latest()->take(5)->get();
            $unreadCount = $user->unreadNotifications()->count();
        }

        $view->with([
            'dashboardAdminLanguages' => $languages,
            'adminNotifications' => $notifications,
            'adminUnreadCount' => $unreadCount,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;

class AdminDashboardAdminComposer
{
    public function compose(View $view): void
    {
        // Languages
        $languages = Language::where('is_active', 1)->get();

        // Notifications
        $user = auth()->user();
        $notifications = $user ? $user->notifications()->latest()->take(5)->get() : collect();
        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;

        $view->with([
            'dashboardAdminLanguages' => $languages,
            'adminNotifications' => $notifications,
            'adminUnreadCount' => $unreadCount,
        ]);
    }
}

<?php

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;

class AdminDashboardAdminComposer
{
    public function compose(View $view): void
    {
        // Provide active languages list (was inline in dashboard-admin layout)
        try {
            $languages = Language::where('is_active', 1)->orderByDesc('is_default')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $languages = collect();
        }
        $view->with('dashboardAdminLanguages', $languages);
    }
}

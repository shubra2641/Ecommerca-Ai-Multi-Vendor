<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LayoutComposer
{
    public function compose(View $view): void
    {
        // Provide setting & selectedFont for layout without inline PHP
        static $data = null;
        if ($data === null) {
            $setting = null;
            $selectedFont = 'Inter';
            try {
                if (Schema::hasTable('settings')) {
                    $setting = Cache::remember('site_settings', 3600, fn() => \App\Models\Setting::first());
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to get site settings: ' . $e->getMessage());
            }
            try {
                $selectedFont = cache()->get('settings.font_family', $setting->font_family ?? 'Inter');
            } catch (\Throwable $e) {
                logger()->warning('Failed to get font family: ' . $e->getMessage());
            }
            $data = compact('setting', 'selectedFont');
        }
        $view->with($data);
    }
}

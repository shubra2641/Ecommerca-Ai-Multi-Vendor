<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SiteBrandingComposer
{
    public function compose(View $view): void
    {
        static $cached = null;
        if ($cached === null) {
            $cached = [
                'setting' => null,
                'selectedFont' => 'Inter',
                'siteName' => config('app.name', 'Easy'),
                'logoPath' => null,
            ];
            try {
                $setting = Cache::remember('site_settings', 3600, fn() => \App\Models\Setting::first());
                if ($setting) {
                    $cached['setting'] = $setting;
                    $cached['selectedFont'] = cache()->get('settings.font_family', $setting->font_family ?? 'Inter');
                    $cached['siteName'] = $setting->site_name ?? $cached['siteName'];
                    $cached['logoPath'] = $setting->logo ?? null;
                }
            } catch (\Throwable $e) {
                // Silent fail to keep defaults - intentionally empty
                null;
            }
        }
        $view->with($cached);
    }
}

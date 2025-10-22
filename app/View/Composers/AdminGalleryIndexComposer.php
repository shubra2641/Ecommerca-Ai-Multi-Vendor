<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class AdminGalleryIndexComposer
{
    public function compose(View $view): void
    {
        try {
            $settingLogo = optional(Setting::first())->logo;
        } catch (\Throwable $e) {
            $settingLogo = null;
        }
        $view->with('gallerySettingLogo', $settingLogo);
    }
}

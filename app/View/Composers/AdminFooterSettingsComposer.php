<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class AdminFooterSettingsComposer
{
    public function compose(View $view): void
    {
        $view
            ->with('footerSettingsTitle', __('Footer Settings'));
    }
}

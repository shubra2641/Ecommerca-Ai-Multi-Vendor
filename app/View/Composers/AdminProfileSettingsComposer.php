<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class AdminProfileSettingsComposer
{
    public function compose(View $view): void
    {
        $availableFonts = [
            'Inter', 'Roboto', 'Poppins', 'Montserrat', 'Open Sans', 'Lato', 'Nunito', 'Work Sans',
            'Cairo', 'Noto Sans Arabic', 'Tajawal', 'Almarai', 'Changa', 'El Messiri',
        ];
        $view->with('profileAvailableFonts', $availableFonts);
    }
}

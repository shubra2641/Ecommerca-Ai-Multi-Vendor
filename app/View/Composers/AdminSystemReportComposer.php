<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminSystemReportComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['systemData']['storage']['disk_usage'])) {
            return;
        }

        $usage = $data['systemData']['storage']['disk_usage'];
        $percentage = (int) ($usage['percentage'] ?? 0);
        $classes = ['w-0p', 'w-25p', 'w-50p', 'w-75p', 'w-100p'];
        $class = $classes[min(4, (int) ($percentage / 25))];

        $view->with(['sysDiskPct' => $percentage, 'sysDiskClass' => $class]);
    }
}

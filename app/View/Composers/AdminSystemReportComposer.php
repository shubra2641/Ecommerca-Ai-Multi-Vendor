<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class AdminSystemReportComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['systemData']['storage']['disk_usage'])) {
            return;
        }
        $usage = $data['systemData']['storage']['disk_usage'];
        $pct = (int) ($usage['percentage'] ?? 0);
        $class = $pct >= 100 ? 'w-100p' : ($pct >= 75 ? 'w-75p' :
            ($pct >= 50 ? 'w-50p' : ($pct >= 25 ? 'w-25p' : 'w-0p')));
        $view->with(['sysDiskPct' => $pct, 'sysDiskClass' => $class]);
    }
}

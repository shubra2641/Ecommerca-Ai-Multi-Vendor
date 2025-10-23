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
        $class = $this->getDiskUsageClass($percentage);

        $view->with(['sysDiskPct' => $percentage, 'sysDiskClass' => $class]);
    }

    private function getDiskUsageClass(int $percentage): string
    {
        if ($percentage >= 100) {
            return 'w-100p';
        }

        if ($percentage >= 75) {
            return 'w-75p';
        }

        if ($percentage >= 50) {
            return 'w-50p';
        }

        if ($percentage >= 25) {
            return 'w-25p';
        }

        return 'w-0p';
    }
}

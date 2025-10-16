<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminDashboardComposer
{
    public function compose(View $view): void
    {
        $recentActivity = $view->getData()['recentActivity'] ?? null;
        if (is_array($recentActivity)) {
            $mapped = [];
            foreach ($recentActivity as $act) {
                $type = $act['type'] ?? null;
                $grad = $type === 'user' ? 'bg-grad-user' : ($type === 'approval' ? 'bg-grad-approval' : 'bg-grad-warn');
                $act['gradient'] = $grad;
                $mapped[] = $act;
            }
            $view->with('recentActivityWithGrad', $mapped);
        }
    }
}

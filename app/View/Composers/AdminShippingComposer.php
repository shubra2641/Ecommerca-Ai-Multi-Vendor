<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class AdminShippingComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['groups'])) {
            return;
        }
        $groups = $data['groups'];
        $samples = [];
        foreach ($groups as $g) {
            try {
                $samples[$g->id] = $g->locations()->with(['country', 'governorate', 'city'])->limit(3)->get();
            } catch (\Throwable $e) {
                $samples[$g->id] = collect();
            }
        }
        $view->with('shippingLocationSamples', $samples);
    }
}

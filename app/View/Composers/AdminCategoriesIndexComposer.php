<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminCategoriesIndexComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (! isset($data['categories'])) {
            return;
        }
        $categories = $data['categories'];
        $total = null;
        try {
            if (method_exists($categories, 'total')) {
                $total = $categories->total();
            } elseif (method_exists($categories, 'count')) {
                $total = $categories->count();
            }
        } catch (\Throwable $e) {
            $total = null;
        }
        $view->with('aciTotals', [
            'total' => $total,
            'active' => $categories->where('active', true)->count(),
            'parent' => $categories->where('parent_id', null)->count(),
            'child' => $categories->where('parent_id', '!=', null)->count(),
        ]);
    }
}

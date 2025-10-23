<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class ErrorRoleComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $user = $data['user'] ?? auth()->user();
        $role = optional($user)->role ?? 'user';
        $dashboard = match ($role) {
            'admin' => route('admin.dashboard'),
            'vendor' => route('vendor.dashboard'),
            default => route('user.dashboard'),
        };
        $view->with('role', $role)->with('dashboard', $dashboard);
    }
}

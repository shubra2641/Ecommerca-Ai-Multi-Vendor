<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminUsersFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $user = $data['user'] ?? null;
        $formAction = $user && $user->exists && $user->id
            ? route('admin.users.update', $user)
            : route('admin.users.store');
        $view->with('userFormAction', $formAction);
    }
}

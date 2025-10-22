<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorWithdrawal;

class VendorWithdrawalPolicy
{
    /**
     * Determine whether the user can view the withdrawal.
     */
    public function view(User $user, VendorWithdrawal $withdrawal): bool
    {
        // Allow admins or owning vendor
        if ($user->role === 'admin') {
            return true;
        }

        return $withdrawal->user_id === $user->id;
    }

    /**
     * Determine whether the user can create a withdrawal.
     */
    public function create(User $user): bool
    {
        return $user->role === 'vendor';
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ShippingZone;
use App\Models\User;

class ShippingZonePolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(?User $user, ShippingZone $zone): bool
    {
        return $this->isAdmin($user);
    }

    public function create(?User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(?User $user, ShippingZone $zone): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(?User $user, ShippingZone $zone): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(?User $user, ShippingZone $zone): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }

    private function isAdmin(?User $user): bool
    {
        return $user && $user->role === 'admin';
    }
}

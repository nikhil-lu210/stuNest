<?php

namespace App\Observers;

use App\Support\SystemRoles;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    public function deleting(Role $role): void
    {
        if (SystemRoles::isProtectedRole($role)) {
            throw new \RuntimeException(__('This role cannot be deleted.'));
        }
    }
}

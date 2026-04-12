<?php

namespace App\Policies;

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $viewer): bool
    {
        return true;
    }

    /**
     * The Developer role is only visible to Developer users.
     */
    public function view(User $viewer, Role $role): Response
    {
        if (SystemRoles::isDeveloperRole($role) && ! SystemRoles::viewerIsDeveloper($viewer)) {
            return Response::denyWithStatus(404);
        }

        return Response::allow();
    }

    public function update(User $viewer, Role $role): Response
    {
        return $this->view($viewer, $role);
    }

    public function delete(User $viewer, Role $role): bool
    {
        if (SystemRoles::isProtectedRole($role)) {
            return false;
        }

        return $this->view($viewer, $role)->allowed();
    }
}

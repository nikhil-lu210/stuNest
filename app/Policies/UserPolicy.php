<?php

namespace App\Policies;

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $viewer): bool
    {
        return true;
    }

    /**
     * Non-Developers cannot view Developer-role users (also enforced by global scope + 404 on binding).
     */
    public function view(User $viewer, User $model): Response
    {
        if ($model->hasRole(SystemRoles::DEVELOPER_NAME) && ! SystemRoles::viewerIsDeveloper($viewer)) {
            return Response::denyWithStatus(404);
        }

        return Response::allow();
    }

    public function update(User $viewer, User $model): Response
    {
        return $this->view($viewer, $model);
    }

    public function delete(User $viewer, User $model): bool
    {
        if ($model->developer_anchor || $model->super_admin_anchor) {
            return false;
        }

        return $this->view($viewer, $model)->allowed();
    }
}

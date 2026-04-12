<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function deleting(User $user): void
    {
        if ($user->developer_anchor || $user->super_admin_anchor) {
            throw new \RuntimeException(__('This system user cannot be deleted.'));
        }
    }

    public function forceDeleting(User $user): void
    {
        if ($user->developer_anchor || $user->super_admin_anchor) {
            throw new \RuntimeException(__('This system user cannot be deleted.'));
        }
    }
}

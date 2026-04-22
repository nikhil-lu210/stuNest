<?php

namespace App\Policies;

use App\Models\Property\Property;
use App\Models\User;

class PropertyPolicy
{
    /**
     * Staff roles that can view and manage any property in administration.
     */
    protected function isStaffAdmin(User $user): bool
    {
        return $user->hasAnyRole(['Developer', 'Super Admin', 'Admin']);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Property $property): bool
    {
        return $this->isStaffAdmin($user) || (int) $property->user_id === (int) $user->id;
    }

    public function update(User $user, Property $property): bool
    {
        return $this->isStaffAdmin($user) || (int) $property->user_id === (int) $user->id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }
}

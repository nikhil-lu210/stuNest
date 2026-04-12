<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/**
 * System Spatie roles (web guard) with fixed IDs enforced by migration + seed order.
 */
final class SystemRoles
{
    public const DEVELOPER_ID = 1;

    public const SUPER_ADMIN_ID = 2;

    public const DEVELOPER_NAME = 'Developer';

    public const SUPER_ADMIN_NAME = 'Super Admin';

    public const WEB_GUARD = 'web';

    public static function isDeveloperRole(Role $role): bool
    {
        return $role->guard_name === self::WEB_GUARD
            && ($role->id === self::DEVELOPER_ID || $role->name === self::DEVELOPER_NAME);
    }

    public static function isSuperAdminRole(Role $role): bool
    {
        return $role->guard_name === self::WEB_GUARD
            && ($role->id === self::SUPER_ADMIN_ID || $role->name === self::SUPER_ADMIN_NAME);
    }

    public static function isProtectedRole(Role $role): bool
    {
        return self::isDeveloperRole($role) || self::isSuperAdminRole($role);
    }

    public static function viewerIsDeveloper(?User $user): bool
    {
        return $user !== null && $user->hasRole(self::DEVELOPER_NAME);
    }

    /**
     * Administration (web guard) roles shown in dropdowns and pickers.
     */
    public static function administrationRolesQuery(?User $viewer): Builder
    {
        $q = Role::query()->where('guard_name', self::WEB_GUARD)->orderBy('name');

        if (! self::viewerIsDeveloper($viewer)) {
            $q->where('id', '!=', self::DEVELOPER_ID)
                ->where('name', '!=', self::DEVELOPER_NAME);
        }

        return $q;
    }
}

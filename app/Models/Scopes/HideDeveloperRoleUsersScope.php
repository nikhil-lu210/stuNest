<?php

namespace App\Models\Scopes;

use App\Support\SystemRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Hides users who have the Developer (web) role from non-Developer viewers.
 *
 * Must not call auth()->check() or auth()->user() while the session user is still being
 * resolved: that loads User through this same model and re-enters this scope (stack overflow).
 * Use the guard's hasUser() first (it only checks the cached user reference).
 */
class HideDeveloperRoleUsersScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $guard = auth()->guard();

        if (! method_exists($guard, 'hasUser') || ! $guard->hasUser()) {
            return;
        }

        $viewer = $guard->user();

        if ($viewer === null || SystemRoles::viewerIsDeveloper($viewer)) {
            return;
        }

        $builder->whereDoesntHave('roles', function (Builder $q) {
            $q->where('name', SystemRoles::DEVELOPER_NAME)
                ->where('guard_name', SystemRoles::WEB_GUARD);
        });
    }
}

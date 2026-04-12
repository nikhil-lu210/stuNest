<?php

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'developer_anchor')) {
            return;
        }

        $firstDev = User::withoutGlobalScopes()
            ->whereHas('roles', function ($q) {
                $q->where('name', SystemRoles::DEVELOPER_NAME)
                    ->where('guard_name', SystemRoles::WEB_GUARD);
            })
            ->orderBy('id')
            ->first();

        if ($firstDev && ! $firstDev->developer_anchor) {
            $firstDev->forceFill(['developer_anchor' => true])->saveQuietly();
        }

        $firstSa = User::withoutGlobalScopes()
            ->whereHas('roles', function ($q) {
                $q->where('name', SystemRoles::SUPER_ADMIN_NAME)
                    ->where('guard_name', SystemRoles::WEB_GUARD);
            })
            ->orderBy('id')
            ->first();

        if ($firstSa && ! $firstSa->super_admin_anchor) {
            $firstSa->forceFill(['super_admin_anchor' => true])->saveQuietly();
        }
    }

    public function down(): void
    {
        //
    }
};

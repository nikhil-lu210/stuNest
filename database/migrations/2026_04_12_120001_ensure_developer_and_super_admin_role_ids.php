<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $tables = config('permission.table_names');
        $rolesTable = $tables['roles'];
        $modelHasRoles = $tables['model_has_roles'];
        $roleHasPermissions = $tables['role_has_permissions'];
        $pivotRole = config('permission.column_names.role_pivot_key') ?? 'role_id';

        $dev = DB::table($rolesTable)
            ->where('name', 'Developer')
            ->where('guard_name', 'web')
            ->first();

        $sa = DB::table($rolesTable)
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if (! $dev || ! $sa) {
            return;
        }

        if ((int) $dev->id === 1 && (int) $sa->id === 2) {
            return;
        }

        $offset = 9_000_000;

        Schema::disableForeignKeyConstraints();

        try {
            $ids = DB::table($rolesTable)->orderByDesc('id')->pluck('id');

            foreach ($ids as $id) {
                $newId = (int) $id + $offset;
                DB::table($modelHasRoles)->where($pivotRole, $id)->update([$pivotRole => $newId]);
                DB::table($roleHasPermissions)->where($pivotRole, $id)->update([$pivotRole => $newId]);
                DB::table($rolesTable)->where('id', $id)->update(['id' => $newId]);
            }

            $devNew = (int) $dev->id + $offset;
            $saNew = (int) $sa->id + $offset;

            foreach ([[$devNew, 1], [$saNew, 2]] as [$from, $to]) {
                DB::table($modelHasRoles)->where($pivotRole, $from)->update([$pivotRole => $to]);
                DB::table($roleHasPermissions)->where($pivotRole, $from)->update([$pivotRole => $to]);
                DB::table($rolesTable)->where('id', $from)->update(['id' => $to]);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};

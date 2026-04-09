<?php

use App\Models\PermissionModule;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $module = PermissionModule::firstOrCreate(
            ['name' => 'Institute'],
            ['name' => 'Institute']
        );

        $actions = ['Create', 'Read', 'Update', 'Delete'];
        foreach ($actions as $action) {
            $name = "Institute {$action}";
            Permission::firstOrCreate(
                [
                    'name' => $name,
                    'guard_name' => 'web',
                ],
                [
                    'permission_module_id' => $module->id,
                ]
            );
        }

        $full = [
            'Institute Create',
            'Institute Read',
            'Institute Update',
            'Institute Delete',
        ];

        foreach (['Developer', 'Super Admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($full);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = [
            'Institute Create',
            'Institute Read',
            'Institute Update',
            'Institute Delete',
        ];

        foreach (['Developer', 'Super Admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->revokePermissionTo($names);
            }
        }

        $module = PermissionModule::where('name', 'Institute')->first();
        if ($module) {
            Permission::where('permission_module_id', $module->id)->forceDelete();
            $module->forceDelete();
        }
    }
};

<?php

use App\Models\PermissionModule;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $module = PermissionModule::firstOrCreate(
            ['name' => 'Geography'],
            ['name' => 'Geography']
        );

        foreach (['Create', 'Read', 'Update', 'Delete'] as $action) {
            Permission::firstOrCreate(
                [
                    'name' => "Geography {$action}",
                    'guard_name' => 'web',
                ],
                [
                    'permission_module_id' => $module->id,
                ]
            );
        }

        $full = [
            'Geography Create',
            'Geography Read',
            'Geography Update',
            'Geography Delete',
        ];

        foreach (['Developer', 'Super Admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($full);
            }
        }
    }

    public function down(): void
    {
        $names = [
            'Geography Create',
            'Geography Read',
            'Geography Update',
            'Geography Delete',
        ];

        foreach (['Developer', 'Super Admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->revokePermissionTo($names);
            }
        }

        $module = PermissionModule::where('name', 'Geography')->first();
        if ($module) {
            Permission::where('permission_module_id', $module->id)->forceDelete();
            $module->forceDelete();
        }
    }
};

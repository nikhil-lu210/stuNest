<?php

namespace Database\Seeders\Permission;

use App\Models\PermissionModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'Permission',
            'Role',
            'User',
        ];

        foreach ($modules as $module) {
            $permissionModule = PermissionModule::create([
                'name' => $module
            ]);

            $permissions = [
                'Create',
                'Read',
                'Update',
                'Delete',
            ];

            foreach ($permissions as $permission) {
                $permissionName = "{$permissionModule->name} {$permission}";

                Permission::create([
                    'permission_module_id' => $permissionModule->id,
                    'name' => $permissionName,
                ]);
            }
        }

        $instituteModule = PermissionModule::firstOrCreate(['name' => 'Institute']);
        foreach (['Create', 'Read', 'Update', 'Delete'] as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => "{$instituteModule->name} {$permission}",
                    'guard_name' => 'web',
                ],
                [
                    'permission_module_id' => $instituteModule->id,
                ]
            );
        }

        $geographyModule = PermissionModule::firstOrCreate(['name' => 'Geography']);
        foreach (['Create', 'Read', 'Update', 'Delete'] as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => "{$geographyModule->name} {$permission}",
                    'guard_name' => 'web',
                ],
                [
                    'permission_module_id' => $geographyModule->id,
                ]
            );
        }
    }
}
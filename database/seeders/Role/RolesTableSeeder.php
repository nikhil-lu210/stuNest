<?php

namespace Database\Seeders\Role;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Administration / Vuexy (guard: web) vs client portals (dedicated guards).
     */
    public function run(): void
    {
        $definitions = [
            [
                'name' => 'Developer',
                'guard' => 'web',
                'permissions' => [
                    'Permission Create', 'Permission Read', 'Permission Update', 'Permission Delete',
                    'Role Create', 'Role Read', 'Role Update', 'Role Delete',
                    'User Create', 'User Read', 'User Update', 'User Delete',
                    'Institute Create', 'Institute Read', 'Institute Update', 'Institute Delete',
                    'Geography Create', 'Geography Read', 'Geography Update', 'Geography Delete',
                ],
            ],
            [
                'name' => 'Super Admin',
                'guard' => 'web',
                'permissions' => [
                    'Permission Create', 'Permission Read', 'Permission Update', 'Permission Delete',
                    'Role Create', 'Role Read', 'Role Update', 'Role Delete',
                    'User Create', 'User Read', 'User Update', 'User Delete',
                    'Institute Create', 'Institute Read', 'Institute Update', 'Institute Delete',
                    'Geography Create', 'Geography Read', 'Geography Update', 'Geography Delete',
                ],
            ],
            ['name' => 'Institute Representative', 'guard' => 'institute', 'permissions' => []],
            ['name' => 'Student', 'guard' => 'student', 'permissions' => []],
            ['name' => 'Landlord', 'guard' => 'landlord', 'permissions' => []],
            ['name' => 'Agent', 'guard' => 'agent', 'permissions' => []],
        ];

        foreach ($definitions as $def) {
            $roleInstance = Role::firstOrCreate(
                ['name' => $def['name'], 'guard_name' => $def['guard']],
            );

            $permissions = $def['permissions'];
            if ($permissions !== []) {
                $roleInstance->syncPermissions($permissions);
            }
        }
    }
}

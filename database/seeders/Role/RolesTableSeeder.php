<?php

namespace Database\Seeders\Role;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Developer',
            'Super Admin',
            'Admin',
            'HR Manager',
            'Team Leader',
            'Employee',
            'Institute Representative',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);

            // Assign permissions to roles based on the module
            if ($role === 'Developer') {
                $permissions = [
                    'Permission Create',
                    'Permission Read',
                    'Permission Update',
                    'Permission Delete',
                    
                    'Role Create',
                    'Role Read',
                    'Role Update',
                    'Role Delete',
                    
                    'User Create',
                    'User Read',
                    'User Update',
                    'User Delete',

                    'Institute Create',
                    'Institute Read',
                    'Institute Update',
                    'Institute Delete',
                ];
            } elseif ($role === 'Super Admin') {
                $permissions = [
                    'Permission Create',
                    'Permission Read',
                    'Permission Update',
                    'Permission Delete',
                    
                    'Role Create',
                    'Role Read',
                    'Role Update',
                    'Role Delete',
                    
                    'User Create',
                    'User Read',
                    'User Update',
                    'User Delete',

                    'Institute Create',
                    'Institute Read',
                    'Institute Update',
                    'Institute Delete',
                ];
            } elseif ($role === 'Admin') {
                $permissions = [
                    'Permission Read',
                    
                    'Role Read',
                    
                    'User Create',
                    'User Read',
                    'User Update',
                    'User Delete',

                    'Institute Create',
                    'Institute Read',
                    'Institute Update',
                    'Institute Delete',
                ];
            } elseif ($role === 'HR Manager') {
                $permissions = [
                    'Permission Read',
                    
                    'Role Read',
                    
                    'User Create',
                    'User Read',
                    'User Update',
                ];
            } elseif ($role === 'Team Leader') {
                $permissions = [
                    'User Read',
                ];
            } elseif ($role === 'Employee') {
                $permissions = [
                    'User Read',
                ];
            } elseif ($role === 'Institute Representative') {
                $permissions = [];
            } else {
                $permissions = [
                    'User Read',
                ];
            }

            $roleInstance = Role::findByName($role);
            if ($permissions !== []) {
                $roleInstance->givePermissionTo($permissions);
            }
        }
    }
}
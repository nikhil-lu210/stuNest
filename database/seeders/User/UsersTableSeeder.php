<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a developer
        $developer = User::create([
            'userid' => strtoupper(Str::random(8)),
            'first_name' => 'Demo',
            'last_name' => 'Developer',
            'email' => 'developer@mail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        // Assign a role to the developer
        $developerRole = Role::findByName('Developer', 'web');
        $developer->assignRole($developerRole);
        $developer->forceFill(['developer_anchor' => true])->saveQuietly();

        // Create a superAdmin
        $superAdmin = User::create([
            'userid' => strtoupper(Str::random(8)),
            'first_name' => 'Demo',
            'last_name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        // Assign a role to the superAdmin
        $superAdminRole = Role::findByName('Super Admin', 'web');
        $superAdmin->assignRole($superAdminRole);
        $superAdmin->forceFill(['super_admin_anchor' => true])->saveQuietly();
    }
}

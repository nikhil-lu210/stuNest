<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        $role = Role::where('name', 'Agent')->where('guard_name', 'web')->first();
        if ($role) {
            $role->delete();
        }
    }
};

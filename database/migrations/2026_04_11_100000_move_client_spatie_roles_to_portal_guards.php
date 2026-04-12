<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Client-facing roles use dedicated auth guards (see config/auth.php).
     * Administration staff roles remain on the "web" guard (Vuexy dashboard).
     */
    public function up(): void
    {
        $map = [
            'Student' => 'student',
            'Landlord' => 'landlord',
            'Agent' => 'agent',
            'Institute Representative' => 'institute',
        ];

        foreach ($map as $name => $guard) {
            DB::table('roles')
                ->where('name', $name)
                ->where('guard_name', 'web')
                ->update(['guard_name' => $guard]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $map = [
            'Student' => 'student',
            'Landlord' => 'landlord',
            'Agent' => 'agent',
            'Institute Representative' => 'institute',
        ];

        foreach ($map as $name => $guard) {
            DB::table('roles')
                ->where('name', $name)
                ->where('guard_name', $guard)
                ->update(['guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};

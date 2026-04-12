<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('developer_anchor')->default(false)->after('remember_token');
            $table->boolean('super_admin_anchor')->default(false)->after('developer_anchor');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['developer_anchor', 'super_admin_anchor']);
        });
    }
};

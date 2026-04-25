<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temporary_users', function (Blueprint $table) {
            $table->string('institute_name')->nullable()->after('company_name');
            $table->string('institute_email_code', 125)->nullable()->after('institute_name');
            $table->string('department')->nullable()->after('institute_email_code');
        });
    }

    public function down(): void
    {
        Schema::table('temporary_users', function (Blueprint $table) {
            $table->dropColumn(['institute_name', 'institute_email_code', 'department']);
        });
    }
};

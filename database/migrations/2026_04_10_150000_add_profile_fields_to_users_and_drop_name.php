<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('role', 32)->nullable()->comment('Slug: admin, student, landlord, agent');
            $table->date('dob')->nullable();
            $table->foreignId('institution_id')->nullable()->constrained('institutes')->nullOnDelete();
            $table->string('student_id_number')->nullable();
            $table->string('course_level')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('company_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('license_number')->nullable();
            $table->text('office_address')->nullable();
            $table->string('job_title')->nullable();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE users MODIFY account_status VARCHAR(32) NOT NULL DEFAULT 'unverified'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE users MODIFY account_status VARCHAR(32) NOT NULL DEFAULT 'active'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'dob',
                'student_id_number',
                'course_level',
                'graduation_year',
                'company_name',
                'billing_address',
                'agency_name',
                'license_number',
                'office_address',
                'job_title',
            ]);
        });

    }
};

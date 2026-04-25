<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $backfillProfile = false;
        Schema::table('users', function (Blueprint $table) use (&$backfillProfile) {
            if (! Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id', 100)->nullable();
            }
            if (! Schema::hasColumn('users', 'country_of_citizen')) {
                $table->string('country_of_citizen', 2)->nullable();
            }
            if (! Schema::hasColumn('users', 'is_profile_complete')) {
                $table->boolean('is_profile_complete')->default(false);
                $backfillProfile = true;
            }
        });

        if ($backfillProfile) {
            // Existing users pre–student registration flow: treat as already complete.
            DB::table('users')->update(['is_profile_complete' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_profile_complete')) {
                $table->dropColumn('is_profile_complete');
            }
            if (Schema::hasColumn('users', 'country_of_citizen')) {
                $table->dropColumn('country_of_citizen');
            }
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }
};

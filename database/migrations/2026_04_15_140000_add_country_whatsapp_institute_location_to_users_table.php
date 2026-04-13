<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('institution_id')->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('institute_location_id')->nullable()->after('country_id')->constrained('institute_locations')->nullOnDelete()->cascadeOnUpdate();
            $table->string('whatsapp', 50)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institute_location_id');
            $table->dropConstrainedForeignId('country_id');
            $table->dropColumn('whatsapp');
        });
    }
};

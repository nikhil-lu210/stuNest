<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institute_locations', function (Blueprint $table) {
            $table->dropColumn(['city', 'country']);
        });

        Schema::table('institute_locations', function (Blueprint $table) {
            $table->foreignId('country_id')->after('institute_id')->nullable()->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('institute_locations', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['area_id']);
            $table->dropColumn(['country_id', 'city_id', 'area_id']);
        });

        Schema::table('institute_locations', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->string('country', 2)->default('GB');
        });
    }
};

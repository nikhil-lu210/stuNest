<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('user_id')->constrained('countries')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained('areas')->cascadeOnUpdate()->nullOnDelete();
            $table->string('map_link')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('distance_university_km', 8, 2)->nullable();
            $table->decimal('distance_transit_km', 8, 2)->nullable();
            $table->string('bed_type', 32)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['area_id']);
            $table->dropColumn([
                'country_id',
                'city_id',
                'area_id',
                'map_link',
                'latitude',
                'longitude',
                'distance_university_km',
                'distance_transit_km',
                'bed_type',
            ]);
        });
    }
};

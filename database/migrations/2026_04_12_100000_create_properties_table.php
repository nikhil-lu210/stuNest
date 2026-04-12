<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $table->string('listing_category', 32);
            $table->string('property_type', 32);
            $table->unsignedTinyInteger('bedrooms');
            $table->unsignedTinyInteger('bathrooms');
            $table->string('bathroom_type', 32);
            $table->boolean('is_furnished')->default(false);

            $table->string('rent_duration', 16);
            $table->unsignedInteger('rent_amount')->comment('Amount in minor currency units or whole pounds per product decision');
            $table->string('bills_included', 16);
            $table->json('included_bills')->nullable();
            $table->string('min_contract_length', 32);
            $table->boolean('provides_agreement')->default(false);
            $table->string('deposit_required', 32);
            $table->string('rent_for', 32);

            $table->json('suitable_for');
            $table->string('flatmate_vibe', 32)->nullable();
            $table->json('house_rules');
            $table->json('amenities');

            $table->string('status', 32)->default('draft');
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('available_beds')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

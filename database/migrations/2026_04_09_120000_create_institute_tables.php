<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Institution name should be unique and used as a slug');
            $table->string('email_code', 125)->unique()->comment('Institutional email suffix e.g. @nup.ac.cy, this is used to generate the institutional email address for the institution');
            $table->string('slug')->unique()->comment('Institution slug should be unique and used as a slug');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('institute_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('institutes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->comment('Branch or campus name');
            $table->string('address_line_1')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode', 32)->nullable();
            $table->string('country', 2)->default('GB');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('institute_representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('institutes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('institute_location_id')->constrained('institute_locations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['institute_location_id', 'user_id'], 'institute_location_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_representatives');
        Schema::dropIfExists('institute_locations');
        Schema::dropIfExists('institutes');
    }
};

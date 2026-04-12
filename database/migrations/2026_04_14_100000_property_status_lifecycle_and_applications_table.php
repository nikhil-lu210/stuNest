<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('properties', 'capacity')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->unsignedSmallInteger('capacity')->default(1)->after('status');
                $table->unsignedSmallInteger('available_beds')->default(1)->after('capacity');
            });
        }

        if (Schema::hasTable('properties')) {
            DB::table('properties')->where('status', 'active')->update(['status' => 'published']);
            DB::table('properties')->where('status', 'rented')->update(['status' => 'let_agreed']);
            DB::table('properties')->where('status', 'inactive')->update(['status' => 'archived']);
        }

        if (Schema::hasTable('properties')) {
            DB::table('properties')->orderBy('id')->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    $capacity = max(1, (int) ($row->bedrooms ?? 1));
                    $updates = [
                        'capacity' => $capacity,
                        'available_beds' => $capacity,
                    ];
                    if ($row->status === 'let_agreed') {
                        $updates['available_beds'] = 0;
                    }
                    DB::table('properties')->where('id', $row->id)->update($updates);
                }
            });
        }

        if (! Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('properties')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('status', 32)->default('pending');
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();

                $table->unique(['property_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');

        if (Schema::hasColumn('properties', 'capacity')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn(['capacity', 'available_beds']);
            });
        }
    }
};

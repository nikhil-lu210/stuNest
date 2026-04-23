<?php

use App\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->change();
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->foreignId('support_institute_id')->nullable()->constrained('institutes')->cascadeOnDelete();
            $table->foreignId('support_student_id')->nullable()->constrained('users')->cascadeOnDelete();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['support_institute_id', 'support_student_id', 'created_at'], 'messages_institute_student_created_idx');
        });
    }

    public function down(): void
    {
        Message::query()->whereNull('application_id')->delete();

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_institute_student_created_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['support_institute_id']);
            $table->dropForeign(['support_student_id']);
            $table->dropColumn(['support_institute_id', 'support_student_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable(false)->change();
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
        });
    }
};

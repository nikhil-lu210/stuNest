<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->cascadeOnDelete();
            $table->foreignId('support_institute_id')->nullable()->constrained('institutes')->cascadeOnDelete();
            $table->foreignId('support_student_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['application_id', 'created_at']);
            $table->index(['support_institute_id', 'support_student_id', 'created_at'], 'messages_institute_student_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

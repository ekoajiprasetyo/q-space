<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_request_id')->constrained('file_requests')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users');
            $table->string('original_filename');
            $table->string('google_drive_file_id');
            $table->string('google_drive_url');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->enum('status', ['submitted', 'late', 'reviewed', 'rejected'])->default('submitted');
            $table->text('teacher_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_submissions');
    }
};

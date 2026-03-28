<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('upload_tasks')) {
            Schema::create('upload_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('file_request_id')->constrained('file_requests')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
                $table->string('submitter_name');
                $table->string('class_name');
                $table->text('student_notes')->nullable();
                $table->string('original_filename');
                $table->string('mime_type', 150)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('staged_path');
                $table->string('student_folder_id');
                $table->enum('status', ['queued', 'processing', 'uploaded', 'failed'])->default('queued');
                $table->unsignedInteger('attempts')->default(0);
                $table->text('last_error')->nullable();
                $table->string('google_drive_file_id')->nullable();
                $table->string('google_drive_url')->nullable();
                $table->timestamp('queued_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->index(['file_request_id', 'status']);
                $table->index(['submitter_name', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_tasks');
    }
};

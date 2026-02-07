<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users');
            $table->unsignedBigInteger('class_id')->nullable(); // Link to class system later
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('allowed_extensions')->nullable();
            $table->integer('max_file_size')->default(10); // MB
            $table->timestamp('deadline')->nullable();
            $table->string('google_drive_folder_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_requests');
    }
};

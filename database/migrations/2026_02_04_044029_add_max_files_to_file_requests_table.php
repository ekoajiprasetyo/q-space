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
        Schema::table('file_requests', function (Blueprint $table) {
            $table->integer('max_files')->default(1)->after('max_file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_requests', function (Blueprint $table) {
            $table->dropColumn('max_files');
        });
    }
};

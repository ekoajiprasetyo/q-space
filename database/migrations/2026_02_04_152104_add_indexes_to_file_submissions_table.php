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
        Schema::table('file_submissions', function (Blueprint $table) {
            $table->index(['file_request_id', 'submitter_name']);
            $table->index('submitter_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_submissions', function (Blueprint $table) {
            $table->dropIndex(['file_request_id', 'submitter_name']);
            $table->dropIndex(['submitter_name']);
        });
    }
};

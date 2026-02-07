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
            $table->unsignedBigInteger('student_id')->nullable()->change();
            
            if (!Schema::hasColumn('file_submissions', 'submitter_name')) {
                $table->string('submitter_name')->nullable()->after('student_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_submissions', function (Blueprint $table) {
            // Reverting nullable is tricky if there are null values, but for this dev env it's generally okay to try
            // or just leave it nullable. But strictly:
            $table->unsignedBigInteger('student_id')->nullable(false)->change();
            $table->dropColumn('submitter_name');
        });
    }
};

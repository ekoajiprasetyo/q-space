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
        Schema::table('user_google_tokens', function (Blueprint $table) {
            $table->string('google_email')->nullable()->after('user_id');
            $table->string('google_name')->nullable()->after('google_email');
            $table->string('google_avatar')->nullable()->after('google_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_google_tokens', function (Blueprint $table) {
            $table->dropColumn(['google_email', 'google_name', 'google_avatar']);
        });
    }
};

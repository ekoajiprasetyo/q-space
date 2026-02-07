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
        Schema::table('qr_texts', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique()->after('user_id');
            $table->string('title')->nullable()->after('slug');
            $table->longText('content')->after('title');
            $table->string('theme')->default('default')->after('content');
            $table->integer('views')->default(0)->after('theme');
            $table->boolean('is_active')->default(true)->after('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_texts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'slug', 'title', 'content', 'theme', 'views', 'is_active']);
        });
    }
};

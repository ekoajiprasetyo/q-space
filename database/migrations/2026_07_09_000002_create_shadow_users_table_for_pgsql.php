<?php

use App\Support\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        $table = PostgresSchema::qualify(PostgresSchema::app(), 'users');

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user')->index();
            $table->string('subscription_status')->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->rememberToken();
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('nickname')->nullable();
            $table->string('grade')->nullable();
            $table->string('gender')->nullable();
            $table->string('student_id')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('last_session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        Schema::dropIfExists(PostgresSchema::qualify(PostgresSchema::app(), 'users'));
    }
};

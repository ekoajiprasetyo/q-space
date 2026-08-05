<?php

use App\Support\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = PostgresSchema::usesPgsql()
            ? PostgresSchema::qualify(PostgresSchema::app(), 'static_qr_codes')
            : 'static_qr_codes';

        if (Schema::hasTable($tableName)) {
            return;
        }

        $ownerTable = PostgresSchema::usesPgsql()
            ? PostgresSchema::qualify(PostgresSchema::core(), 'users')
            : 'users';

        Schema::create($tableName, function (Blueprint $table) use ($ownerTable) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on($ownerTable)->cascadeOnDelete();
            $table->string('name', 255);
            $table->text('content');
            $table->json('options');
            $table->string('fingerprint', 64);
            $table->timestamps();
            $table->unique(['user_id', 'fingerprint']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        $tableName = PostgresSchema::usesPgsql()
            ? PostgresSchema::qualify(PostgresSchema::app(), 'static_qr_codes')
            : 'static_qr_codes';

        Schema::dropIfExists($tableName);
    }
};

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

        $cacheTable = PostgresSchema::qualify(PostgresSchema::app(), 'cache');
        $locksTable = PostgresSchema::qualify(PostgresSchema::app(), 'cache_locks');

        if (!Schema::hasTable($cacheTable)) {
            Schema::create($cacheTable, function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration')->index();
            });
        }

        if (!Schema::hasTable($locksTable)) {
            Schema::create($locksTable, function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration')->index();
            });
        }
    }

    public function down(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        Schema::dropIfExists(PostgresSchema::qualify(PostgresSchema::app(), 'cache_locks'));
        Schema::dropIfExists(PostgresSchema::qualify(PostgresSchema::app(), 'cache'));
    }
};

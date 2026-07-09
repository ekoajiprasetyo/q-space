<?php

use App\Support\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        DB::statement('CREATE SCHEMA IF NOT EXISTS "'.PostgresSchema::app().'"');
    }

    public function down(): void
    {
        // Intentionally left blank. Phase 1 schema rollback is handled table-by-table.
    }
};

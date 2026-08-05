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
            ? PostgresSchema::qualify(PostgresSchema::app(), 'short_links')
            : 'short_links';

        if (Schema::hasColumn($tableName, 'source')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->string('source', 30)->default('path')->after('name');
            $table->index(['user_id', 'source']);
        });
    }

    public function down(): void
    {
        $tableName = PostgresSchema::usesPgsql()
            ? PostgresSchema::qualify(PostgresSchema::app(), 'short_links')
            : 'short_links';

        if (!Schema::hasColumn($tableName, 'source')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropIndex(['user_id', 'source']);
            $table->dropColumn('source');
        });
    }
};

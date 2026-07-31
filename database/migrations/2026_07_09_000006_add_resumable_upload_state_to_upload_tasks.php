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
            ? PostgresSchema::qualify(PostgresSchema::app(), 'upload_tasks')
            : 'upload_tasks';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'resumable_upload_uri')) {
                $table->text('resumable_upload_uri')->nullable()->after('google_drive_url');
            }

            if (!Schema::hasColumn($tableName, 'uploaded_bytes')) {
                $table->unsignedBigInteger('uploaded_bytes')->default(0)->after('resumable_upload_uri');
                $table->index('uploaded_bytes');
            }

            if (!Schema::hasColumn($tableName, 'upload_session_started_at')) {
                $table->timestamp('upload_session_started_at')->nullable()->after('uploaded_bytes');
            }

            if (!Schema::hasColumn($tableName, 'last_chunk_uploaded_at')) {
                $table->timestamp('last_chunk_uploaded_at')->nullable()->after('upload_session_started_at');
            }
        });
    }

    public function down(): void
    {
        $tableName = PostgresSchema::usesPgsql()
            ? PostgresSchema::qualify(PostgresSchema::app(), 'upload_tasks')
            : 'upload_tasks';

        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'last_chunk_uploaded_at')) {
                $table->dropColumn('last_chunk_uploaded_at');
            }

            if (Schema::hasColumn($tableName, 'upload_session_started_at')) {
                $table->dropColumn('upload_session_started_at');
            }

            if (Schema::hasColumn($tableName, 'uploaded_bytes')) {
                $table->dropIndex(['uploaded_bytes']);
                $table->dropColumn('uploaded_bytes');
            }

            if (Schema::hasColumn($tableName, 'resumable_upload_uri')) {
                $table->dropColumn('resumable_upload_uri');
            }
        });
    }
};

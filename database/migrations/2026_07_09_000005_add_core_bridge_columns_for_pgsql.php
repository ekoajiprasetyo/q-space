<?php

use App\Support\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        $appSchema = PostgresSchema::app();
        $coreSchema = PostgresSchema::core();

        Schema::table(PostgresSchema::qualify($appSchema, 'file_requests'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'file_requests'), 'teacher_core_user_id')) {
                $table->unsignedBigInteger('teacher_core_user_id')->nullable()->after('teacher_id');
                $table->index('teacher_core_user_id');
            }
        });

        Schema::table(PostgresSchema::qualify($appSchema, 'file_submissions'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'file_submissions'), 'student_core_student_id')) {
                $table->unsignedBigInteger('student_core_student_id')->nullable()->after('student_id');
                $table->index('student_core_student_id');
            }
        });

        Schema::table(PostgresSchema::qualify($appSchema, 'user_google_tokens'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'user_google_tokens'), 'core_user_id')) {
                $table->unsignedBigInteger('core_user_id')->nullable()->after('user_id');
                $table->index('core_user_id');
            }
        });

        Schema::table(PostgresSchema::qualify($appSchema, 'short_links'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'short_links'), 'core_user_id')) {
                $table->unsignedBigInteger('core_user_id')->nullable()->after('user_id');
                $table->index('core_user_id');
            }
        });

        Schema::table(PostgresSchema::qualify($appSchema, 'qr_texts'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'qr_texts'), 'core_user_id')) {
                $table->unsignedBigInteger('core_user_id')->nullable()->after('user_id');
                $table->index('core_user_id');
            }
        });

        Schema::table(PostgresSchema::qualify($appSchema, 'upload_tasks'), function (Blueprint $table) {
            if (!Schema::hasColumn(PostgresSchema::qualify(PostgresSchema::app(), 'upload_tasks'), 'teacher_core_user_id')) {
                $table->unsignedBigInteger('teacher_core_user_id')->nullable()->after('teacher_id');
                $table->index('teacher_core_user_id');
            }
        });

        $this->addForeignKeyIfMissing(
            "{$appSchema}.file_requests",
            'file_requests_teacher_core_user_id_foreign',
            '(teacher_core_user_id)',
            "{$coreSchema}.users(id)",
            'SET NULL'
        );

        $this->addForeignKeyIfMissing(
            "{$appSchema}.file_submissions",
            'file_submissions_student_core_student_id_foreign',
            '(student_core_student_id)',
            "{$coreSchema}.students(id)",
            'SET NULL'
        );

        foreach (['user_google_tokens', 'short_links', 'qr_texts'] as $tableName) {
            $this->addForeignKeyIfMissing(
                "{$appSchema}.{$tableName}",
                "{$tableName}_core_user_id_foreign",
                '(core_user_id)',
                "{$coreSchema}.users(id)",
                'SET NULL'
            );
        }

        $this->addForeignKeyIfMissing(
            "{$appSchema}.upload_tasks",
            'upload_tasks_teacher_core_user_id_foreign',
            '(teacher_core_user_id)',
            "{$coreSchema}.users(id)",
            'SET NULL'
        );
    }

    public function down(): void
    {
        if (!PostgresSchema::usesPgsql()) {
            return;
        }

        $appSchema = PostgresSchema::app();

        foreach ([
            ['file_requests', 'file_requests_teacher_core_user_id_foreign', 'teacher_core_user_id'],
            ['file_submissions', 'file_submissions_student_core_student_id_foreign', 'student_core_student_id'],
            ['user_google_tokens', 'user_google_tokens_core_user_id_foreign', 'core_user_id'],
            ['short_links', 'short_links_core_user_id_foreign', 'core_user_id'],
            ['qr_texts', 'qr_texts_core_user_id_foreign', 'core_user_id'],
            ['upload_tasks', 'upload_tasks_teacher_core_user_id_foreign', 'teacher_core_user_id'],
        ] as [$tableName, $constraint, $column]) {
            DB::statement(sprintf(
                'ALTER TABLE "%s"."%s" DROP CONSTRAINT IF EXISTS "%s"',
                $appSchema,
                $tableName,
                $constraint
            ));

            Schema::table(PostgresSchema::qualify($appSchema, $tableName), function (Blueprint $table) use ($column) {
                if (Schema::hasColumn($table->getTable(), $column)) {
                    $table->dropIndex([$column]);
                    $table->dropColumn($column);
                }
            });
        }
    }

    private function addForeignKeyIfMissing(
        string $qualifiedTable,
        string $constraintName,
        string $localColumns,
        string $qualifiedReference,
        string $onDelete
    ): void {
        $sql = <<<SQL
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = '{$constraintName}'
    ) THEN
        ALTER TABLE {$qualifiedTable}
        ADD CONSTRAINT {$constraintName}
        FOREIGN KEY {$localColumns}
        REFERENCES {$qualifiedReference}
        ON DELETE {$onDelete};
    END IF;
END $$;
SQL;

        DB::unprepared($sql);
    }
};

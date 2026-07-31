<?php

use App\Support\PostgresSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = true;

    public function up(): void
    {
        if (! PostgresSchema::usesPgsql()) {
            return;
        }

        $app = $this->identifier(PostgresSchema::app());
        $core = $this->identifier(PostgresSchema::core());

        foreach ([
            "{$core}.users",
            "{$core}.students",
            "{$app}.file_requests",
            "{$app}.file_submissions",
            "{$app}.user_google_tokens",
            "{$app}.short_links",
            "{$app}.qr_texts",
            "{$app}.upload_tasks",
        ] as $table) {
            if (! Schema::hasTable($table)) {
                throw new \RuntimeException("Identity cutover aborted: required table {$table} is missing.");
            }
        }

        $bridgeColumns = [
            'file_requests' => 'teacher_core_user_id',
            'file_submissions' => 'student_core_student_id',
            'user_google_tokens' => 'core_user_id',
            'short_links' => 'core_user_id',
            'qr_texts' => 'core_user_id',
            'upload_tasks' => 'teacher_core_user_id',
        ];

        foreach ($bridgeColumns as $table => $column) {
            if (! Schema::hasColumn("{$app}.{$table}", $column)) {
                throw new \RuntimeException(
                    "Identity cutover aborted: bridge column {$app}.{$table}.{$column} is missing."
                );
            }
        }

        // Fill every transitional bridge before changing the canonical columns.
        DB::statement(<<<SQL
            UPDATE "{$app}".file_requests target
            SET teacher_core_user_id = users.id
            FROM "{$core}".users users
            WHERE target.teacher_core_user_id IS NULL
              AND target.teacher_id = users.id
        SQL);

        foreach (['user_google_tokens', 'short_links', 'qr_texts'] as $table) {
            DB::statement(<<<SQL
                UPDATE "{$app}"."{$table}" target
                SET core_user_id = users.id
                FROM "{$core}".users users
                WHERE target.core_user_id IS NULL
                  AND target.user_id = users.id
            SQL);
        }

        DB::statement(<<<SQL
            UPDATE "{$app}".upload_tasks target
            SET teacher_core_user_id = users.id
            FROM "{$core}".users users
            WHERE target.teacher_core_user_id IS NULL
              AND target.teacher_id = users.id
        SQL);

        DB::statement(<<<SQL
            UPDATE "{$app}".file_submissions target
            SET student_core_student_id = students.id
            FROM "{$core}".students students
            WHERE target.student_core_student_id IS NULL
              AND target.student_id = students.user_id
        SQL);

        $this->assertNoRows(
            'file_requests without a core teacher',
            "SELECT count(*) FROM \"{$app}\".file_requests WHERE teacher_core_user_id IS NULL"
        );
        $this->assertNoRows(
            'Google tokens without a core user',
            "SELECT count(*) FROM \"{$app}\".user_google_tokens WHERE core_user_id IS NULL"
        );
        $this->assertNoRows(
            'short links without a core user',
            "SELECT count(*) FROM \"{$app}\".short_links WHERE core_user_id IS NULL"
        );
        $this->assertNoRows(
            'QR texts without a core user',
            "SELECT count(*) FROM \"{$app}\".qr_texts WHERE core_user_id IS NULL"
        );
        $this->assertNoRows(
            'upload tasks without a core teacher',
            "SELECT count(*) FROM \"{$app}\".upload_tasks WHERE teacher_core_user_id IS NULL"
        );
        $this->assertNoRows(
            'registered submissions without a core student',
            "SELECT count(*) FROM \"{$app}\".file_submissions WHERE student_id IS NOT NULL AND student_core_student_id IS NULL"
        );
        $this->assertNoRows(
            'duplicate Google Drive token owners',
            "SELECT count(*) FROM (SELECT core_user_id FROM \"{$app}\".user_google_tokens GROUP BY core_user_id HAVING count(*) > 1) duplicates"
        );

        // Make the original domain columns canonical, now pointing directly at core.
        DB::statement("UPDATE \"{$app}\".file_requests SET teacher_id = teacher_core_user_id");
        DB::statement("UPDATE \"{$app}\".user_google_tokens SET user_id = core_user_id");
        DB::statement("UPDATE \"{$app}\".short_links SET user_id = core_user_id");
        DB::statement("UPDATE \"{$app}\".qr_texts SET user_id = core_user_id");
        DB::statement("UPDATE \"{$app}\".upload_tasks SET teacher_id = teacher_core_user_id");
        DB::statement(<<<SQL
            UPDATE "{$app}".file_submissions
            SET student_id = student_core_student_id
            WHERE student_core_student_id IS NOT NULL
        SQL);

        $legacyConstraints = [
            'file_requests' => [
                'file_requests_teacher_id_foreign',
                'file_requests_teacher_core_user_id_foreign',
            ],
            'file_submissions' => [
                'file_submissions_student_id_foreign',
                'file_submissions_student_core_student_id_foreign',
            ],
            'user_google_tokens' => [
                'user_google_tokens_user_id_foreign',
                'user_google_tokens_core_user_id_foreign',
            ],
            'short_links' => [
                'short_links_user_id_foreign',
                'short_links_core_user_id_foreign',
            ],
            'qr_texts' => [
                'qr_texts_user_id_foreign',
                'qr_texts_core_user_id_foreign',
            ],
            'upload_tasks' => [
                'upload_tasks_teacher_id_foreign',
                'upload_tasks_teacher_core_user_id_foreign',
            ],
        ];

        foreach ($legacyConstraints as $table => $constraints) {
            foreach ($constraints as $constraint) {
                DB::statement(
                    "ALTER TABLE \"{$app}\".\"{$table}\" DROP CONSTRAINT IF EXISTS \"{$constraint}\""
                );
            }
        }

        DB::statement(<<<SQL
            ALTER TABLE "{$app}".file_requests
            ADD CONSTRAINT file_requests_teacher_id_foreign
            FOREIGN KEY (teacher_id) REFERENCES "{$core}".users(id) ON DELETE RESTRICT
        SQL);
        DB::statement(<<<SQL
            ALTER TABLE "{$app}".file_submissions
            ADD CONSTRAINT file_submissions_student_id_foreign
            FOREIGN KEY (student_id) REFERENCES "{$core}".students(id) ON DELETE SET NULL
        SQL);
        DB::statement(<<<SQL
            ALTER TABLE "{$app}".user_google_tokens
            ADD CONSTRAINT user_google_tokens_user_id_foreign
            FOREIGN KEY (user_id) REFERENCES "{$core}".users(id) ON DELETE CASCADE
        SQL);
        DB::statement(<<<SQL
            ALTER TABLE "{$app}".short_links
            ADD CONSTRAINT short_links_user_id_foreign
            FOREIGN KEY (user_id) REFERENCES "{$core}".users(id) ON DELETE CASCADE
        SQL);
        DB::statement(<<<SQL
            ALTER TABLE "{$app}".qr_texts
            ADD CONSTRAINT qr_texts_user_id_foreign
            FOREIGN KEY (user_id) REFERENCES "{$core}".users(id) ON DELETE CASCADE
        SQL);
        DB::statement(<<<SQL
            ALTER TABLE "{$app}".upload_tasks
            ADD CONSTRAINT upload_tasks_teacher_id_foreign
            FOREIGN KEY (teacher_id) REFERENCES "{$core}".users(id) ON DELETE CASCADE
        SQL);

        DB::statement(<<<SQL
            CREATE UNIQUE INDEX IF NOT EXISTS user_google_tokens_user_id_unique
            ON "{$app}".user_google_tokens(user_id)
        SQL);

        foreach ($bridgeColumns as $table => $column) {
            DB::statement("ALTER TABLE \"{$app}\".\"{$table}\" DROP COLUMN \"{$column}\"");
        }

        if (Schema::hasTable("{$app}.users")) {
            $remainingReferences = (int) DB::scalar(<<<SQL
                SELECT count(*)
                FROM pg_constraint
                WHERE contype = 'f'
                  AND confrelid = '"{$app}".users'::regclass
            SQL);

            if ($remainingReferences !== 0) {
                throw new \RuntimeException(
                    "Identity cutover aborted: {$remainingReferences} foreign key(s) still reference {$app}.users."
                );
            }

            DB::statement("DROP TABLE \"{$app}\".users");
        }
    }

    public function down(): void
    {
        if (PostgresSchema::usesPgsql()) {
            throw new \RuntimeException(
                'The shared identity cutover is intentionally irreversible. Restore the pre-cutover database backup instead.'
            );
        }
    }

    private function assertNoRows(string $label, string $countQuery): void
    {
        $count = (int) DB::scalar($countQuery);

        if ($count > 0) {
            throw new \RuntimeException("Identity cutover aborted: {$count} {$label}.");
        }
    }

    private function identifier(string $value): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value)) {
            throw new \RuntimeException("Invalid PostgreSQL schema identifier: {$value}");
        }

        return $value;
    }
};

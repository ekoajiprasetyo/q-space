<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\PostgresSchema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IdentityStatusCommand extends Command
{
    protected $signature = 'qspace:identity-status';

    protected $description = 'Verify that Q-Space authentication and ownership use the shared core identity';

    public function handle(): int
    {
        $this->info('Q-Space shared identity status');

        if (! PostgresSchema::usesPgsql()) {
            $this->warn('Non-PostgreSQL environments keep the standalone users table for local development.');

            return self::SUCCESS;
        }

        $app = PostgresSchema::app();
        $core = PostgresSchema::core();
        $failures = [];

        $this->line('- auth table: '.(new User)->getTable());
        $this->line('- shared users: '.(Schema::hasTable("{$core}.users") ? 'ready' : 'missing'));
        $this->line('- shared students: '.(Schema::hasTable("{$core}.students") ? 'ready' : 'missing'));
        $this->line('- shadow users: '.(Schema::hasTable("{$app}.users") ? 'present' : 'removed'));

        if ((new User)->getTable() !== "{$core}.users") {
            $failures[] = 'Auth model does not resolve to core.users.';
        }

        if (! Schema::hasTable("{$core}.users") || ! Schema::hasTable("{$core}.students")) {
            $failures[] = 'Shared identity tables are incomplete.';
        }

        if (Schema::hasTable("{$app}.users")) {
            $failures[] = 'The shadow users table is still present.';
        }

        $legacyBridgeColumns = [
            'file_requests' => 'teacher_core_user_id',
            'file_submissions' => 'student_core_student_id',
            'user_google_tokens' => 'core_user_id',
            'short_links' => 'core_user_id',
            'qr_texts' => 'core_user_id',
            'upload_tasks' => 'teacher_core_user_id',
        ];

        foreach ($legacyBridgeColumns as $table => $column) {
            if (Schema::hasColumn("{$app}.{$table}", $column)) {
                $failures[] = "Legacy bridge column {$app}.{$table}.{$column} is still present.";
            }
        }

        $expectedReferences = [
            'file_requests_teacher_id_foreign' => "{$core}.users",
            'file_submissions_student_id_foreign' => "{$core}.students",
            'user_google_tokens_user_id_foreign' => "{$core}.users",
            'short_links_user_id_foreign' => "{$core}.users",
            'qr_texts_user_id_foreign' => "{$core}.users",
            'upload_tasks_teacher_id_foreign' => "{$core}.users",
        ];

        $references = collect(DB::select(<<<'SQL'
            SELECT con.conname,
                   referenced_ns.nspname || '.' || referenced.relname AS referenced_table
            FROM pg_constraint con
            JOIN pg_class source ON source.oid = con.conrelid
            JOIN pg_namespace source_ns ON source_ns.oid = source.relnamespace
            JOIN pg_class referenced ON referenced.oid = con.confrelid
            JOIN pg_namespace referenced_ns ON referenced_ns.oid = referenced.relnamespace
            WHERE con.contype = 'f'
              AND source_ns.nspname = ?
        SQL, [$app]))->keyBy('conname');

        foreach ($expectedReferences as $constraint => $expectedTable) {
            $actual = $references->get($constraint)?->referenced_table;
            $this->line("- {$constraint}: ".($actual ?? 'missing'));

            if ($actual !== $expectedTable) {
                $failures[] = "{$constraint} must reference {$expectedTable}.";
            }
        }

        if ($failures === []) {
            $this->info('Identity cutover is healthy: Q-Space uses core identity only.');

            return self::SUCCESS;
        }

        $this->error('Identity cutover verification failed:');
        foreach ($failures as $failure) {
            $this->line('- '.$failure);
        }

        return self::FAILURE;
    }
}

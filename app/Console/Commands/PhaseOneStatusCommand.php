<?php

namespace App\Console\Commands;

use App\Support\PostgresSchema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PhaseOneStatusCommand extends Command
{
    protected $signature = 'qspace:phase1-status';

    protected $description = 'Show Phase 1 PostgreSQL bridge readiness for Q-Space';

    public function handle(): int
    {
        $this->info('Q-Space Phase 1 status');
        $this->newLine();

        $default = config('database.default');
        $driver = config("database.connections.{$default}.driver");

        $this->line(sprintf('- default connection: %s', $default));
        $this->line(sprintf('- driver: %s', $driver));
        $this->line(sprintf('- app schema: %s', config('database.schemas.app', 'public')));
        $this->line(sprintf('- core schema: %s', config('database.schemas.core', 'core')));
        $this->line(sprintf('- search path: %s', config('database.schemas.search_path', 'public')));
        $this->line(sprintf(
            '- local registration enabled: %s',
            config('app.auth_bridge.allow_local_registration', true) ? 'yes' : 'no'
        ));
        $this->line(sprintf(
            '- google autocreate enabled: %s',
            config('app.auth_bridge.allow_google_user_autocreate', true) ? 'yes' : 'no'
        ));

        if (!PostgresSchema::usesPgsql()) {
            $this->newLine();
            $this->warn('Environment ini belum menggunakan PostgreSQL. Migration bridge belum bisa diverifikasi penuh.');

            return self::SUCCESS;
        }

        $app = PostgresSchema::app();
        $core = PostgresSchema::core();

        $this->newLine();
        $this->info('Table readiness');

        foreach ([
            "{$app}.users",
            "{$app}.file_requests",
            "{$app}.file_submissions",
            "{$app}.user_google_tokens",
            "{$app}.short_links",
            "{$app}.qr_texts",
            "{$app}.upload_tasks",
            "{$app}.cache",
            "{$app}.cache_locks",
            "{$app}.job_batches",
            "{$core}.users",
            "{$core}.students",
        ] as $table) {
            $this->line(sprintf('- %s: %s', $table, Schema::hasTable($table) ? 'ready' : 'missing'));
        }

        if (
            !Schema::hasTable("{$app}.file_requests") ||
            !Schema::hasTable("{$app}.file_submissions") ||
            !Schema::hasTable("{$core}.users")
        ) {
            $this->newLine();
            $this->warn('Sebagian tabel bridge belum ada, jadi audit mapping belum dijalankan.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('Bridge coverage');

        $rows = DB::select("
            select *
            from (
                select 'file_requests.teacher_core_user_id' as relation_name,
                       count(*) filter (where teacher_id is not null) as source_rows,
                       count(*) filter (where teacher_id is not null and teacher_core_user_id is not null) as mapped_rows
                from {$app}.file_requests
                union all
                select 'file_submissions.student_core_student_id',
                       count(*) filter (where student_id is not null),
                       count(*) filter (where student_id is not null and student_core_student_id is not null)
                from {$app}.file_submissions
                union all
                select 'user_google_tokens.core_user_id',
                       count(*) filter (where user_id is not null),
                       count(*) filter (where user_id is not null and core_user_id is not null)
                from {$app}.user_google_tokens
                union all
                select 'short_links.core_user_id',
                       count(*) filter (where user_id is not null),
                       count(*) filter (where user_id is not null and core_user_id is not null)
                from {$app}.short_links
                union all
                select 'qr_texts.core_user_id',
                       count(*) filter (where user_id is not null),
                       count(*) filter (where user_id is not null and core_user_id is not null)
                from {$app}.qr_texts
                union all
                select 'upload_tasks.teacher_core_user_id',
                       count(*) filter (where teacher_id is not null),
                       count(*) filter (where teacher_id is not null and teacher_core_user_id is not null)
                from {$app}.upload_tasks
            ) summary
        ");

        foreach ($rows as $row) {
            $this->line(sprintf('- %s: %d/%d', $row->relation_name, $row->mapped_rows, $row->source_rows));
        }

        return self::SUCCESS;
    }
}

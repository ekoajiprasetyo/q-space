<?php

namespace App\Console\Commands;

use App\Support\PostgresSchema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncCoreRelationsCommand extends Command
{
    protected $signature = 'qspace:sync-core-relations {--dry-run : Show the sync plan without writing data}';

    protected $description = 'Populate Phase 1 bridge columns from q_space domain tables into core user/student references';

    public function handle(): int
    {
        if (!PostgresSchema::usesPgsql()) {
            $this->error('Command ini hanya dipakai saat koneksi default menggunakan PostgreSQL.');

            return self::FAILURE;
        }

        $app = PostgresSchema::app();
        $core = PostgresSchema::core();

        $requiredTables = [
            "{$app}.file_requests",
            "{$app}.file_submissions",
            "{$app}.user_google_tokens",
            "{$app}.short_links",
            "{$app}.qr_texts",
            "{$app}.upload_tasks",
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("Table {$table} belum ada. Jalankan migration Phase 1 terlebih dahulu.");

                return self::FAILURE;
            }
        }

        $stats = [
            'file_requests' => DB::scalar("select count(*) from {$app}.file_requests where teacher_id is not null"),
            'file_submissions' => DB::scalar("select count(*) from {$app}.file_submissions where student_id is not null"),
            'user_google_tokens' => DB::scalar("select count(*) from {$app}.user_google_tokens where user_id is not null"),
            'short_links' => DB::scalar("select count(*) from {$app}.short_links where user_id is not null"),
            'qr_texts' => DB::scalar("select count(*) from {$app}.qr_texts where user_id is not null"),
            'upload_tasks' => DB::scalar("select count(*) from {$app}.upload_tasks where teacher_id is not null"),
        ];

        foreach ($stats as $table => $count) {
            $this->line(sprintf('%s: %s row(s) kandidat', $table, $count));
        }

        if ($this->option('dry-run')) {
            $this->comment('Dry run aktif. Tidak ada data yang diubah.');
            $this->reportAudit($app, $core);

            return self::SUCCESS;
        }

        DB::statement("
            update {$app}.file_requests fr
            set teacher_core_user_id = u.id
            from {$core}.users u
            where fr.teacher_id = u.id
        ");

        DB::statement("
            update {$app}.user_google_tokens tgt
            set core_user_id = u.id
            from {$core}.users u
            where tgt.user_id = u.id
        ");

        DB::statement("
            update {$app}.short_links sl
            set core_user_id = u.id
            from {$core}.users u
            where sl.user_id = u.id
        ");

        DB::statement("
            update {$app}.qr_texts qt
            set core_user_id = u.id
            from {$core}.users u
            where qt.user_id = u.id
        ");

        DB::statement("
            update {$app}.upload_tasks ut
            set teacher_core_user_id = u.id
            from {$core}.users u
            where ut.teacher_id = u.id
        ");

        DB::statement("
            update {$app}.file_submissions fs
            set student_core_student_id = s.id
            from {$core}.students s
            where fs.student_id is not null
              and s.user_id = fs.student_id
        ");

        $this->info('Sinkronisasi kolom bridge ke core selesai.');
        $this->reportAudit($app, $core);

        return self::SUCCESS;
    }

    private function reportAudit(string $app, string $core): void
    {
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

        $this->newLine();
        $this->info('Ringkasan bridge coverage:');

        foreach ($rows as $row) {
            $this->line(sprintf(
                '- %s: %d/%d mapped',
                $row->relation_name,
                $row->mapped_rows,
                $row->source_rows
            ));
        }

        $orphans = DB::select("
            select *
            from (
                select 'file_requests.teacher_id -> core.users.id' as relation_name,
                       count(*) as orphan_rows
                from {$app}.file_requests fr
                left join {$core}.users u on u.id = fr.teacher_id
                where fr.teacher_id is not null and u.id is null
                union all
                select 'file_submissions.student_id -> core.students.user_id',
                       count(*)
                from {$app}.file_submissions fs
                left join {$core}.students s on s.user_id = fs.student_id
                where fs.student_id is not null and s.id is null
                union all
                select 'user_google_tokens.user_id -> core.users.id',
                       count(*)
                from {$app}.user_google_tokens tgt
                left join {$core}.users u on u.id = tgt.user_id
                where tgt.user_id is not null and u.id is null
                union all
                select 'short_links.user_id -> core.users.id',
                       count(*)
                from {$app}.short_links sl
                left join {$core}.users u on u.id = sl.user_id
                where sl.user_id is not null and u.id is null
                union all
                select 'qr_texts.user_id -> core.users.id',
                       count(*)
                from {$app}.qr_texts qt
                left join {$core}.users u on u.id = qt.user_id
                where qt.user_id is not null and u.id is null
                union all
                select 'upload_tasks.teacher_id -> core.users.id',
                       count(*)
                from {$app}.upload_tasks ut
                left join {$core}.users u on u.id = ut.teacher_id
                where ut.teacher_id is not null and u.id is null
            ) orphan_summary
        ");

        $this->newLine();
        $this->info('Orphan audit:');

        foreach ($orphans as $row) {
            $this->line(sprintf('- %s: %d orphan', $row->relation_name, $row->orphan_rows));
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Support\PostgresSchema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncCoreUsersCommand extends Command
{
    protected $signature = 'qspace:sync-core-users {--dry-run : Show how many users would be synchronized without writing}';

    protected $description = 'Synchronize Phase 1 shadow users from core.users into q_space.users';

    public function handle(): int
    {
        if (!PostgresSchema::usesPgsql()) {
            $this->error('Command ini hanya dipakai saat koneksi default menggunakan PostgreSQL.');

            return self::FAILURE;
        }

        $appUsersTable = PostgresSchema::qualify(PostgresSchema::app(), 'users');
        $coreUsersTable = PostgresSchema::qualify(PostgresSchema::core(), 'users');

        if (!Schema::hasTable($appUsersTable)) {
            $this->error("Shadow table {$appUsersTable} belum ada. Jalankan migration Phase 1 terlebih dahulu.");

            return self::FAILURE;
        }

        $rows = DB::table($coreUsersTable)
            ->select([
                'id',
                'name',
                'email',
                'email_verified_at',
                'password',
                'role',
                'subscription_status',
                'subscription_expires_at',
                'remember_token',
                'created_at',
                'updated_at',
                'google_id',
                'avatar',
                'nickname',
                'grade',
                'gender',
                'student_id',
                'is_active',
                'last_session_id',
            ])
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        $this->info('Ditemukan '.count($rows).' user di core.users.');

        if ($this->option('dry-run')) {
            $this->comment('Dry run aktif. Tidak ada perubahan yang ditulis ke q_space.users.');

            return self::SUCCESS;
        }

        if ($rows === []) {
            $this->comment('Tidak ada user yang perlu disinkronkan.');

            return self::SUCCESS;
        }

        DB::table($appUsersTable)->upsert(
            $rows,
            ['id'],
            [
                'name',
                'email',
                'email_verified_at',
                'password',
                'role',
                'subscription_status',
                'subscription_expires_at',
                'remember_token',
                'updated_at',
                'google_id',
                'avatar',
                'nickname',
                'grade',
                'gender',
                'student_id',
                'is_active',
                'last_session_id',
            ]
        );

        $this->info('Sinkronisasi core.users -> q_space.users selesai.');

        return self::SUCCESS;
    }
}

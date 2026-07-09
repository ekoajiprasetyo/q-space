# Q-Space PostgreSQL Migration Audit

Tanggal audit: 2026-07-09
Workspace: `D:\xampp\htdocs\q-space`
Target runtime: Tencent Cloud VPS `43.133.152.206`
Target domain: `space.q-link.my.id`
Short-link domain: `s.q-link.my.id`

## Konteks yang dibawa dari ekosistem

- `q-link` adalah master/core ekosistem.
- `q-link` dan `q-exam` sudah berjalan di VPS Tencent Cloud `43.133.152.206`.
- Arsitektur database target adalah satu PostgreSQL server dengan schema separation.
- Kepemilikan data inti tetap di `q-link` / schema `core`:
  - `core.users`
  - `core.students`
  - `core.classes`
- Kepemilikan data domain `q-space` tetap berada di schema `q_space`:
  - `file_requests`
  - `file_submissions`
  - `user_google_tokens`
  - `short_links`
  - `qr_texts`
  - `upload_tasks`

## Ringkasan hasil audit

Status saat ini: `q-space` belum siap untuk langsung menjadi consumer murni `core.users`.

Alasan utamanya:

- Auth masih memakai model lokal `App\Models\User`.
- Route Breeze untuk `register`, `login`, `forgot-password`, dan `reset-password` masih aktif.
- Login Google masih bisa membuat user lokal baru.
- Hampir semua ownership domain memakai FK `user_id`, `teacher_id`, atau `student_id` ke tabel `users`.
- File migrasi aktif untuk `users`, `password_reset_tokens`, `sessions`, `cache`, dan `jobs` tidak lagi berada di folder migrasi utama, sehingga deployment baru tidak bisa bootstrap identitas lokal secara lengkap tanpa langkah tambahan.
- Dokumen deploy dan sebagian bootstrap masih berasumsi layout cPanel/MySQL lama.

## Temuan baru dari env shared hosting lama

File referensi yang diberikan user mengonfirmasi beberapa fakta penting untuk migrasi:

- `q-space` production sebelumnya memang masih memakai `MySQL`.
- Domain produksi sudah lama memakai `https://space.q-link.my.id`.
- Session sharing sebelumnya sudah disiapkan dengan:
  - `SESSION_DOMAIN=.q-link.my.id`
  - `SESSION_COOKIE=qlink_sso_session`
- `APP_KEY` production lama tersedia, sehingga secara teknis bisa dipakai lagi bila targetnya tetap kompatibel dengan sesi ekosistem yang sama.
- OAuth Google production sudah diarahkan ke `https://space.q-link.my.id/auth/google/callback`.
- File env lama memiliki duplikasi `FILESYSTEM_DISK`, dan nilai terakhir jatuh ke `local`, jadi konfigurasi shared hosting lama tidak boleh diadopsi mentah-mentah ke VPS.

Implikasi:

- Migrasi ke PostgreSQL bukan kelanjutan dari setup PostgreSQL lama, tetapi perubahan driver nyata dari MySQL ke PostgreSQL.
- Kontrak domain dan cookie SSO boleh dipertahankan karena sudah pernah dipakai di produksi lama.
- Template env VPS harus diperlakukan sebagai hasil kurasi, bukan copy-paste langsung dari env shared hosting.

## Temuan aplikasi

### 1. Lapisan auth dan identitas

- `config/auth.php` masih menunjuk provider `users` ke `App\Models\User`.
- `app/Models/User.php` adalah model lokal penuh dengan `name`, `email`, `password`, `role`, dan `google_id`.
- `routes/auth.php` masih mengaktifkan registrasi lokal dan reset password lokal.
- `app/Http/Controllers/Auth/GoogleAuthController.php` membuat user lokal jika email belum ada.
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` dan `RegisteredUserController.php` masih memakai flow auth lokal standar Laravel.

Implikasi:

- Jika langsung memindahkan auth ke `core.users`, ada risiko memutus login, role check, dan relasi data yang sekarang mengandalkan tabel `users` lokal.
- Fase aman harus mempertahankan identity compatibility terlebih dulu, baru dilanjut refactor identitas penuh.

### 2. Kepemilikan data domain q-space

Tabel domain yang memang cocok tetap di schema `q_space`:

- `file_requests`
- `file_submissions`
- `user_google_tokens`
- `short_links`
- `qr_texts`
- `upload_tasks`

Ketergantungan ke user lokal:

- `file_requests.teacher_id -> users.id`
- `file_submissions.student_id -> users.id`
- `user_google_tokens.user_id -> users.id`
- `short_links.user_id -> users.id`
- `qr_texts.user_id -> users.id`
- `upload_tasks.teacher_id -> users.id`

Implikasi:

- Semua tabel domain sudah siap dipisah ke schema `q_space`.
- Yang belum siap adalah boundary identitas, bukan boundary domain data.

### 3. Konfigurasi database

Temuan:

- `config/database.php` sudah punya koneksi `pgsql`, tetapi sebelumnya `search_path` masih hardcoded ke `public`.
- Repo ini belum punya pola schema-aware seperti `DB_SCHEMA=q_space,core,public` dan `DB_APP_SCHEMA=q_space`.
- Repo ini belum menyamai kontrak `Q-Exam` secara penuh untuk `DB_CORE_SCHEMA`, `SESSION_CONNECTION`, `DB_QUEUE_CONNECTION`, dan `DB_CACHE_CONNECTION`.
- Default `.env.production` masih menunjuk ke `mysql`.

Perbaikan yang disiapkan pada turn ini:

- `pgsql.search_path` sekarang membaca `DB_SCHEMA`.
- Ditambahkan `database.schemas.app`, `database.schemas.core`, dan `database.schemas.search_path` agar konsisten dengan pola `Q-Exam`.

### 4. Domain dan bootstrap deploy

Temuan:

- `routes/web.php` sebelumnya meng-hardcode domain short link `s.q-link.my.id`.
- Main app domain sudah memakai `APP_DOMAIN`, tetapi short link belum.
- `app/Providers/AppServiceProvider.php` masih mengasumsikan path cPanel `../public_html/space.q-link`.
- `DEPLOYMENT_GUIDE_HOSTING.md` masih untuk shared hosting/cPanel, bukan VPS Tencent yang sekarang aktif.

Perbaikan yang disiapkan pada turn ini:

- Short-link domain sekarang membaca `SHORTLINK_DOMAIN`.
- Public path bootstrap sekarang bisa memakai `APP_PUBLIC_PATH`, dengan fallback legacy ke layout cPanel lama.

### 5. Integrasi Google Drive dan queue

Temuan:

- Integrasi upload async sudah cukup matang untuk dibawa ke VPS.
- Upload besar disalurkan lewat `upload_tasks` + `UploadSubmissionToDriveJob`.
- Queue runner publik masih memanggil `queue:work --once`, sehingga di VPS tetap lebih baik memakai worker/supervisor native.
- Token Google disimpan di tabel `user_google_tokens` dan terenkripsi di cast model.

Implikasi:

- Untuk migrasi VPS, queue worker permanen harus menjadi bagian wajib runbook.
- Cron/public trigger bisa dipertahankan hanya sebagai fallback, bukan mekanisme utama produksi.

## Risiko migrasi utama

### Risiko tinggi

1. Auth langsung diarahkan ke `core.users` tanpa fase aman.
2. Session dibagi dengan `q-link`, tetapi `q-space` tidak punya kompatibilitas `users.id` yang sama.
3. Migrasi domain tables dijalankan ke PostgreSQL tanpa keputusan eksplisit untuk tabel identitas lokal fase 1.

### Risiko menengah

1. Worker queue belum dipasang permanen di VPS sehingga upload besar terlihat berhasil tetapi task tertahan.
2. `.env.production` lama masih mengandung asumsi MySQL/shared hosting dan tidak boleh dijadikan sumber deploy final.
3. Dokumen deploy lama bisa menyesatkan jalur install jika dipakai ulang mentah-mentah.
4. Env shared hosting lama memiliki override `FILESYSTEM_DISK=local` di bagian bawah file, sehingga bisa diam-diam mengubah perilaku storage jika disalin apa adanya.

## Rekomendasi fase migrasi

### Phase 1 - Safe cutover ke VPS PostgreSQL

Target phase ini:

- App live di VPS Tencent.
- Semua tabel domain `q-space` pindah ke schema `q_space`.
- Auth tetap kompatibel dulu lewat tabel identitas lokal/shadow yang menjaga `id` selaras dengan `core.users`.
- Session domain dan `APP_KEY` disiapkan agar SSO antar subdomain bisa dibuka bertahap.

Bentuk implementasi yang direkomendasikan:

- Pertahankan tabel `q_space.users` sebagai shadow identity sementara.
- Sinkronkan data dasar dari `core.users` ke `q_space.users` dengan `id` yang sama.
- Jangan ubah dulu seluruh controller/model ke schema-qualified `core.users`.
- Pastikan role minimum yang dibutuhkan `q-space` tersedia di tabel shadow.

### Phase 2 - Refactor identitas ke core

Target phase ini:

- Provider auth tidak lagi bergantung pada shadow table lokal.
- Relasi domain yang sekarang menunjuk `users.id` mulai dirapikan ke boundary identity yang lebih eksplisit.
- Registrasi lokal dan reset password lokal dimatikan.
- Google login berubah dari "buat user lokal" menjadi "temukan user core/shadow yang sudah disetujui".

## Keputusan eksekusi yang dibutuhkan sebelum deploy final

1. Tetapkan nama schema app: `q_space`.
2. Putuskan mekanisme Phase 1:
   - shadow table `q_space.users` yang sinkron dari `core.users`, atau
   - refactor auth langsung ke `core.users` dalam satu lompatan berisiko lebih tinggi.
3. Putuskan mode session produksi:
   - berbagi cookie/domain dengan `q-link` sekarang, atau
   - hidupkan dulu login lokal fase transisi.
4. Siapkan worker queue permanen di VPS.
5. Rotasi kredensial sensitif lama yang masih tersimpan di file environment historis.
6. Putuskan apakah `APP_KEY` lama akan dipertahankan demi kompatibilitas sesi, atau diganti dan semua sesi lama dianggap putus.

## Kesimpulan

`q-space` sudah cukup dekat untuk dipindahkan ke VPS Tencent dan schema `q_space`, tetapi belum aman untuk langsung menjadi consumer identitas murni dari `core.users`.

Rute paling aman adalah:

1. cutover PostgreSQL + schema `q_space`,
2. pertahankan kompatibilitas identitas via shadow users,
3. stabilkan deploy, queue, dan domain,
4. baru lanjutkan refactor identity ke `core`.

Blueprint struktur final yang harus diikuti disimpan di `Q_SPACE_POSTGRES_BLUEPRINT.md` agar sejajar dengan dokumen `POSTGRES_SCHEMA_BLUEPRINT.md` milik `Q-Exam`.

Audit live langsung dari VPS PostgreSQL disimpan di `Q_SPACE_VPS_POSTGRES_LIVE_AUDIT.md` untuk memastikan planning migrasi mengikuti kondisi server nyata, bukan asumsi lokal.

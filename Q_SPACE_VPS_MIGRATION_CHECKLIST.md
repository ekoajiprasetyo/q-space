# Q-Space VPS Migration Checklist

Target host: `43.133.152.206`
App root target: `/var/www/q-space`
Public root target: `/var/www/q-space/public`
App domain: `space.q-link.my.id`
Short-link domain: `s.q-link.my.id`
Database strategy: PostgreSQL server tunggal dengan schema separation

## Phase 0 - Preflight

- [ ] Pastikan repo lokal bersih untuk file yang akan dipaketkan.
- [ ] Pastikan keputusan Phase 1 identity sudah disetujui:
  - shadow `q_space.users`, atau
  - direct refactor ke `core.users`.
- [ ] Jadikan [Q_SPACE_IMPORT_GUARDRAILS.md](D:\xampp\htdocs\q-space\Q_SPACE_IMPORT_GUARDRAILS.md:1) sebagai aturan import resmi sebelum menyentuh data lama.
- [ ] Jika dipilih `clean cutover` tanpa histori MySQL, jadikan [Q_SPACE_POST_CUTOVER_CHECKLIST.md](D:\xampp\htdocs\q-space\Q_SPACE_POST_CUTOVER_CHECKLIST.md:1) sebagai checklist utama setelah deploy.
- [ ] Ambil backup database produksi `q-link` sebelum menambah schema baru.
- [ ] Catat `APP_KEY` produksi lama `q-space` dan `q-link`, lalu putuskan apakah sesi lama perlu tetap kompatibel.
- [ ] Siapkan kredensial PostgreSQL produksi di VPS Tencent.
- [ ] Siapkan OAuth Google production untuk `space.q-link.my.id`.
- [ ] Perlakukan env shared hosting lama sebagai referensi nilai, bukan file yang boleh dipakai ulang mentah.

## Phase 1 - Kontrak database

- [ ] Buat schema PostgreSQL:
  - `core` jika belum ada
  - `q_space`
- [ ] Gunakan kontrak database yang sama seperti `Q-Exam`:
  - database `qlink_prod`
  - schema app `q_space`
  - schema shared `core`
- [ ] Set `DB_SCHEMA=q_space,core,public`
- [ ] Set `DB_APP_SCHEMA=q_space`
- [ ] Set `DB_CORE_SCHEMA=core`
- [ ] Tentukan strategi tabel identitas:
  - `q_space.users`
  - `q_space.password_reset_tokens`
  - `q_space.sessions`
  - `q_space.cache`
  - `q_space.cache_locks`
- [ ] Pastikan tabel domain `q_space` masuk ke schema app:
  - `file_requests`
  - `file_submissions`
  - `user_google_tokens`
  - `short_links`
  - `qr_texts`
  - `upload_tasks`
  - `jobs`
  - `failed_jobs`
  - `job_batches`
  - `migrations`

## Phase 2 - Audit kode sebelum cutover

- [ ] Verifikasi `config/database.php` membaca `DB_SCHEMA`.
- [ ] Verifikasi `config/database.php` juga membaca `DB_CORE_SCHEMA` seperti pola `Q-Exam`.
- [ ] Verifikasi short link domain membaca `SHORTLINK_DOMAIN`.
- [ ] Verifikasi public path bisa diatur via `APP_PUBLIC_PATH`.
- [ ] Verifikasi tidak ada path shared-hosting yang dipakai sebagai asumsi wajib.
- [ ] Verifikasi flow upload async masih memakai queue `uploads`.

## Phase 3 - Server provisioning di VPS

- [ ] Buat direktori aplikasi `/var/www/q-space`.
- [ ] Upload source code ke VPS tanpa `.git`, `node_modules`, dan file lokal sensitif yang tidak dipakai.
- [ ] Salin environment production final ke `/var/www/q-space/.env`.
- [ ] Pastikan `.env` final tidak mewarisi override yang konflik dari shared hosting lama, terutama `FILESYSTEM_DISK=local`.
- [ ] Samakan kontrak env infrastruktur dengan `Q-Exam`:
  - `SESSION_CONNECTION=pgsql`
  - `SESSION_TABLE=sessions`
  - `DB_QUEUE_CONNECTION=pgsql`
  - `DB_QUEUE_TABLE=jobs`
  - `DB_CACHE_CONNECTION=pgsql`
  - `DB_CACHE_TABLE=cache`
- [ ] Install dependency:
  - `composer install --no-dev --optimize-autoloader`
  - `npm install`
  - `npm run build`
- [ ] Pastikan permission writable untuk:
  - `storage`
  - `bootstrap/cache`

## Phase 4 - Nginx dan domain

- [ ] Tambahkan server block `space.q-link.my.id` ke `/var/www/q-space/public`.
- [ ] Tambahkan server block `s.q-link.my.id` ke root yang sama.
- [ ] Pastikan HTTPS aktif.
- [ ] Pastikan request host `space.q-link.my.id` masuk ke app domain utama.
- [ ] Pastikan request host `s.q-link.my.id` melayani short code redirect.

## Phase 5 - Database execution

- [ ] Jalankan audit readiness awal:
  - `php artisan qspace:phase1-status`
- [ ] Jalankan migrasi ke PostgreSQL target.
- [ ] Jika memilih shadow users, jalankan bootstrap tabel identitas fase 1 lebih dulu.
- [ ] Jangan import `users` MySQL lama langsung ke PostgreSQL.
- [ ] Impor atau sinkronkan user yang dibutuhkan dari `core.users` ke `q_space.users` dengan `id` tetap sama:
  - `php artisan qspace:sync-core-users`
- [ ] Audit hanya 6 tabel domain dari source MySQL lama sebelum import:
  - `file_requests`
  - `file_submissions`
  - `user_google_tokens`
  - `short_links`
  - `qr_texts`
  - `upload_tasks`
- [ ] Tahan row yang tidak punya pasangan valid di `core.users` atau `core.students`; jangan buat akun baru otomatis.
- [ ] Isi kolom bridge relasi domain ke `core`:
  - `php artisan qspace:sync-core-relations`
- [ ] Jalankan audit coverage setelah sync:
  - `php artisan qspace:phase1-status`
- [ ] Verifikasi foreign key domain tidak gagal saat insert data baru.
- [ ] Verifikasi queue tables berhasil dibuat.

## Phase 6 - Queue dan background processing

- [ ] Jalankan worker untuk queue `uploads,default`.
- [ ] Pasang service `systemd` atau `supervisor` untuk worker permanen.
- [ ] Verifikasi retry, timeout, dan log upload task berjalan.
- [ ] Pertahankan route trigger queue hanya sebagai fallback operasional.

## Phase 7 - Smoke test wajib

- [ ] Buka `https://space.q-link.my.id`.
- [ ] Verifikasi login guru/admin berhasil.
- [ ] Verifikasi siswa tetap tidak bisa masuk dashboard.
- [ ] Buat 1 file request baru.
- [ ] Upload file publik dari link slug.
- [ ] Verifikasi `upload_tasks` berubah dari `queued` ke `uploaded`.
- [ ] Verifikasi file benar-benar masuk ke Google Drive.
- [ ] Buat short link dan cek redirect via `https://s.q-link.my.id/{code}`.
- [ ] Buat QR text dan cek public page.

## Phase 8 - Hardening setelah live

- [ ] Nonaktifkan registrasi lokal bila Phase 1 tidak lagi membutuhkan onboarding manual.
- [ ] Evaluasi penutupan forgot-password/reset-password lokal.
- [ ] Rotasi secret lama yang tersimpan di file historis.
- [ ] Dokumentasikan hasil final: path VPS, schema final, worker command, dan keputusan identity.

## Catatan operator

- Jangan pakai lagi asumsi shared hosting `/home/englishh/...` untuk deployment `q-space`.
- Jangan langsung memotong `User` lokal sebelum strategi identity phase 1 selesai.
- Jika session akan dibagi dengan `q-link`, samakan `APP_KEY`, `SESSION_COOKIE`, dan `SESSION_DOMAIN`, lalu pastikan user id tetap kompatibel.
- Env shared hosting lama mengonfirmasi `q-space` sebelumnya masih `MySQL`; migrasi ini adalah cutover driver database, bukan sekadar pindah server.
- Jika produksi sudah diputuskan tanpa import histori, jangan membuka kembali langkah import kecuali ada kebutuhan recovery yang benar-benar spesifik.

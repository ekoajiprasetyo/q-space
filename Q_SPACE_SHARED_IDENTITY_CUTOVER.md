# Q-Space Shared Identity Cutover

Target akhir produksi:

- autentikasi Q-Space langsung memakai `core.users`
- data siswa memakai `core.students`
- registrasi, login, verifikasi, dan perubahan akun dilakukan di Q-Link
- OAuth Google di Q-Space hanya menyimpan token Google Drive pada `q_space.user_google_tokens`
- schema `q_space` tidak memiliki tabel user bayangan

## Guardrail sebelum migration

1. Aktifkan maintenance mode dengan secret bypass.
2. Hentikan worker queue Q-Space.
3. Buat backup PostgreSQL untuk `core`, `q_space`, dan tabel migration.
4. Buat backup terpisah `q_space.users` dengan permission `0600`.
5. Catat checksum SHA-256 kedua backup.
6. Jalankan audit bridge dan pastikan tidak ada owner domain yang gagal dipetakan.

Migration `2026_07_31_000001_cutover_identity_to_core_for_pgsql.php` bersifat fail-closed dan transactional. Migration akan dibatalkan bila:

- tabel core/domain tidak lengkap
- ada token, link, QR, file request, upload task, atau submission terdaftar tanpa pasangan core
- ada lebih dari satu token Google Drive untuk user yang sama
- masih ada foreign key tak dikenal menuju `q_space.users`

## Transformasi canonical

- `file_requests.teacher_id` menjadi FK langsung ke `core.users.id`
- `file_submissions.student_id` menjadi FK langsung ke `core.students.id`
- `user_google_tokens.user_id` menjadi FK langsung ke `core.users.id`
- `short_links.user_id` menjadi FK langsung ke `core.users.id`
- `qr_texts.user_id` menjadi FK langsung ke `core.users.id`
- `upload_tasks.teacher_id` menjadi FK langsung ke `core.users.id`

Kolom bridge transisi dihapus setelah nilainya disalin ke kolom canonical. Tabel `q_space.users` dihapus paling akhir setelah seluruh referensi dipindahkan.

## Verifikasi setelah migration

```bash
php artisan qspace:identity-status
php artisan migrate:status
php artisan optimize:clear
```

Smoke test minimum:

1. guru aktif dari Q-Link membuka dashboard Q-Space
2. guru belum diverifikasi atau nonaktif ditolak
3. siswa ditolak
4. logout/login lintas subdomain tetap konsisten
5. hubungkan Google Drive dan pastikan hanya satu token per core user
6. buat file request, short link, dan QR text
7. pastikan data lama tetap terlihat oleh pemilik yang sama

## Pemulihan

Migration tidak mempunyai rollback otomatis karena mengembalikan shadow identity dapat menghidupkan kembali dua sumber akun. Bila cutover gagal setelah commit:

1. aktifkan maintenance mode
2. hentikan worker
3. pulihkan backup database pre-cutover secara utuh
4. pulihkan kode versi sebelumnya
5. bersihkan cache config dan aplikasi
6. verifikasi login Q-Link dan Q-Space sebelum membuka maintenance

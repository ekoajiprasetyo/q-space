# Q-Space VPS PostgreSQL Live Audit

Tanggal audit live: 2026-07-09
Host VPS: `43.133.152.206`
Metode akses: SSH ke user `ubuntu`, lalu inspeksi PostgreSQL lokal di VPS
Database target: `qlink_prod`

## Ringkasan Eksekutif

Hasil inspeksi live di VPS menegaskan bahwa pola database produksi saat ini memang sudah mengikuti arsitektur ekosistem yang kita sepakati:

- database aktif adalah `qlink_prod`
- schema yang ada saat ini hanya:
  - `core`
  - `q_exam`
  - `public`
- schema `q_space` **belum ada**
- `public` tidak berisi tabel aplikasi
- `core` dipakai untuk data master dan infrastruktur bersama minimum
- `q_exam` dipakai untuk data domain ujian dan infrastruktur lokal aplikasi ujian

Kesimpulan paling penting untuk migrasi:

- `q_space` harus dibuat sebagai schema baru di database yang sama
- struktur `q_space` harus mengikuti pola hidup `q_exam`, bukan pola shared hosting lama
- relasi ke data master harus mengarah ke `core`

## Fakta Live Database

### 1. Database dan versi engine

- current database: `qlink_prod`
- engine: `PostgreSQL 16.14`

### 2. Schema yang benar-benar ada

Schema non-system yang ditemukan:

- `core`
- `public`
- `q_exam`

Tidak ditemukan:

- `q_space`
- `q_game`
- schema domain lain

### 3. Jumlah tabel per schema

- `core`: 6 tabel
- `q_exam`: 27 tabel
- `public`: 0 tabel aplikasi

Ini sangat penting karena menunjukkan bahwa produksi live sudah bersih dari pola "campur semua tabel di public".

## Struktur Live Schema `core`

Tabel yang ada di `core`:

- `classes`
- `migrations`
- `password_reset_tokens`
- `sessions`
- `students`
- `users`

### Kolom penting `core.classes`

- `id`
- `name`
- `grade`
- `academic_year`
- `description`
- `is_active`
- `created_at`
- `updated_at`

### Kolom penting `core.students`

- `id`
- `user_id`
- `class_id`
- `nis`
- `nisn`
- `nickname`
- `gender`
- `birth_date`
- `birth_place`
- `address`
- `phone`
- `parent_name`
- `parent_phone`
- `is_active`
- `created_at`
- `updated_at`

### Kolom penting `core.users`

- `id`
- `name`
- `email`
- `email_verified_at`
- `password`
- `role`
- `subscription_status`
- `subscription_expires_at`
- `remember_token`
- `created_at`
- `updated_at`
- `google_id`
- `avatar`
- `nickname`
- `grade`
- `gender`
- `student_id`
- `is_active`
- `last_session_id`

### Tabel infrastruktur bersama di `core`

- `sessions`
- `password_reset_tokens`
- `migrations`

Catatan:

- ini memperkuat pola bahwa sesi bersama memang tetap diletakkan di `core`
- `core.users` masih membawa beberapa kolom transisi lama, jadi aplikasi satelit tetap tidak boleh menjadikan kolom transisi itu sebagai model domain jangka panjang

## Struktur Live Schema `q_exam`

Tabel yang ada di `q_exam`:

- `activity_log`
- `cache`
- `cache_locks`
- `exam_answers`
- `exam_classes`
- `exam_participants`
- `exam_questions`
- `exam_student`
- `exam_types`
- `exams`
- `failed_jobs`
- `job_batches`
- `jobs`
- `media`
- `media_files`
- `migrations`
- `model_has_permissions`
- `model_has_roles`
- `notifications`
- `permissions`
- `question_categories`
- `question_options`
- `questions`
- `role_has_permissions`
- `roles`
- `subjects`
- `topics`

Ini adalah bukti live bahwa aplikasi satelit di produksi memang memisahkan:

- data bisnis domain
- tabel permission
- tabel cache
- tabel queue
- tabel migrations

ke schema aplikasinya sendiri.

## Relasi Lintas Schema Yang Aktif di Produksi

Pemeriksaan constraint live memperlihatkan constraint berikut di `q_exam`:

- `exam_classes.class_id -> classes.id`
- `exam_participants.student_id -> students.id`
- `exam_participants.paused_by -> users.id`
- `exam_student.student_id -> students.id`
- `media_files.uploaded_by -> users.id`

Catatan penting:

- definisi constraint yang terbaca dari PostgreSQL tidak menuliskan schema secara eksplisit
- tetapi karena `q_exam` tidak memiliki tabel `classes`, `students`, atau `users`, referensi tersebut efektif mengarah ke schema `core` lewat kontrak `search_path`
- verifikasi data menunjukkan tidak ada orphan untuk:
  - `exam_classes.class_id`
  - `exam_student.student_id`
  - `exam_participants.student_id`
  - `exam_participants.paused_by`
  - `media_files.uploaded_by`

Implikasi untuk `q_space`:

- kita boleh mengikuti pola live yang sama
- tetapi untuk migration baru, referensi explicit ke `core` tetap lebih mudah dipahami dan diaudit

## Kondisi Data Live

Jumlah data master di `core` saat audit:

- `core.users`: 11
- `core.students`: 4
- `core.classes`: 11

Jumlah data sampel di `q_exam` saat audit:

- `q_exam.exams`: 0
- `q_exam.exam_classes`: 0
- `q_exam.exam_student`: 0
- `q_exam.exam_participants`: 0
- `q_exam.media_files`: 3

Catatan:

- ini berarti struktur database sudah siap, walaupun volume data domain ujian aktif masih rendah
- untuk `q_space`, kita tidak perlu memaksa pola import massal yang rumit sebelum schema siap

## Privilege User Database

User aplikasi `qlink_user` berhasil dipakai untuk membaca database `qlink_prod`.

Temuan privilege yang relevan:

- bisa login ke `qlink_prod`
- punya privilege `CREATE` pada database
- punya `USAGE` ke schema `core`
- punya `USAGE` dan `CREATE` pada `q_exam`

Implikasi:

- secara prinsip, user aplikasi ini cukup untuk menambah schema baru `q_space`
- migration `q_space` bisa mengikuti jalur yang sama seperti `q_exam`

## Dampak Langsung ke Planning Migrasi Q-Space

### Yang Sudah Jelas

- target database final harus `qlink_prod`
- target search path harus `q_space,core,public`
- `sessions` bersama tidak perlu dipindah ke schema `q_space`
- `cache`, `jobs`, `failed_jobs`, `job_batches`, dan `migrations` sebaiknya lokal di `q_space`
- `q_space` tidak boleh membuat business table di `public`

### Struktur yang Direkomendasikan untuk `q_space`

Schema `q_space` harus minimal menampung:

- `file_requests`
- `file_submissions`
- `user_google_tokens`
- `short_links`
- `qr_texts`
- `upload_tasks`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `migrations`

### Relasi target yang direkomendasikan

- `q_space.file_requests.teacher_id -> core.users.id`
- `q_space.file_submissions.student_id -> core.students.id` bila mode final memakai siswa master
- `q_space.user_google_tokens.user_id -> core.users.id`
- `q_space.short_links.user_id -> core.users.id`
- `q_space.qr_texts.user_id -> core.users.id`
- `q_space.upload_tasks.teacher_id -> core.users.id`

## Keputusan Teknis yang Disarankan

1. Buat `q_space` sebagai schema baru di `qlink_prod`.
2. Jangan letakkan tabel domain `Q-Space` di `public`.
3. Samakan kontrak env `Q-Space` dengan `Q-Exam`:
   - `DB_DATABASE=qlink_prod`
   - `DB_SCHEMA=q_space,core,public`
   - `DB_APP_SCHEMA=q_space`
   - `DB_CORE_SCHEMA=core`
4. Biarkan `sessions` tetap di `core`.
5. Letakkan queue, cache, failed jobs, dan migrations di `q_space`.
6. Jalankan fase aman untuk identity bridge sebelum mengalihkan semua relasi langsung ke `core`.

## Kesimpulan

Database PostgreSQL live di VPS sudah cukup rapi dan konsisten untuk menjadi fondasi migrasi `Q-Space`.

Yang perlu dilakukan bukan mendesain ulang pola database dari nol, tetapi menambahkan schema `q_space` dengan kontrak yang sama seperti `q_exam`, lalu menyesuaikan boundary identitas `Q-Space` agar kompatibel dengan `core`.

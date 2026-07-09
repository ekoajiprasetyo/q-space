# Q-Space PostgreSQL Schema Blueprint

## Tujuan

Menyamakan struktur database `Q-Space` dengan pola PostgreSQL yang sudah dipakai `Q-Link` dan `Q-Exam` di VPS Tencent.

Target akhirnya:

- `Q-Link` tetap menjadi aplikasi master ekosistem
- data bersama disimpan di schema `core`
- data domain `Q-Space` disimpan di schema `q_space`
- relasi lintas aplikasi memakai ID yang konsisten

## Database Target

Gunakan satu database PostgreSQL bersama, sama seperti pola `Q-Exam`:

- database: `qlink_prod`
- schema shared: `core`
- schema aplikasi: `q_space`

Search path yang direkomendasikan untuk `Q-Space`:

```env
DB_CONNECTION=pgsql
DB_SCHEMA=q_space,core,public
DB_APP_SCHEMA=q_space
DB_CORE_SCHEMA=core
```

## Tabel Yang Tetap di `core`

Tabel ini tidak boleh menjadi sumber kebenaran lokal `Q-Space` pada desain final:

- `users`
- `classes`
- `students`
- `sessions`
- `password_reset_tokens`

Alasan:

- `Q-Link` adalah master account dan data akademik dasar
- sesi lintas subdomain sudah memakai kontrak cookie yang sama
- `Q-Space` nantinya perlu membaca guru, admin, dan siswa dari sumber identitas yang sama

## Tabel Yang Tetap Lokal di `q_space`

Semua data yang hanya dipakai domain `Q-Space` harus tetap berada di schema aplikasi:

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

Catatan:

- `migrations` di `q_space` harus terpisah dari `core` seperti pola `q_exam`
- queue dan cache tetap lokal ke schema `q_space`, bukan dibagi ke semua aplikasi
- token Google adalah data domain `Q-Space`, bukan data master identitas

## Tabel Lama Yang Tidak Boleh Menjadi Sumber Kebenaran Final

`Q-Space` saat ini masih memiliki model `User` lokal dan jejak migrasi lokal untuk identitas di folder backup.

Artinya:

- `users` lokal hanya boleh dipakai sebagai jembatan fase aman
- `Q-Space` tidak boleh menjadi tempat final untuk membuat identitas user ekosistem
- pada desain akhir, register dan login mandiri harus diarahkan ke kontrak master `Q-Link`

## Relasi Cross-Schema Yang Harus Dipertahankan

Desain final yang direkomendasikan:

- `q_space.file_requests.teacher_id -> core.users.id`
- `q_space.file_submissions.student_id -> core.students.id` bila submission memang terkait siswa terdaftar
- `q_space.user_google_tokens.user_id -> core.users.id`
- `q_space.short_links.user_id -> core.users.id`
- `q_space.qr_texts.user_id -> core.users.id`
- `q_space.upload_tasks.teacher_id -> core.users.id`

Relasi lokal yang tetap di `q_space`:

- `q_space.file_submissions.file_request_id -> q_space.file_requests.id`
- `q_space.upload_tasks.file_request_id -> q_space.file_requests.id`

## Fase Aman Yang Direkomendasikan

Karena `Q-Space` masih tergantung pada `User` lokal, fase transisi perlu dibuat eksplisit.

### Phase 1

- pertahankan `q_space.users` sebagai shadow identity sementara
- sinkronkan `id` user dengan `core.users.id`
- biarkan foreign key domain tetap menunjuk ke `users.id` lokal sementara
- jangan ubah semua relasi dan auth sekaligus

### Phase 2

- model shared membaca `core.users`, `core.students`, dan `core.classes`
- register lokal dihentikan
- login Google tidak lagi membuat user liar
- foreign key domain dipindah ke referensi master yang tepat

## Kontrak Session dan SSO

Samakan pola dengan `Q-Exam`:

```env
SESSION_DRIVER=database
SESSION_CONNECTION=pgsql
SESSION_TABLE=sessions
SESSION_COOKIE=qlink_sso_session
SESSION_DOMAIN=.q-link.my.id
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Catatan:

- jika targetnya benar-benar berbagi sesi lintas subdomain, `APP_KEY` harus konsisten dengan kontrak ekosistem yang aktif
- `sessions` adalah data infrastruktur bersama, tetapi cache dan jobs tetap lokal di `q_space`

## Kontrak Infrastruktur

Supaya sama dengan pola `q_exam`, `Q-Space` sebaiknya memakai:

```env
DB_QUEUE_CONNECTION=pgsql
DB_QUEUE_TABLE=jobs
DB_CACHE_CONNECTION=pgsql
DB_CACHE_TABLE=cache
```

Implikasi:

- tabel queue lokal akan hidup di schema `q_space`
- tabel cache lokal akan hidup di schema `q_space`
- deployment `Q-Space` tidak akan bentrok dengan `Q-Link` atau `Q-Exam`

## Aturan Batas Schema

### Yang Boleh Masuk `core`

Hanya data yang benar-benar dipakai lintas aplikasi:

- akun pengguna
- profil siswa
- kelas
- sesi login bersama
- token reset password

### Yang Tidak Boleh Masuk `core`

Data domain `Q-Space` tidak boleh merembes ke schema bersama:

- request file
- submission file
- token Google Drive
- short link
- QR text
- task upload
- queue aplikasi
- cache aplikasi

## Kesimpulan Final

Struktur yang harus kita pegang agar sama dengan pola VPS `Q-Link` dan `Q-Exam` adalah:

- `core` sebagai schema master ekosistem
- `q_space` sebagai schema domain `Q-Space`
- relasi lintas schema hanya dipakai untuk referensi ke data master
- tabel infrastruktur aplikasi tetap lokal di `q_space`
- `Q-Space` boleh memakai shadow identity sementara, tetapi arah akhirnya tetap ke `core`

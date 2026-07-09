# Q-Space Import Guardrails

Status keputusan saat ini:

- produksi `q-space` sudah live dan stabil di VPS
- histori MySQL lama diputuskan untuk tidak diimport
- dokumen ini dipertahankan sebagai arsip kebijakan bila suatu saat ada kebutuhan recovery data terbatas

Dokumen ini menjadi pagar eksekusi bila suatu saat ada kebutuhan membawa sebagian data `q-space` lama dari MySQL shared hosting ke PostgreSQL VPS.

Tujuan utamanya:

- mencegah tabel `users` menjadi ganda, bentrok, atau kembali berantakan
- memastikan `core.users` tetap menjadi master identitas ekosistem
- membatasi import hanya pada data domain `q_space` yang benar-benar dibutuhkan
- mencegah keputusan skip import yang sudah diambil sekarang dibatalkan secara tergesa

## Prinsip Utama

1. `core.users` adalah satu-satunya sumber kebenaran final untuk identitas user ekosistem.
2. `q_space.users` hanya shadow table fase transisi, bukan tempat import penuh user lama.
3. Tabel `users` dari MySQL lama tidak boleh di-import mentah ke PostgreSQL.
4. Data domain `q_space` boleh di-import hanya setelah pemilik datanya bisa dipetakan ke `core.users` atau `core.students`.
5. Semua row yang tidak bisa dipetakan harus masuk daftar audit, bukan dipaksa dibuatkan user baru.

## Posisi Saat Ini

Per `9 Juli 2026`, keputusan operasional yang dipakai adalah:

- tidak ada import histori MySQL lama ke PostgreSQL produksi
- PostgreSQL `q_space` menjadi baseline bersih yang baru
- backup MySQL lama cukup disimpan sebagai arsip read-only

Karena itu, seluruh bagian di bawah ini bersifat kontinjensi, bukan langkah wajib rollout saat ini.

## Data Yang Diimport

Import dari MySQL lama ke PostgreSQL hanya untuk tabel domain ini:

- `file_requests`
- `file_submissions`
- `user_google_tokens`
- `short_links`
- `qr_texts`
- `upload_tasks`

Data infrastruktur tidak perlu dibawa dari hosting lama:

- `sessions`
- `password_reset_tokens`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

## Data Yang Tidak Diimport Sebagai Sumber Final

Tabel berikut tidak boleh diangkat mentah dari MySQL lama ke PostgreSQL:

- `users`
- `students`
- `classes`

Aturan penggantinya:

- `core.users` tetap master
- `core.students` tetap master
- `core.classes` tetap master
- `q_space.users` diisi dari `core.users` memakai command `qspace:sync-core-users`

## Strategi Anti-Duplikasi User

### Langkah wajib

1. Jalankan migration PostgreSQL phase 1.
2. Jalankan `php artisan qspace:sync-core-users`.
3. Pastikan `q_space.users.id = core.users.id`.
4. Baru import tabel domain.
5. Setelah import, jalankan `php artisan qspace:sync-core-relations`.

### Larangan keras

- jangan import dump `users` MySQL langsung ke `q_space.users`
- jangan membuat user baru di PostgreSQL hanya karena ada row domain yang belum match
- jangan menjadikan email duplikat dari MySQL lama sebagai akun baru otomatis

## Aturan Mapping User Lama

Prioritas pencocokan owner lama ke `core.users`:

1. `email`
2. `google_id`
3. `name` hanya untuk audit manual, bukan auto-merge

Prioritas pencocokan submission siswa ke `core.students`:

1. `student_id` lama jika memang ada padanan yang jelas di `core`
2. email siswa melalui `core.users`
3. nama pengirim hanya untuk audit manual

## Hasil Yang Diharapkan Per Tabel

### `q_space.users`

- sumber data: `core.users`
- metode isi: `qspace:sync-core-users`
- status import dari MySQL: tidak diimport penuh

### `q_space.file_requests`

- tetap diimport dari MySQL lama
- `teacher_id` lama dipertahankan sementara untuk kompatibilitas phase 1
- `teacher_core_user_id` harus terisi setelah sync relations

### `q_space.file_submissions`

- tetap diimport dari MySQL lama
- `student_id` lama boleh tetap ada sementara
- `student_core_student_id` harus diisi bila siswa punya padanan di `core.students`
- guest submission tanpa siswa terdaftar tetap bisa hidup dengan `submitter_name`

### `q_space.user_google_tokens`

- tetap diimport dari MySQL lama
- hanya valid bila `user_id` lama bisa dipetakan ke `core.users`
- row tanpa owner valid harus dipisahkan untuk audit

### `q_space.short_links`

- tetap diimport dari MySQL lama
- `core_user_id` harus terisi bila owner valid ditemukan
- `short_code` harus lolos audit unik sebelum import

### `q_space.qr_texts`

- tetap diimport dari MySQL lama
- `core_user_id` harus terisi bila owner valid ditemukan
- `slug` harus lolos audit unik sebelum import

### `q_space.upload_tasks`

- tetap diimport dari MySQL lama bila memang ada data historis yang masih dibutuhkan
- `teacher_core_user_id` harus terisi bila teacher valid ditemukan
- task gagal/terputus boleh dibersihkan bila memang tidak lagi relevan

## Query Audit Wajib Pada Database Lama MySQL

Sebelum import final, jalankan audit ini pada MySQL lama.

### 1. Hitung jumlah row per tabel

```sql
SELECT 'users' AS table_name, COUNT(*) AS total FROM users
UNION ALL
SELECT 'file_requests', COUNT(*) FROM file_requests
UNION ALL
SELECT 'file_submissions', COUNT(*) FROM file_submissions
UNION ALL
SELECT 'user_google_tokens', COUNT(*) FROM user_google_tokens
UNION ALL
SELECT 'short_links', COUNT(*) FROM short_links
UNION ALL
SELECT 'qr_texts', COUNT(*) FROM qr_texts
UNION ALL
SELECT 'upload_tasks', COUNT(*) FROM upload_tasks;
```

### 2. Cari email user ganda di MySQL lama

```sql
SELECT email, COUNT(*) AS total
FROM users
WHERE email IS NOT NULL AND email <> ''
GROUP BY email
HAVING COUNT(*) > 1
ORDER BY total DESC, email;
```

### 3. Cari `google_id` ganda di MySQL lama

```sql
SELECT google_id, COUNT(*) AS total
FROM users
WHERE google_id IS NOT NULL AND google_id <> ''
GROUP BY google_id
HAVING COUNT(*) > 1
ORDER BY total DESC, google_id;
```

### 4. Cari domain row yang menunjuk user tidak ada

```sql
SELECT 'file_requests.teacher_id' AS relation_name, COUNT(*) AS orphan_rows
FROM file_requests fr
LEFT JOIN users u ON u.id = fr.teacher_id
WHERE fr.teacher_id IS NOT NULL AND u.id IS NULL
UNION ALL
SELECT 'file_submissions.student_id', COUNT(*)
FROM file_submissions fs
LEFT JOIN users u ON u.id = fs.student_id
WHERE fs.student_id IS NOT NULL AND u.id IS NULL
UNION ALL
SELECT 'user_google_tokens.user_id', COUNT(*)
FROM user_google_tokens gt
LEFT JOIN users u ON u.id = gt.user_id
WHERE gt.user_id IS NOT NULL AND u.id IS NULL
UNION ALL
SELECT 'short_links.user_id', COUNT(*)
FROM short_links sl
LEFT JOIN users u ON u.id = sl.user_id
WHERE sl.user_id IS NOT NULL AND u.id IS NULL
UNION ALL
SELECT 'qr_texts.user_id', COUNT(*)
FROM qr_texts qt
LEFT JOIN users u ON u.id = qt.user_id
WHERE qt.user_id IS NOT NULL AND u.id IS NULL
UNION ALL
SELECT 'upload_tasks.teacher_id', COUNT(*)
FROM upload_tasks ut
LEFT JOIN users u ON u.id = ut.teacher_id
WHERE ut.teacher_id IS NOT NULL AND u.id IS NULL;
```

### 5. Audit `short_code` ganda

```sql
SELECT short_code, COUNT(*) AS total
FROM short_links
WHERE short_code IS NOT NULL AND short_code <> ''
GROUP BY short_code
HAVING COUNT(*) > 1
ORDER BY total DESC, short_code;
```

### 6. Audit `slug` ganda

```sql
SELECT slug, COUNT(*) AS total
FROM qr_texts
WHERE slug IS NOT NULL AND slug <> ''
GROUP BY slug
HAVING COUNT(*) > 1
ORDER BY total DESC, slug;
```

## Query Audit Mapping Ke PostgreSQL Master

Setelah kita punya dump atau akses baca MySQL lama, hasil user lama perlu dicocokkan dengan `core.users`.

Checklist audit yang harus keluar:

- user lama yang match ke `core.users` via `email`
- user lama yang match ke `core.users` via `google_id`
- user lama yang tidak punya match sama sekali
- email ganda di MySQL yang menunjuk ke satu user `core`
- email aktif di `core` yang ternyata menunjuk banyak user lama

## Keputusan Import Yang Aman

### Aman langsung diimport

- row domain dengan owner yang punya match tunggal ke `core`
- row domain guest yang memang tidak membutuhkan identity formal
- row dengan key unik yang lolos audit

### Tahan untuk audit manual

- user lama dengan email ganda
- user lama dengan `google_id` ganda
- row domain yang menunjuk user lama tanpa pasangan di `core`
- row domain yang menunjuk lebih dari satu kandidat di `core`
- row `short_links` atau `qr_texts` yang punya slug/code bentrok

### Jangan dibawa

- akun sampah lama yang tidak dipakai domain data mana pun
- session, cache, queue, reset token dari shared hosting lama
- row domain rusak yang tidak punya owner valid dan tidak punya nilai historis

## Urutan Eksekusi Yang Disetujui

1. audit MySQL lama
2. bersihkan konflik user lama di level mapping
3. sync `core.users` ke `q_space.users`
4. import 6 tabel domain ke `q_space`
5. isi bridge `*_core_user_id` dan `*_core_student_id`
6. audit orphan
7. baru lanjut refactor identitas phase 2

## Catatan Operasional

Saat ini kita belum menjalankan audit langsung ke MySQL lama karena koneksi source belum tersedia dari workspace ini. Jadi dokumen ini menjadi kontrak eksekusi agar nanti import tidak dilakukan secara tergesa dan tidak mengulang kekacauan shared hosting lama.

# Q-Space Phase 1 Execution Guide

Dokumen ini adalah runbook eksekusi untuk phase 1 bridge PostgreSQL `Q-Space`.

Status rollout saat ini:

- phase 1 sudah berhasil dijalankan di VPS
- produksi diputuskan memakai baseline bersih tanpa import histori MySQL lama
- dokumen ini tetap relevan untuk audit ulang, rebuild environment, atau recovery

Gunakan ini setelah environment target benar-benar mengarah ke PostgreSQL, baik:

- environment uji lokal
- clone database uji
- atau VPS `43.133.152.206` saat waktunya deploy

## Tujuan Phase 1

- membuat schema `q_space`
- menjaga `Q-Space` tetap bisa hidup dengan shadow `users`
- menyiapkan bridge ke `core.users` dan `core.students`
- menghentikan pembuatan akun liar di produksi

## Prasyarat

- `DB_CONNECTION=pgsql`
- `DB_DATABASE=qlink_prod`
- `DB_SCHEMA=q_space,core,public`
- `DB_APP_SCHEMA=q_space`
- `DB_CORE_SCHEMA=core`
- `AUTH_ALLOW_LOCAL_REGISTRATION=false` untuk produksi
- `AUTH_ALLOW_GOOGLE_USER_AUTOCREATE=false` untuk produksi

Template referensi:

- [Q_SPACE_VPS_ENV_TEMPLATE.txt](D:\xampp\htdocs\q-space\Q_SPACE_VPS_ENV_TEMPLATE.txt:1)

## Urutan Eksekusi

### 1. Cek status awal

```powershell
php artisan qspace:phase1-status
```

Tujuan:

- memastikan driver sudah `pgsql`
- memastikan schema app/core terbaca benar
- melihat tabel apa saja yang sudah ada

### 2. Jalankan migration phase 1

```powershell
php artisan migrate --force
```

Target hasil minimal:

- schema `q_space` terbentuk
- shadow `q_space.users` terbentuk
- `q_space.cache`
- `q_space.cache_locks`
- `q_space.job_batches`
- kolom bridge `*_core_user_id` / `*_core_student_id`

### 3. Sinkronkan shadow users

```powershell
php artisan qspace:sync-core-users
```

Dry run bila perlu:

```powershell
php artisan qspace:sync-core-users --dry-run
```

### 4. Isi kolom bridge relasi domain

```powershell
php artisan qspace:sync-core-relations
```

Dry run bila perlu:

```powershell
php artisan qspace:sync-core-relations --dry-run
```

### 5. Audit coverage phase 1

```powershell
php artisan qspace:phase1-status
```

Fokus audit:

- apakah `mapped_rows` mendekati seluruh `source_rows`
- apakah masih ada orphan yang harus dibersihkan manual

Catatan untuk kondisi produksi sekarang:

- bila tabel domain masih kosong karena keputusan skip import histori, hasil `0/0 mapped` adalah kondisi normal

## Interpretasi Hasil

### Aman lanjut ke step berikutnya jika:

- driver benar-benar `pgsql`
- table readiness untuk schema `q_space` lengkap
- coverage mapping tidak menunjukkan gap tak terduga
- orphan = 0 atau jelas penyebabnya

### Tahan refactor controller jika:

- kolom bridge belum terisi
- orphan masih tinggi
- ada data domain yang menunjuk user lokal yang tidak ada padanan di `core`

## Setelah Phase 1 Berhasil

Baru lanjut ke tahap berikut:

1. refactor query/controller agar mulai memakai relation `core*`
2. audit flow dashboard/files/paths/codes
3. validasi upload queue di PostgreSQL
4. smoke test di environment target

## Command Yang Tersedia

```powershell
php artisan qspace:phase1-status
php artisan qspace:sync-core-users
php artisan qspace:sync-core-relations
```

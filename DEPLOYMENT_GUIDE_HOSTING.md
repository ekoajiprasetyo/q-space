# Panduan Deployment Q-Space ke Hosting

Panduan ini menjelaskan langkah-langkah untuk mengupload aplikasi Q-Space ke hosting agar terintegrasi dengan ekosistem Q-Link.

## 1. Persiapan File
1. Pastikan Anda sudah menjalankan `npm run build` di lokal (sudah dilakukan otomatis).
2. File konfigurasi `.env.production` sudah disiapkan. Anda perlu mengisinya dengan kredensial yang beral.

## 2. Struktur Direktori di Hosting
Struktur yang disarankan agar aman:
```
/home/englishh/
├── ...
├── public_html/          # Web Root utama (Q-Link)
│   ├── ...
│   ├── space/            # Subdomain space.q-link.my.id (akan kita buat)
│   └── ...
└── q-space-core/         # Folder aplikasi Laravel Q-Space (DI LUAR public_html agar aman)
```

## 3. Langkah Upload

### A. Upload Core Aplikasi
1. Buat folder `q-space-core` di root direktori user (sejajar dengan `public_html`).
2. Upload semua file dari folder proyek lokal KECUALI:
   - `node_modules`
   - `.git`
   - `.env` (gunakan `.env.production` nanti)
3. Atau, zip seluruh folder proyek (tanpa node_modules), upload, lalu ekstrak di `q-space-core`.

### B. Konfigurasi Environment
1. Rename file `.env.production` menjadi `.env` di dalam folder `q-space-core` di hosting.
2. Edit `.env` tersebut dan isi:
   - `APP_KEY`: **WAJIB SAMA** dengan APP_KEY milik Q-Link (`q-link.my.id`). Copy dari `.env` Q-Link.
   - `DB_PASSWORD`: Password database user `englishh_quser`.

### C. Setup Subdomain & Memindahkan Folder Public (PENTING!)
1. Buat subdomain `space.q-link.my.id` di cPanel. Pastikan Document Root-nya mengarah ke folder baru, misalnya `public_html/space.q-link`.
2. Sekarang di File Manager, buka folder `q-space-core/public` yang baru Anda upload.
3. **PILIH SEMUA FILE** (Select All) di dalam folder `public` tersebut (termasuk `.htaccess`, `index.php`, folder `build`, dll).
4. Klik **MOVE** (Pindahkan) dan arahkan tujuannya ke folder subdomain Anda: `/public_html/space.q-link`.
   > **Intinya:** Folder `public` di dalam `q-space-core` nanti akan kosong. Isinya pindah ke folder subdomain agar bisa diakses internet.
5. Sekarang edit file `index.php` yang sudah ada di `public_html/space.q-link/index.php`.
   Ubah jalur `require` agar menunjuk kembali ke folder `q-space-core`:
   ```php
   // Ubah 2 baris ini:
   require __DIR__.'/../../q-space-core/storage/bootstrap/autoload.php'; // (Jika baris ini ada)
   require __DIR__.'/../../q-space-core/bootstrap/app.php';
   ```
   *Catatan: Gunakan `../` sebanyak yang diperlukan untuk "naik" dari folder space ke root, lalu masuk ke folder q-space-core.*

### D. Symlink Storage
Karena kita memindahkan folder public, symlink storage mungkin rusak.
Buat symlink manual via Terminal cPanel atau Script PHP:
```bash
ln -s /home/englishh/q-space-core/storage/app/public /home/englishh/public_html/space/storage
```

## 4. Database & Migrasi
1. Masuk ke Terminal cPanel atau SSH.
2. Masuk ke folder aplikasi:
   ```bash
   cd q-space-core
   ```
3. Jalankan migrasi:
   ```bash
   php artisan migrate --force
   ```
   *Catatan: Ini aman karena kita sudah memindahkan file migrasi `users` yang konflik.*

## 4.1. Aktivasi Queue Worker (WAJIB untuk Upload File Besar)
Mulai versi upload async, file besar diproses lewat queue agar request web tidak timeout.

1. Pastikan `.env` di hosting berisi:
   - `QUEUE_CONNECTION=database`
   - `DB_QUEUE_RETRY_AFTER=3600`
2. Tambahkan Cron Job di cPanel (setiap 1 menit):
   ```bash
   * * * * * /usr/local/bin/php /home/englishh/q-space-core/artisan queue:work database --queue=uploads,default --sleep=1 --tries=3 --timeout=1800 --stop-when-empty >> /home/englishh/q-space-core/storage/logs/queue-worker.log 2>&1
   ```
3. Alternatif ringan (jika hosting melarang worker panjang), pakai:
   ```bash
   * * * * * /usr/local/bin/php /home/englishh/q-space-core/artisan schedule:run >> /dev/null 2>&1
   ```
   lalu atur scheduler untuk menjalankan `queue:work --stop-when-empty`.

## 5. Verifikasi
1. Buka `https://space.q-link.my.id`
2. Coba login dengan akun Q-Link Anda. Seharusnya langsung berhasil (SSO).

## Catatan Penting
- **APP_KEY** harus sama persis antara Q-Link dan Q-Space agar login (session) bisa dibagi.
- **SESSION_DOMAIN** di `.env` harus `.q-link.my.id` (dengan titik di depan).

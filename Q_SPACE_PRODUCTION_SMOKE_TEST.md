# Q-Space Production Smoke Test

Dokumen ini adalah runbook uji manual pasca cutover untuk produksi `Q-Space`.

Target aktif:

- app: `https://space.q-link.my.id`
- short links: `https://s.q-link.my.id`

Kondisi arsitektur saat dokumen ini ditulis:

- database produksi memakai PostgreSQL `qlink_prod`
- schema app: `q_space`
- schema shared: `core`
- histori MySQL lama tidak diimport
- mutasi identitas lokal ditutup dan diarahkan ke `Q-Link`

## Persiapan

Sebelum mulai, siapkan:

- 1 akun guru/admin yang valid di ekosistem `Q-Link`
- 1 browser normal
- 1 browser incognito atau device kedua untuk membuka link publik upload
- 1 file kecil untuk uji upload, misalnya PDF atau JPG
- jika memungkinkan, akses ke Google Drive akun guru untuk verifikasi hasil upload

## 1. Login dan boundary identitas

### 1.1 Login normal

1. Buka [login](https://space.q-link.my.id/login)
2. Login dengan akun guru/admin yang aktif
3. Pastikan masuk ke dashboard tanpa error

Hasil yang diharapkan:

- halaman dashboard terbuka
- tidak ada error role
- menu `Files`, `Paths`, dan `Codes` terlihat

### 1.2 Forgot password harus diarahkan ke Q-Link

1. Saat logout atau di tab baru, buka [forgot password](https://space.q-link.my.id/forgot-password)

Hasil yang diharapkan:

- browser diarahkan ke `https://q-link.my.id/forgot-password`

### 1.3 Halaman profil tidak boleh memutasi identitas lokal

1. Login sebagai guru/admin
2. Buka halaman profil dari navigasi

Hasil yang diharapkan:

- muncul notifikasi bahwa pengelolaan akun dipusatkan di `Q-Link`
- form perubahan identitas lokal tidak lagi menjadi jalur aktif utama

## 2. File request dan Google token

### 2.1 Cek koneksi Google Drive

1. Buka [Files](https://space.q-link.my.id/files)

Hasil yang diharapkan:

- jika token Google belum tersambung, ada ajakan hubungkan Google Drive
- jika token sudah tersambung, daftar file request bisa dipakai normal

### 2.2 Buat file request baru

1. Dari halaman `Files`, buka `Create File Request`
2. Isi:
   - judul unik, misalnya `Smoke Test 2026-07-09`
   - deskripsi singkat
   - `max_files=1`
   - deadline beberapa menit ke depan atau kosong
3. Simpan

Hasil yang diharapkan:

- kembali ke daftar `Files`
- muncul pesan sukses
- file request baru terlihat di daftar
- link detail bisa dibuka

## 3. Public upload dan upload task

### 3.1 Buka link publik

1. Buka detail file request
2. Ambil link publik upload
3. Buka link itu di browser incognito atau device kedua

Hasil yang diharapkan:

- halaman upload publik terbuka
- judul dan deskripsi request tampil benar

### 3.2 Upload file

1. Isi nama
2. Isi kelas
3. Isi catatan singkat
4. Upload 1 file kecil
5. Submit

Hasil yang diharapkan:

- halaman sukses tampil
- file ditandai sedang diproses atau berhasil diterima
- tidak ada error token guru atau error queue

### 3.3 Cek status upload task

1. Kembali ke halaman detail file request di dashboard guru
2. Refresh beberapa kali bila perlu

Hasil yang diharapkan:

- `upload_tasks` berubah dari `queued` ke `processing` lalu `uploaded`
- jika gagal, error yang tampil harus spesifik dan bisa ditindaklanjuti

### 3.4 Verifikasi file masuk ke Google Drive

1. Buka Google Drive akun guru
2. Masuk ke folder request yang baru dibuat

Hasil yang diharapkan:

- file upload muncul di folder siswa yang sesuai
- nama file dan struktur folder masuk akal

## 4. Short link

### 4.1 Buat short link baru

1. Buka [Paths](https://space.q-link.my.id/paths)
2. Buat short link dengan URL tujuan yang aman untuk diuji
3. Simpan

Hasil yang diharapkan:

- row baru muncul di daftar
- kode short link terbentuk

### 4.2 Uji redirect

1. Buka short link hasil generate di domain `s.q-link.my.id`

Hasil yang diharapkan:

- redirect berjalan ke URL tujuan
- counter visit bertambah setelah refresh daftar

## 5. QR text

### 5.1 Buat QR text baru

1. Buka [Codes](https://space.q-link.my.id/codes)
2. Buat QR text dengan isi singkat

Hasil yang diharapkan:

- item baru muncul di daftar QR text
- slug/public URL terbentuk

### 5.2 Uji halaman publik

1. Buka public URL QR text, formatnya seperti `/t/{slug}`

Hasil yang diharapkan:

- konten tampil normal
- tidak error 404
- view counter bertambah bila fitur itu dipakai di UI

## 6. Logout dan sesi

1. Logout dari `Q-Space`
2. Login lagi
3. Jika memakai alur lintas subdomain, cek apakah sesi tetap sehat saat berpindah antara `q-link.my.id` dan `space.q-link.my.id`

Hasil yang diharapkan:

- logout bersih
- login ulang berhasil
- tidak ada loop auth aneh

## 7. Jika Ada Kegagalan

Jika salah satu langkah gagal, catat minimal:

- tanggal dan jam kejadian
- URL yang dibuka
- akun yang dipakai
- langkah terakhir sebelum gagal
- pesan error yang terlihat
- apakah error muncul di UI, redirect, atau proses upload

## Definisi Lulus

Smoke test dianggap lulus bila:

- login guru/admin berhasil
- boundary identitas mengarah ke `Q-Link`
- file request bisa dibuat
- public upload berjalan
- upload task selesai atau minimal bergerak wajar di queue
- file masuk ke Google Drive
- short link redirect normal
- QR text public page normal

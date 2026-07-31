# Q-Space Post-Cutover Checklist

Dokumen ini menjadi checklist resmi setelah keputusan `clean cutover`:

- `q-space` produksi live di VPS
- PostgreSQL `q_space` dipakai sebagai baseline baru
- histori MySQL lama tidak diimport

Target domain aktif:

- `https://space.q-link.my.id`
- `https://s.q-link.my.id`

Tanggal keputusan: `2026-07-09`

## 1. Bekukan keputusan migrasi

- [ ] Catat bahwa produksi `q-space` resmi berjalan tanpa import histori MySQL lama.
- [ ] Simpan backup MySQL shared hosting sebagai arsip read-only.
- [ ] Pastikan tim memahami bahwa PostgreSQL `q_space` sekarang adalah sumber data produksi aktif.

## 2. Kunci boundary identitas

- [ ] Pastikan `AUTH_ALLOW_LOCAL_REGISTRATION=false`.
- [ ] Pastikan `AUTH_ALLOW_LOCAL_IDENTITY_MUTATION=false`.
- [ ] Pastikan model auth Q-Space membaca `core.users`.
- [ ] Pastikan tabel `q_space.users` sudah tidak ada.
- [ ] Pastikan tidak ada flow produksi yang membuat user lokal liar lagi.
- [ ] Pastikan OAuth Google Q-Space hanya dipakai untuk menghubungkan Google Drive.

## 3. Audit environment produksi

- [ ] Pastikan `DB_CONNECTION=pgsql`.
- [ ] Pastikan `DB_SCHEMA=q_space,core,public`.
- [ ] Pastikan `DB_APP_SCHEMA=q_space`.
- [ ] Pastikan `DB_CORE_SCHEMA=core`.
- [ ] Pastikan `SESSION_CONNECTION=pgsql`.
- [ ] Pastikan `SESSION_COOKIE=qlink_sso_session`.
- [ ] Pastikan `SESSION_DOMAIN=.q-link.my.id`.
- [ ] Pastikan `APP_DOMAIN=space.q-link.my.id`.
- [ ] Pastikan `SHORTLINK_DOMAIN=s.q-link.my.id`.
- [ ] Pastikan tidak ada file env lama yang bisa mengganggu cache config.

## 4. Audit database live

- [ ] Jalankan `php artisan qspace:identity-status`.
- [ ] Pastikan schema `q_space` lengkap.
- [ ] Pastikan seluruh foreign key identity mengarah ke `core.users` atau `core.students`.
- [ ] Pastikan kolom bridge transisi sudah dihapus.
- [ ] Pastikan backup pre-cutover dapat dibaca dan checksum-nya tersimpan.

## 5. Smoke test fungsional final

Referensi runbook:

- [Q_SPACE_PRODUCTION_SMOKE_TEST.md](D:\xampp\htdocs\q-space\Q_SPACE_PRODUCTION_SMOKE_TEST.md:1)

- [ ] Login dengan akun ekosistem yang valid.
- [ ] Verifikasi user nonaktif ditolak.
- [ ] Buat `file_request` baru.
- [ ] Upload submission dari link publik.
- [ ] Verifikasi `upload_tasks` bergerak sesuai status proses.
- [ ] Verifikasi file masuk ke Google Drive.
- [ ] Buat short link baru dan uji redirect di `s.q-link.my.id`.
- [ ] Buat QR text baru dan uji public page.
- [ ] Logout dan login ulang untuk memastikan sesi tetap sehat.

## 6. Hardening operasi server

- [ ] Pastikan worker queue permanen aktif.
- [ ] Pastikan restart policy worker aman.
- [ ] Pastikan log aplikasi dan log worker bisa dipantau.
- [ ] Pastikan backup PostgreSQL terjadwal.
- [ ] Pastikan sertifikat HTTPS aktif dan renewal otomatis sehat.

## 7. Housekeeping repo dan deploy

- [ ] Pertahankan dokumen import guardrails hanya sebagai arsip kontinjensi.
- [ ] Hapus asumsi operasional yang masih menyiratkan MySQL sebagai source aktif.
- [ ] Simpan runbook deploy final untuk rebuild server jika diperlukan.

## 8. Arah fase berikutnya

- [ ] Pantau error autentikasi dan foreign key setelah cutover.
- [ ] Uji hard-delete akun hanya melalui prosedur yang menangani data domain lintas aplikasi.
- [ ] Hapus backup shadow setelah masa retensi dan verifikasi pemulihan selesai.

## Definisi Selesai

Cutover ini dianggap benar-benar selesai bila:

- aplikasi stabil di produksi
- flow utama lolos smoke test
- tidak ada kebutuhan import histori lama
- boundary identitas tidak lagi membuka peluang user ganda
- operasi server sudah punya backup dan worker yang stabil

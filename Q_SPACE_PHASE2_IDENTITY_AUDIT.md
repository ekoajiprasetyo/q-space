# Q-Space Phase 2 Identity Audit

Tanggal audit: `2026-07-09`
Workspace: `D:\xampp\htdocs\q-space`
Status produksi: `clean cutover` aktif, tanpa import histori MySQL lama

## Tujuan

Memetakan bagian aplikasi yang masih bergantung langsung pada shadow identity `q_space.users` agar refactor phase 2 bisa dilakukan bertahap tanpa mengganggu produksi.

## Ringkasan

Ketergantungan ke `User` lokal masih terbagi menjadi 4 kelompok:

1. auth layer
2. domain ownership query
3. Google Drive token lookup
4. profile/password/account management

Yang paling aman dikunci lebih dulu adalah mutasi identitas lokal. Karena itu repo ini sekarang mendukung flag:

```env
AUTH_ALLOW_LOCAL_IDENTITY_MUTATION=false
```

Saat flag itu dimatikan:

- edit profil lokal diarahkan ke `Q-Link`
- ganti password lokal diarahkan ke `Q-Link`
- reset password lokal diarahkan ke `Q-Link`
- hapus akun lokal ditahan

## Peta Ketergantungan Saat Ini

### A. Auth dan account lifecycle

Masih langsung memakai `App\Models\User`:

- `config/auth.php`
- `app/Http/Controllers/Auth/GoogleAuthController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`
- `app/Http/Requests/ProfileUpdateRequest.php`

Makna arsitektural:

- `q-space` masih login terhadap shadow user lokal
- identitas master belum dibaca langsung dari `core.users`
- registrasi dan auto-create sudah ditahan, tetapi provider auth belum dipindah

### B. Query domain yang masih bergantung pada kolom legacy

Masih memakai kolom legacy sebagai filter utama:

- `file_requests.teacher_id`
- `file_submissions.student_id`
- `user_google_tokens.user_id`
- `short_links.user_id`
- `qr_texts.user_id`
- `upload_tasks.teacher_id`

Controller yang paling dominan:

- `app/Http/Controllers/FileRequestController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/ShortLinkController.php`
- `app/Http/Controllers/QrCodeController.php`
- `app/Http/Controllers/QrTextController.php`
- `app/Jobs/UploadSubmissionToDriveJob.php`

Makna arsitektural:

- walaupun bridge `*_core_user_id` dan `*_core_student_id` sudah ada, query utama masih belum memakainya
- ownership domain masih kompatibel fase 1, belum native ke boundary `core`

### C. Mutasi profil/password lokal

Sebelum guard baru ditambahkan, area berikut dapat mengubah shadow identity secara langsung:

- `app/Http/Controllers/ProfileController.php`
- `app/Http/Controllers/Auth/PasswordController.php`
- `app/Http/Controllers/Auth/PasswordResetLinkController.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`

Risiko utamanya:

- nama, email, password, atau penghapusan akun lokal bisa divergen dari `core.users`
- itu membuat SSO dan identitas ekosistem berpotensi tidak rapi lagi

### D. UI yang menampilkan user login

Banyak blade view masih membaca `Auth::user()` langsung. Ini normal untuk phase 1 selama provider auth masih memakai shadow user lokal.

Contoh:

- `resources/views/dashboard.blade.php`
- `resources/views/dashboard/teacher.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/profile/edit.blade.php`

Ini bukan target refactor pertama, selama object auth masih sengaja dipertahankan kompatibel.

## Prioritas Refactor

### Prioritas 1 - Sudah aman dikunci sekarang

- larang mutasi identitas lokal di produksi
- jadikan `Q-Link` satu-satunya tempat ubah profil/password

Status:

- sudah didukung oleh flag `AUTH_ALLOW_LOCAL_IDENTITY_MUTATION`

### Prioritas 2 - Refactor query ownership domain

Target:

- `FileRequest` mulai mengandalkan `teacher_core_user_id`
- `UploadTask` mulai mengandalkan `teacher_core_user_id`
- `UserGoogleToken`, `ShortLink`, dan `QrText` mulai mengandalkan `core_user_id`
- `FileSubmission` mulai mengandalkan `student_core_student_id` bila benar-benar butuh siswa terdaftar

Catatan:

- ini paling cocok dilakukan per area fitur, bukan sekaligus
- flow yang paling cocok dikerjakan dulu: short link, QR text, lalu dashboard

Status update:

- `ShortLink` sudah punya scope ownership yang mengutamakan `core_user_id` dengan fallback aman ke `user_id`
- `QrText` sudah punya scope ownership yang mengutamakan `core_user_id` dengan fallback aman ke `user_id`
- create flow baru untuk `ShortLink` dan `QrText` sekarang otomatis mengisi `core_user_id` saat koneksi default memakai PostgreSQL
- authorization delete/update pada dua area itu juga sudah mulai membaca owner bridge
- `DashboardController` sudah mulai memakai scope bridge ownership untuk statistik `FileRequest` dan `ShortLink`
- `UserGoogleToken` sudah punya scope ownership bridge untuk lookup token guru
- `FileRequestController` mulai memakai ownership bridge untuk daftar file, create request, authorization, dan lookup token
- `UploadTask` mulai mengisi dan membaca `teacher_core_user_id` sebagai owner bridge
- `UploadSubmissionToDriveJob` sudah mencari token guru lewat identity bridge, bukan hanya `teacher_id` legacy

### Prioritas 3 - Auth provider ke model shared

Target akhir:

- auth provider tidak lagi tergantung pada `App\Models\User`
- sumber identitas baca langsung dari `core.users` atau lewat shared auth abstraction

Catatan:

- ini perubahan paling sensitif
- jangan dilakukan sebelum query domain dan lifecycle account sudah lebih bersih

## Rekomendasi Urutan Kerja Berikutnya

1. aktifkan `AUTH_ALLOW_LOCAL_IDENTITY_MUTATION=false` di produksi
2. smoke test halaman profile agar redirect dan pesan bantuan tampil benar
3. smoke test `ShortLink` dan `QrText` di produksi setelah refactor bridge ownership
4. smoke test flow file request dan upload task di produksi
5. setelah itu evaluasi auth provider shared yang lebih native ke `core`

## Kesimpulan

`q-space` saat ini sudah stabil untuk produksi phase 1, tetapi masih memakai shadow identity sebagai lapisan kompatibilitas. Arah phase 2 yang paling aman bukan langsung memotong `User` lokal, melainkan:

1. kunci mutasi identitas lokal
2. pindahkan ownership query ke bridge kolom `core`
3. baru siapkan auth provider shared yang lebih native ke ekosistem

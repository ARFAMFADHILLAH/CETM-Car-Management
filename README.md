# CarManage-CETM

Aplikasi Manajemen Peminjaman Mobil **PT Central Elektrindo Mandiri** — platform internal untuk mengelola pemesanan dan peminjaman kendaraan operasional perusahaan dalam satu sistem.

Dibangun dengan Laravel 13.26 (PHP ^8.5) + Inertia.js v3 + Vue 3 + TypeScript + Tailwind CSS 4 + Vite 8 + MySQL/MariaDB.

## Fitur Utama

### Autentikasi & Keamanan (Laravel Fortify)
- Login / logout dengan rate-limit anti brute-force
- Lupa kata sandi & reset kata sandi via tautan email
- Konfirmasi kata sandi sebelum mengakses area sensitif
- Autentikasi dua faktor (2FA/TOTP) dengan QR code + kode pemulihan
- Passkey/WebAuthn — masuk tanpa kata sandi memakai biometrik atau PIN perangkat
- Kata sandi tersimpan ter-hash (bcrypt), sesi terenkripsi di database

### Modul Peminjaman Mobil (User)
- **Ajukan Peminjaman** — form lengkap: divisi, mobil, tanggal, tujuan, keperluan, KM awal, tangki BBM
- **Daftar Pengajuan** — riwayat pengajuan beserta status (pending / disetujui / ditolak / selesai)
- **Cek Ketersediaan Mobil** — filter otomatis berdasarkan tanggal peminjaman (mencegah booking bentrok)

### Modul Peminjaman Mobil (Admin)
- **Approve Peminjaman** — setujui atau tolak pengajuan pengguna
- **Jadwal Mobil** — kalender bulanan jadwal peminjaman seluruh armada

### Manajemen Data (Admin)
- **Manajemen Pengguna** — CRUD pengguna + reset password
- **Manajemen Admin** — CRUD admin + reset password
- **Data Mobil** — CRUD kendaraan beserta status (tersedia / tidak tersedia / di servis)

### Laporan (Admin)
- Laporan peminjaman, mobil, dan pengguna
- Export ke **PDF** (DomPDF) dan **Excel** (Maatwebsite/Excel)

### Chatbot Virtual
- Asisten berbasis database (keyword matching + query Eloquent)
- Bisa bertanya: info mobil, jadwal, status pengajuan, divisi, cara pengajuan
- Tidak memerlukan provider eksternal / API key

### Profil & Pengaturan
- Upload & ganti foto profil (JPEG/PNG/WebP, maks 2 MB)
- Kelola nama, email, nomor HP
- Tampilan: tema terang / gelap / ikuti sistem
- Hapus akun (perlu konfirmasi kata sandi)

### Lainnya
- Dashboard terproteksi middleware `auth` + `verified`
- Notifikasi aktivitas peminjaman

## Alur Bisnis

```
Registrasi  ──►  Verifikasi Email  ──►  Login (+ 2FA / Passkey opsional)  ──►  Dashboard
                                                                                   │
          ┌────────────────────────────────────────────────────────────────────────┘
          ▼
   Ajukan Peminjaman  ──►  Admin Review  ──►  Disetujui / Ditolak
                                                     │
                                               Mobil Dipakai
                                                     │
                                               Selesai (KM akal terisi)
```

## Cara Menjalankan

```bash
composer install           # install dependency backend
npm install                # install dependency frontend
cp .env.example .env       # lalu atur koneksi database (lihat catatan di bawah)
php artisan key:generate
php artisan migrate --seed # migrasi + akun demo
php artisan storage:link   # symlink storage untuk foto profil
npm run dev                # terminal kedua: Vite dev server
php artisan serve          # terminal ketiga → http://localhost:8000
```

### Akun Demo

| Nama | Email | Password | Role |
|------|-------|----------|------|
| Administrator | admin@example.com | password | Admin |
| Budi Santoso | budi@example.com | password | User |
| Siti Rahayu | siti@example.com | password | User |
| Andi Wijaya | andi@example.com | password | User |

## Production

```bash
composer install --no-dev --optimize-autoloader
npm run build              # build aset frontend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Setelah `git pull` selalu jalankan `php artisan migrate` untuk migrasi terbaru. Setelah memperbarui kode frontend (Vue/TypeScript), jalankan kembali `npm run build` — tanpa itu halaman masih memakai aset versi lama.

## Teknologi

| Teknologi | Kegunaan |
|-----------|----------|
| Laravel 13.26 (PHP ^8.5) | Framework, routing, Eloquent ORM |
| Inertia.js v3 + Vue 3 + TypeScript | SPA tanpa API terpisah — halaman dirender langsung dari route Laravel |
| Tailwind CSS 4 | Styling utility-first |
| Vite 8 | Build asset frontend |
| Laravel Fortify | Backend autentikasi: login, register, verifikasi email, reset sandi, 2FA, passkey |
| Wayfinder | Generate fungsi TypeScript dari route/controller Laravel |
| reka-ui + Lucide | Komponen UI headless & ikon |
| DomPDF + Maatwebsite/Excel | Export laporan ke PDF dan Excel |
| MySQL/MariaDB | Basis data |
| Pest 5 (Feature Tests) | Pengujian otomatis — 98 kasus uji |

## Struktur Direktori

```
├── app/
│   ├── Concerns/                 # ProfileValidationRules
│   ├── Enums/                    # CarStatus, PeminjamanStatus, UserRole
│   ├── Exports/                  # PeminjamanExport (Maatwebsite)
│   ├── Http/Controllers/
│   │   ├── CarController.php     # CRUD mobil
│   │   ├── ChatbotController.php # chatbot keyword-based
│   │   ├── ManajemenAdminController.php
│   │   ├── ManajemenPenggunaController.php
│   │   ├── PeminjamanController.php  # ajukan, daftar, approve
│   │   ├── ReportController.php      # laporan + export PDF/Excel
│   │   └── Settings/            # profil, keamanan akun
│   └── Models/                  # User, Car, Peminjaman, Divisi, Role
├── database/
│   ├── migrations/              # users, cars, peminjaman, divisi, roles
│   ├── factories/               # UserFactory, CarFactory, PeminjamanFactory
│   └── seeders/                 # akun demo, data mobil, divisi
├── resources/js/
│   ├── pages/
│   │   ├── admin/               # approve peminjaman, manajemen pengguna/admin, laporan
│   │   ├── user/                # ajukan peminjaman, daftar pengajuan
│   │   ├── chatbot/             # chatbot virtual
│   │   ├── jadwal/              # kalender jadwal mobil
│   │   ├── mobil/               # data mobil
│   │   ├── notifikasi/          # notifikasi
│   │   ├── settings/            # profil, keamanan, tampilan
│   │   └── auth/                # login, register, lupa sandi, 2FA, passkey
│   ├── components/              # komponen UI (sidebar, form, 2FA, passkey, chatbot)
│   ├── layouts/                 # AppLayout, AuthLayout, settings
│   ├── composables/             # useAppearance, useTwoFactorAuth, dll.
│   ├── mock/                    # data mock (peminjaman)
│   ├── actions/                 # (Wayfinder) controller actions
│   └── routes/                  # (Wayfinder) fungsi route bertipe
├── routes/
│   ├── web.php                  # semua route domain
│   └── settings.php             # profil, keamanan, passkey endpoints
├── resources/views/
│   └── reports/                 # blade template export PDF peminjaman
├── tests/
│   ├── Feature/Auth/            # login, register, verifikasi email, reset, 2FA
│   ├── Feature/Settings/        # profil, keamanan
│   └── Unit/
└── public/
```

## Menjalankan Pengujian

```bash
php artisan test --compact      # seluruh test suite (98 tests, Pest)
php artisan test --filter=NamaTest  # jalankan test tertentu
composer run lint               # format kode PHP (Pint)
npm run types:check             # type-check TypeScript (vue-tsc)
```

## Catatan

- `.env.example` bawaan memakai **SQLite** sebagai opsi cepat tanpa server database; untuk pengembangan maupun produksi disarankan **MySQL/MariaDB** seperti instruksi di atas.
- **Keamanan `.env`**: file `.env` menyimpan kredensial database dan `APP_KEY`, sudah diabaikan git sejak awal (terdaftar di `.gitignore`), dan **tidak boleh pernah di-commit atau di-push** ke GitHub/repository mana pun. Untuk berbagi konfigurasi, gunakan hanya `.env.example` yang berisi placeholder tanpa nilai asli.
- Rute autentikasi (login, register, reset kata sandi, 2FA, passkey, verifikasi email) didaftarkan otomatis oleh **Laravel Fortify** — tidak didefinisikan manual di `routes/web.php`.
- Folder `resources/js/actions`, `resources/js/routes` digenerate otomatis oleh plugin **Wayfinder** saat `npm run dev` / `npm run build` — jangan diedit manual.
- Middleware `auth` + `verified` melindungi dashboard: pengguna belum verifikasi email akan dialihkan ke halaman verifikasi.
- **Chatbot** bekerja sepenuhnya secara lokal (keyword matching + database query) — tidak memerlukan API key atau provider eksternal.
- **Foto profil** disimpan di `storage/app/public/users/`. Jalankan `php artisan storage:link` untuk membuat symlink ke `public/storage`.
- Export laporan PDF menggunakan **DomPDF**, export Excel menggunakan **Maatwebsite/Excel**.

---

© 2026 Arfa Muhammad Fadhillah – PT Central Elektrindo Mandiri.

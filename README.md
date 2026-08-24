# CarManage-CETM

Aplikasi Manajemen Peminjaman Mobil **PT Central Elektrindo Mandiri** — platform internal untuk mengelola pemesanan dan peminjaman kendaraan operasional perusahaan dalam satu sistem.

Dibangun dengan Laravel 13.26 (PHP ^8.3) + Inertia.js v3 + Vue 3 + TypeScript + Tailwind CSS 4 + Vite 8 + MySQL/MariaDB.

> Saat ini aplikasi berada pada tahap fondasi: autentikasi & keamanan lengkap sudah berjalan, sedangkan modul domain peminjaman mobil sedang dikembangkan (lihat [Rencana Pengembangan](#rencana-pengembangan)).

## Fitur Utama

### Autentikasi Lengkap (Laravel Fortify)
- Login / logout dengan rate-limit anti brute-force
- Lupa kata sandi & reset kata sandi via tautan email
- Konfirmasi kata sandi sebelum mengakses area sensitif

### Keamanan Lanjutan
- Autentikasi dua faktor (2FA/TOTP) dengan QR code + kode pemulihan (recovery codes)
- Passkey/WebAuthn — masuk tanpa kata sandi memakai biometrik atau PIN perangkat
- Kata sandi tersimpan ter-hash (bcrypt), sesi terenkripsi di database

### Area Setelah Login
- Dashboard terproteksi middleware `auth` + `verified` (wajib login DAN sudah verifikasi email)
- Pengaturan profil: ubah nama/email, hapus akun (perlu konfirmasi kata sandi)
- Keamanan akun: ganti kata sandi, kelola 2FA, daftar passkey
- Tampilan: tema terang / gelap / ikuti sistem
- Halaman Welcome publik untuk pengunjung yang belum login

## Rencana Pengembangan

| Peran | Rencana Fungsi |
|-------|----------------|
| Admin | Kelola master kendaraan, jadwal ketersediaan, persetujuan & riwayat peminjaman armada |
| Karyawan | Ajukan peminjaman mobil operasional, pantau status pengajuan & riwayat pinjam |

Modul domain (master kendaraan, booking, approval, pengembalian) belum tersedia pada basis data dan kode saat ini.

## Alur Bisnis

```
Registrasi  ──►  Verifikasi Email  ──►  Login (+ 2FA / Passkey opsional)  ──►  Dashboard
                                                                                  │
              Profil / Keamanan Akun / Tampilan  ◄────────────────────────────────┘

(Rencana) Karyawan ajukan pinjam mobil ──► Admin setujui ──► mobil dipakai ──► dikembalikan
```

## Cara Menjalankan

```bash
composer install           # install dependency backend
npm install                # install dependency frontend
cp .env.example .env       # lalu atur koneksi database (lihat catatan di bawah)
php artisan key:generate
php artisan migrate --seed # migrasi + akun demo
npm run dev                # terminal kedua: Vite dev server
php artisan serve          # terminal ketiga → http://localhost:8000
```

### Akun Demo

| Nama | Email | Password |
|------|-------|----------|
| Test User | test@example.com | password |

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
| Laravel 13.26 (PHP ^8.3) | Framework, routing, Eloquent ORM |
| Inertia.js v3 + Vue 3 + TypeScript | SPA tanpa API terpisah — halaman dirender langsung dari route Laravel |
| Tailwind CSS 4 | Styling utility-first |
| Vite 8 | Build asset frontend (+ SSR opsional) |
| Laravel Fortify | Backend autentikasi: login, register, verifikasi email, reset sandi, 2FA, passkey |
| Wayfinder | Generate fungsi TypeScript dari route/controller Laravel (`resources/js/actions`, `resources/js/routes`) |
| reka-ui + Lucide | Komponen UI headless & ikon |
| MySQL/MariaDB | Basis data |
| Pest 5 (Feature Tests) | Pengujian otomatis — 39 kasus uji (`php artisan test`) |

## Struktur Direktori

```
├── app/
│   ├── Http/Controllers/
│   │   └── Settings/            # profil, keamanan akun
│   ├── Models/                  # User (dukungan 2FA & passkey)
│   └── Providers/
├── database/
│   ├── migrations/              # users, cache, jobs, passkeys, kolom 2FA
│   ├── factories/               # UserFactory
│   └── seeders/                 # akun demo
├── resources/js/
│   ├── pages/                   # Welcome, Dashboard, auth/*, settings/*
│   ├── components/              # komponen UI (sidebar, form, 2FA, passkey)
│   ├── layouts/                 # AppLayout, AuthLayout
│   ├── composables/             # useAppearance, useTwoFactorAuth, dll.
│   ├── actions/                 # (Wayfinder) controller actions
│   └── routes/                  # (Wayfinder) fungsi route bertipe
├── routes/
│   ├── web.php                  # welcome, dashboard
│   └── settings.php             # profil, keamanan, passkey endpoints
├── tests/
│   ├── Feature/Auth/            # login, register, verifikasi email, reset, 2FA
│   ├── Feature/Settings/        # profil, keamanan
│   └── Unit/
└── public/
```

## Menjalankan Pengujian

```bash
php artisan test       # seluruh test suite (Pest)
composer run lint      # format kode PHP (Pint)
npm run types:check    # type-check TypeScript (vue-tsc)
composer run ci:check  # lint + format + types + test sekaligus
```

## Catatan

- `.env.example` bawaan memakai **SQLite** sebagai opsi cepat tanpa server database; untuk pengembangan maupun produksi disarankan **MySQL/MariaDB** seperti instruksi di atas.
- **Keamanan `.env`**: file `.env` menyimpan kredensial database dan `APP_KEY`, sudah diabaikan git sejak awal (terdaftar di `.gitignore`), dan **tidak boleh pernah di-commit atau di-push** ke GitHub/repository mana pun. Untuk berbagi konfigurasi, gunakan hanya `.env.example` yang berisi placeholder tanpa nilai asli.
- Rute autentikasi (login, register, reset kata sandi, 2FA, passkey, verifikasi email) didaftarkan otomatis oleh **Laravel Fortify** — tidak didefinisikan manual di `routes/web.php`.
- Folder `resources/js/actions`, `resources/js/routes`, dan `resources/js/wayfinder` digenerate otomatis oleh plugin **Wayfinder** saat `npm run dev` / `npm run build` — jangan diedit manual.
- Middleware `auth` + `verified` melindungi dashboard: warga belum verifikasi email akan dialihkan ke halaman verifikasi.
- Modul domain peminjaman mobil (master kendaraan, booking, approval) belum tersedia — lihat bagian [Rencana Pengembangan](#rencana-pengembangan).

---

© 2026 Arfa Muhammad Fadhillah – PT Central Elektrindo Mandiri.

# Dashboard Barang

Dashboard Barang adalah aplikasi internal untuk mencatat pengajuan, penerimaan, stok minimal, dan barang keluar. Aplikasi dibangun dengan Laravel dan antarmuka TailAdmin.

## Fitur utama

- Autentikasi pengguna dan pengelolaan profil.
- Dashboard ringkasan stok dan pengajuan.
- Pengajuan barang beserta persetujuan atau penolakan disposisi.
- Penerimaan barang, termasuk penerimaan parsial.
- Daftar belanja dari pengajuan yang disetujui.
- Peringatan stok habis atau menipis berdasarkan batas minimal.
- Pencatatan barang keluar dari lemari.
- Pencarian, pagination, dan ekspor laporan CSV pada modul terkait.

Lihat detailnya di [dokumentasi fitur](docs/features.md) dan [arsitektur proyek](docs/architecture.md).

## Teknologi

- PHP 8.2+ dan Laravel 12
- Blade, Tailwind CSS v4, Alpine.js, dan Vite
- MySQL (konfigurasi bawaan)
- Laravel Sail/Docker tersedia sebagai opsi lingkungan pengembangan

## Prasyarat

- PHP 8.2 atau lebih baru
- Composer
- Node.js 18+ dan npm
- MySQL, atau Docker Desktop untuk menggunakan Laravel Sail

## Instalasi lokal

1. Siapkan konfigurasi aplikasi.

   ```powershell
   Copy-Item .env.example .env
   ```

2. Ubah variabel `DB_*` di `.env` sesuai database lokal. Nilai awal mengarah ke database `dashboard_barang` pada MySQL lokal.

3. Pasang dependensi dan siapkan aplikasi.

   ```powershell
   composer install
   npm install
   php artisan key:generate
   php artisan migrate
   ```

4. Jalankan aplikasi dan Vite secara bersamaan.

   ```powershell
   composer run dev
   ```

   Aplikasi akan tersedia pada alamat yang ditampilkan oleh `php artisan serve` (umumnya `http://127.0.0.1:8000`).

## Menjalankan dengan Laravel Sail

Pastikan Docker Desktop aktif, lalu atur `.env` untuk jaringan container:

```env
DB_HOST=mysql
DB_USERNAME=sail
DB_PASSWORD=password
REDIS_HOST=redis
```

Kemudian jalankan:

```powershell
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Secara bawaan aplikasi Sail tersedia di `http://localhost`. Lihat [panduan deployment dan operasi](docs/deployment.md) untuk detail konfigurasi.

## Perintah penting

```powershell
# Server Laravel, antrean, log, dan Vite
composer run dev

# Build asset produksi
npm run build

# Menjalankan pengujian
composer test

# Mengosongkan cache konfigurasi
php artisan optimize:clear
```

## Dokumentasi

- [Arsitektur](docs/architecture.md)
- [Fitur dan alur bisnis](docs/features.md)
- [Deployment dan operasi](docs/deployment.md)

## Lisensi

Proyek ini menggunakan lisensi yang tercantum pada file [LICENSE](LICENSE).

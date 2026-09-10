# Deployment dan Operasi

## Konfigurasi produksi

Sebelum deploy, buat `.env` di server berdasarkan `.env.example` dan isi nilai rahasia secara aman. Minimal periksa:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://contoh-domain.com

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Jangan menyimpan `.env`, `APP_KEY`, atau kredensial database di Git.

## Langkah rilis

1. Pasang dependensi PHP tanpa paket pengembangan dan build asset frontend.

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

2. Jalankan migrasi database.

   ```bash
   php artisan migrate --force
   ```

3. Simpan cache Laravel untuk produksi.

   ```bash
   php artisan optimize
   ```

4. Pastikan proses web server menunjuk ke direktori `public/`, bukan ke akar proyek.

5. Pastikan direktori `storage/` dan `bootstrap/cache/` dapat ditulis oleh pengguna web server.

Jika aplikasi memakai antrean database, jalankan worker yang persisten, misalnya melalui Supervisor atau systemd:

```bash
php artisan queue:work --tries=1
```

## Laravel Sail

`docker-compose.yml` menyediakan service aplikasi PHP 8.4, MySQL 8, Redis, dan Mailpit. Untuk Sail, set `DB_HOST=mysql` dan `REDIS_HOST=redis` agar aplikasi menggunakan nama service Docker.

Perintah yang umum digunakan:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
./vendor/bin/sail down
```

## Pemeriksaan setelah rilis

- Buka halaman masuk dan lakukan autentikasi dengan akun yang valid.
- Pastikan dashboard menampilkan data tanpa galat koneksi database.
- Uji pembuatan pengajuan dan ekspor CSV jika fitur tersebut dipakai.
- Periksa log aplikasi pada `storage/logs/laravel.log` bila terjadi masalah.

## Pemeliharaan

Saat konfigurasi lingkungan berubah, gunakan `php artisan optimize:clear` sebelum membuat cache lagi dengan `php artisan optimize`. Jalankan `composer test` sebelum rilis untuk memeriksa regresi.

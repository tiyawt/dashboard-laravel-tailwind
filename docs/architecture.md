# Arsitektur

## Gambaran

Dashboard Barang adalah aplikasi Laravel monolitik: rute web memanggil controller, controller berinteraksi dengan model Eloquent, dan hasilnya dirender oleh Blade. Asset CSS dan JavaScript diproses melalui Vite.

```text
Browser -> routes/web.php -> Controller -> Model/Eloquent -> MySQL
                                      |
                                      v
                                Blade + Tailwind + Alpine
```

Semua rute bisnis berada di balik middleware `auth` dan `prevent-back`. Halaman masuk (`/signin`) hanya dapat diakses tamu.

## Struktur penting

```text
app/
  Http/Controllers/     Logika tiap modul dan ekspor CSV
  Http/Middleware/      Middleware PreventBackHistory
  Models/               Model dan relasi Eloquent
  Observers/            Otomasi setelah perubahan pengajuan
resources/
  css/app.css           Token Tailwind dan gaya global
  js/app.js             Entry point JavaScript
  views/layouts/        Kerangka halaman utama dan autentikasi
  views/components/     Komponen Blade yang digunakan ulang
  views/pages/          Tampilan per modul
routes/web.php          Rute antarmuka web
database/migrations/    Skema database
```

## Model dan relasi data

```text
master_barang (1) ----< pengajuan_barang (1) ----< penerimaan_barang
       |
       +---- (1) minimal_stock
       |
       +----< stok_keluar_lemari
```

`MinimalStock` menghitung status stok dari data barang: `habis` saat stok nol atau kurang, `menipis` saat stok tidak melebihi batas minimal, selain itu `aman`.

`PengajuanBarang` menghitung `total_diterima` dari seluruh penerimaan terkait. Nilai `selisih` adalah total diterima dikurangi volume pengajuan; nilai negatif berarti kebutuhan belum terpenuhi.

## Frontend

- Layout dashboard menggunakan `resources/views/layouts/app.blade.php`.
- Layout layar penuh, misalnya autentikasi, menggunakan `resources/views/layouts/fullscreen-layout.blade.php`.
- Gunakan komponen Blade dengan prefix seperti `<x-common.*>`, `<x-ui.*>`, dan `<x-form.*>` agar tampilan konsisten.
- Tailwind CSS v4 dikonfigurasi dalam `resources/css/app.css`; proyek tidak memakai `tailwind.config.js`.
- Alpine.js dipakai untuk interaksi UI ringan, termasuk dropdown dan mode tema.

## Menambahkan modul baru

1. Tambahkan model dan migrasi bila membutuhkan tabel baru.
2. Buat controller di `app/Http/Controllers`.
3. Daftarkan rute di `routes/web.php` dalam grup middleware yang sesuai.
4. Buat halaman Blade di `resources/views/pages/<nama-modul>/`.
5. Gunakan layout dan komponen yang sudah ada, kemudian tambahkan pengujian bila perilaku bisnis berubah.

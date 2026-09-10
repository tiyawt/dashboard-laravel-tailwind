# Fitur dan Alur Bisnis

## Akses pengguna

Pengguna yang belum masuk diarahkan ke `/signin`. Setelah autentikasi, seluruh modul aplikasi memerlukan sesi login. Pengguna dapat memperbarui profil pada `/profile`.

## Dashboard

Halaman `/` dan `/dashboard` menampilkan:

- Total master barang.
- Jumlah pengajuan berstatus `pending`.
- Jumlah stok `habis` dan `menipis`.
- Lima peringatan stok, pengajuan terbaru, dan barang keluar terbaru.

## Pengajuan barang

Pengajuan menyimpan barang, tanggal, volume, harga per unit, pihak/divisi peminta, kondisi barang, status disposisi, dan keterangan. Status disposisi yang tersedia adalah `pending`, `acc`, dan `rejected`.

Pengajuan dapat dibuat, diubah, dihapus, diperbarui statusnya, atau diekspor ke CSV. Pengajuan berstatus `acc` menjadi sumber daftar belanja.

## Daftar belanja dan penerimaan

Daftar belanja berisi pengajuan yang disetujui. Modul ini mendukung pencarian nama barang atau divisi peminta serta ekspor CSV per bulan dan tahun.

Satu pengajuan dapat memiliki beberapa catatan penerimaan. Karena itu penerimaan dapat dicatat sebagian sampai kebutuhan pengajuan terpenuhi. Status pemenuhan dihitung dari perbandingan jumlah diterima dan volume pengajuan.

## Stok minimal

Setiap master barang maksimal memiliki satu aturan batas minimal stok. Sistem memberi label:

| Kondisi | Aturan |
| --- | --- |
| Habis | Stok kurang dari atau sama dengan 0 |
| Menipis | Stok lebih dari 0 dan kurang dari atau sama dengan batas minimal |
| Aman | Stok lebih tinggi dari batas minimal |

Aturan stok minimal dapat dibuat, diubah, dihapus, dan diekspor ke CSV.

## Barang keluar

Modul barang keluar mencatat barang, kondisi barang lama, tanggal keluar, jumlah, pelapor, lokasi, status, dan keterangan. Status yang tersedia adalah `not_yet` dan `done`. Data dapat dibuat, diubah, dihapus, serta diekspor ke CSV.

## Daftar rute utama

| Modul | Awalan URL |
| --- | --- |
| Masuk | `/signin` |
| Dashboard | `/` atau `/dashboard` |
| Pengajuan barang | `/pengajuan-barang` |
| Daftar belanja | `/daftar-belanja` |
| Penerimaan barang | `/penerimaan-barang` |
| Stok minimal | `/stok-minimal` |
| Barang keluar | `/barang-keluar` |
| Profil | `/profile` |

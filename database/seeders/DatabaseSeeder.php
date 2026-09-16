<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AsetInventaris;
use App\Models\MasterLokasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            [
                ['nama' => 'Gedung Baru', 'kode' => 'GB', 'tipe' => 'Gedung'],
                ['nama' => 'Gedung Lama', 'kode' => 'GL', 'tipe' => 'Gedung'],
                ['nama' => 'Lantai 1', 'kode' => '1', 'tipe' => 'Lantai'],
                ['nama' => 'Lantai 2', 'kode' => '2', 'tipe' => 'Lantai'],
                ['nama' => 'Basement', 'kode' => 'B', 'tipe' => 'Lantai'],
                ['nama' => 'Diklat', 'kode' => 'D', 'tipe' => 'Lokasi'],
                ['nama' => 'Radiologi', 'kode' => 'RD', 'tipe' => 'Lokasi'],
                ['nama' => 'Poli Gigi', 'kode' => 'PG', 'tipe' => 'Lokasi'],
            ] as $location
        ) {
            MasterLokasi::updateOrCreate(['tipe' => $location['tipe'], 'kode' => $location['kode']], $location);
        }

        // 1. User Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@gmail.com',
                'password' => Hash::make('abcde'),
                'role'     => 'superadmin',
                'phone'    => '+62 812 0000 1111',
                'avatar'   => null,
            ]
        );

        $gedung = MasterLokasi::where('tipe', 'Gedung')->where('kode', 'GB')->first();
        $lantai = MasterLokasi::where('tipe', 'Lantai')->where('kode', 'B')->first();
        $lokasi = MasterLokasi::where('tipe', 'Lokasi')->where('kode', 'D')->first();

        $aset = AsetInventaris::updateOrCreate(
            ['no_inventaris' => '2025/GB/B/D/D/1'],
            [
                'tahun_perolehan' => 2025,
                'gedung_id' => $gedung?->id,
                'lantai_id' => $lantai?->id,
                'lokasi_id' => $lokasi?->id,
                'nama_barang' => 'PC (CPU Set)',
                'spesifikasi' => 'Intel Core i5, RAM 8GB, SSD 256GB',
                'lantai' => 'Lantai B',
                'lokasi' => 'Diklat',
                'nama_user' => 'Ibu Neneng',
                'kelengkapan' => 'Alat dan Kabel Listrik',
                'jumlah' => 1,
                'tanggal_entry' => '2026-07-03',
                'keterangan' => 'Windows 10',
                'status' => 'Aktif',
            ]
        );

        $aset->maintenanceLogs()->updateOrCreate(
            ['tanggal' => '2026-08-15', 'gejala_masalah' => 'Komputer lambat saat membuka aplikasi'],
            [
                'pelapor' => 'Ibu Neneng',
                'penyebab' => 'Penyimpanan hampir penuh',
                'tindakan_penanganan' => 'Pembersihan file sementara dan pemeriksaan sistem',
                'teknisi' => 'Tim IT',
                'status' => 'Selesai',
            ]
        );

        // 2. User Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('abcde'),
                'role'     => 'admin',
                'phone'    => '+62 812 2222 3333',
                'avatar'   => null,
            ]
        );
    }
}

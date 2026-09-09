@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Dashboard Utama" />

<div class="space-y-6">

    <!-- 1. GRID KARTU METRIK STATISTIK -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
        
        <!-- Total Barang Master -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Master Barang</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalMasterBarang }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Total item barang terdaftar</p>
        </div>

        <!-- Pengajuan Menunggu (Pending) -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pengajuan Pending</span>
                    <h4 class="mt-1 text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pengajuanPending }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Butuh persetujuan disposisi</p>
        </div>

        <!-- Stok Menipis -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Stok Menipis</span>
                    <h4 class="mt-1 text-2xl font-bold text-orange-500">{{ $stokMenipisCount }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500 dark:bg-orange-500/15">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Mendekati batas minimal</p>
        </div>

        <!-- Stok Habis -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Stok Habis</span>
                    <h4 class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ $stokHabisCount }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Harus segera diisi/diajukan</p>
        </div>

    </div>

    <!-- 2. GRID KONTEN UTAMA (Tabel Peringatan Stok & Pengajuan Terbaru) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- TABEL 1: Peringatan Stok Menipis / Habis -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Peringatan Stok Minimal</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang yang butuh restock segera</p>
                </div>
                <a href="{{ route('stok-minimal.index') }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50 text-xs font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-800/30">
                            <th class="p-2.5">Nama Barang</th>
                            <th class="p-2.5">Stok Fisik</th>
                            <th class="p-2.5">Batas Minimal</th>
                            <th class="p-2.5">Status Alert</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        @forelse($alertBarangs as $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="p-2.5 font-medium text-gray-800 dark:text-white/90">{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td class="p-2.5 font-bold text-gray-800 dark:text-white/90">{{ $item->jumlah_stock }}</td>
                            <td class="p-2.5 text-gray-500 dark:text-gray-400">{{ $item->minimal }}</td>
                            <td class="p-2.5">
                                @if($item->status_alert === 'habis')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400 uppercase">Habis</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400 uppercase">Menipis</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-xs text-gray-400">Semua stok barang dalam kondisi aman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: Pengajuan Barang Terbaru -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Pengajuan Barang Terbaru</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Aktivitas pengajuan terkini</p>
                </div>
                <a href="{{ route('pengajuan.index') }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50 text-xs font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-800/30">
                            <th class="p-2.5">Barang</th>
                            <th class="p-2.5">Divisi</th>
                            <th class="p-2.5">Volume</th>
                            <th class="p-2.5">Disposisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        @forelse($pengajuanTerbaru as $p)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="p-2.5 font-medium text-gray-800 dark:text-white/90">{{ $p->barang->nama_barang ?? '-' }}</td>
                            <td class="p-2.5 text-gray-500 dark:text-gray-400">{{ $p->permintaan }}</td>
                            <td class="p-2.5 text-gray-800 dark:text-white/90">{{ $p->volume }} {{ $p->barang->satuan ?? '' }}</td>
                            <td class="p-2.5">
                                @if($p->status_disposisi === 'acc')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400 uppercase">ACC</span>
                                @elseif($p->status_disposisi === 'pending')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400 uppercase">PENDING</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400 uppercase">REJECTED</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-xs text-gray-400">Belum ada pengajuan barang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 3. TABEL BARANG KELUAR TERBARU -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Riwayat Pengeluaran Stok Terkini</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Catatan barang keluar dari lemari</p>
            </div>
            <a href="{{ route('barang-keluar.index') }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">Lihat Semua →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50 text-xs font-medium text-gray-500 dark:border-gray-800 dark:bg-gray-800/30">
                        <th class="p-2.5">Tanggal</th>
                        <th class="p-2.5">Nama Barang</th>
                        <th class="p-2.5">Jumlah Keluar</th>
                        <th class="p-2.5">Pelapor</th>
                        <th class="p-2.5">Lokasi Tujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    @forelse($barangKeluarTerbaru as $bk)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                        <td class="p-2.5 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($bk->tanggal_keluar)->format('d/m/Y') }}</td>
                        <td class="p-2.5 font-medium text-gray-800 dark:text-white/90">{{ $bk->barang->nama_barang ?? '-' }}</td>
                        <td class="p-2.5 font-bold text-red-500">-{{ $bk->jumlah }} {{ $bk->barang->satuan ?? '' }}</td>
                        <td class="p-2.5 text-gray-800 dark:text-white/90">{{ $bk->pelapor }}</td>
                        <td class="p-2.5 text-gray-500 dark:text-gray-400">{{ $bk->lokasi }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-xs text-gray-400">Belum ada aktivitas barang keluar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Catatan Barang Keluar" />

<!-- Container Card Utama -->
<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- Header Form -->
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Form Edit Barang Keluar
            </h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Perbarui rincian riwayat pengambilan barang dari lemari/stok.
            </p>
        </div>
        <a href="{{ route('barang-keluar.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            ← Kembali
        </a>
    </div>

    <!-- Form Edit Data -->
    <form action="{{ route('barang-keluar.update', $barangKeluar->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- 1. Pilih Barang (FK -> master_barang) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="barang_id" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                        @foreach($masterBarang as $b)
                        <option value="{{ $b->id }}" {{ old('barang_id', $barangKeluar->barang_id) == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_barang }} (Sisa Stok: {{ $b->jumlah_stock }} {{ $b->satuan }})
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- 2. Tanggal Keluar -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tanggal Keluar <span class="text-red-500">*</span>
                </label>
                <x-form.date-picker
                    name="tanggal_keluar"
                    value="{{ old('tanggal_keluar', $barangKeluar->tanggal_keluar) }}"
                    required />
            </div>

            <!-- 3. Jumlah Barang Keluar -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Jumlah Barang Keluar <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    value="{{ old('jumlah', $barangKeluar->jumlah) }}"
                    placeholder="Contoh: 2"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 4. Kondisi Barang Lama -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kondisi Barang Lama
                </label>
                <input
                    type="text"
                    name="kondisi_barang_lama"
                    value="{{ old('kondisi_barang_lama', $barangKeluar->kondisi_barang_lama) }}"
                    placeholder="Contoh: Rusak Ringan / Bekas"
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 5. Pelapor / Pengambil -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Pelapor / Pengambil <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="pelapor"
                    value="{{ old('pelapor', $barangKeluar->pelapor) }}"
                    placeholder="Contoh: Andi (IT Support)"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 6. Lokasi Penempatan -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Lokasi Penempatan / Tujuan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="lokasi"
                    value="{{ old('lokasi', $barangKeluar->lokasi) }}"
                    placeholder="Contoh: Ruang Meeting Lt. 2"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 7. Status (Done / Not Yet) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status Pengambilan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="status" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                        <option value="done" {{ old('status', $barangKeluar->status) == 'done' ? 'selected' : '' }}>DONE (Mengurangi Stok Fisik)</option>
                        <option value="not_yet" {{ old('status', $barangKeluar->status) == 'not_yet' ? 'selected' : '' }}>NOT YET (Draf / Belum Diambil)</option>
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- 8. Keterangan -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan
                </label>
                <textarea
                    name="keterangan"
                    rows="3"
                    placeholder="Catatan tambahan..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('keterangan', $barangKeluar->keterangan) }}</textarea>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('barang-keluar.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700">
                Update Catatan
            </button>
        </div>
    </form>

</div>
@endsection
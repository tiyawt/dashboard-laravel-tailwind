@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Catat Barang Keluar" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Form Barang Keluar Lemari
            </h3>
        </div>
        <a href="{{ route('barang-keluar.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('barang-keluar.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- Barang -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Pilih Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="barang_id" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="" disabled selected>Pilih Barang dari Master</option>
                        @foreach($masterBarang as $b)
                        <option value="{{ $b->id }}"
                            {{ $b->jumlah_stock <= 0 ? 'disabled' : '' }}
                            {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_barang }} — Sisa Stok: {{ $b->jumlah_stock }} {{ $b->satuan }}
                            {{ $b->jumlah_stock <= 0 ? '(STOK HABIS)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>

                <!-- Tampilkan Error Jika Kena Validasi -->
                @error('jumlah')
                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Keluar -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tanggal Keluar <span class="text-red-500">*</span>
                </label>
                <x-form.date-picker
                    name="tanggal_keluar"
                    value="{{ date('Y-m-d') }}"
                    required />
            </div>

            <!-- Jumlah -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Jumlah Barang Keluar <span class="text-red-500">*</span>
                </label>
                <input type="number" name="jumlah" min="1" placeholder="Contoh: 2" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Kondisi Barang Lama (Auto Suggest / Input Optional) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kondisi Barang Lama
                </label>
                <input type="text" name="kondisi_barang_lama" placeholder="Contoh: Rusak Ringan / Bekas" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Pelapor -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Pelapor / Pengambil <span class="text-red-500">*</span>
                </label>
                <input type="text" name="pelapor" placeholder="Contoh: Andi (IT Support)" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Lokasi -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Lokasi Penempatan / Tujuan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="lokasi" placeholder="Contoh: Ruang Meeting Lt. 2" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Status (Done / Not Yet) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status Pengambilan <span class="text-red-500">*</span>
                </label>
                <select name="status" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="done" selected>DONE (Mengurangi Stok Fisik)</option>
                    <option value="not_yet">NOT YET (Draf / Belum Diambil)</option>
                </select>
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('barang-keluar.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Simpan Transaksi
            </button>
        </div>
    </form>

</div>
@endsection
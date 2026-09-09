@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Barang & Batas Stok Minimal" />

<!-- Container Card Utama (Sesuai Style TailAdmin) -->
<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- Header Form -->
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Form Edit Master Barang & Stok Minimal
            </h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Perbarui data barang master atau ubah batas minimal stoknya.
            </p>
        </div>
        <a href="{{ route('stok-minimal.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            ← Kembali
        </a>
    </div>

    <!-- Form Edit Data -->
    <form action="{{ route('stok-minimal.update', $minimalStock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- 1. Nama Barang (Master Barang) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="nama_barang"
                    value="{{ old('nama_barang', $minimalStock->barang->nama_barang ?? '') }}"
                    placeholder="Contoh: Kertas A4 80gr"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 2. Satuan (Master Barang) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Satuan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="satuan"
                    value="{{ old('satuan', $minimalStock->barang->satuan ?? '') }}"
                    placeholder="Contoh: Pcs, Unit, Rim, Box"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 3. Batas Minimal Stok -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Batas Minimal Stok <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="minimal"
                    min="0"
                    value="{{ old('minimal', $minimalStock->minimal) }}"
                    placeholder="Contoh: 10"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 4. Rentang Waktu -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Rentang Waktu
                </label>
                <input
                    type="text"
                    name="rentang_waktu"
                    value="{{ old('rentang_waktu', $minimalStock->rentang_waktu) }}"
                    placeholder="Contoh: Bulanan / Mingguan"
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 5. Keterangan -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan
                </label>
                <textarea
                    name="keterangan"
                    rows="3"
                    placeholder="Tambahkan catatan khusus..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('keterangan', $minimalStock->keterangan) }}</textarea>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('stok-minimal.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700">
                Update Barang
            </button>
        </div>
    </form>

</div>
@endsection
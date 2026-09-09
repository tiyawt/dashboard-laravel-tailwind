@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Pengajuan Barang" />

<!-- Container Card Utama -->
<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- Header Form -->
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Form Edit Pengajuan Barang
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui informasi pengajuan barang berikut secara benar.</p>
        </div>
        <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            ← Kembali
        </a>
    </div>

    <!-- Form Edit Data -->
    <form action="{{ route('pengajuan.update', $pengajuan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- 1. Nama Barang (FK -> master_barang) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="barang_id" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                        @foreach($masterBarang as $b)
                        <option value="{{ $b->id }}" {{ old('barang_id', $pengajuan->barang_id) == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_barang }} (Satuan: {{ $b->satuan }})
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

            <!-- 2. Tanggal Pengajuan -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tanggal Pengajuan <span class="text-red-500">*</span>
                </label>
                <x-form.date-picker
                    name="tanggal_pengajuan"
                    value="{{ date('Y-m-d') }}"
                    required />
            </div>

            <!-- 3. Volume / Jumlah -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Volume <span class="text-red-500">*</span>
                </label>
                <input type="number" name="volume" min="1" value="{{ old('volume', $pengajuan->volume) }}" placeholder="Masukkan jumlah barang" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 4. Harga Per Unit -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Harga / Unit (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="harga_per_unit" min="0" value="{{ old('harga_per_unit', $pengajuan->harga_per_unit) }}" placeholder="Contoh: 20000" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 5. Permintaan (Divisi) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Permintaan (Divisi/Unit) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="permintaan" value="{{ old('permintaan', $pengajuan->permintaan) }}" placeholder="Contoh: HR, IT, Finance" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 6. Status Barang (Baru / Bekas) -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status Kondisi Barang <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="status_barang" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                        <option value="baru" {{ old('status_barang', $pengajuan->status_barang) == 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="bekas" {{ old('status_barang', $pengajuan->status_barang) == 'bekas' ? 'selected' : '' }}>Bekas</option>
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- 7. Status Disposisi -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Status Disposisi <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="status_disposisi" required class="h-[46px] w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                        <option value="pending" {{ old('status_disposisi', $pengajuan->status_disposisi) == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="acc" {{ old('status_disposisi', $pengajuan->status_disposisi) == 'acc' ? 'selected' : '' }}>ACC</option>
                        <option value="rejected" {{ old('status_disposisi', $pengajuan->status_disposisi) == 'rejected' ? 'selected' : '' }}>REJECTED</option>
                    </select>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- 8. Link SPB & Invoice -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Link SPB & Invoice (Opsional)
                </label>
                <input type="text" name="link_spb_invoice" value="{{ old('link_spb_invoice', $pengajuan->link_spb_invoice) }}" placeholder="https://drive.google.com/..." class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800" />
            </div>

            <!-- 9. Keterangan -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan khusus pengajuan..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('keterangan', $pengajuan->keterangan) }}</textarea>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('pengajuan.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700">
                Update Pengajuan
            </button>
        </div>
    </form>

</div>
@endsection
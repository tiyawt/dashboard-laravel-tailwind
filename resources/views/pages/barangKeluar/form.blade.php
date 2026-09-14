@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="{{ $barangKeluar->exists ? 'Edit Catatan Barang Keluar' : 'Catat Barang Keluar' }}" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Form {{ $barangKeluar->exists ? 'Edit Barang Keluar' : 'Barang Keluar Lemari' }}</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $barangKeluar->exists ? 'Perbarui rincian riwayat pengambilan barang dari lemari/stok.' : 'Catat transaksi pengeluaran barang dari lemari/stok.' }}</p>
        </div>
        <a href="{{ route('barang-keluar.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">← Kembali</a>
    </div>

    <form action="{{ $barangKeluar->exists ? route('barang-keluar.update', $barangKeluar->id) : route('barang-keluar.store') }}" method="POST">
        @csrf
        @if($barangKeluar->exists)
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Barang <span class="text-red-500">*</span></label>
                <select name="barang_id" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="" disabled {{ old('barang_id', $barangKeluar->barang_id) ? '' : 'selected' }}>Pilih Barang dari Master</option>
                    @foreach($masterBarang as $barang)
                    <option value="{{ $barang->id }}" {{ old('barang_id', $barangKeluar->barang_id) == $barang->id ? 'selected' : '' }} {{ !$barangKeluar->exists && $barang->jumlah_stock <= 0 ? 'disabled' : '' }}>{{ $barang->nama_barang }} — Sisa Stok: {{ $barang->jumlah_stock }} {{ $barang->satuan }}{{ $barang->jumlah_stock <= 0 ? ' (STOK HABIS)' : '' }}</option>
                    @endforeach
                </select>
                @error('jumlah')<p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Keluar <span class="text-red-500">*</span></label>
                <x-form.date-picker name="tanggal_keluar" value="{{ old('tanggal_keluar', $barangKeluar->tanggal_keluar ?: date('Y-m-d')) }}" required />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Barang Keluar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $barangKeluar->jumlah) }}" placeholder="Contoh: 2" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi Barang Lama</label>
                <input type="text" name="kondisi_barang_lama" value="{{ old('kondisi_barang_lama', $barangKeluar->kondisi_barang_lama) }}" placeholder="Contoh: Rusak Ringan / Bekas" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pelapor / Pengambil <span class="text-red-500">*</span></label>
                <input type="text" name="pelapor" value="{{ old('pelapor', $barangKeluar->pelapor) }}" placeholder="Contoh: Andi (IT Support)" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Penempatan / Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $barangKeluar->lokasi) }}" placeholder="Contoh: Ruang Meeting Lt. 2" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            @if(auth()->user()?->role === 'admin')
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pengambilan</label>
                <div class="flex h-[46px] w-full items-center rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">NOT YET (Menunggu persetujuan IT)</div>
            </div>
            @else
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pengambilan <span class="text-red-500">*</span></label>
                <select name="status" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="done" {{ old('status', $barangKeluar->status ?: 'done') === 'done' ? 'selected' : '' }}>DONE (Mengurangi Stok Fisik)</option>
                    <option value="not_yet" {{ old('status', $barangKeluar->status) === 'not_yet' ? 'selected' : '' }}>NOT YET (Draf / Belum Diambil)</option>
                </select>
            </div>
            @endif

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('keterangan', $barangKeluar->keterangan) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('barang-keluar.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">{{ $barangKeluar->exists ? 'Update Catatan' : 'Simpan Transaksi' }}</button>
        </div>
    </form>
</div>
@endsection
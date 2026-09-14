@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="{{ $pengajuan->exists ? 'Edit Pengajuan Barang' : 'Tambah Pengajuan Barang' }}" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Form {{ $pengajuan->exists ? 'Edit' : 'Tambah' }} Pengajuan Barang</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $pengajuan->exists ? 'Perbarui informasi pengajuan barang berikut secara benar.' : 'Isi semua informasi pengajuan barang secara lengkap.' }}</p>
        </div>
        <a href="{{ route('pengajuan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">← Kembali</a>
    </div>

    <form action="{{ $pengajuan->exists ? route('pengajuan.update', $pengajuan->id) : route('pengajuan.store') }}" method="POST">
        @csrf
        @if($pengajuan->exists)
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Barang <span class="text-red-500">*</span></label>
                <select name="barang_id" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="" disabled {{ old('barang_id', $pengajuan->barang_id) ? '' : 'selected' }}>Pilih Barang dari Master</option>
                    @foreach($masterBarang as $barang)
                    <option value="{{ $barang->id }}" {{ old('barang_id', $pengajuan->barang_id) == $barang->id ? 'selected' : '' }}>{{ $barang->nama_barang }} (Satuan: {{ $barang->satuan }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                <x-form.date-picker name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', $pengajuan->tanggal_pengajuan ?: date('Y-m-d')) }}" required />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Volume <span class="text-red-500">*</span></label>
                <input type="number" name="volume" min="1" value="{{ old('volume', $pengajuan->volume) }}" placeholder="Masukkan jumlah barang" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Harga / Unit (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="harga_per_unit" min="0" value="{{ old('harga_per_unit', $pengajuan->harga_per_unit) }}" placeholder="Contoh: 20000" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Permintaan (Divisi/Unit) <span class="text-red-500">*</span></label>
                <input type="text" name="permintaan" value="{{ old('permintaan', $pengajuan->permintaan) }}" placeholder="Contoh: HR, IT, Finance" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Kondisi Barang <span class="text-red-500">*</span></label>
                <select name="status_barang" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach(['baru' => 'Baru', 'bekas' => 'Bekas'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status_barang', $pengajuan->status_barang ?: 'baru') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Disposisi <span class="text-red-500">*</span></label>
                <select name="status_disposisi" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach(['pending' => 'PENDING', 'acc' => 'ACC', 'rejected' => 'REJECTED'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status_disposisi', $pengajuan->status_disposisi ?: 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Link SPB & Invoice (Opsional)</label>
                <input type="text" name="link_spb_invoice" value="{{ old('link_spb_invoice', $pengajuan->link_spb_invoice) }}" placeholder="https://drive.google.com/..." class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan khusus pengajuan..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('keterangan', $pengajuan->keterangan) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('pengajuan.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700">{{ $pengajuan->exists ? 'Update Pengajuan' : 'Simpan Pengajuan' }}</button>
        </div>
    </form>
</div>
@endsection
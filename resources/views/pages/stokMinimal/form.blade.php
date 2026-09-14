@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="{{ $minimalStock->exists ? 'Edit Barang & Batas Stok Minimal' : 'Tambah Barang Master & Minimal Stok' }}" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Form {{ $minimalStock->exists ? 'Edit' : 'Tambah' }} Master Barang & Stok Minimal</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $minimalStock->exists ? 'Perbarui data barang master atau ubah batas minimal stoknya.' : 'Tambahkan barang master dan batas minimal stoknya.' }}</p>
        </div>
        <a href="{{ route('stok-minimal.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">← Kembali</a>
    </div>

    <form action="{{ $minimalStock->exists ? route('stok-minimal.update', $minimalStock->id) : route('stok-minimal.store') }}" method="POST" x-data="{ itemName: @js(old('nama_barang', $minimalStock->barang->nama_barang ?? '')), existingItemNames: @js($existingItemNames), get isDuplicate() { const name = this.itemName.trim().toLocaleLowerCase(); return name !== '' && this.existingItemNames.some((existingName) => existingName.toLocaleLowerCase() === name); } }">
        @csrf
        @if($minimalStock->exists)
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" x-model="itemName" value="{{ old('nama_barang', $minimalStock->barang->nama_barang ?? '') }}" placeholder="Contoh: Kertas A4 80gr" required :aria-invalid="isDuplicate" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" :class="isDuplicate ? 'border-red-500 focus:border-red-500' : ''" />
                <p x-cloak x-show="isDuplicate" class="mt-1.5 text-sm text-red-500">Nama barang sudah terdaftar. Barang yang sama tidak dapat ditambahkan lagi.</p>
                @error('nama_barang')<p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" value="{{ old('satuan', $minimalStock->barang->satuan ?? '') }}" placeholder="Contoh: Pcs, Unit, Rim, Box" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Batas Minimal Stok <span class="text-red-500">*</span></label>
                <input type="number" name="minimal" min="0" value="{{ old('minimal', $minimalStock->minimal) }}" placeholder="Contoh: 10" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rentang Waktu</label>
                <input type="text" name="rentang_waktu" value="{{ old('rentang_waktu', $minimalStock->rentang_waktu) }}" placeholder="Contoh: Bulanan / Mingguan" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan khusus..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('keterangan', $minimalStock->keterangan) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('stok-minimal.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm dark:border-gray-700 dark:text-gray-300">Batal</a>
            <button type="submit" :disabled="isDuplicate" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">{{ $minimalStock->exists ? 'Update Barang' : 'Simpan Barang' }}</button>
        </div>
    </form>
</div>
@endsection
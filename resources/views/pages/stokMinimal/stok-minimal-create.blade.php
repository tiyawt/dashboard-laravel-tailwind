@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah Barang Master & Minimal Stok" />

<div class="w-full max-w-full rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <form
        action="{{ route('stok-minimal.store') }}"
        method="POST"
        x-data="{
            itemName: @js(old('nama_barang', '')),
            existingItemNames: @js($existingItemNames),
            get isDuplicate() {
                const normalizedName = this.itemName.trim().toLocaleLowerCase();
                return normalizedName !== '' && this.existingItemNames.some((name) => name.toLocaleLowerCase() === normalizedName);
            }
        }">
        @csrf
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Barang *</label>
                <input type="text" name="nama_barang" x-model="itemName" placeholder="Contoh: Kertas A4 80gr" required :aria-invalid="isDuplicate" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:text-white" :class="isDuplicate ? 'border-red-500 focus:border-red-500' : ''" />
                <p x-cloak x-show="isDuplicate" class="mt-1.5 text-sm text-red-500">
                    Nama barang sudah terdaftar. Barang yang sama tidak dapat ditambahkan lagi.
                </p>
                @error('nama_barang')
                    <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan *</label>
                <input type="text" name="satuan" placeholder="Contoh: Pcs, Unit, Rim, Box" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:text-white" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Batas Minimal Stok *</label>
                <input type="number" name="minimal" min="0" placeholder="Contoh: 10" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:text-white" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rentang Waktu</label>
                <input type="text" name="rentang_waktu" placeholder="Contoh: Bulanan / Mingguan" class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:text-white" />
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm dark:border-gray-700 dark:text-white"></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t pt-4 dark:border-gray-800">
            <a href="{{ route('stok-minimal.index') }}" class="rounded-lg border px-5 py-2.5 text-sm dark:border-gray-700 dark:text-gray-300">Batal</a>
            <button type="submit" :disabled="isDuplicate" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan Barang</button>
        </div>
    </form>
</div>
@endsection

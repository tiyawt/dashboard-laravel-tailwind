@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Isi Data Penerimaan Barang" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Penerimaan: {{ $penerimaan->pengajuan->barang->nama_barang ?? 'Barang' }}
            </h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                Total Volume Pengajuan: <span class="font-semibold text-blue-600">{{ $penerimaan->pengajuan->volume }} {{ $penerimaan->pengajuan->barang->satuan }}</span>
                | Status Barang: <span class="font-semibold capitalize text-gray-700 dark:text-gray-300">{{ $penerimaan->pengajuan->status_barang }}</span>
            </p>
        </div>
        <a href="{{ route('penerimaan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('penerimaan.update', $penerimaan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- Tanggal Pengambilan -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tanggal Pengambilan <span class="text-red-500">*</span>
                </label>
                <x-form.date-picker
                    name="tanggal_pengambilan"
                    value="{{ date('Y-m-d') }}"
                    required />
            </div>

            <!-- Jumlah Diterima -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Jumlah Diterima <span class="text-red-500">*</span>
                </label>
                <input type="number"
                    name="jumlah_diterima"
                    min="1"
                    max="{{ $penerimaan->pengajuan->volume }}"
                    value="{{ old('jumlah_diterima', $penerimaan->jumlah_diterima) }}"
                    placeholder="Masukkan jumlah barang yang sampai"
                    required
                    class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Penerima -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama Penerima <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penerima" value="{{ old('penerima', $penerimaan->penerima) }}" placeholder="Contoh: Budi (Staff HR)" required class="h-[46px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <!-- Keterangan -->
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Keterangan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Catatan kondisi penerimaan..." class="w-full rounded-lg border border-gray-300 bg-transparent p-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('keterangan', $penerimaan->keterangan) }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('penerimaan.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Simpan Penerimaan
            </button>
        </div>
    </form>

</div>
@endsection
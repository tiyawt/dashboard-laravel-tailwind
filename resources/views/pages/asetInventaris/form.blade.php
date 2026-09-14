@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb
    pageTitle="{!! $aset->exists ? 'Edit Aset & Inventaris' : 'Tambah Aset & Inventaris' !!}" />

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ $aset->exists ? 'Edit Data Aset' : 'Tambah Data Aset' }}
        </h2>

        <a href="{{ route('aset-inventaris.index') }}" 
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            ← Kembali
        </a>
    </div>

    <form
        action="{{ $aset->exists
        ? route('aset-inventaris.update', $aset->id)
        : route('aset-inventaris.store') }}"
        method="POST"
        class="grid grid-cols-1 gap-5 md:grid-cols-2">

        @csrf

        @if($aset->exists)
        @method('PUT')
        @endif

        {{-- No. Inventaris --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                No. Inventaris
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="no_inventaris"
                value="{{ old('no_inventaris', $aset->no_inventaris) }}"
                required
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- Nama Barang --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nama Barang
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="nama_barang"
                value="{{ old('nama_barang', $aset->nama_barang) }}"
                required
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- Spesifikasi --}}
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Spesifikasi
            </label>

            <textarea
                name="spesifikasi"
                rows="3"
                class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('spesifikasi', $aset->spesifikasi) }}</textarea>
        </div>

        {{-- Lantai --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Lantai
            </label>

            <input
                type="text"
                name="lantai"
                value="{{ old('lantai', $aset->lantai) }}"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- Lokasi --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Lokasi
            </label>

            <input
                type="text"
                name="lokasi"
                value="{{ old('lokasi', $aset->lokasi) }}"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- User --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
            </label>

            <input
                type="text"
                name="user"
                value="{{ old('user', $aset->user) }}"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- Jumlah --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Jumlah
                <span class="text-red-500">*</span>
            </label>

            <input
                type="number"
                name="jumlah"
                value="{{ old('jumlah', $aset->jumlah) }}"
                required
                min="1"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
        </div>

        {{-- Tanggal Entry --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Tanggal Entry
                <span class="text-red-500">*</span>
            </label>
            <x-form.date-picker
                name="tanggal_entry"
                value="{{ date('Y-m-d') }}"
                required />
        </div>

        {{-- Kelengkapan --}}
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Kelengkapan
            </label>

            <textarea
                name="kelengkapan"
                rows="3"
                class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('kelengkapan', $aset->kelengkapan) }}</textarea>
        </div>

        {{-- Keterangan --}}
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Keterangan
            </label>

            <textarea
                name="keterangan"
                rows="3"
                class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('keterangan', $aset->keterangan) }}</textarea>
        </div>

        {{-- Tombol --}}
        <div class="md:col-span-2 flex justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">

            <a
                href="{{ route('aset-inventaris.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                Batal
            </a>

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Simpan
            </button>

        </div>

    </form>

</div>

@endsection
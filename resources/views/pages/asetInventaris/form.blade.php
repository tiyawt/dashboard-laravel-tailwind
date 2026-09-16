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
        class="grid grid-cols-1 gap-5 md:grid-cols-2"
        @if($aset->exists)
        x-data="{ status: @js(old('status', $aset->status)) }"
        @endif>

        @csrf

        @if($aset->exists)
        @method('PUT')
        @endif

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Perolehan <span class="text-red-500">*</span></label>
            <select name="tahun_perolehan" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">Pilih Tahun</option>
                @foreach(range(now()->year + 1, 1988) as $year)
                <option value="{{ $year }}" @selected((string) old('tahun_perolehan', $aset->tahun_perolehan) === (string) $year)>{{ $year }}</option>
                @endforeach
            </select>
            @error('tahun_perolehan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
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

        {{-- Gedung --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gedung <span class="text-red-500">*</span></label>
            <select name="gedung_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">Pilih Gedung</option>
                @foreach($locations['gedung'] as $location)<option value="{{ $location->id }}" @selected(old('gedung_id', $aset->gedung_id) == $location->id)>{{ $location->nama }} ({{ $location->kode }})</option>@endforeach
            </select>
        </div>

        {{-- Lantai --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lantai <span class="text-red-500">*</span></label>
            <select name="lantai_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">Pilih Lantai</option>
                @foreach($locations['lantai'] as $location)<option value="{{ $location->id }}" @selected(old('lantai_id', $aset->lantai_id) == $location->id)>{{ $location->nama }} ({{ $location->kode }})</option>@endforeach
            </select>
        </div>

        {{-- Lokasi --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Divisi <span class="text-red-500">*</span></label>
            <select name="lokasi_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">Pilih Lokasi</option>
                @foreach($locations['lokasi'] as $location)<option value="{{ $location->id }}" @selected(old('lokasi_id', $aset->lokasi_id) == $location->id)>{{ $location->nama }} ({{ $location->kode }})</option>@endforeach
            </select>
        </div>

        {{-- User --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                User
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="user"
                value="{{ old('user', $aset->user) }}"
                required
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            @error('user')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
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
                value="{{ old('tanggal_entry', $aset->tanggal_entry?->format('Y-m-d') ?? date('Y-m-d')) }}"
                required />
        </div>

        @if($aset->exists)
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Aset</label>
            <select name="status" x-model="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>
        <div x-show="status === 'Nonaktif'" x-cloak>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Nonaktif <span class="text-red-500">*</span></label>
            <x-form.date-picker
                name="tanggal_nonaktif"
                value="{{ old('tanggal_nonaktif', $aset->tanggal_nonaktif?->format('Y-m-d')) }}"
                placeholder="Pilih tanggal nonaktif" />
            @error('tanggal_nonaktif')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2" x-show="status === 'Nonaktif'" x-cloak>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Alasan Nonaktif <span class="text-red-500">*</span>
            </label>

            <textarea
                name="alasan_nonaktif"
                rows="2"
                class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('alasan_nonaktif', $aset->alasan_nonaktif) }}</textarea>

            @error('alasan_nonaktif')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        @endif

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
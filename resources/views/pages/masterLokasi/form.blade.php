@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="{{ $location->exists ? 'Edit Master Lokasi' : 'Tambah Master Lokasi' }}" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $location->exists ? 'Edit Master Lokasi' : 'Tambah Master Lokasi' }}</h2>
        <a href="{{ route('master-lokasi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">← Kembali</a>
        
    </div>

    <form action="{{ $location->exists ? route('master-lokasi.update', $location->id) : route('master-lokasi.store') }}" method="POST">
        @csrf
        @if($location->exists)
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lokasi <span class="text-red-500">*</span></label>
                <input name="nama" value="{{ old('nama', $location->nama) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode <span class="text-red-500">*</span></label>
                <input name="kode" value="{{ old('kode', $location->kode) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe <span class="text-red-500">*</span></label>
                <select name="tipe" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    <option value="">Pilih Tipe</option>
                    @foreach(['Gedung', 'Lantai', 'Divisi'] as $type)
                    <option value="{{ $type }}" @selected(old('tipe', $location->tipe === 'Lokasi' ? 'Divisi' : $location->tipe) === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
            <a href="{{ route('master-lokasi.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm dark:border-gray-700 dark:text-gray-300">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">{{ $location->exists ? 'Simpan Perubahan' : 'Tambah Lokasi' }}</button>
        </div>
    </form>
</div>
@endsection
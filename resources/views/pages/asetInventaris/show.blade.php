@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Detail Aset & Inventaris" />

@if(session('success'))<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>@endif
<div x-data="{ maintenanceModalOpen: {{ $errors->any() ? 'true' : 'false' }}, maintenancePage: 1, maintenancePerPage: 10, maintenanceLogs: @js($aset->maintenanceLogs->values()), get maintenancePageCount() { return Math.max(1, Math.ceil(this.maintenanceLogs.length / this.maintenancePerPage)); }, get maintenanceLogsPage() { const start = (this.maintenancePage - 1) * this.maintenancePerPage; return this.maintenanceLogs.slice(start, start + this.maintenancePerPage); }, formatMaintenanceDate(value) { return value ? new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '-'; } }" class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $aset->nama_barang }}</h2>
            <p class="text-sm text-gray-500">{{ $aset->no_inventaris }}</p>
        </div>
        <a href="{{ route('aset-inventaris.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            ← Kembali
        </a>
    </div>
    <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white">A. Informasi Umum Barang</h3>
        <dl class="grid gap-4 md:grid-cols-2">@foreach(['No. Inventaris'=>'no_inventaris','Nama Barang'=>'nama_barang','Spesifikasi'=>'spesifikasi','Lantai'=>'lantai','Lokasi'=>'lokasi','User'=>'user','Kelengkapan'=>'kelengkapan','Jumlah'=>'jumlah','Tanggal Entry'=>'tanggal_entry','Keterangan'=>'keterangan'] as $label => $field)<div class="border-b border-gray-100 pb-3 dark:border-gray-800">
                <dt class="text-sm tracking-wide text-gray-500">{{ $label }}</dt>
                <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $field === 'tanggal_entry' ? $aset->$field?->translatedFormat('j F Y') : ($field === 'jumlah' ? $aset->$field . ' Unit' : ($aset->$field ?: '-')) }}</dd>
            </div>@endforeach</dl>
    </section>
    <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">B. Histori Pemeliharaan (Maintenance Log)</h3><button type="button" @click="maintenanceModalOpen = true" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">+ Tambah Catatan Maintenance</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[950px] w-full text-start">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">@foreach(['Tanggal','Pelapor','Gejala / Masalah','Penyebab','Tindakan / Penanganan','Teknisi','Status'] as $heading)<th class="px-4 py-3 text-start text-sm font-semibold text-gray-500">{{ $heading }}</th>@endforeach</tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    <template x-if="maintenanceLogs.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada catatan maintenance.</td>
                        </tr>
                    </template>
                    <template x-for="log in maintenanceLogsPage" :key="log.id">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" x-text="formatMaintenanceDate(log.tanggal)"></td>
                            <td class="px-4 py-3 text-sm" x-text="log.pelapor"></td>
                            <td class="px-4 py-3 text-sm" x-text="log.gejala_masalah"></td>
                            <td class="px-4 py-3 text-sm" x-text="log.penyebab || '-' "></td>
                            <td class="px-4 py-3 text-sm" x-text="log.tindakan_penanganan || '-' "></td>
                            <td class="px-4 py-3 text-sm" x-text="log.teknisi || '-' "></td>
                            <td class="px-4 py-3 text-sm"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600" x-text="log.status"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="maintenancePageCount > 1" class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-800">
            <button type="button" @click="maintenancePage = Math.max(1, maintenancePage - 1)" :disabled="maintenancePage === 1" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300">Sebelumnya</button>
            <span class="text-xs text-gray-500" x-text="`Halaman ${maintenancePage} dari ${maintenancePageCount}`"></span>
            <button type="button" @click="maintenancePage = Math.min(maintenancePageCount, maintenancePage + 1)" :disabled="maintenancePage === maintenancePageCount" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300">Berikutnya</button>
        </div>
    </section>
    <div x-show="maintenanceModalOpen" x-cloak class="fixed inset-0 z-[999999] flex h-full items-start justify-center overflow-y-auto overscroll-contain bg-gray-900/60 p-4 sm:items-center" @keydown.escape.window="maintenanceModalOpen = false">
        <div class="my-2 w-full max-w-xl rounded-2xl bg-white shadow-xl sm:my-4 dark:bg-gray-900" @click.outside="maintenanceModalOpen = false">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tambah Catatan Maintenance</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $aset->no_inventaris }}</p>
                </div>
                <button type="button" @click="maintenanceModalOpen = false" class="text-2xl leading-none text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Tutup">&times;</button>
            </div>
            <form action="{{ route('aset-inventaris.maintenance.store', $aset->id) }}" method="POST" class="grid gap-4 p-5 md:grid-cols-2">
                @csrf
                @foreach([['tanggal','Tanggal','date'],['pelapor','Pelapor','text'],['teknisi','Teknisi','text']] as [$name,$label,$type])
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $name === 'tanggal' ? date('Y-m-d') : '') }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    @error($name)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                @endforeach
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option {{ old('status') === 'Open' ? 'selected' : '' }}>Open</option>
                        <option {{ old('status') === 'Dalam Penanganan' ? 'selected' : '' }}>Dalam Penanganan</option>
                        <option {{ old('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                @foreach([['gejala_masalah','Gejala / Masalah'],['penyebab','Penyebab'],['tindakan_penanganan','Tindakan / Penanganan']] as [$name,$label])
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                    <textarea name="{{ $name }}" rows="2" {{ $name === 'gejala_masalah' ? 'required' : '' }} class="w-full rounded-lg border border-gray-300 bg-transparent p-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old($name) }}</textarea>
                    @error($name)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                @endforeach
                <div class="md:col-span-2 flex justify-end gap-3 border-t border-gray-200 pt-4 dark:border-gray-800">
                    <button type="button" @click="maintenanceModalOpen = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
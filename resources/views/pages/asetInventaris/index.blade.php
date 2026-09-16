@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Aset & Inventaris" />

@if(session('success'))
<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>
@endif

<div x-data="window.assetInventoryPage(
        @js($aset->getCollection()->values()),
        {
            scanUrl: @js(route('aset-inventaris.scan')),
            baseUrl: @js(url('/aset-inventaris'))
        }
    )" class="w-full rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Aset & Inventaris</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-2">Cari nomor inventaris untuk melihat detail dan histori pemeliharaan.</p>
            <a href="{{ route('aset-inventaris.export') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 h-[42px] py-2.5 text-sm font-medium text-white hover:bg-green-700">Download Excel</a>

        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('aset-inventaris.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">+ Tambah Aset</a>
        </div>
    </div>

    <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
        <form action="{{ route('aset-inventaris.index') }}" method="GET" class="flex min-w-0 flex-1 gap-2">
            <div class="relative min-w-0 flex-1">
                <input x-ref="searchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor, nama, lokasi, atau user..." class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pe-11 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />

                <button
                    type="button"
                    @click="openScanner()"
                    class="absolute end-1 top-1 flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800" title="Scan barcode" aria-label="Scan barcode">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7V5a1 1 0 0 1 1-1h2M17 4h2a1 1 0 0 1 1 1v2M20 17v2a1 1 0 0 1-1 1h-2M7 20H5a1 1 0 0 1-1-1v-2M7 8v8M10 8v8M14 8v8M17 8v8" />
                    </svg>
                </button>
            </div>
            <button type="submit" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cari</button>
        </form>
        <button type="button" @click="printBarcodes()" :disabled="!selectedIds.length || printInProgress" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
            <span x-text="printInProgress ? 'Menyiapkan...' : `Cetak Barcode${selectedIds.length ? ` (${selectedIds.length})` : ''}`"></span>
        </button>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-[980px] w-full text-start">
            <thead>
                <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                    <th class="w-12 px-4 py-3 text-start">
                        <input type="checkbox" @change="toggleAll($event.target.checked)" :checked="allSelected" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" aria-label="Pilih semua aset di halaman ini">
                    </th>
                    @foreach(['No. Inventaris','Nama Barang','Spesifikasi','Lantai','Lokasi','User','Keterangan','Status','Aksi'] as $heading)
                    <th class="px-4 py-3 text-start text-sm font-semibold text-gray-500">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($aset as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-4">
                        <input type="checkbox" value="{{ $item->id }}" x-model.number="selectedIds" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" aria-label="Pilih {{ $item->no_inventaris }}">
                    </td>
                    <td class="px-4 py-4 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $item->no_inventaris }}</td>
                    <td class="px-4 py-4 text-sm text-gray-800 dark:text-white/90">{{ $item->nama_barang }}</td>
                    <td class="max-w-[220px] truncate px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->spesifikasi ?: '-' }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->lantai ?: '-' }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->lokasi ?: '-' }}</td>
                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->user ?: '-' }}</td>
                    <td class="max-w-[180px] truncate px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->keterangan ?: '-' }}</td>
                    <td class="px-4 py-4 text-sm">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $item->status === 'Aktif' ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">{{ $item->status }}</span>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('aset-inventaris.show', $item->id) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800" title="Detail"><span aria-hidden="true">&#128065;</span><span>Detail</span></a>
                            <a href="{{ route('aset-inventaris.edit', $item->id) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-800" title="Edit"><span aria-hidden="true">&#9998;</span><span>Edit</span></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data aset inventaris.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">{{ $aset->links() }}</div>

    <div x-show="scannerOpen" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center bg-gray-900/60 p-4" @keydown.escape.window="closeScanner">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.outside="closeScanner">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Scan Barcode</h3><button type="button" @click="closeScanner" class="text-2xl text-gray-400" aria-label="Tutup">&times;</button>
            </div>
            <div id="barcode-reader" class="w-full"></div>
            <p x-text="scannerMessage" class="mt-3 text-sm text-gray-500 dark:text-gray-400"></p>
            <div class="mt-4 flex gap-2"><input x-ref="manualCode" @keydown.enter="findByCode" type="text" placeholder="Masukkan nomor barcode manual" class="h-11 min-w-0 flex-1 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"><button type="button" @click="findByCode" class="rounded-lg bg-blue-600 px-4 text-sm font-medium text-white">Cari</button></div>
        </div>
    </div>

    <div x-show="detailOpen" x-cloak class="fixed inset-0 z-[999998] flex h-full items-start justify-center overflow-y-auto overscroll-contain bg-gray-900/60 p-4 sm:items-center" @keydown.escape.window="detailOpen = false">
        <div class="my-2 w-full max-w-4xl rounded-2xl bg-white shadow-xl sm:my-8 dark:bg-gray-900" @click.outside="detailOpen = false">
            <div class="flex items-start justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-blue-600">Detail Aset</p>
                    <h3 class="mt-1 text-xl font-semibold text-gray-800 dark:text-white" x-text="selected?.nama_barang"></h3>
                    <p class="text-sm text-gray-500" x-text="selected?.no_inventaris"></p>
                </div><button type="button" @click="detailOpen = false" class="text-2xl text-gray-400" aria-label="Tutup">&times;</button>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h4 class="mb-4 font-semibold text-gray-800 dark:text-white">Informasi Umum Barang</h4>
                    <dl class="space-y-3 text-xs">
                        <template x-for="field in detailFields" :key="field.label">
                            <div class="grid grid-cols-[130px_1fr] gap-3">
                                <dt class="text-gray-500" x-text="field.label"></dt>
                                <dd class="font-medium text-gray-800 dark:text-gray-200" x-text="field.value || '-' "></dd>
                            </div>
                        </template>
                    </dl>
                </section>
                <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h4 class="font-semibold text-gray-800 dark:text-white">Histori Pemeliharaan</h4><a :href="selected ? maintenanceUrl(selected.id) : '#'" class="whitespace-nowrap rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">+ Tambah Catatan</a>
                    </div>
                    <div class="max-h-80 space-y-3 overflow-y-auto overscroll-contain">
                        <template x-if="!selected?.maintenance_logs?.length">
                            <p class="text-sm text-gray-500">Belum ada histori maintenance.</p>
                        </template>
                        <template x-for="log in maintenanceLogsPage" :key="log.id">
                            <article class="border-b border-gray-100 pb-3 text-sm dark:border-gray-800">
                                <div class="flex justify-between gap-2"><strong class="text-gray-800 dark:text-white" x-text="formatDate(log.tanggal)"></strong><span class="rounded-full bg-blue-50 px-2 py-1 text-xs text-blue-600" x-text="log.status"></span></div>
                                <p class="mt-1 text-gray-600 dark:text-gray-400" x-text="log.gejala_masalah"></p>
                                <p class="mt-1 text-xs text-gray-500" x-text="'Pelapor: ' + (log.pelapor || '-') + ' | Teknisi: ' + (log.teknisi || '-')"></p>
                            </article>
                        </template>
                    </div>
                    <div x-show="maintenancePageCount > 1" class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                        <button type="button" @click="maintenancePage--" :disabled="maintenancePage === 1" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300">Sebelumnya</button>
                        <span class="text-xs text-gray-500" x-text="`Hal. ${maintenancePage}/${maintenancePageCount}`"></span>
                        <button type="button" @click="maintenancePage++" :disabled="maintenancePage === maintenancePageCount" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300">Berikutnya</button>
                    </div>
                </section>
            </div>
            <div class="flex justify-end border-t border-gray-200 px-5 py-4 dark:border-gray-800"><a :href="selected ? detailUrl(selected.id) : '#'" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">Buka Halaman Detail</a></div>
        </div>
    </div>
</div>
@endsection
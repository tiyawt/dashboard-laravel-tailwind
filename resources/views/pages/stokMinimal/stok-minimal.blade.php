@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Batas Stock Minimal Barang" />

@if(session('success'))
<div class="mb-4 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Header & Action -->
    <div class="mb-6 flex min-w-0 flex-col gap-2 px-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Standar Minimal Stock
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola batas minimal stock barang di sini.</p>
            <!-- Form Export Berdasarkan Bulan & Tahun -->
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form
                    action="{{ route('stok-minimal.export') }}"
                    method="GET"
                    class="flex w-full sm:w-auto">
                    <div x-data="{ open: false }" class="relative w-full sm:w-auto">

                        <button
                            type="button"
                            @click="open = !open"
                            class="inline-flex h-[42px] w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-500 sm:w-auto">
                            Download

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            @click.outside="open = false"
                            x-transition
                            class="absolute left-0 right-auto z-20 mt-2 w-full min-w-40 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800 sm:left-auto sm:right-0 sm:w-40">
                            <button
                                type="submit"
                                name="format"
                                value="xlsx"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                Download Excel
                            </button>

                            <button
                                type="submit"
                                name="format"
                                value="pdf"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                Download PDF
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form action="{{ route('stok-minimal.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[280px]" />
                </div>
            </form>
            <a href="{{ route('stok-minimal.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                + Tambah Barang Baru
            </a>
        </div>
    </div>

    <!-- Table Data -->
    <div class="w-full max-w-full overflow-x-auto">
        <table class="min-w-[1000px] text-left border-collapse">
            <thead>
                <tr class="border-gray-200 border-y dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Nama Barang</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Satuan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Jumlah Stock</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Batas Minimal</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Selisih</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Status Alert</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Rentang Waktu</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Keterangan</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($minimalStocks as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $item->barang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->barang->satuan ?? '-' }}
                    </td>
                    <!-- Computed: Jumlah Stock Fisik Saat Ini -->
                    <td class="px-4 py-3.5 text-sm font-semibold text-gray-800 dark:text-white/90">
                        {{ $item->jumlah_stock }}
                    </td>
                    <!-- Input Manual: Minimal Stock -->
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->minimal }}
                    </td>
                    <!-- Computed: Selisih -->
                    <td class="px-4 py-3.5 text-sm font-semibold {{ $item->selisih < 0 ? 'text-red-500' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $item->selisih }}
                    </td>
                    <!-- Computed: Badge Status Alert -->
                    <td class="px-4 py-3.5 text-sm">
                        @if($item->status_alert === 'habis')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400 uppercase">
                            HABIS
                        </span>
                        @elseif($item->status_alert === 'menipis')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400 uppercase">
                            MENIPIS
                        </span>
                        @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400 uppercase">
                            AMAN
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->rentang_waktu ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('stok-minimal.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Edit Barang">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="currentColor" />
                                    <path d="M11.3787 5.79289L3 14.1716V17H5.82843L14.2071 8.62132L11.3787 5.79289Z" fill="currentColor" />
                                </svg>
                            </a>
                            <form action="{{ route('stok-minimal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Menghapus barang ini juga akan menghapus data pengajuannya. Yakin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                        Belum ada standar minimal stok barang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $minimalStocks->links() }}
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Stok Keluar Lemari" />

@if(session('success'))
<div class="mb-4 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">
    <span class="text-sm font-medium">{{ session('success') }}</span>
</div>
@endif

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- Header & Search -->
    <div class="mb-4 flex min-w-0 flex-col gap-2 px-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Daftar Stok Barang Keluar
            </h3>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <!-- Form Export Berdasarkan Bulan & Tahun -->
                <form action="{{ route('barang-keluar.export') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <!-- Dropdown Pilih Bulan -->
                    <select name="bulan" class="h-[42px] rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @for($m = 1; $m <= 12; $m++)
                            @php $monthVal=sprintf('%02d', $m); @endphp
                            <option value="{{ $monthVal }}" {{ date('m') == $monthVal ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                            @endfor
                    </select>

                    <!-- Input/Select Tahun -->
                    <select name="tahun" class="h-[42px] rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>

                    <!-- Tombol Download -->
                    <button type="submit" class="inline-flex h-[42px] items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                        Download Excel
                    </button>
                </form>
            </div>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form action="{{ route('barang-keluar.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / pelapor / lokasi..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 xl:w-[280px]" />
                </div>
            </form>
            <a href="{{ route('barang-keluar.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                + Catat Barang Keluar
            </a>
        </div>
    </div>

    <!-- Table Data -->
    <div class="w-full max-w-full overflow-x-auto">
        <table class="min-w-[1100px] text-left border-collapse">
            <thead>
                <tr class="border-gray-200 border-y dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Tgl Keluar</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Nama Barang</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Jumlah Keluar</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Kondisi Barang Lama</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Pelapor</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Lokasi</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Status</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Keterangan</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($barangKeluar as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $item->barang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-red-500">
                        -{{ $item->jumlah }} {{ $item->barang->satuan ?? '' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->kondisi_barang_lama ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->pelapor }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->lokasi }}
                    </td>
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        @if($item->status === 'done')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400">
                            DONE
                        </span>
                        @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400">
                            NOT YET
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-center">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Edit tetap tampil -->
                            <a href="{{ route('barang-keluar.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Edit Catatan">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="currentColor" />
                                    <path d="M11.3787 5.79289L3 14.1716V17H5.82843L14.2071 8.62132L11.3787 5.79289Z" fill="currentColor" />
                                </svg>
                            </a>

                            <!-- Tombol Hapus hanya tampil jika status BUKAN 'done' -->
                            @if(strtolower($item->status) !== 'done')
                            <form action="{{ route('barang-keluar.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draf barang keluar ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600" title="Hapus Draf">
                                    🗑️
                                </button>
                            </form>
                            @else
                            <!-- Indikator Terkunci -->
                            <span class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Transaksi DONE tidak dapat dihapus">
                                🔒
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada catatan barang keluar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $barangKeluar->links() }}
    </div>
</div>
@endsection
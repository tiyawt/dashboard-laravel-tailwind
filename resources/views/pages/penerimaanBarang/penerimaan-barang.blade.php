@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Penerimaan Barang / Barang Datang" />

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
                Daftar Penerimaan Barang
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Otomatis terbuat dari pengajuan yang di-ACC.</p>
            <form action="{{ route('penerimaan-barang.export') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="bulan" class="h-[42px] rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @for($m = 1; $m <= 12; $m++)
                        @php $monthVal=sprintf('%02d', $m); @endphp
                        <option value="{{ $monthVal }}" {{ date('m') == $monthVal ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                        @endfor
                </select>

                <select name="tahun" class="h-[42px] rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="inline-flex h-[42px] items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                    Download Excel/CSV
                </button>
            </form>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form action="{{ route('penerimaan.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / penerima..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]" />
                </div>
            </form>
        </div>
    </div>

    <!-- Table Data -->
    <div class="w-full max-w-full overflow-x-auto">
        <table class="min-w-[1240px] text-left border-collapse">
            <thead>
                <tr class="border-gray-200 border-y dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Nama Barang</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Tgl Pengajuan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Status Barang</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Divisi Permintaan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Total Pengajuan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Tgl Pengambilan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Jumlah Diterima</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Penerima</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Keterangan</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($penerimaans as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $item->pengajuan->barang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->pengajuan?->tanggal_pengajuan ? \Carbon\Carbon::parse($item->pengajuan->tanggal_pengajuan)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400 capitalize">
                            {{ $item->pengajuan->status_barang ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->pengajuan->permintaan ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->pengajuan->volume ?? 0 }} {{ $item->pengajuan->barang->satuan ?? '' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->tanggal_pengambilan ? \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-blue-600 dark:text-blue-400">
                        {{ $item->jumlah_diterima }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->penerima ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('penerimaan.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Edit Catatan">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="currentColor" />
                                    <path d="M11.3787 5.79289L3 14.1716V17H5.82843L14.2071 8.62132L11.3787 5.79289Z" fill="currentColor" />
                                </svg>
                            </a>
                            <form action="{{ route('penerimaan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penerimaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 2C8.44772 2 8 2.44772 8 3V4H4C3.44772 4 3 4.44772 3 5C3 5.55228 3.44772 6 4 6H5V16C5 17.1046 5.89543 18 7 18H13C14.1046 18 15 17.1046 15 16V6H16C16.5523 6 17 5.55228 17 5C17 4.44772 16.5523 4 16 4H12V3C12 2.44772 11.5523 2 11 2H9ZM10 4V3.5H10V4H10ZM7 8C7.55228 8 8 8.44772 8 9V14C8 14.5523 7.55228 15 7 15C6.44772 15 6 14.5523 6 14V9C6 8.44772 6.44772 8 7 8ZM13 8C13.5523 8 14 8.44772 14 9V14C14 14.5523 13.5523 15 13 15C12.4477 15 12 14.5523 12 14V9C12 8.44772 12.4477 8 13 8Z" fill="currentColor" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada barang yang di-ACC atau siap diterima.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $penerimaans->links() }}
    </div>
</div>
@endsection

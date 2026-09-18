@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Pengajuan Barang" />

<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <!-- Header & Search -->
    <div class="mb-6 flex min-w-0 flex-col gap-2 px-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div class="min-w-0">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Daftar Pengajuan Barang
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pengajuan barang di sini.</p>
            <!-- Form Export Berdasarkan Bulan & Tahun -->
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <form
                    action="{{ route('pengajuan-barang.export') }}"
                    method="GET"
                    class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center">
                    <select name="bulan" class="h-[42px] w-full rounded-lg border border-gray-300 px-3 text-sm sm:w-auto">
                        @for($m = 1; $m <= 12; $m++)
                            @php
                            $monthVal=sprintf('%02d', $m);
                            @endphp

                            <option
                            value="{{ $monthVal }}"
                            {{ date('m') == $monthVal ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                            @endfor
                    </select>

                    <select name="tahun" class="h-[42px] w-full rounded-lg border border-gray-300 px-3 text-sm sm:w-auto">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}">
                            {{ $y }}
                        </option>
                        @endfor
                    </select>

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
            <form action="{{ route('pengajuan.index') }}" method="GET">
                <div class="relative">
                    <button type="submit" class="absolute -translate-y-1/2 ltr:left-4 rtl:right-4 top-1/2">
                        <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" fill="" />
                        </svg>
                    </button>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 ltr:pl-[42px] ltr:pr-4 rtl:pr-[42px] rtl:pl-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]" />
                </div>
            </form>
            <a href="{{ route('pengajuan.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                + Tambah Pengajuan
            </a>
        </div>
    </div>

    <div class="w-full max-w-full overflow-x-auto">
        <table class="min-w-[1200px] text-left border-collapse">
            <thead>
                <tr class="border-gray-200 border-y dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <th scope="col" class="px-4 py-3 min-w-[140px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Tanggal Pengajuan</th>
                    <th scope="col" class="px-4 py-3 min-w-[180px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Nama Barang</th>
                    <th scope="col" class="px-4 py-3 min-w-[100px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Volume</th>
                    <th scope="col" class="px-4 py-3 min-w-[100px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Satuan</th>
                    <th scope="col" class="px-4 py-3 min-w-[140px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Harga/unit</th>
                    <th scope="col" class="px-4 py-3 min-w-[160px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Link SPB & INVOICE</th>
                    <th scope="col" class="px-4 py-3 min-w-[150px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Permintaan</th>
                    <th scope="col" class="px-4 py-3 min-w-[140px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Status Barang</th>
                    <th scope="col" class="px-4 py-3 min-w-[150px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Status Disposisi</th>
                    <th scope="col" class="px-4 py-3 min-w-[180px] font-medium text-gray-500 text-theme-sm dark:text-gray-400">Keterangan</th>
                    <th scope="col" class="px-4 py-3 min-w-[120px] text-center font-medium text-gray-500 text-theme-sm dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($pengajuans as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90 whitespace-nowrap">
                        {{ $item->barang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $item->volume }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $item->barang->satuan ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        Rp {{ number_format($item->harga_per_unit, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        @if(!empty($item->link_spb_invoice))
                        @php
                        $url = $item->link_spb_invoice;
                        if (!\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
                        $url = 'https://' . $url;
                        }
                        @endphp
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline flex items-center gap-1">
                            Lihat Dokumen
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $item->permintaan }}</td>
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400 capitalize">
                            {{ $item->status_barang }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        <!-- Dropdown Update Status Disposisi secara instan -->
                        <form action="{{ route('pengajuan.update-status', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status_disposisi" onchange="this.form.submit()"
                                class="text-xs font-semibold px-2 py-1 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none 
                                {{ $item->status_disposisi == 'acc' ? 'text-green-600 bg-green-50 dark:bg-green-500/15' : '' }}
                                {{ $item->status_disposisi == 'pending' ? 'text-yellow-600 bg-yellow-50 dark:bg-yellow-500/15' : '' }}
                                {{ $item->status_disposisi == 'rejected' ? 'text-red-600 bg-red-50 dark:bg-red-500/15' : '' }}">
                                <option value="pending" {{ $item->status_disposisi == 'pending' ? 'selected' : '' }}>PENDING</option>
                                <option value="acc" {{ $item->status_disposisi == 'acc' ? 'selected' : '' }}>ACC</option>
                                <option value="rejected" {{ $item->status_disposisi == 'rejected' ? 'selected' : '' }}>REJECTED</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $item->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3.5 text-sm text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('pengajuan.edit', $item->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Edit Pengajuan">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="currentColor" />
                                    <path d="M11.3787 5.79289L3 14.1716V17H5.82843L14.2071 8.62132L11.3787 5.79289Z" fill="currentColor" />
                                </svg>
                            </a>
                            <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
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
                    <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada data pengajuan barang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $pengajuans->links() }}
    </div>
</div>
@endsection
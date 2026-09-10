@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Daftar Belanja (Barang Belum Dibeli)" />

<!-- Container Utama Card -->
<div class="w-full max-w-full overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">

    <!-- Header & Search -->
    <div class="mb-4 flex min-w-0 flex-col gap-2 px-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Monitoring Daftar Belanja & Pemenuhan Stok
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan selisih pemenuhan barang dari pengajuan yang di-ACC.</p>
            <form action="{{ route('daftar-belanja.export') }}" method="GET" class="flex flex-wrap items-center gap-2">
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
                    Download Excel
                </button>
            </form>


        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form action="{{ route('daftar-belanja.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / divisi..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]" />
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
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Divisi Permintaan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Volume Dibutuhkan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Total Diterima</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Selisih Kekurangan</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Harga/Unit</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Perkiraan Biaya Kurang</th>
                    <th class="px-4 py-3 font-medium text-gray-500 text-sm">Status Pemenuhan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($daftarBelanja as $item)
                @php
                $selisih = $item->selisih; // Otomatis dipanggil dari Accessor di PengajuanBarang.php
                $biayaKurang = abs($selisih) * $item->harga_per_unit;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $item->barang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        {{ $item->permintaan }}
                    </td>
                    <!-- Target Pengajuan -->
                    <td class="px-4 py-3.5 text-sm text-gray-800 dark:text-white/90">
                        {{ $item->volume }} {{ $item->barang->satuan ?? '' }}
                    </td>
                    <!-- Computed Total Diterima -->
                    <td class="px-4 py-3.5 text-sm font-semibold text-blue-600 dark:text-blue-400">
                        {{ $item->total_diterima }} {{ $item->barang->satuan ?? '' }}
                    </td>
                    <!-- Computed Selisih (Negatif = Kurang) -->
                    <td class="px-4 py-3.5 text-sm font-bold {{ $selisih < 0 ? 'text-red-500' : 'text-green-600' }}">
                        {{ $selisih }} {{ $item->barang->satuan ?? '' }}
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                        Rp {{ number_format($item->harga_per_unit, 0, ',', '.') }}
                    </td>
                    <!-- Perkiraan Biaya Sisa Barang yang Belum Dibeli -->
                    <td class="px-4 py-3.5 text-sm font-medium text-gray-800 dark:text-white/90">
                        @if($selisih < 0)
                            Rp {{ number_format($biayaKurang, 0, ',', '.') }}
                            @else
                            <span class="text-gray-400">Rp 0</span>
                            @endif
                    </td>
                    <!-- Badge Indicator -->
                    <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                        @if($selisih < 0)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400">
                            BELUM LENGKAP
                            </span>
                            @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400">
                                LENGKAP
                            </span>
                            @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada barang pengajuan ACC yang perlu dibeli/dipenuhi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
        {{ $daftarBelanja->links() }}
    </div>
</div>
@endsection

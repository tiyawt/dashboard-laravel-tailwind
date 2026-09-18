@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Lokasi" />

@if(session('success'))
<div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">{{ $errors->first() }}</div>
@endif

<div class="space-y-5">
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex-col">
                <h2 class="font-semibold text-gray-800 dark:text-white">Daftar Master Lokasi</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data master lokasi di sini.</p>
            </div>
            <div class="w-full sm:w-auto">
                <div class="mt-4 flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap">
                    <!-- Form Export Berdasarkan Bulan & Tahun -->
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <form
                            action="{{ route('master-lokasi.export') }}"
                            method="GET"
                            class="flex w-full flex-wrap items-center gap-2 sm:w-auto">

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
                                    class="absolute left-0 right-auto z-20 mt-2 w-full min-w-0 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800 sm:left-auto sm:right-0 sm:w-40">
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
                    <a href="{{ route('master-lokasi.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg h-[42px] bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">+ Tambah Lokasi</a>

                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[600px] w-full text-start">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <th class="px-5 py-3 text-start text-sm font-semibold text-gray-500">Nama Lokasi</th>
                        <th class="px-5 py-3 text-start text-sm font-semibold text-gray-500">Kode</th>
                        <th class="px-5 py-3 text-start text-sm font-semibold text-gray-500">Tipe Bangunan</th>
                        <th class="px-5 py-3 text-start text-sm font-semibold text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($locations as $location)
                    <tr>
                        <td class="px-5 py-3 text-sm text-gray-800 dark:text-white/90">{{ $location->nama }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">{{ $location->kode }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $location->tipe === 'Lokasi' ? 'Divisi' : $location->tipe }}</td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('master-lokasi.edit', $location->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="Edit Lokasi">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                        <path d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z" fill="currentColor" />
                                        <path d="M11.3787 5.79289L3 14.1716V17H5.82843L14.2071 8.62132L11.3787 5.79289Z" fill="currentColor" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">Belum ada master lokasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="w-full max-w-full overflow-hidden border-t border-gray-200 px-6 py-4 dark:border-white/[0.05]">
            {{ $locations->links() }}
        </div>
    </section>
</div>
@endsection
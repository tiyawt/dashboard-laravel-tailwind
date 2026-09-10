<?php

namespace App\Http\Controllers;

use App\Models\StokKeluarLemari;
use App\Models\MasterBarang;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barangKeluar = StokKeluarLemari::with('barang')
            ->when($search, function ($query, $search) {
                $normalizedSearch = '%' . Str::lower(trim($search)) . '%';

                return $query->where(function ($query) use ($normalizedSearch) {
                    $query->whereHas('barang', function ($q) use ($normalizedSearch) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', [$normalizedSearch]);
                    })->orWhereRaw('LOWER(pelapor) LIKE ?', [$normalizedSearch])
                        ->orWhereRaw('LOWER(lokasi) LIKE ?', [$normalizedSearch]);
                });
            })
            ->latest()
            ->paginate(10);

        return view('pages.barangKeluar.barang-keluar', compact('barangKeluar'));
    }

    public function create()
    {
        $masterBarang = MasterBarang::all();
        return view('pages.barangKeluar.barang-keluar-create', compact('masterBarang'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()?->role === 'admin';

        $validated = $request->validate([
            'barang_id'           => 'required|exists:master_barang,id',
            'kondisi_barang_lama' => 'nullable|string',
            'tanggal_keluar'      => 'required|date',
            'jumlah'              => 'required|numeric|min:1',
            'pelapor'             => 'required|string',
            'lokasi'              => 'required|string',
            'status'              => $isAdmin
                ? 'nullable|in:done,not_yet'
                : 'required|in:done,not_yet',
            'keterangan'          => 'nullable|string',
        ]);

        $barang       = MasterBarang::findOrFail($request->barang_id);
        $stokTersedia = (int) $barang->jumlah_stock;
        $jumlahMinta  = (int) $request->jumlah;

        // VALIDASI BERLAKU UNTUK SEMUA STATUS (DONE MAUPUN NOT_YET)
        // 1. Cek jika stok barang sudah 0/habis
        if ($stokTersedia <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => "Stok untuk barang '{$barang->nama_barang}' sudah HABIS (0)! Tidak dapat mencatat pengeluaran barang."]);
        }

        // 2. Cek jika jumlah yang diminta melebihi stok yang ada
        if ($jumlahMinta > $stokTersedia) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => "Stok tidak cukup! Stok tersedia hanya {$stokTersedia} {$barang->satuan}, tetapi Anda memasukkan {$jumlahMinta}."]);
        }

        if ($isAdmin) {
            $validated['status'] = 'not_yet';
        }

        StokKeluarLemari::create($validated);

        return redirect()->route('barang-keluar.index')->with('success', 'Catatan barang keluar berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barangKeluar = StokKeluarLemari::findOrFail($id);

        if (Auth::user()?->role === 'admin' && strtolower($barangKeluar->status) === 'done') {
            return redirect()->route('barang-keluar.index')->withErrors([
                'error' => 'Catatan barang keluar yang sudah DONE hanya dapat dikelola oleh superadmin.',
            ]);
        }

        $masterBarang = MasterBarang::all();

        return view('pages.barangKeluar.barang-keluar-edit', compact('barangKeluar', 'masterBarang'));
    }

    public function update(Request $request, $id)
    {
        $isAdmin = Auth::user()?->role === 'admin';
        $barangKeluar = StokKeluarLemari::findOrFail($id);

        if ($isAdmin && strtolower($barangKeluar->status) === 'done') {
            return redirect()->route('barang-keluar.index')->withErrors([
                'error' => 'Catatan barang keluar yang sudah DONE hanya dapat dikelola oleh superadmin.',
            ]);
        }

        $validated = $request->validate([
            'barang_id'           => 'required|exists:master_barang,id',
            'kondisi_barang_lama' => 'nullable|string',
            'tanggal_keluar'      => 'required|date',
            'jumlah'              => 'required|numeric|min:1',
            'pelapor'             => 'required|string',
            'lokasi'              => 'required|string',
            'status'              => $isAdmin
                ? 'nullable|in:done,not_yet'
                : 'required|in:done,not_yet',
            'keterangan'          => 'nullable|string',
        ]);

        $barang       = MasterBarang::findOrFail($request->barang_id);

        // Hitung stok tersedia (kembalikan stok lama jika transaksi sebelumnya sudah DONE)
        $stokTersedia = (int) $barang->jumlah_stock +
            (strtolower($barangKeluar->status) === 'done' && $barangKeluar->barang_id == $barang->id
                ? (int) $barangKeluar->jumlah
                : 0);

        $jumlahMinta = (int) $request->jumlah;

        // VALIDASI BERLAKU UNTUK SEMUA STATUS
        if ($stokTersedia <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => "Stok untuk barang '{$barang->nama_barang}' tidak mencukupi (0)!"]);
        }

        if ($jumlahMinta > $stokTersedia) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['jumlah' => "Stok tidak cukup! Stok tersedia hanya {$stokTersedia} {$barang->satuan}, tetapi Anda memasukkan {$jumlahMinta}."]);
        }

        if ($isAdmin) {
            $validated['status'] = 'not_yet';
        }

        $barangKeluar->update($validated);

        return redirect()->route('barang-keluar.index')->with('success', 'Data barang keluar berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barangKeluar = StokKeluarLemari::findOrFail($id);

        // Kunci penghapusan jika transaksi sudah selesai / DONE
        if (strtolower($barangKeluar->status) === 'done') {
            return redirect()->back()->withErrors([
                'error' => 'Transaksi barang keluar yang sudah bernilai DONE tidak dapat dihapus agar stok fisik tetap konsisten!'
            ]);
        }

        $barangKeluar->delete();

        return redirect()->route('barang-keluar.index')->with('success', 'Data barang keluar (draft) berhasil dihapus!');
    }

    // Export CSV
    public function exportCsv(Request $request)
    {
        // Ambil input bulan dan tahun dari request (default: bulan & tahun saat ini)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Filter data barang keluar berdasarkan bulan dan tahun
        $data = StokKeluarLemari::with('barang')
            ->whereMonth('tanggal_keluar', $bulan)
            ->whereYear('tanggal_keluar', $tahun)
            ->latest()
            ->get();

        $fileName = "laporan_barang_keluar_{$tahun}_{$bulan}.xlsx";
        $columns = ['Tanggal Keluar', 'Nama Barang', 'Jumlah', 'Satuan', 'Pelapor', 'Lokasi', 'Status', 'Keterangan'];
        $formatDate = static fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '-';

        $rows = $data->map(fn ($item) => [
            $formatDate($item->tanggal_keluar),
            $item->barang?->nama_barang ?? '-',
            $item->jumlah,
            $item->barang?->satuan ?? '',
            $item->pelapor,
            $item->lokasi,
            strtoupper($item->status),
            $item->keterangan ?? '-',
        ])->all();

        return SimpleXlsxExporter::download($columns, $rows, $fileName, 'Barang Keluar');
    }
}

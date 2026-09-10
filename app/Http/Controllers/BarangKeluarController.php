<?php

namespace App\Http\Controllers;

use App\Models\StokKeluarLemari;
use App\Models\MasterBarang;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barangKeluar = StokKeluarLemari::with('barang')
            ->when($search, function ($query, $search) {
                return $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%");
                })->orWhere('pelapor', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
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
        $validated = $request->validate([
            'barang_id'           => 'required|exists:master_barang,id',
            'kondisi_barang_lama' => 'nullable|string',
            'tanggal_keluar'      => 'required|date',
            'jumlah'              => 'required|numeric|min:1',
            'pelapor'             => 'required|string',
            'lokasi'              => 'required|string',
            'status'              => 'required|in:done,not_yet',
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

        StokKeluarLemari::create($validated);

        return redirect()->route('barang-keluar.index')->with('success', 'Catatan barang keluar berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barangKeluar = StokKeluarLemari::findOrFail($id);
        $masterBarang = MasterBarang::all();

        return view('pages.barangKeluar.barang-keluar-edit', compact('barangKeluar', 'masterBarang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'barang_id'           => 'required|exists:master_barang,id',
            'kondisi_barang_lama' => 'nullable|string',
            'tanggal_keluar'      => 'required|date',
            'jumlah'              => 'required|numeric|min:1',
            'pelapor'             => 'required|string',
            'lokasi'              => 'required|string',
            'status'              => 'required|in:done,not_yet',
            'keterangan'          => 'nullable|string',
        ]);

        $barangKeluar = StokKeluarLemari::findOrFail($id);
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

        $fileName = "laporan_barang_keluar_{$tahun}_{$bulan}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal Keluar', 'Nama Barang', 'Jumlah', 'Satuan', 'Pelapor', 'Lokasi', 'Status', 'Keterangan'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');

            // Header kolom CSV
            fputcsv($file, $columns);

            // Baris data
            foreach ($data as $item) {
                fputcsv($file, [
                    $item->tanggal_keluar,
                    $item->barang->nama_barang ?? '-',
                    $item->jumlah,
                    $item->barang->satuan ?? '',
                    $item->pelapor,
                    $item->lokasi,
                    strtoupper($item->status),
                    $item->keterangan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

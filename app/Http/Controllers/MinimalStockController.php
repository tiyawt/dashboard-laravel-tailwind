<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MinimalStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MinimalStockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Eager-loading relasi barang untuk pencarian & kalkulasi
        $minimalStocks = MinimalStock::with('barang')
            ->when($search, function ($query, $search) {
                return $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('pages.stokMinimal.stok-minimal', compact('minimalStocks'));
    }

    public function create()
    {
        return view('pages.stokMinimal.stok-minimal-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'   => 'required|string|unique:master_barang,nama_barang',
            'satuan'        => 'required|string',
            'minimal'       => 'required|numeric|min:0',
            'rentang_waktu' => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        // Simpan serentak ke master_barang dan minimal_stock
        DB::transaction(function () use ($request) {
            $barang = MasterBarang::create([
                'nama_barang' => $request->nama_barang,
                'satuan'      => $request->satuan,
            ]);

            MinimalStock::create([
                'barang_id'     => $barang->id,
                'minimal'       => $request->minimal,
                'rentang_waktu' => $request->rentang_waktu,
                'keterangan'    => $request->keterangan,
            ]);
        });

        return redirect()->route('stok-minimal.index')->with('success', 'Barang dan batas stok minimal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $minimalStock = MinimalStock::with('barang')->findOrFail($id);
        return view('pages.stokMinimal.stok-minimal-edit', compact('minimalStock'));
    }

    public function update(Request $request, $id)
    {
        $minimalStock = MinimalStock::findOrFail($id);

        $request->validate([
            'nama_barang'   => 'required|string|unique:master_barang,nama_barang,' . $minimalStock->barang_id,
            'satuan'        => 'required|string',
            'minimal'       => 'required|numeric|min:0',
            'rentang_waktu' => 'nullable|string',
            'keterangan'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $minimalStock) {
            // Update Master Barang
            $minimalStock->barang->update([
                'nama_barang' => $request->nama_barang,
                'satuan'      => $request->satuan,
            ]);

            // Update Batas Minimal
            $minimalStock->update([
                'minimal'       => $request->minimal,
                'rentang_waktu' => $request->rentang_waktu,
                'keterangan'    => $request->keterangan,
            ]);
        });

        return redirect()->route('stok-minimal.index')->with('success', 'Batas stok minimal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $minimalStock = MinimalStock::findOrFail($id);

        // Hapus MasterBarang akan otomatis menghapus MinimalStock via Cascade
        $minimalStock->barang()->delete();

        return redirect()->route('stok-minimal.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function exportCsv()
    {
        $data = MinimalStock::with('barang')->latest()->get();

        $fileName = "laporan_stok_minimal_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Barang', 'Satuan', 'Jumlah Stok Fisik', 'Batas Minimal', 'Selisih', 'Status Alert', 'Rentang Waktu', 'Keterangan'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $item) {
                fputcsv($file, [
                    $item->barang->nama_barang ?? '-',
                    $item->barang->satuan ?? '-',
                    $item->jumlah_stock,
                    $item->minimal,
                    $item->selisih,
                    strtoupper($item->status_alert),
                    $item->rentang_waktu ?? '-',
                    $item->keterangan ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

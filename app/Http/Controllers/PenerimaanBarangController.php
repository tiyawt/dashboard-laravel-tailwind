<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Models\PengajuanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PenerimaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Ambil data penerimaan beserta pengajuan dan master barangnya
        $penerimaans = PenerimaanBarang::with(['pengajuan.barang'])
            ->when($search, function ($query, $search) {
                $normalizedSearch = '%' . Str::lower(trim($search)) . '%';

                return $query->where(function ($query) use ($normalizedSearch) {
                    $query->whereHas('pengajuan.barang', function ($q) use ($normalizedSearch) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', [$normalizedSearch]);
                    })->orWhereRaw('LOWER(penerima) LIKE ?', [$normalizedSearch]);
                });
            })
            ->latest()
            ->paginate(10);

        return view('pages.penerimaanBarang.penerimaan-barang', compact('penerimaans'));
    }

    public function edit($id)
    {
        // Mengambil data penerimaan beserta relasi pengajuan & barang
        $penerimaan = PenerimaanBarang::with('pengajuan.barang')->findOrFail($id);

        return view('pages.penerimaanBarang.penerimaan-barang-edit', compact('penerimaan'));
    }

    public function update(Request $request, $id)
    {
        $penerimaan = PenerimaanBarang::findOrFail($id);
        $pengajuan  = $penerimaan->pengajuan;

        // Hitung akumulasi penerimaan dari transaksi lain (jika ada parsial)
        $penerimaanLain = PenerimaanBarang::where('pengajuan_barang_id', $pengajuan->id)
            ->where('id', '!=', $id)
            ->sum('jumlah_diterima');

        // Sisa batas penerimaan yang diperbolehkan
        $maxBolehDiterima = $pengajuan->volume - $penerimaanLain;

        $validated = $request->validate([
            'tanggal_pengambilan' => 'required|date',
            'jumlah_diterima'     => "required|numeric|min:1|max:{$maxBolehDiterima}",
            'penerima'            => 'required|string|max:255',
            'keterangan'          => 'nullable|string',
        ], [
            'jumlah_diterima.max' => "Jumlah diterima tidak boleh melebihi sisa pengajuan ({$maxBolehDiterima} {$pengajuan->barang->satuan}).",
            'jumlah_diterima.min' => 'Jumlah diterima minimal 1 item.',
        ]);

        $penerimaan->update($validated);

        return redirect()->route('penerimaan.index')->with('success', 'Data penerimaan barang berhasil diperbarui!');
    }

    // Fitur tambah baris baru jika barang datang secara bertahap (multi-receipts)
    public function storePartial(Request $request, $pengajuan_barang_id)
    {
        $request->validate([
            'tanggal_pengambilan' => 'required|date',
            'jumlah_diterima'     => 'required|numeric|min:1',
            'penerima'            => 'required|string',
            'keterangan'          => 'nullable|string',
        ]);

        PenerimaanBarang::create([
            'pengajuan_barang_id' => $pengajuan_barang_id,
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
            'jumlah_diterima'     => $request->jumlah_diterima,
            'penerima'            => $request->penerima,
            'keterangan'          => $request->keterangan,
        ]);

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan bertahap berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $penerimaan = PenerimaanBarang::findOrFail($id);
        $penerimaan->delete();

        return redirect()->route('penerimaan.index')->with('success', 'Data penerimaan berhasil dihapus!');
    }

    public function exportCsv(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $data = PenerimaanBarang::with(['pengajuan.barang'])
            ->whereMonth('tanggal_pengambilan', $bulan)
            ->whereYear('tanggal_pengambilan', $tahun)
            ->latest()
            ->get();

        $fileName = "laporan_penerimaan_barang_{$tahun}_{$bulan}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Barang', 'Tgl Pengajuan', 'Status Kondisi', 'Divisi Permintaan', 'Total Pengajuan', 'Tgl Pengambilan', 'Jumlah Diterima', 'Penerima', 'Keterangan'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $item) {
                fputcsv($file, [
                    $item->pengajuan->barang->nama_barang ?? '-',
                    $item->pengajuan?->tanggal_pengajuan ?? '-',
                    strtoupper($item->pengajuan->status_barang ?? '-'),
                    $item->pengajuan->permintaan ?? '-',
                    $item->pengajuan->volume ?? 0,
                    $item->tanggal_pengambilan ?? '-',
                    $item->jumlah_diterima,
                    $item->penerima ?? '-',
                    $item->keterangan ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

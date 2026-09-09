<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use App\Models\MasterBarang;
use Illuminate\Http\Request;

class PengajuanBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pengajuans = PengajuanBarang::with('barang')
            ->when($search, function ($query, $search) {
                return $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%");
                })->orWhere('permintaan', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        $masterBarang = MasterBarang::all();

        return view('pages.pengajuanBarang.pengajuan-barang', compact('pengajuans', 'masterBarang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'         => 'required|exists:master_barang,id',
            'tanggal_pengajuan' => 'required|date',
            'volume'            => 'required|numeric|min:1',
            'harga_per_unit'    => 'required|numeric|min:0',
            'link_spb_invoice' =>  'nullable|string',
            'permintaan'        => 'required|string',
            'status_barang'     => 'required|in:baru,bekas',
            'status_disposisi'  => 'required|in:pending,acc,rejected',
            'keterangan'        => 'nullable|string',
        ]);

        PengajuanBarang::create($validated);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan barang berhasil ditambahkan!');
    }

    public function create()
    {
        $masterBarang = \App\Models\MasterBarang::all();
        return view('pages.pengajuanBarang.pengajuan-barang-create', compact('masterBarang'));
    }

    public function edit($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $masterBarang = MasterBarang::all();

        return view('pages.pengajuanBarang.pengajuan-barang-edit', compact('pengajuan', 'masterBarang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'barang_id'         => 'required|exists:master_barang,id',
            'tanggal_pengajuan' => 'required|date',
            'volume'            => 'required|numeric|min:1',
            'harga_per_unit'    => 'required|numeric|min:0',
            'link_spb_invoice'  => 'nullable|string',
            'permintaan'        => 'required|string',
            'status_barang'     => 'required|in:baru,bekas',
            'status_disposisi'  => 'required|in:pending,acc,rejected',
            'keterangan'        => 'nullable|string',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->update($validated);

        return redirect()->route('pengajuan.index')->with('success', 'Data pengajuan barang berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_disposisi' => 'required|in:pending,acc,rejected',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);

        // Perubahan status ini otomatis memicu PengajuanBarangObserver jika bernilai 'acc'
        $pengajuan->update([
            'status_disposisi' => $request->status_disposisi,
        ]);

        return redirect()->back()->with('success', 'Status disposisi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Pengajuan barang berhasil dihapus!');
    }

    public function exportCsv(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $data = PengajuanBarang::with('barang')
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->latest()
            ->get();

        $fileName = "laporan_pengajuan_barang_{$tahun}_{$bulan}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal Pengajuan', 'Nama Barang', 'Volume', 'Harga/Unit', 'Total Harga', 'Permintaan (Divisi)', 'Status Barang', 'Status Disposisi', 'Link SPB/Invoice', 'Keterangan'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $item) {
                fputcsv($file, [
                    $item->tanggal_pengajuan,
                    $item->barang->nama_barang ?? '-',
                    $item->volume,
                    $item->harga_per_unit,
                    $item->volume * $item->harga_per_unit,
                    $item->permintaan,
                    strtoupper($item->status_barang),
                    strtoupper($item->status_disposisi),
                    $item->link_spb_invoice ?? '-',
                    $item->keterangan ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

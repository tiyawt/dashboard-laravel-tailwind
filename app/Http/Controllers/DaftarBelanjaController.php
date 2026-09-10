<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DaftarBelanjaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil pengajuan yang statusnya 'acc' beserta relasi barang & penerimaannya
        $daftarBelanja = PengajuanBarang::with(['barang', 'penerimaan'])
            ->where('status_disposisi', 'acc')
            ->when($search, function ($query, $search) {
                $normalizedSearch = '%' . Str::lower(trim($search)) . '%';

                return $query->where(function ($query) use ($normalizedSearch) {
                    $query->whereHas('barang', function ($q) use ($normalizedSearch) {
                        $q->whereRaw('LOWER(nama_barang) LIKE ?', [$normalizedSearch]);
                    })->orWhereRaw('LOWER(permintaan) LIKE ?', [$normalizedSearch]);
                });
            })
            ->latest()
            ->paginate(10);

        return view('pages.daftarBelanja.daftar-belanja', compact('daftarBelanja'));
    }

    public function exportCsv(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $data = PengajuanBarang::with(['barang', 'penerimaan'])
            ->where('status_disposisi', 'acc')
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->latest()
            ->get();

        $fileName = "laporan_daftar_belanja_{$tahun}_{$bulan}.xlsx";
        $columns = ['Nama Barang', 'Tgl Pengajuan', 'Divisi Permintaan', 'Volume Dibutuhkan', 'Total Diterima', 'Selisih Kekurangan', 'Harga/Unit', 'Perkiraan Biaya Kurang', 'Status Pemenuhan'];
        $formatDate = static fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '-';

        $rows = $data->map(function ($item) use ($formatDate) {
            $selisih = $item->selisih;
            $biayaKurang = $selisih < 0 ? abs($selisih) * $item->harga_per_unit : 0;
            $statusPemenuhan = $selisih < 0 ? 'BELUM LENGKAP' : 'LENGKAP/LUNAS';

            return [
                $item->barang?->nama_barang ?? '-',
                $formatDate($item->tanggal_pengajuan),
                $item->permintaan,
                $item->volume,
                $item->total_diterima,
                $selisih,
                $item->harga_per_unit,
                $biayaKurang,
                $statusPemenuhan,
            ];
        })->all();

        return SimpleXlsxExporter::download($columns, $rows, $fileName, 'Daftar Belanja');
    }
}

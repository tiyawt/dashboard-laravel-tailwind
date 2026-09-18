<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use App\Services\SimpleXlsxExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DaftarBelanjaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $totalDiterima = DB::table('penerimaan_barang')
            ->selectRaw('COALESCE(SUM(jumlah_diterima), 0)')
            ->whereColumn('pengajuan_barang_id', 'pengajuan_barang.id');

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
            ->orderByRaw("CASE WHEN ({$totalDiterima->toSql()}) < pengajuan_barang.volume THEN 0 ELSE 1 END", $totalDiterima->getBindings())
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pages.daftarBelanja.daftar-belanja', compact('daftarBelanja'));
    }

    public function export(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $format = $request->input('format', 'pdf');

        $totalDiterima = DB::table('penerimaan_barang')
            ->selectRaw('COALESCE(SUM(jumlah_diterima), 0)')
            ->whereColumn('pengajuan_barang_id', 'pengajuan_barang.id');

        $data = PengajuanBarang::with(['barang', 'penerimaan'])
            ->where('status_disposisi', 'acc')
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->orderByRaw(
                "CASE
                WHEN ({$totalDiterima->toSql()}) < pengajuan_barang.volume THEN 0
                ELSE 1
            END",
                $totalDiterima->getBindings()
            )
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->get();

        $data->each(function ($item) {
            $item->biayaKurang = $item->selisih < 0
                ? abs($item->selisih) * $item->harga_per_unit
                : 0;

            $item->statusPemenuhan = $item->selisih < 0
                ? 'BELUM LENGKAP'
                : 'LENGKAP/LUNAS';
        });

        // PDF
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pages.daftarBelanja.daftar-belanja-pdf', [
                'data' => $data,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            return $pdf->download(
                "laporan_daftar_belanja_{$tahun}_{$bulan}.pdf"
            );
        }

        // Excel
        if (in_array($format, ['excel', 'xlsx'], true)) {
            $columns = [
                'Nama Barang',
                'Tgl Pengajuan',
                'Divisi Permintaan',
                'Volume Dibutuhkan',
                'Total Diterima',
                'Selisih Kekurangan',
                'Harga/Unit',
                'Perkiraan Biaya Kurang',
                'Status Pemenuhan'
            ];

            $formatDate = static fn($date) =>
            $date
                ? \Carbon\Carbon::parse($date)->format('d/m/Y')
                : '-';

            $rows = $data->map(function ($item) use ($formatDate) {
                return [
                    $item->barang?->nama_barang ?? '-',
                    $formatDate($item->tanggal_pengajuan),
                    $item->permintaan,
                    $item->volume,
                    $item->total_diterima,
                    $item->selisih,
                    $item->harga_per_unit,
                    $item->biayaKurang,
                    $item->statusPemenuhan,
                ];
            })->all();

            return SimpleXlsxExporter::download(
                $columns,
                $rows,
                "laporan_daftar_belanja_{$tahun}_{$bulan}.xlsx",
                'Daftar Belanja'
            );
        }
    }
}

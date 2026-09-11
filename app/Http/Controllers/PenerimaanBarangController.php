<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanBarang;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenerimaanBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $totalPenerimaan = PenerimaanBarang::query()
            ->select('pengajuan_barang_id')
            ->selectRaw('SUM(jumlah_diterima) as total_diterima')
            ->groupBy('pengajuan_barang_id');

        $penerimaans = PenerimaanBarang::query()
            ->select('penerimaan_barang.*')
            ->join('pengajuan_barang', 'pengajuan_barang.id', '=', 'penerimaan_barang.pengajuan_barang_id')
            ->leftJoinSub($totalPenerimaan, 'total_penerimaan', function ($join) {
                $join->on(
                    'total_penerimaan.pengajuan_barang_id',
                    '=',
                    'penerimaan_barang.pengajuan_barang_id'
                );
            })
            ->with(['pengajuan.barang'])
            ->when($search, function ($query, $search) {
                $normalizedSearch = '%' . Str::lower(trim($search)) . '%';

                return $query->where(function ($query) use ($normalizedSearch) {
                    $query->whereHas('pengajuan.barang', function ($q) use ($normalizedSearch) {
                        $q->whereRaw(
                            'LOWER(nama_barang) LIKE ?',
                            [$normalizedSearch]
                        );
                    })->orWhereRaw(
                        'LOWER(penerima) LIKE ?',
                        [$normalizedSearch]
                    );
                });
            })
            ->orderByRaw('CASE WHEN COALESCE(total_penerimaan.total_diterima, 0) >= pengajuan_barang.volume THEN 1 ELSE 0 END')
            ->orderBy('pengajuan_barang.tanggal_pengajuan')
            ->orderBy('penerimaan_barang.id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'pages.penerimaanBarang.penerimaan-barang',
            compact('penerimaans')
        );
    }

    /**
     * Edit penerimaan barang
     */
    public function edit($id)
    {
        $penerimaan = PenerimaanBarang::with('pengajuan.barang')
            ->findOrFail($id);

        $pengajuan = $penerimaan->pengajuan;

        // Batas tambahan dihitung dari seluruh penerimaan yang sudah tercatat.
        $sudahDiterima = $pengajuan->penerimaan()->sum('jumlah_diterima');

        // Total pengajuan - total yang sudah diterima.
        $sisaPengajuan = max(
            0,
            $pengajuan->volume - $sudahDiterima
        );

        if ($sisaPengajuan === 0) {
            return redirect()
                ->route('penerimaan.index')
                ->with('info', 'Pengajuan ini sudah terpenuhi seluruhnya dan tidak dapat diedit lagi.');
        }

        return view(
            'pages.penerimaanBarang.penerimaan-barang-edit',
            compact(
                'penerimaan',
                'sudahDiterima',
                'sisaPengajuan'
            )
        );
    }

    /**
     * Update penerimaan barang
     */
    public function update(Request $request, $id)
    {
        $penerimaan = PenerimaanBarang::with('pengajuan.barang')
            ->findOrFail($id);

        $pengajuan = $penerimaan->pengajuan;

        // Nilai yang dikirim adalah tambahan penerimaan, bukan pengganti nilai lama.
        $sudahDiterima = $pengajuan->penerimaan()->sum('jumlah_diterima');

        // Sisa = total pengajuan - sudah diterima sebelumnya
        $sisaPengajuan = max(
            0,
            $pengajuan->volume - $sudahDiterima
        );

        $validated = $request->validate([
            'tanggal_pengambilan' => 'required|date',

            'jumlah_diterima' => [
                'required',
                'integer',
                'min:1',
                "max:{$sisaPengajuan}",
            ],

            'penerima' => 'required|string|max:255',

            'keterangan' => 'nullable|string',
        ], [
            'jumlah_diterima.required' =>
            'Jumlah diterima wajib diisi.',

            'jumlah_diterima.min' =>
            'Jumlah diterima minimal 1 item.',

            'jumlah_diterima.max' =>
            "Jumlah diterima tidak boleh melebihi sisa pengajuan ({$sisaPengajuan} {$pengajuan->barang->satuan}).",
        ]);

        $penerimaan->update([
            'tanggal_pengambilan' => $validated['tanggal_pengambilan'],
            'jumlah_diterima' => $penerimaan->jumlah_diterima + $validated['jumlah_diterima'],
            'penerima' => $validated['penerima'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('penerimaan.index')
            ->with(
                'success',
                'Jumlah penerimaan barang berhasil ditambahkan!'
            );
    }

    /**
     * Hapus penerimaan barang
     */
    public function destroy($id)
    {
        $penerimaan = PenerimaanBarang::findOrFail($id);

        $penerimaan->delete();

        return redirect()
            ->route('penerimaan.index')
            ->with(
                'success',
                'Data penerimaan berhasil dihapus!'
            );
    }

    /**
     * Export laporan penerimaan barang
     */
    public function exportCsv(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $data = PenerimaanBarang::with(['pengajuan.barang'])
            ->whereMonth('tanggal_pengambilan', $bulan)
            ->whereYear('tanggal_pengambilan', $tahun)
            ->latest()
            ->get();

        $fileName = "laporan_penerimaan_barang_{$tahun}_{$bulan}.xlsx";

        $columns = [
            'Nama Barang',
            'Tgl Pengajuan',
            'Status Kondisi',
            'Divisi Permintaan',
            'Total Pengajuan',
            'Sudah Diterima',
            'Sisa',
            'Tgl Pengambilan',
            'Jumlah Diterima',
            'Penerima',
            'Keterangan',
        ];

        $formatDate = static function ($date) {
            return $date
                ? \Carbon\Carbon::parse($date)->format('d/m/Y')
                : '-';
        };

        $rows = $data->map(function ($item) use ($formatDate) {
            $pengajuan = $item->pengajuan;

            $totalPengajuan = $pengajuan?->volume ?? 0;

            $sudahDiterima = PenerimaanBarang::where(
                'pengajuan_barang_id',
                $item->pengajuan_barang_id
            )->sum('jumlah_diterima');

            $sisa = max(
                0,
                $totalPengajuan - $sudahDiterima
            );

            return [
                $pengajuan?->barang?->nama_barang ?? '-',
                $formatDate($pengajuan?->tanggal_pengajuan),
                strtoupper($pengajuan?->status_barang ?? '-'),
                $pengajuan?->permintaan ?? '-',
                $totalPengajuan,
                $sudahDiterima,
                $sisa,
                $formatDate($item->tanggal_pengambilan),
                $item->jumlah_diterima,
                $item->penerima ?? '-',
                $item->keterangan ?? '-',
            ];
        })->all();

        return SimpleXlsxExporter::download(
            $columns,
            $rows,
            $fileName,
            'Penerimaan Barang'
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\PengajuanBarang;
use App\Models\MinimalStock;
use App\Models\PenerimaanBarang;
use App\Models\StokKeluarLemari;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Ringkasan Metrik
        $totalMasterBarang = MasterBarang::count();
        $pengajuanPending  = PengajuanBarang::where('status_disposisi', 'pending')->count();

        // Ambil data stok minimal untuk menghitung stok alert
        $minimalStocks = MinimalStock::with('barang')->get();
        $stokHabisCount  = $minimalStocks->where('status_alert', 'habis')->count();
        $stokMenipisCount = $minimalStocks->where('status_alert', 'menipis')->count();

        // 2. Data Tabel Alert Stok (Kondisi Habis / Menipis)
        $alertBarangs = $minimalStocks
            ->filter(function ($item) {
                return in_array($item->status_alert, ['habis', 'menipis']);
            })
            ->sortBy(function ($item) {
                return $item->status_alert === 'habis' ? 0 : 1;
            })
            ->take(5);

        // 3. Data Pengajuan Terbaru
        $pengajuanTerbaru = PengajuanBarang::with('barang')->latest()->take(5)->get();

        // 4. Riwayat Barang Keluar Terbaru
        $barangKeluarTerbaru = StokKeluarLemari::with('barang')->where('status', 'done')->latest()->take(5)->get();

        return view('pages.dashboard.dashboard', compact(
            'totalMasterBarang',
            'pengajuanPending',
            'stokHabisCount',
            'stokMenipisCount',
            'alertBarangs',
            'pengajuanTerbaru',
            'barangKeluarTerbaru'
        ));
    }
}

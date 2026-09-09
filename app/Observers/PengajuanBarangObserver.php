<?php

namespace App\Observers;

use App\Models\PengajuanBarang;
use App\Models\PenerimaanBarang;

class PengajuanBarangObserver
{
    /**
     * Terpanggil saat pertama kali Pengajuan Baru dibuat
     */
    public function created(PengajuanBarang $pengajuanBarang): void
    {
        // Jika saat dibuat langsung diset status 'acc'
        if ($pengajuanBarang->status_disposisi === 'acc') {
            $this->buatDrafPenerimaan($pengajuanBarang->id);
        }
    }

    /**
     * Terpanggil saat data Pengajuan diedit/diperbarui
     */
    public function updated(PengajuanBarang $pengajuanBarang): void
    {
        // Jika status_disposisi diubah menjadi 'acc'
        if ($pengajuanBarang->isDirty('status_disposisi') && $pengajuanBarang->status_disposisi === 'acc') {
            $this->buatDrafPenerimaan($pengajuanBarang->id);
        }
    }

    /**
     * Helper privat biar kodenya rapi dan nggak berulang
     */
    private function buatDrafPenerimaan($pengajuanId): void
    {
        $exists = PenerimaanBarang::where('pengajuan_barang_id', $pengajuanId)->exists();

        if (!$exists) {
            PenerimaanBarang::create([
                'pengajuan_barang_id' => $pengajuanId,
                'jumlah_diterima'     => 0,
            ]);
        }
    }
}

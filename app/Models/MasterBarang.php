<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'master_barang';
    protected $guarded = ['id'];

    // --- RELASI ---
    public function pengajuan(): HasMany
    {
        return $this->hasMany(PengajuanBarang::class, 'barang_id');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluarLemari::class, 'barang_id');
    }

    public function minimalStock(): HasOne
    {
        return $this->hasOne(MinimalStock::class, 'barang_id');
    }

    // --- ACCESSOR / KALKULASI OTOMATIS ---

    // 1. Total Barang Diterima (Dari seluruh pengajuan yang berstatus 'acc')
    public function getTotalDiterimaAttribute(): int
    {
        return (int) PenerimaanBarang::query()
            ->whereHas('pengajuan', function ($query) {
                $query->where('barang_id', $this->id)
                    ->where('status_disposisi', 'acc');
            })
            ->sum('jumlah_diterima');
    }

    // 2. Total Barang Keluar (Hanya yang berstatus 'done')
    public function getTotalKeluarAttribute(): int
    {
        return (int) $this->stokKeluar()
            ->where('status', 'done')
            ->sum('jumlah');
    }

    // 3. Jumlah Stock Fisik Saat Ini
    public function getJumlahStockAttribute(): int
    {
        return $this->total_diterima - $this->total_keluar;
    }
}

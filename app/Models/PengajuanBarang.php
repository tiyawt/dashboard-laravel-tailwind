<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';
    protected $guarded = ['id'];

    // --- RELASI ---
    public function barang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    public function penerimaan(): HasMany
    {
        return $this->hasMany(PenerimaanBarang::class, 'pengajuan_barang_id');
    }

    // --- ACCESSOR / KALKULASI OTOMATIS ---

    // Total Diterima khusus untuk baris pengajuan ini
    public function getTotalDiterimaAttribute(): int
    {
        return (int) $this->penerimaan()->sum('jumlah_diterima');
    }

    // Selisih = Total Diterima - Volume Pengajuan
    // (Negatif = kurang/belum dibeli, 0 = pas/lengkap, Positif = kelebihan)
    public function getSelisihAttribute(): int
    {
        return $this->total_diterima - $this->volume;
    }
}
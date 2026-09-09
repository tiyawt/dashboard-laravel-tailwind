<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinimalStock extends Model
{
    use HasFactory;

    protected $table = 'minimal_stock';
    protected $guarded = ['id'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class, 'barang_id');
    }

    // --- ACCESSOR / KALKULASI OTOMATIS ---

    // Ambil jumlah stock dari MasterBarang
    public function getJumlahStockAttribute(): int
    {
        return $this->barang ? $this->barang->jumlah_stock : 0;
    }

    // Selisih = Jumlah Stock - Batas Minimal
    public function getSelisihAttribute(): int
    {
        return $this->jumlah_stock - $this->minimal;
    }

    // Status Alert Badge (Habis / Menipis / Aman)
    public function getStatusAlertAttribute(): string
    {
        $stok = $this->jumlah_stock;
        $min = $this->minimal;

        if ($stok <= 0) {
            return 'habis';   // Warna Merah
        } elseif ($stok <= $min) {
            return 'menipis'; // Warna Kuning / Orange
        }

        return 'aman';        // Warna Hijau
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerimaanBarang extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_barang';
    protected $guarded = ['id'];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBarang::class, 'pengajuan_barang_id');
    }
}
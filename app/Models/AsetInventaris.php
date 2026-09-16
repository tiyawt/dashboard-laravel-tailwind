<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetInventaris extends Model
{
    use HasFactory;

    protected $table = 'aset_inventaris';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_entry' => 'date',
        'tanggal_nonaktif' => 'date',
    ];

    public function getUserAttribute(): ?string
    {
        return $this->attributes['nama_user'] ?? null;
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class)->latest('tanggal');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AsetHistory::class, 'aset_inventaris_id')
            ->latest('tanggal')
            ->latest('id');
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(MasterLokasi::class, 'gedung_id');
    }

    public function lantaiMaster(): BelongsTo
    {
        return $this->belongsTo(MasterLokasi::class, 'lantai_id');
    }

    public function lokasiMaster(): BelongsTo
    {
        return $this->belongsTo(MasterLokasi::class, 'lokasi_id');
    }
}

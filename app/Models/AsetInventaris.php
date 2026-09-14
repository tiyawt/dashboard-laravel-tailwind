<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsetInventaris extends Model
{
    use HasFactory;

    protected $table = 'aset_inventaris';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_entry' => 'date',
    ];

    public function getUserAttribute(): ?string
    {
        return $this->attributes['nama_user'] ?? null;
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class)->latest('tanggal');
    }
}

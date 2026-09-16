<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterLokasi extends Model
{
    protected $table = 'master_lokasi';
    protected $guarded = ['id'];

    public function asetAsGedung(): HasMany
    {
        return $this->hasMany(AsetInventaris::class, 'gedung_id');
    }

    public function asetAsLantai(): HasMany
    {
        return $this->hasMany(AsetInventaris::class, 'lantai_id');
    }

    public function asetAsLokasi(): HasMany
    {
        return $this->hasMany(AsetInventaris::class, 'lokasi_id');
    }
}

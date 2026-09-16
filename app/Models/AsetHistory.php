<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetHistory extends Model
{
    protected $table = 'aset_histories';
    protected $guarded = ['id'];

    protected $casts = ['tanggal' => 'date'];

    public function aset(): BelongsTo
    {
        return $this->belongsTo(AsetInventaris::class, 'aset_inventaris_id');
    }
}

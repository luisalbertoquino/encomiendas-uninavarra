<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncomiendaHistorial extends Model
{
    protected $table = 'encomienda_historial';

    protected $fillable = ['encomienda_id', 'estado', 'por', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function encomienda(): BelongsTo
    {
        return $this->belongsTo(Encomienda::class);
    }
}

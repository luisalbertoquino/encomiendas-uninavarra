<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interesado extends Model
{
    protected $table = 'interesados';

    protected $fillable = ['nombre', 'cedula', 'correo'];

    public function encomiendas(): HasMany
    {
        return $this->hasMany(Encomienda::class);
    }
}

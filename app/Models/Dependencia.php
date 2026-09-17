<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dependencia extends Model
{
    protected $fillable = ['nombre', 'correo', 'nombre_interesado', 'cedula_interesado', 'enlace_drive'];

    public function encomiendas(): HasMany
    {
        return $this->hasMany(Encomienda::class);
    }
}

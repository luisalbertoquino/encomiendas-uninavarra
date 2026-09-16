<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colaborador extends Model
{
    protected $table = 'colaboradores';

    protected $fillable = ['nombre', 'cedula', 'correo'];

    public function encomiendas(): HasMany
    {
        return $this->hasMany(Encomienda::class);
    }
}

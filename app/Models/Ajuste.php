<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $fillable = ['estacion', 'prefijo', 'url'];

    public static function actual(): self
    {
        return static::query()->firstOrCreate([]);
    }
}

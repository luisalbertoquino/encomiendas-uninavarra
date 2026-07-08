<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Encomienda extends Model
{
    protected $fillable = [
        'codigo', 'fecha', 'tipo', 'descripcion', 'remitente', 'guia',
        'recibe', 'interesado', 'dependencia_id', 'whatsapp', 'correo',
        'obs', 'documento_interesado', 'enlace_drive',
        'estado', 'entregado_a', 'fecha_entrega',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(EncomiendaHistorial::class)->orderBy('fecha');
    }

    public static function generarCodigo(string $prefijo, \DateTimeInterface $fecha): string
    {
        $stamp = $fecha->format('Ymd');

        return DB::transaction(function () use ($prefijo, $stamp) {
            $consecutivo = static::where('codigo', 'like', "{$prefijo}-{$stamp}-%")
                ->lockForUpdate()
                ->count();

            return sprintf('%s-%s-%02d', $prefijo, $stamp, $consecutivo + 1);
        });
    }

    public function normalizarWhatsapp(): ?string
    {
        $digits = preg_replace('/\D/', '', $this->whatsapp ?? '');
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 10) {
            $digits = '57'.$digits;
        }

        return $digits;
    }

    public function mensajeAviso(string $estacion): string
    {
        return sprintf(
            'Hola %s, le informamos desde UNINAVARRA (%s) que llegó su encomienda: %s. '.
            'Código de seguimiento: %s. Recibida el %s por %s. '.
            'Puede reclamarla en recepción. — UNINAVARRA',
            $this->interesado,
            $estacion,
            $this->descripcion,
            $this->codigo,
            $this->fecha->format('d/m/Y H:i'),
            $this->recibe
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'inscripcion_id',
        'monto',
        'metodo',
        'referencia_qr',
        'estado',
        'observacion',
        'revisado_por',
        'fecha_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto'      => 'decimal:2',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public static function generarReferenciaQR(int $inscripcionId): string
    {
        return 'QR-CUP-' . strtoupper(substr(md5($inscripcionId . now()), 0, 8)) . '-' . $inscripcionId;
    }
}
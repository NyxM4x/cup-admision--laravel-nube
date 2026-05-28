<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class Bitacora extends Model
{
    protected $table = 'bitacora';

    // Solo manejamos created_at manualmente, no updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'descripcion',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relación: un registro de bitácora pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── INMUTABILIDAD: nadie puede editar ni eliminar ───────────────────

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('Los registros de bitácora son inmutables y no pueden modificarse.');
    }

    public function delete(): ?bool
    {
        throw new RuntimeException('Los registros de bitácora son inmutables y no pueden eliminarse.');
    }

    protected static function boot(): void
    {
        parent::boot();

        // Bloquear también actualizaciones y eliminaciones masivas (query builder)
        static::updating(fn() => throw new RuntimeException('Los registros de bitácora son inmutables.'));
        static::deleting(fn() => throw new RuntimeException('Los registros de bitácora no pueden eliminarse.'));
    }
}
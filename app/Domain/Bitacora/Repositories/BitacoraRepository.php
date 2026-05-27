<?php

namespace App\Domain\Bitacora\Repositories;

use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Builder;

class BitacoraRepository
{
    /**
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = []): Builder
    {
        $query = Bitacora::query()->with('user:id,name,email');

        if (! empty($filtros['user_id'])) {
            $query->where('user_id', $filtros['user_id']);
        }

        if (! empty($filtros['modulo'])) {
            $query->where('modulo', $filtros['modulo']);
        }

        if (! empty($filtros['accion'])) {
            $query->where('accion', $filtros['accion']);
        }

        if (! empty($filtros['ip'])) {
            $query->where('ip', 'ilike', '%'.$filtros['ip'].'%');
        }

        if (! empty($filtros['q'])) {
            $query->where('descripcion', 'ilike', '%'.$filtros['q'].'%');
        }

        if (! empty($filtros['fecha_desde'])) {
            $query->where('created_at', '>=', $filtros['fecha_desde'].' 00:00:00');
        }
        if (! empty($filtros['fecha_hasta'])) {
            $query->where('created_at', '<=', $filtros['fecha_hasta'].' 23:59:59');
        }

        return $query->orderBy('id', 'desc');
    }

    public function obtenerPorId(int $id): ?Bitacora
    {
        return Bitacora::with('user')->find($id);
    }

    /**
     * @return array<int, string>
     */
    public function modulosDisponibles(): array
    {
        return Bitacora::distinct()->pluck('modulo')->filter()->sort()->values()->toArray();
    }

    /**
     * @return array<int, string>
     */
    public function accionesDisponibles(): array
    {
        return Bitacora::distinct()->pluck('accion')->filter()->sort()->values()->toArray();
    }

    public function totalRegistros(): int
    {
        return Bitacora::count();
    }

    public function totalHoy(): int
    {
        return Bitacora::whereDate('created_at', now()->toDateString())->count();
    }

    public function totalUltimas24h(): int
    {
        return Bitacora::where('created_at', '>=', now()->subHours(24))->count();
    }

    public function eventosCriticosUltimas24h(): int
    {
        // LOGIN_FAIL, LOGIN_INACTIVO, ACCESO_DENEGADO se consideran críticos
        return Bitacora::whereIn('accion', ['LOGIN_FAIL', 'LOGIN_INACTIVO', 'ACCESO_DENEGADO'])
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
    }
}

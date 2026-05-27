<?php

namespace App\Domain\GestionGlobal\Aulas\Repositories;

use App\Models\Aula;
use Illuminate\Database\Eloquent\Builder;

class AulaRepository
{
    /**
     * @param  string  $estado  activos | inactivos | todos
     */
    public function listar(?string $q = null, string $estado = 'activos', ?string $edificio = null): Builder
    {
        $query = Aula::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'ilike', "%{$q}%")
                    ->orWhere('edificio', 'ilike', "%{$q}%")
                    ->orWhere('equipamiento', 'ilike', "%{$q}%");
            });
        }

        if ($edificio) {
            $query->where('edificio', $edificio);
        }

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        return $query->orderBy('edificio')->orderBy('codigo');
    }

    public function obtenerPorId(int $id): ?Aula
    {
        return Aula::find($id);
    }

    public function codigoExiste(string $codigo, ?int $exceptoId = null): bool
    {
        $query = Aula::where('codigo', $codigo);
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }

        return $query->exists();
    }

    /**
     * @return array<int, string>
     */
    public function edificiosDisponibles(): array
    {
        return Aula::distinct()->orderBy('edificio')->pluck('edificio')->filter()->toArray();
    }

    public function setActivo(Aula $aula, bool $activo): void
    {
        $aula->activo = $activo;
        $aula->save();
    }

    public function contarActivos(): int
    {
        return Aula::where('activo', true)->count();
    }

    public function capacidadTotalActiva(): int
    {
        return (int) Aula::where('activo', true)->sum('capacidad');
    }
}

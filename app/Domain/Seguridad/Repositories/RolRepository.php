<?php

namespace App\Domain\Seguridad\Repositories;

use App\Models\Rol;
use Illuminate\Database\Eloquent\Builder;

class RolRepository
{
    /**
     * @param  string  $estado  activos | inactivos | todos
     */
    public function listar(?string $q = null, string $estado = 'activos'): Builder
    {
        $query = Rol::query()->withCount('usuarios')->withCount('permisos');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        return $query->orderBy('nombre');
    }

    public function obtenerPorId(int $id): ?Rol
    {
        return Rol::with('permisos')->find($id);
    }

    public function nombreExiste(string $nombre, ?int $exceptoId = null): bool
    {
        $query = Rol::where('nombre', $nombre);
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }

        return $query->exists();
    }

    public function tieneUsuariosActivos(Rol $rol): bool
    {
        return $rol->usuarios()->where('activo', true)->exists();
    }

    public function setActivo(Rol $rol, bool $activo): void
    {
        $rol->activo = $activo;
        $rol->save();
    }

    public function sincronizarPermisos(Rol $rol, array $permisoIds): void
    {
        $rol->permisos()->sync($permisoIds);
    }
}

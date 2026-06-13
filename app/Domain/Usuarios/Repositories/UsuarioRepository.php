<?php

namespace App\Domain\Usuarios\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UsuarioRepository
{
    /**
     * Roles que SÍ aparecen en la gestión de usuarios.
     * Los postulantes se gestionan desde el módulo de Postulantes, no aquí.
     */
    private const ROLES_VISIBLES = [
        'Administrador',
        'Coordinador CUP',
        'Docente',
        'Auditor',
    ];

    public function listar(?string $q = null, string $estado = 'activos'): Builder
    {
        $query = User::query()
            ->with('rol')
            // Excluir postulantes de esta vista
            ->whereHas('rol', fn ($r) =>
                $r->whereIn('nombre', self::ROLES_VISIBLES)
            );

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%")
                    ->orWhere('ci', 'ilike', "%{$q}%");
            });
        }

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        return $query->orderBy('name');
    }

    public function obtenerPorId(int $id): ?User
    {
        return User::with('rol')->find($id);
    }

    public function emailExiste(string $email, ?int $exceptoId = null): bool
    {
        $query = User::where('email', $email);
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        return $query->exists();
    }

    public function ciExiste(?string $ci, ?int $exceptoId = null): bool
    {
        if (! $ci) return false;
        $query = User::where('ci', $ci);
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        return $query->exists();
    }

    public function contarAdministradoresActivos(): int
    {
        return User::whereHas('rol', fn ($q) => $q->where('nombre', 'Administrador'))
            ->where('activo', true)
            ->count();
    }

    public function setActivo(User $user, bool $activo): void
    {
        $user->activo = $activo;
        $user->save();
    }
}
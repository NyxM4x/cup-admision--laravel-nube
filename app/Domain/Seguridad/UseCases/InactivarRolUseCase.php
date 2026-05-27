<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Seguridad\Repositories\RolRepository;
use App\Models\Rol;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class InactivarRolUseCase
{
    public function __construct(private RolRepository $repo) {}

    public function ejecutar(int $rolId): Rol
    {
        $rol = $this->repo->obtenerPorId($rolId);
        if (! $rol) {
            throw (new ModelNotFoundException())->setModel(Rol::class, [$rolId]);
        }

        // No inactivar el rol del sistema
        if ($rol->nombre === 'Administrador') {
            throw ValidationException::withMessages([
                'rol' => 'No se puede inactivar el rol Administrador.',
            ]);
        }

        // No inactivar un rol con usuarios activos
        if ($this->repo->tieneUsuariosActivos($rol)) {
            throw ValidationException::withMessages([
                'rol' => 'No se puede inactivar el rol porque tiene usuarios activos asignados. Reasigná esos usuarios primero.',
            ]);
        }

        $this->repo->setActivo($rol, false);

        BitacoraLogger::registrar(
            'ROL_INACTIVADO',
            'Seguridad',
            'Rol inactivado: '.$rol->nombre
        );

        return $rol;
    }
}

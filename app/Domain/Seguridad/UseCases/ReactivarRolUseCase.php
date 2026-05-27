<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Seguridad\Repositories\RolRepository;
use App\Models\Rol;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReactivarRolUseCase
{
    public function __construct(private RolRepository $repo) {}

    public function ejecutar(int $rolId): Rol
    {
        $rol = $this->repo->obtenerPorId($rolId);
        if (! $rol) {
            throw (new ModelNotFoundException())->setModel(Rol::class, [$rolId]);
        }

        $this->repo->setActivo($rol, true);

        BitacoraLogger::registrar(
            'ROL_REACTIVADO',
            'Seguridad',
            'Rol reactivado: '.$rol->nombre
        );

        return $rol;
    }
}

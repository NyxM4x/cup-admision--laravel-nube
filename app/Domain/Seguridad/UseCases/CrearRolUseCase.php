<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Seguridad\Repositories\RolRepository;
use App\Models\Rol;
use Illuminate\Validation\ValidationException;

class CrearRolUseCase
{
    public function __construct(private RolRepository $repo) {}

    public function ejecutar(array $datos, array $permisoIds = []): Rol
    {
        if ($this->repo->nombreExiste($datos['nombre'])) {
            throw ValidationException::withMessages([
                'nombre' => 'Ya existe un rol con ese nombre.',
            ]);
        }

        $datos['activo'] = $datos['activo'] ?? true;

        $rol = Rol::create($datos);

        if (! empty($permisoIds)) {
            $this->repo->sincronizarPermisos($rol, $permisoIds);
        }

        BitacoraLogger::registrar(
            'ROL_CREADO',
            'Seguridad',
            'Rol creado: '.$rol->nombre.' con '.count($permisoIds).' permisos'
        );

        return $rol->load('permisos');
    }
}

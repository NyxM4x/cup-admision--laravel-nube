<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Seguridad\Repositories\RolRepository;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EditarRolUseCase
{
    public function __construct(private RolRepository $repo) {}

    public function ejecutar(int $rolId, array $datos, array $permisoIds = []): Rol
    {
        $rol = $this->repo->obtenerPorId($rolId);
        if (! $rol) {
            throw (new ModelNotFoundException())->setModel(Rol::class, [$rolId]);
        }

        $esAdministrador = $rol->nombre === 'Administrador';

        // El rol Administrador no puede cambiar de nombre
        if ($esAdministrador && isset($datos['nombre']) && $datos['nombre'] !== $rol->nombre) {
            throw ValidationException::withMessages([
                'nombre' => 'No se puede modificar el nombre del rol Administrador (rol del sistema).',
            ]);
        }

        // Nombre único (excepto el propio)
        if (isset($datos['nombre']) && $this->repo->nombreExiste($datos['nombre'], $rolId)) {
            throw ValidationException::withMessages([
                'nombre' => 'Ya existe otro rol con ese nombre.',
            ]);
        }

        $rol->update($datos);

        // El rol Administrador SIEMPRE conserva todos los permisos del sistema
        if ($esAdministrador) {
            $totalPermisos = Permiso::count();
            if (count($permisoIds) < $totalPermisos) {
                $permisoIds = Permiso::pluck('id')->all();
            }
        }

        $this->repo->sincronizarPermisos($rol, $permisoIds);

        BitacoraLogger::registrar(
            'ROL_EDITADO',
            'Seguridad',
            'Rol editado: '.$rol->nombre
        );

        return $rol->load('permisos');
    }
}

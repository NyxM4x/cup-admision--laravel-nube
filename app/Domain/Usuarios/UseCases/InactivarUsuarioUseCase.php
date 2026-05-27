<?php

namespace App\Domain\Usuarios\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Usuarios\Repositories\UsuarioRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InactivarUsuarioUseCase
{
    public function __construct(private UsuarioRepository $repo) {}

    public function ejecutar(int $userId): User
    {
        $user = $this->repo->obtenerPorId($userId);
        if (! $user) {
            throw (new ModelNotFoundException())->setModel(User::class, [$userId]);
        }

        // No permitir auto-inactivación
        if ($userId === Auth::id()) {
            throw ValidationException::withMessages([
                'usuario' => 'No podés inactivar tu propia cuenta mientras estás logueado.',
            ]);
        }

        // No permitir inactivar al último administrador activo
        $esAdminActivo = $user->rol && $user->rol->nombre === 'Administrador' && $user->activo;
        if ($esAdminActivo && $this->repo->contarAdministradoresActivos() === 1) {
            throw ValidationException::withMessages([
                'usuario' => 'No se puede inactivar al último administrador activo del sistema.',
            ]);
        }

        $this->repo->setActivo($user, false);

        BitacoraLogger::registrar(
            'USUARIO_INACTIVADO',
            'Seguridad',
            'Usuario inactivado: '.$user->email
        );

        return $user;
    }
}

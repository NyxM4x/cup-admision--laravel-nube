<?php

namespace App\Domain\Usuarios\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\Usuarios\Repositories\UsuarioRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReactivarUsuarioUseCase
{
    public function __construct(private UsuarioRepository $repo) {}

    public function ejecutar(int $userId): User
    {
        $user = $this->repo->obtenerPorId($userId);
        if (! $user) {
            throw (new ModelNotFoundException())->setModel(User::class, [$userId]);
        }

        $this->repo->setActivo($user, true);
        $user->failed_logins = 0;
        $user->bloqueado_hasta = null;
        $user->save();

        BitacoraLogger::registrar(
            'USUARIO_REACTIVADO',
            'Seguridad',
            'Usuario reactivado/desbloqueado: '.$user->email
        );

        return $user;
    }
}

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

        BitacoraLogger::registrar(
            'USUARIO_REACTIVADO',
            'Seguridad',
            'Usuario reactivado: '.$user->email
        );

        return $user;
    }
}

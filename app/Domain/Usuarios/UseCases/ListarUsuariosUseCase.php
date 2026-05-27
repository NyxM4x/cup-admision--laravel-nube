<?php

namespace App\Domain\Usuarios\UseCases;

use App\Domain\Usuarios\Repositories\UsuarioRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListarUsuariosUseCase
{
    public function __construct(private UsuarioRepository $repo) {}

    public function ejecutar(?string $q, string $estado, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->listar($q, $estado)->paginate($perPage);
    }
}

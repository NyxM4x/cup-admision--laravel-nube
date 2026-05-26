<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Seguridad\Repositories\RolRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListarRolesUseCase
{
    public function __construct(private RolRepository $repo) {}

    public function ejecutar(?string $q, string $estado, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->listar($q, $estado)->paginate($perPage);
    }
}

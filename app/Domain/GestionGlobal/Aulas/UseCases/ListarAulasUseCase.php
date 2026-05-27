<?php

namespace App\Domain\GestionGlobal\Aulas\UseCases;

use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListarAulasUseCase
{
    public function __construct(private AulaRepository $repo) {}

    public function ejecutar(?string $q, string $estado, ?string $edificio, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->listar($q, $estado, $edificio)->paginate($perPage)->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'total_activas'   => $this->repo->contarActivos(),
            'capacidad_total' => $this->repo->capacidadTotalActiva(),
            'edificios'       => count($this->repo->edificiosDisponibles()),
        ];
    }
}

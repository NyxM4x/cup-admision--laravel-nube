<?php

namespace App\Domain\Bitacora\UseCases;

use App\Domain\Bitacora\Repositories\BitacoraRepository;
use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * CU06 - Visualizar bitácora (read-only).
 *
 * NOTA: consultar la bitácora NO registra en bitácora (evita loop infinito y
 * auditar al auditor no aporta valor en este alcance).
 */
class ConsultarBitacoraUseCase
{
    public function __construct(private BitacoraRepository $repo) {}

    /**
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros, int $perPage = 25): LengthAwarePaginator
    {
        return $this->repo->listar($filtros)->paginate($perPage)->withQueryString();
    }

    public function obtenerDetalle(int $id): ?Bitacora
    {
        return $this->repo->obtenerPorId($id);
    }

    /**
     * @return array<string, int>
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'total'                 => $this->repo->totalRegistros(),
            'hoy'                   => $this->repo->totalHoy(),
            'ultimas_24h'           => $this->repo->totalUltimas24h(),
            'eventos_criticos_24h'  => $this->repo->eventosCriticosUltimas24h(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function obtenerOpcionesFiltros(): array
    {
        return [
            'modulos'  => $this->repo->modulosDisponibles(),
            'acciones' => $this->repo->accionesDisponibles(),
            'usuarios' => User::orderBy('name')->get(['id', 'name', 'email']),
        ];
    }
}

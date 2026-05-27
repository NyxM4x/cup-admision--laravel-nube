<?php

namespace App\Domain\GestionGlobal\Aulas\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use App\Models\Aula;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReactivarAulaUseCase
{
    public function __construct(private AulaRepository $repo) {}

    public function ejecutar(int $aulaId): Aula
    {
        $aula = $this->repo->obtenerPorId($aulaId);
        if (! $aula) {
            throw (new ModelNotFoundException())->setModel(Aula::class, [$aulaId]);
        }

        $this->repo->setActivo($aula, true);

        BitacoraLogger::registrar(
            'AULA_REACTIVADA',
            'GestionGlobal',
            'Aula reactivada: '.$aula->codigo
        );

        return $aula;
    }
}

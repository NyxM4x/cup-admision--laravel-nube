<?php

namespace App\Domain\GestionGlobal\Aulas\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use App\Models\Aula;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InactivarAulaUseCase
{
    public function __construct(private AulaRepository $repo) {}

    public function ejecutar(int $aulaId): Aula
    {
        $aula = $this->repo->obtenerPorId($aulaId);
        if (! $aula) {
            throw (new ModelNotFoundException())->setModel(Aula::class, [$aulaId]);
        }

        // TODO: cuando exista la tabla horarios + asignaciones_aula, validar:
        //   if ($aula->asignacionesActivas()->exists()) {
        //       throw new ValidationException('No se puede inactivar un aula con horarios activos asignados.');
        //   }

        $this->repo->setActivo($aula, false);

        BitacoraLogger::registrar(
            'AULA_INACTIVADA',
            'GestionGlobal',
            'Aula inactivada: '.$aula->codigo
        );

        return $aula;
    }
}

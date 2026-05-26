<?php

namespace App\Domain\GestionGlobal\Aulas\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use App\Models\Aula;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EditarAulaUseCase
{
    public function __construct(private AulaRepository $repo) {}

    public function ejecutar(int $aulaId, array $datos): Aula
    {
        $aula = $this->repo->obtenerPorId($aulaId);
        if (! $aula) {
            throw (new ModelNotFoundException())->setModel(Aula::class, [$aulaId]);
        }

        if (isset($datos['codigo']) && $this->repo->codigoExiste($datos['codigo'], $aulaId)) {
            throw ValidationException::withMessages([
                'codigo' => 'Ya existe otra aula con ese código.',
            ]);
        }

        if (isset($datos['capacidad']) && (int) $datos['capacidad'] <= 0) {
            throw ValidationException::withMessages([
                'capacidad' => 'La capacidad debe ser mayor a 0.',
            ]);
        }

        // Detectar campos cambiados para la bitácora
        $cambiados = [];
        foreach ($datos as $campo => $valor) {
            if ((string) $aula->{$campo} !== (string) $valor) {
                $cambiados[] = $campo;
            }
        }

        $aula->update($datos);

        BitacoraLogger::registrar(
            'AULA_EDITADA',
            'GestionGlobal',
            'Aula editada: '.$aula->codigo.' (campos: '.(empty($cambiados) ? 'ninguno' : implode(', ', $cambiados)).')'
        );

        return $aula;
    }
}

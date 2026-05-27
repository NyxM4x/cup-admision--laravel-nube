<?php

namespace App\Domain\GestionGlobal\Aulas\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use App\Models\Aula;
use Illuminate\Validation\ValidationException;

class CrearAulaUseCase
{
    public function __construct(private AulaRepository $repo) {}

    public function ejecutar(array $datos): Aula
    {
        if ($this->repo->codigoExiste($datos['codigo'])) {
            throw ValidationException::withMessages([
                'codigo' => 'Ya existe un aula con ese código.',
            ]);
        }

        if ((int) ($datos['capacidad'] ?? 0) <= 0) {
            throw ValidationException::withMessages([
                'capacidad' => 'La capacidad debe ser mayor a 0.',
            ]);
        }

        $datos['activo'] = $datos['activo'] ?? true;

        $aula = Aula::create($datos);

        BitacoraLogger::registrar(
            'AULA_CREADA',
            'GestionGlobal',
            'Aula creada: '.$aula->codigo.' en '.$aula->edificio.' (capacidad: '.$aula->capacidad.')'
        );

        return $aula;
    }
}

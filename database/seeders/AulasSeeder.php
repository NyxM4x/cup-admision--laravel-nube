<?php

namespace Database\Seeders;

use App\Models\Aula;
use Illuminate\Database\Seeder;

class AulasSeeder extends Seeder
{
    /**
     * Aulas representativas del CUP. Idempotente (firstOrCreate por código).
     */
    public function run(): void
    {
        $aulas = [
            ['codigo' => 'A-101', 'edificio' => 'Bloque A', 'capacidad' => 70,  'equipamiento' => 'Proyector, pizarra, aire acondicionado'],
            ['codigo' => 'A-102', 'edificio' => 'Bloque A', 'capacidad' => 70,  'equipamiento' => 'Proyector, pizarra'],
            ['codigo' => 'A-201', 'edificio' => 'Bloque A', 'capacidad' => 70,  'equipamiento' => 'Proyector, pizarra'],
            ['codigo' => 'A-202', 'edificio' => 'Bloque A', 'capacidad' => 70,  'equipamiento' => 'Proyector, pizarra'],
            ['codigo' => 'B-101', 'edificio' => 'Bloque B', 'capacidad' => 80,  'equipamiento' => 'Proyector, pizarra, 40 computadoras'],
            ['codigo' => 'B-102', 'edificio' => 'Bloque B', 'capacidad' => 80,  'equipamiento' => 'Proyector, pizarra, 40 computadoras'],
            ['codigo' => 'B-201', 'edificio' => 'Bloque B', 'capacidad' => 50,  'equipamiento' => 'Laboratorio de electrónica'],
            ['codigo' => 'C-301', 'edificio' => 'Bloque C', 'capacidad' => 100, 'equipamiento' => 'Auditorio con proyector y sonido'],
        ];

        foreach ($aulas as $aula) {
            Aula::firstOrCreate(
                ['codigo' => $aula['codigo']],
                array_merge($aula, ['activo' => true])
            );
        }
    }
}

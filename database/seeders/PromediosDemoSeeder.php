<?php

namespace Database\Seeders;

use App\Models\Periodo;
use App\Models\Postulante;
use App\Models\ResultadoAdmision;
use Illuminate\Database\Seeder;

class PromediosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            $this->command->warn('No hay periodo activo; PromediosDemoSeeder omitido.');

            return;
        }

        // Postulantes activos con inscripción en el periodo activo
        $postulantes = Postulante::where('activo', true)
            ->whereHas('inscripciones', fn ($w) => $w->where('periodo_id', $periodo->id))
            ->take(200)
            ->get();

        $creados = 0;
        foreach ($postulantes as $p) {
            $promedio = round(mt_rand(3000, 9500) / 100, 2); // 30.00 – 95.00
            $aprobado = $promedio >= 51;

            ResultadoAdmision::updateOrCreate(
                ['postulante_id' => $p->id, 'periodo_id' => $periodo->id],
                [
                    'promedio_final'  => $promedio,
                    'estado_admision' => $aprobado ? 'aprobado' : 'reprobado',
                ]
            );
            $creados++;
        }

        $this->command->info("PromediosDemoSeeder: {$creados} resultados creados para periodo #{$periodo->id}.");
    }
}

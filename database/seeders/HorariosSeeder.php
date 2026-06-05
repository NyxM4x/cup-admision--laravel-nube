<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorariosSeeder extends Seeder
{
    public function run(): void
    {
        $horarios = [
            ['M1', 'Mañana', 'Lunes,Miércoles,Viernes', '08:00:00', '10:00:00', 'Mañana temprana - LMV'],
            ['M2', 'Mañana', 'Martes,Jueves',           '10:00:00', '12:00:00', 'Mañana - MJ'],
            ['T1', 'Tarde',  'Lunes,Miércoles,Viernes', '14:00:00', '16:00:00', 'Tarde temprana - LMV'],
            ['T2', 'Tarde',  'Martes,Jueves',           '16:00:00', '18:00:00', 'Tarde - MJ'],
            ['N1', 'Noche',  'Lunes,Miércoles,Viernes', '19:00:00', '21:00:00', 'Noche - LMV'],
            ['N2', 'Noche',  'Martes,Jueves',           '19:00:00', '21:00:00', 'Noche - MJ'],
        ];

        foreach ($horarios as [$cod, $turno, $dias, $ini, $fin, $desc]) {
            Horario::updateOrCreate(
                ['codigo' => $cod],
                [
                    'turno'       => $turno,
                    'dias'        => $dias,
                    'hora_inicio' => $ini,
                    'hora_fin'    => $fin,
                    'descripcion' => $desc,
                    'activo'      => true,
                ]
            );
        }

        $this->command->info('   ✓ 6 horarios fijos (M1/M2/T1/T2/N1/N2)');
    }
}

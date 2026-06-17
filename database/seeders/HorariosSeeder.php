<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorariosSeeder extends Seeder
{
    public function run(): void
    {
        $horarios = [
            [
                'codigo'      => 'G-MAÑANA-1',
                'turno'       => 'Mañana',
                'dias'        => 'Lunes,Martes,Miércoles,Jueves,Viernes',
                'hora_inicio' => '07:00:00',
                'hora_fin'    => '12:00:00',
                'descripcion' => 'Turno mañana completo',
            ],
            [
                'codigo'      => 'G-TARDE-1',
                'turno'       => 'Tarde',
                'dias'        => 'Lunes,Martes,Miércoles,Jueves,Viernes',
                'hora_inicio' => '13:00:00',
                'hora_fin'    => '18:00:00',
                'descripcion' => 'Turno tarde completo',
            ],
        ];

        foreach ($horarios as $h) {
            Horario::updateOrCreate(
                ['codigo' => $h['codigo']],
                [
                    'turno'       => $h['turno'],
                    'dias'        => $h['dias'],
                    'hora_inicio' => $h['hora_inicio'],
                    'hora_fin'    => $h['hora_fin'],
                    'descripcion' => $h['descripcion'],
                    'activo'      => true,
                ]
            );
        }

        // Eliminar todos los horarios que no sean los 2 fijos
        Horario::whereNotIn('codigo', ['G-MAÑANA-1', 'G-TARDE-1'])->delete();

        $this->command->info('   ✓ 2 horarios fijos (G-MAÑANA-1 / G-TARDE-1)');
    }
}

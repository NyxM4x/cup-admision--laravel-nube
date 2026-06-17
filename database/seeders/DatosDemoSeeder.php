<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Poblando 500 postulantes demo...');

        // 1. Obtener periodo activo
        $periodoId = DB::table('periodos')->where('activo', true)->orderBy('id')->value('id');
        if (! $periodoId) {
            $this->command->error('❌ No existe periodo activo.');
            return;
        }
        $this->command->info("   ✓ Periodo activo id=$periodoId");

        // 2. Obtener carreras existentes
        $carreraIds = DB::table('carreras')->where('activo', true)->pluck('id')->toArray();
        if (empty($carreraIds)) {
            $this->command->error('❌ No hay carreras activas.');
            return;
        }
        $this->command->info('   ✓ '.count($carreraIds).' carreras encontradas');

// ============= 3) MATERIAS (con días estructurados) =============
        // Limpiar grupo_materias y grupos antes de borrar materias (FK constraint)
        DB::table('grupo_materias')->delete();
        DB::table('grupos')->delete();
        // Esquema real: sigla (no codigo), sin descripcion, dias NOT NULL.
        DB::table('materias')->delete();
        $materias = [
            ['sigla' => 'MAT', 'nombre' => 'Matemáticas', 'dias' => 'LXV', 'dias_dictado' => ['lunes', 'miercoles', 'viernes'], 'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00'],
            ['sigla' => 'FIS', 'nombre' => 'Física',      'dias' => 'MJ',  'dias_dictado' => ['martes', 'jueves'],              'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00'],
            ['sigla' => 'COM', 'nombre' => 'Computación', 'dias' => 'LXV', 'dias_dictado' => ['lunes', 'miercoles', 'viernes'], 'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00'],
            ['sigla' => 'ING', 'nombre' => 'Inglés',      'dias' => 'MJ',  'dias_dictado' => ['martes', 'jueves'],              'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00'],
        ];
        foreach ($materias as $m) {
            DB::table('materias')->insert([
                'sigla'         => $m['sigla'],
                'nombre'        => $m['nombre'],
                'dias'          => $m['dias'],
                'dias_dictado'  => json_encode($m['dias_dictado']),
                'hora_inicio'   => $m['hora_inicio'],
                'hora_fin'      => $m['hora_fin'],
                'cant_examenes' => 3,
                'peso_examen1'  => 30,
                'peso_examen2'  => 30,
                'peso_examen3'  => 40,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
        $this->command->info('   ✓ 4 materias con horarios estructurados');

        // 3.1) Limpiar SOLO los postulantes demo anteriores (CI empieza en 80)
        DB::table('postulacion_carreras')
            ->whereIn('inscripcion_id', function ($q) {
                $q->select('id')->from('inscripciones')
                  ->whereIn('postulante_id', function ($q2) {
                      $q2->select('id')->from('postulantes')
                         ->whereIn('persona_id', function ($q3) {
                             $q3->select('id')->from('personas')
                                ->where('ci', 'like', '80%');
                         });
                  });
            })->delete();

        DB::table('inscripciones')
            ->whereIn('postulante_id', function ($q) {
                $q->select('id')->from('postulantes')
                  ->whereIn('persona_id', function ($q2) {
                      $q2->select('id')->from('personas')
                         ->where('ci', 'like', '80%');
                  });
            })->delete();

        DB::table('postulantes')
            ->whereIn('persona_id', function ($q) {
                $q->select('id')->from('personas')
                  ->where('ci', 'like', '80%');
            })->delete();

        DB::table('personas')->where('ci', 'like', '80%')->delete();

        $this->command->info('   ✓ Limpieza de demos anteriores OK');

        // 4. Crear 500 postulantes
        $nombres = ['Juan','Pedro','María','Lucía','Carlos','Ana','Diego','Sofía','Luis','Camila',
                    'Roberto','Daniela','Andrés','Valeria','Mateo','Renata','Fabián','Antonella',
                    'Sebastián','Catalina','Joaquín','Isabella','Nicolás','Emma','Tomás','Martina',
                    'Gabriel','Florencia','Lautaro','Agustina'];

        $apellidos = ['García','Pérez','Soto','Vargas','Mendoza','Aliaga','Roca','Méndez','Suárez',
                      'Rivera','Cuéllar','Justiniano','Sandoval','Saavedra','Banzer','Antelo',
                      'Paz','Gutiérrez','Ortiz','Velasco','Hurtado','Salvatierra','Parada','Limpias',
                      'Egüez','Cronenbold','Foianini','Pacheco','Vaca','Roda'];

        $colegios = ['U.E. San Calixto','Colegio La Salle','Colegio Don Bosco',
                     'U.E. Marista','Colegio Alemán','Colegio Americano',
                     'U.E. Nacional Florida','U.E. Mariscal Sucre','U.E. 6 de Agosto',
                     "Colegio Saint Andrew's",'Colegio Anglo Americano','U.E. Bolivia'];

        $creados = 0;

        DB::transaction(function () use (
            &$creados, $carreraIds, $periodoId,
            $nombres, $apellidos, $colegios
        ) {
            for ($i = 1; $i <= 500; $i++) {

                $activo = ($i <= 400); // 400 activos, 100 pendientes
                $sexo   = ($i % 2 === 0) ? 'M' : 'F';
                $nombre = $nombres[array_rand($nombres)].' '
                        . $apellidos[array_rand($apellidos)].' '
                        . $apellidos[array_rand($apellidos)];

                $ci = '80'.str_pad($i, 5, '0', STR_PAD_LEFT); // 8000001 … 8000500

                // Persona
                $personaId = DB::table('personas')->insertGetId([
                    'ci'               => $ci,
                    'nombre'           => $nombre,
                    'fecha_nacimiento' => '200'.rand(5,8).'-'
                                       . str_pad(rand(1,12),2,'0',STR_PAD_LEFT).'-'
                                       . str_pad(rand(1,28),2,'0',STR_PAD_LEFT),
                    'sexo'             => $sexo,
                    'direccion'        => 'Av. '.($apellidos[array_rand($apellidos)]).' #'.rand(100,9999),
                    'telefono'         => '7'.rand(1,9).rand(100000,999999),
                    'correo'           => 'postulante'.$i.'@cup.test',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Postulante
                $postulanteId = DB::table('postulantes')->insertGetId([
                    'persona_id' => $personaId,
                    'colegio'    => $colegios[array_rand($colegios)],
                    'estado'     => 'inscrito',
                    'activo'     => $activo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Inscripción
                $inscripcionId = DB::table('inscripciones')->insertGetId([
                    'postulante_id'     => $postulanteId,
                    'periodo_id'        => $periodoId,
                    'fecha_inscripcion' => now()->subDays(rand(1,45))->toDateString(),
                    'estado'            => 'activa',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // Carrera 1
                $c1 = $carreraIds[array_rand($carreraIds)];
                DB::table('postulacion_carreras')->insert([
                    'inscripcion_id' => $inscripcionId,
                    'carrera_id'     => $c1,
                    'prioridad'      => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Carrera 2 (60% elige segunda opción)
                if (rand(1,100) <= 60 && count($carreraIds) > 1) {
                    $c2 = $c1;
                    while ($c2 === $c1) {
                        $c2 = $carreraIds[array_rand($carreraIds)];
                    }
                    DB::table('postulacion_carreras')->insert([
                        'inscripcion_id' => $inscripcionId,
                        'carrera_id'     => $c2,
                        'prioridad'      => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }

                $creados++;
            }
        });

        $this->command->info("   ✓ $creados postulantes creados (400 activos + 100 pendientes)");
        $this->command->info('🎉 DatosDemoSeeder completo.');
    }
}
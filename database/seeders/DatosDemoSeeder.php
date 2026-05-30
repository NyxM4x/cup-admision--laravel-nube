<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Poblando datos demo del CUP...');

        // ============= 1) PERIODO ACTIVO =============
        // periodos no tiene codigo/nombre/estado: reutilizar el activo o crear uno.
        $periodoId = DB::table('periodos')->where('activo', true)->orderBy('id', 'desc')->value('id');
        if (! $periodoId) {
            $periodoId = DB::table('periodos')->insertGetId([
                'fecha_ini_inscripcion' => '2026-01-15',
                'fecha_fin_inscripcion' => '2026-02-15',
                'fecha_ini_curso'       => '2026-02-20',
                'fecha_fin_curso'       => '2026-04-30',
                'activo'                => true,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
        }
        $this->command->info("   ✓ Periodo activo (id=$periodoId)");

        // ============= 2) CARRERAS + CUPOS =============
        $carreras = [
            ['codigo' => 'ING-SIS', 'nombre' => 'Ingeniería en Sistemas',          'cupo' => 80],
            ['codigo' => 'ING-INF', 'nombre' => 'Ingeniería Informática',           'cupo' => 60],
            ['codigo' => 'ING-TEL', 'nombre' => 'Ingeniería en Telecomunicaciones', 'cupo' => 50],
            ['codigo' => 'ING-RED', 'nombre' => 'Ingeniería en Redes',              'cupo' => 40],
        ];
        foreach ($carreras as $c) {
            DB::table('carreras')->updateOrInsert(
                ['codigo' => $c['codigo']],
                [
                    'nombre'      => $c['nombre'],
                    'descripcion' => 'Carrera de la FICCT-UAGRM',
                    'activo'      => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
            $carreraId = DB::table('carreras')->where('codigo', $c['codigo'])->value('id');

            DB::table('cupo_carreras')->updateOrInsert(
                ['carrera_id' => $carreraId, 'periodo_id' => $periodoId],
                [
                    'cupo_max'   => $c['cupo'], // columna real: cupo_max (no cupo_maximo)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('   ✓ 4 carreras + cupos');

        // ============= 3) MATERIAS (con días estructurados) =============
        // Esquema real: sigla (no codigo), sin descripcion, dias NOT NULL.
        DB::table('materias')->delete();
        $materias = [
            ['sigla' => 'MAT', 'nombre' => 'Matemática', 'dias' => 'LXV', 'dias_dictado' => ['lunes', 'miercoles', 'viernes'], 'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00'],
            ['sigla' => 'FIS', 'nombre' => 'Física',     'dias' => 'MJ',  'dias_dictado' => ['martes', 'jueves'],              'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00'],
            ['sigla' => 'QUI', 'nombre' => 'Química',    'dias' => 'LXV', 'dias_dictado' => ['lunes', 'miercoles', 'viernes'], 'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00'],
            ['sigla' => 'LEN', 'nombre' => 'Lenguaje',   'dias' => 'MJ',  'dias_dictado' => ['martes', 'jueves'],              'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00'],
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

        // ============= 4) PROFESIONES =============
        $profesiones = ['Lic. en Matemática', 'Lic. en Física', 'Ing. Química', 'Lic. en Literatura', 'Ing. en Sistemas', 'Lic. en Pedagogía'];
        foreach ($profesiones as $p) {
            DB::table('profesiones')->updateOrInsert(['nombre' => $p], ['created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('   ✓ 6 profesiones');

        // ============= 5) DOCENTES =============
        DB::table('docentes')->delete();
        DB::table('personas')->whereIn('ci', ['1001', '1002', '1003', '1004', '1005', '1006'])->delete();

        $docentes = [
            ['ci' => '1001', 'nombre' => 'María Pérez Soto',      'profesion' => 'Lic. en Matemática', 'anios' => 12, 'sexo' => 'F'],
            ['ci' => '1002', 'nombre' => 'Carlos García Vargas',  'profesion' => 'Lic. en Física',      'anios' => 8,  'sexo' => 'M'],
            ['ci' => '1003', 'nombre' => 'Ana Soto Mendoza',      'profesion' => 'Ing. Química',        'anios' => 15, 'sexo' => 'F'],
            ['ci' => '1004', 'nombre' => 'Roberto Méndez Aliaga', 'profesion' => 'Lic. en Literatura',  'anios' => 6,  'sexo' => 'M'],
            ['ci' => '1005', 'nombre' => 'Lucía Vargas Roca',     'profesion' => 'Lic. en Pedagogía',   'anios' => 10, 'sexo' => 'F'],
            ['ci' => '1006', 'nombre' => 'Jorge Aliaga Vaca',     'profesion' => 'Ing. en Sistemas',    'anios' => 5,  'sexo' => 'M'],
        ];
        foreach ($docentes as $d) {
            $personaId = DB::table('personas')->insertGetId([
                'ci'               => $d['ci'],
                'nombre'           => $d['nombre'],
                'fecha_nacimiento' => '1985-01-01',
                'sexo'             => $d['sexo'],
                'direccion'        => 'Santa Cruz de la Sierra',
                'telefono'         => '7'.rand(0, 9).rand(100000, 999999),
                'correo'           => strtolower(str_replace(' ', '.', $d['nombre'])).'@docente.uagrm.bo',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $profesionId = DB::table('profesiones')->where('nombre', $d['profesion'])->value('id');
            DB::table('docentes')->insert([
                'persona_id'        => $personaId,
                'profesion_id'      => $profesionId,
                'anios_experiencia' => $d['anios'],
                'activo'            => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
        $this->command->info('   ✓ 6 docentes');

        // ============= 6) AULAS =============
        $aulasExtra = [
            ['codigo' => 'A-301', 'edificio' => 'Bloque A', 'capacidad' => 80, 'equipamiento' => 'Proyector, pizarra, AC'],
            ['codigo' => 'B-301', 'edificio' => 'Bloque B', 'capacidad' => 80, 'equipamiento' => 'Lab. computación 40 PCs'],
            ['codigo' => 'C-401', 'edificio' => 'Bloque C', 'capacidad' => 80, 'equipamiento' => 'Auditorio chico'],
            ['codigo' => 'C-402', 'edificio' => 'Bloque C', 'capacidad' => 80, 'equipamiento' => 'Proyector, pizarra'],
        ];
        foreach ($aulasExtra as $a) {
            DB::table('aulas')->updateOrInsert(
                ['codigo' => $a['codigo']],
                array_merge($a, ['activo' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
        $this->command->info('   ✓ Aulas adicionales');

        // ============= 7) POSTULANTES (500) =============
        // Limpieza en orden child -> parent
        DB::table('postulacion_carreras')->delete();
        DB::table('documentos_postulantes')->delete();
        DB::table('inscripciones')->delete();
        DB::table('postulantes')->delete();
        DB::table('personas')->where('ci', 'like', '8%')->delete();

        $nombres = ['Juan', 'Pedro', 'María', 'Lucía', 'Carlos', 'Ana', 'Diego', 'Sofía', 'Luis', 'Camila',
            'Roberto', 'Daniela', 'Andrés', 'Valeria', 'Mateo', 'Renata', 'Fabián', 'Antonella',
            'Sebastián', 'Catalina', 'Joaquín', 'Isabella', 'Nicolás', 'Emma', 'Tomás', 'Martina',
            'Gabriel', 'Florencia', 'Lautaro', 'Agustina'];
        $apellidos = ['García', 'Pérez', 'Soto', 'Vargas', 'Mendoza', 'Aliaga', 'Roca', 'Méndez', 'Suárez',
            'Rivera', 'Cuéllar', 'Justiniano', 'Sandoval', 'Saavedra', 'Banzer', 'Antelo',
            'Paz', 'Gutiérrez', 'Ortiz', 'Velasco', 'Hurtado', 'Salvatierra', 'Parada', 'Limpias',
            'Egüez', 'Cronenbold', 'Foianini', 'Pacheco', 'Vaca', 'Roda'];
        $colegios = ['U.E. San Calixto', 'Colegio La Salle', 'Colegio Don Bosco',
            'U.E. Marista', 'Colegio Alemán', 'Colegio Americano',
            'U.E. Nacional Florida', 'U.E. Mariscal Sucre', 'U.E. 6 de Agosto',
            "Colegio Saint Andrew's", 'Colegio Anglo Americano', 'U.E. Bolivia'];

        $carreraIds = DB::table('carreras')->pluck('id')->toArray();
        $totalPostulantes = 500;

        DB::transaction(function () use ($totalPostulantes, $nombres, $apellidos, $colegios, $carreraIds, $periodoId) {
            for ($i = 1; $i <= $totalPostulantes; $i++) {
                $sexo = rand(0, 1) === 0 ? 'M' : 'F';
                $nombre = $nombres[array_rand($nombres)].' '.$apellidos[array_rand($apellidos)].' '.$apellidos[array_rand($apellidos)];

                $personaId = DB::table('personas')->insertGetId([
                    'ci'               => '8'.str_pad($i, 6, '0', STR_PAD_LEFT),
                    'nombre'           => $nombre,
                    'fecha_nacimiento' => '200'.rand(5, 8).'-'.str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT).'-'.str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'sexo'             => $sexo,
                    'direccion'        => 'Av. '.$apellidos[array_rand($apellidos)].' #'.rand(100, 9999),
                    'telefono'         => '7'.rand(0, 9).rand(100000, 999999),
                    'correo'           => 'postulante'.$i.'@test.bo',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                $postulanteId = DB::table('postulantes')->insertGetId([
                    'persona_id' => $personaId,
                    'colegio'    => $colegios[array_rand($colegios)],
                    'estado'     => 'inscrito',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $inscripcionId = DB::table('inscripciones')->insertGetId([
                    'postulante_id'     => $postulanteId,
                    'periodo_id'        => $periodoId,
                    'fecha_inscripcion' => now()->subDays(rand(1, 30)),
                    'estado'            => 'activa', // CHECK constraint: no acepta 'inscrito'
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $carrera1 = $carreraIds[array_rand($carreraIds)];
                DB::table('postulacion_carreras')->insert([
                    'inscripcion_id' => $inscripcionId,
                    'carrera_id'     => $carrera1,
                    'prioridad'      => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                if (rand(1, 100) <= 60) {
                    $carrera2 = $carrera1;
                    while ($carrera2 === $carrera1) {
                        $carrera2 = $carreraIds[array_rand($carreraIds)];
                    }
                    DB::table('postulacion_carreras')->insert([
                        'inscripcion_id' => $inscripcionId,
                        'carrera_id'     => $carrera2,
                        'prioridad'      => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        });

        $this->command->info("   ✓ $totalPostulantes postulantes con inscripción y carreras");

        // ============= REQUISITOS DEMO =============
        $requisitosDemo = [
            ['nombre' => 'CI', 'descripcion' => 'Documento de identidad vigente', 'obligatorio' => true],
            ['nombre' => 'Certificado de notas', 'descripcion' => 'Notas del último año de secundaria', 'obligatorio' => true],
            ['nombre' => 'Diploma de bachiller', 'descripcion' => 'Diploma o certificado provisional', 'obligatorio' => true],
            ['nombre' => 'Fotografía', 'descripcion' => '2 fotos fondo blanco tamaño 4x4', 'obligatorio' => true],
            ['nombre' => 'Boleta de inscripción', 'descripcion' => 'Comprobante de inscripción al CUP', 'obligatorio' => false],
        ];
        foreach ($requisitosDemo as $r) {
            DB::table('requisitos')->updateOrInsert(
                ['nombre' => $r['nombre'], 'periodo_id' => $periodoId],
                [
                    'descripcion'      => $r['descripcion'],
                    'obligatorio'      => $r['obligatorio'],
                    'formato_aceptado' => 'PDF,JPG,PNG',
                    'tamanio_max_kb'   => 2048, // columna real (no tamano_max_mb)
                    'activo'           => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }
        $this->command->info('   ✓ 5 requisitos demo');

        $this->command->info('🎉 DatosDemoSeeder completo.');
    }
}

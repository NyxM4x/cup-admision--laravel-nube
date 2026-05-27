<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════
<<<<<<< HEAD
        // SEEDERS DE ARIANY
        // ══════════════════════════════════════
        $this->call([
            SeguridadSeeder::class,
            AulasSeeder::class,
        ]);

        // ══════════════════════════════════════
        // 1. USUARIO ADMINISTRADOR
        // ══════════════════════════════════════
        DB::table('users')->insert([
            'name'              => 'Administrador CUP',
            'email'             => 'admin@ficct.uagrm.edu.bo',
            'password'          => Hash::make('admin1234'),
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ══════════════════════════════════════
=======
        // 1. USUARIO ADMINISTRADOR
        // ══════════════════════════════════════
        DB::table('users')->insert([
            'name'              => 'Administrador CUP',
            'email'             => 'admin@ficct.uagrm.edu.bo',
            'password'          => Hash::make('admin1234'),
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ══════════════════════════════════════
>>>>>>> 8ae6c2cf1aebee465d2464e1931cebf1c097d218
        // 2. PERIODOS
        // ══════════════════════════════════════
        DB::table('periodos')->insert([
            [
                'fecha_ini_inscripcion' => '2026-01-05',
                'fecha_fin_inscripcion' => '2026-02-28',
                'fecha_ini_curso'       => '2026-03-02',
                'fecha_fin_curso'       => '2026-06-30',
                'activo'                => true,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'fecha_ini_inscripcion' => '2026-07-06',
                'fecha_fin_inscripcion' => '2026-08-28',
                'fecha_ini_curso'       => '2026-09-01',
                'fecha_fin_curso'       => '2026-11-30',
                'activo'                => false,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ]);

        $periodo1 = DB::table('periodos')->where('activo', true)->first();

        // ══════════════════════════════════════
        // 3. CARRERAS + CUPOS
        // ══════════════════════════════════════
        $carreras = [
            ['codigo' => 'INFO', 'nombre' => 'Ingeniería Informática',                  'descripcion' => 'Desarrollo de software y sistemas de información.'],
            ['codigo' => 'SIST', 'nombre' => 'Ingeniería de Sistemas',                  'descripcion' => 'Análisis, diseño y gestión de sistemas computacionales.'],
            ['codigo' => 'REDES','nombre' => 'Ingeniería en Redes y Telecomunicaciones','descripcion' => 'Infraestructura de redes y comunicaciones digitales.'],
            ['codigo' => 'ROBO', 'nombre' => 'Robótica',                                'descripcion' => 'Automatización, inteligencia artificial y robótica aplicada.'],
        ];

        foreach ($carreras as $c) {
            $id = DB::table('carreras')->insertGetId([
                'codigo'      => $c['codigo'],
                'nombre'      => $c['nombre'],
                'descripcion' => $c['descripcion'],
                'activo'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::table('cupo_carreras')->insert([
                'carrera_id'  => $id,
                'periodo_id'  => $periodo1->id,
                'cupo_max'    => 80,
                'fecha_cofi'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ══════════════════════════════════════
        // 4. MATERIAS
        // ══════════════════════════════════════
        $materias = [
            ['sigla' => 'MAT', 'nombre' => 'Matemáticas', 'dias' => 'Lunes, Miércoles'],
            ['sigla' => 'FIS', 'nombre' => 'Física',      'dias' => 'Martes, Jueves'],
            ['sigla' => 'ING', 'nombre' => 'Inglés',      'dias' => 'Lunes, Viernes'],
            ['sigla' => 'COM', 'nombre' => 'Computación', 'dias' => 'Miércoles, Viernes'],
        ];

        foreach ($materias as $m) {
            DB::table('materias')->insert([
                'sigla'      => $m['sigla'],
                'nombre'     => $m['nombre'],
                'dias'       => $m['dias'],
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ══════════════════════════════════════
        // 5. REQUISITOS
        // ══════════════════════════════════════
        $requisitos = [
            ['nombre' => 'Fotocopia de CI o Pasaporte', 'descripcion' => 'Documento de identidad vigente del postulante.',          'obligatorio' => true,  'formato_aceptado' => 'PDF,JPG,PNG', 'tamanio_max_kb' => 2048],
            ['nombre' => 'Certificado de Nacimiento',   'descripcion' => 'Emitido por el SEGIP o Registro Civil.',                  'obligatorio' => true,  'formato_aceptado' => 'PDF,JPG,PNG', 'tamanio_max_kb' => 2048],
            ['nombre' => 'Boletín Final de Secundaria', 'descripcion' => 'Boletín con notas del último año de secundaria.',         'obligatorio' => true,  'formato_aceptado' => 'PDF,JPG,PNG', 'tamanio_max_kb' => 5120],
            ['nombre' => 'Certificado de Bachiller',    'descripcion' => 'Título de bachiller original o fotocopia legalizada.',    'obligatorio' => true,  'formato_aceptado' => 'PDF,JPG,PNG', 'tamanio_max_kb' => 2048],
            ['nombre' => 'Foto Carnet 4x4',             'descripcion' => 'Foto reciente fondo blanco, ropa formal.',                'obligatorio' => true,  'formato_aceptado' => 'JPG,PNG',     'tamanio_max_kb' => 1024],
            ['nombre' => 'Comprobante de Pago',         'descripcion' => 'Comprobante del pago de inscripción al CUP-FICCT.',       'obligatorio' => true,  'formato_aceptado' => 'PDF,JPG,PNG', 'tamanio_max_kb' => 2048],
        ];

        foreach ($requisitos as $r) {
            DB::table('requisitos')->insert([
                'periodo_id'       => $periodo1->id,
                'nombre'           => $r['nombre'],
                'descripcion'      => $r['descripcion'],
                'obligatorio'      => $r['obligatorio'],
                'formato_aceptado' => $r['formato_aceptado'],
                'tamanio_max_kb'   => $r['tamanio_max_kb'],
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // ══════════════════════════════════════
        // 6. PROFESIONES
        // ══════════════════════════════════════
        $profesiones = [
            ['nombre' => 'Ingeniería de Sistemas',               'nivel_jerarquico' => 'Licenciatura'],
            ['nombre' => 'Ingeniería Informática',               'nivel_jerarquico' => 'Licenciatura'],
            ['nombre' => 'Matemáticas',                          'nivel_jerarquico' => 'Licenciatura'],
            ['nombre' => 'Física',                               'nivel_jerarquico' => 'Licenciatura'],
            ['nombre' => 'Inglés',                               'nivel_jerarquico' => 'Licenciatura'],
            ['nombre' => 'Ingeniería en Telecomunicaciones',     'nivel_jerarquico' => 'Licenciatura'],
        ];

        foreach ($profesiones as $p) {
            DB::table('profesiones')->insert([
                'nombre'           => $p['nombre'],
                'nivel_jerarquico' => $p['nivel_jerarquico'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // ══════════════════════════════════════
        // 7. DOCENTES (con sus personas)
        // ══════════════════════════════════════
        $docentes = [
            ['ci' => '3456789',  'nombre' => 'Carlos Mendoza Vaca',      'sexo' => 'M', 'telefono' => '71234567', 'correo' => 'cmendoza@ficct.uagrm.edu.bo',  'profesion' => 'Matemáticas',                      'experiencia' => 12],
            ['ci' => '4567890',  'nombre' => 'Ana Flores Ribera',        'sexo' => 'F', 'telefono' => '72345678', 'correo' => 'aflores@ficct.uagrm.edu.bo',    'profesion' => 'Física',                           'experiencia' => 8],
            ['ci' => '5678901',  'nombre' => 'Roberto Suárez Pedraza',   'sexo' => 'M', 'telefono' => '73456789', 'correo' => 'rsuarez@ficct.uagrm.edu.bo',    'profesion' => 'Inglés',                           'experiencia' => 15],
            ['ci' => '6789012',  'nombre' => 'Lucía Torrez Montaño',     'sexo' => 'F', 'telefono' => '74567890', 'correo' => 'ltorrez@ficct.uagrm.edu.bo',    'profesion' => 'Ingeniería de Sistemas',            'experiencia' => 10],
            ['ci' => '7890123',  'nombre' => 'Miguel Vargas Justiniano', 'sexo' => 'M', 'telefono' => '75678901', 'correo' => 'mvargas@ficct.uagrm.edu.bo',    'profesion' => 'Ingeniería Informática',           'experiencia' => 7],
            ['ci' => '8901234',  'nombre' => 'Patricia Hurtado Limpias', 'sexo' => 'F', 'telefono' => '76789012', 'correo' => 'phurtado@ficct.uagrm.edu.bo',   'profesion' => 'Ingeniería en Telecomunicaciones', 'experiencia' => 9],
        ];

        foreach ($docentes as $d) {
            $personaId = DB::table('personas')->insertGetId([
                'ci'               => $d['ci'],
                'nombre'           => $d['nombre'],
                'fecha_nacimiento' => '1985-06-15',
                'sexo'             => $d['sexo'],
                'direccion'        => 'Santa Cruz de la Sierra',
                'telefono'         => $d['telefono'],
                'correo'           => $d['correo'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $profesionId = DB::table('profesiones')
                ->where('nombre', $d['profesion'])->first()->id;

            DB::table('docentes')->insert([
                'persona_id'         => $personaId,
                'profesion_id'       => $profesionId,
                'anios_experiencia'  => $d['experiencia'],
                'certif_docente'     => null,
                'certif_profesional' => null,
                'activo'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        // ══════════════════════════════════════
        // 8. POSTULANTES + INSCRIPCIONES + POSTULACION CARRERAS
        // ══════════════════════════════════════
        $postulantes = [
            ['ci' => '9012345', 'nombre' => 'María Fernanda Rojas Díaz',    'sexo' => 'F', 'telefono' => '77890123', 'correo' => 'mrojas@gmail.com',    'colegio' => 'U.E. San Calixto',           'carrera1' => 'INFO', 'carrera2' => 'SIST'],
            ['ci' => '9123456', 'nombre' => 'Juan Pablo Morales Vega',      'sexo' => 'M', 'telefono' => '78901234', 'correo' => 'jpmorales@gmail.com',  'colegio' => 'U.E. Don Bosco',             'carrera1' => 'SIST', 'carrera2' => 'INFO'],
            ['ci' => '9234567', 'nombre' => 'Sofía Alejandra Pinto Cruz',   'sexo' => 'F', 'telefono' => '79012345', 'correo' => 'sapinto@gmail.com',    'colegio' => 'U.E. La Salle',              'carrera1' => 'ROBO', 'carrera2' => null],
            ['ci' => '9345678', 'nombre' => 'Diego Armando Ríos Sandoval',  'sexo' => 'M', 'telefono' => '70123456', 'correo' => 'darios@gmail.com',     'colegio' => 'U.E. Alemán',                'carrera1' => 'REDES','carrera2' => 'SIST'],
            ['ci' => '9456789', 'nombre' => 'Valentina Ortiz Mercado',      'sexo' => 'F', 'telefono' => '71234560', 'correo' => 'vortiz@gmail.com',     'colegio' => 'U.E. Tecnológico',           'carrera1' => 'INFO', 'carrera2' => 'ROBO'],
            ['ci' => '9567890', 'nombre' => 'Sebastián Castro Romero',      'sexo' => 'M', 'telefono' => '72345601', 'correo' => 'scastro@gmail.com',    'colegio' => 'U.E. Santa Ana',             'carrera1' => 'SIST', 'carrera2' => null],
            ['ci' => '9678901', 'nombre' => 'Camila Belén Torres Suárez',   'sexo' => 'F', 'telefono' => '73456012', 'correo' => 'cbtorres@gmail.com',   'colegio' => 'U.E. María Auxiliadora',     'carrera1' => 'ROBO', 'carrera2' => 'INFO'],
            ['ci' => '9789012', 'nombre' => 'Andrés Felipe Quiroga Lara',   'sexo' => 'M', 'telefono' => '74560123', 'correo' => 'afquiroga@gmail.com',  'colegio' => 'U.E. Sagrado Corazón',       'carrera1' => 'REDES','carrera2' => 'INFO'],
            ['ci' => '9890123', 'nombre' => 'Isabella Mendez Vargas',       'sexo' => 'F', 'telefono' => '75601234', 'correo' => 'imendez@gmail.com',    'colegio' => 'U.E. Bilingüe Santa Cruz',   'carrera1' => 'INFO', 'carrera2' => 'SIST'],
            ['ci' => '9901234', 'nombre' => 'Mateo Alejandro Peña Molina',  'sexo' => 'M', 'telefono' => '76012345', 'correo' => 'mapeña@gmail.com',     'colegio' => 'U.E. Bolivariano',           'carrera1' => 'SIST', 'carrera2' => 'REDES'],
        ];

        foreach ($postulantes as $p) {
            // Crear Persona
            $personaId = DB::table('personas')->insertGetId([
                'ci'               => $p['ci'],
                'nombre'           => $p['nombre'],
                'fecha_nacimiento' => '2006-03-10',
                'sexo'             => $p['sexo'],
                'direccion'        => 'Santa Cruz de la Sierra',
                'telefono'         => $p['telefono'],
                'correo'           => $p['correo'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Crear Postulante
            $postulanteId = DB::table('postulantes')->insertGetId([
                'persona_id'  => $personaId,
                'colegio'     => $p['colegio'],
                'estado'      => 'inscrito',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Crear Inscripción
            $inscripcionId = DB::table('inscripciones')->insertGetId([
                'postulante_id'     => $postulanteId,
                'periodo_id'        => $periodo1->id,
                'fecha_inscripcion' => now()->toDateString(),
                'estado'            => 'activa',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Carrera 1 (obligatoria)
            $carrera1Id = DB::table('carreras')->where('codigo', $p['carrera1'])->first()->id;
            DB::table('postulacion_carreras')->insert([
                'inscripcion_id' => $inscripcionId,
                'carrera_id'     => $carrera1Id,
                'prioridad'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Carrera 2 (opcional)
            if ($p['carrera2']) {
                $carrera2Id = DB::table('carreras')->where('codigo', $p['carrera2'])->first()->id;
                DB::table('postulacion_carreras')->insert([
                    'inscripcion_id' => $inscripcionId,
                    'carrera_id'     => $carrera2Id,
                    'prioridad'      => 2,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
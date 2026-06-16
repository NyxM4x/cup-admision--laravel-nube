<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════
        // SEEDERS DE ARIANY (seguridad, aulas, horarios)
        // ══════════════════════════════════════
        $this->call([
            SeguridadSeeder::class,
            AulasSeeder::class,
            HorariosSeeder::class,
        ]);

        // ══════════════════════════════════════
        // 1. USUARIO ADMINISTRADOR
        // ══════════════════════════════════════
        $rolAdminId = DB::table('roles')->where('nombre', 'Administrador')->value('id');

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@ficct.uagrm.edu.bo'],
            [
                'name'              => 'Administrador CUP',
                'password'          => Hash::make('Admin123@'),
                'rol_id'            => $rolAdminId,
                'activo'            => true,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // ══════════════════════════════════════
        // 2. CARRERAS
        // ══════════════════════════════════════
        $carreras = [
            ['codigo' => 'INFO',  'nombre' => 'Ingeniería Informática',                   'descripcion' => 'Desarrollo de software y sistemas de información.'],
            ['codigo' => 'SIST',  'nombre' => 'Ingeniería de Sistemas',                   'descripcion' => 'Análisis, diseño y gestión de sistemas computacionales.'],
            ['codigo' => 'REDES', 'nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'descripcion' => 'Infraestructura de redes y comunicaciones digitales.'],
            ['codigo' => 'ROBO',  'nombre' => 'Robótica',                                 'descripcion' => 'Automatización, inteligencia artificial y robótica aplicada.'],
        ];

        foreach ($carreras as $c) {
            DB::table('carreras')->updateOrInsert(
                ['codigo' => $c['codigo']],
                [
                    'nombre'      => $c['nombre'],
                    'descripcion' => $c['descripcion'],
                    'activo'      => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }

        // ══════════════════════════════════════
        // 3. MATERIAS
        // ══════════════════════════════════════
        $materias = [
            ['sigla' => 'MAT', 'nombre' => 'Matemáticas', 'dias' => 'Lunes, Miércoles',  'dias_dictado' => ['lunes', 'miercoles'],          'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00', 'peso1' => 30, 'peso2' => 30, 'peso3' => 40],
            ['sigla' => 'FIS', 'nombre' => 'Física',      'dias' => 'Martes, Jueves',     'dias_dictado' => ['martes', 'jueves'],            'hora_inicio' => '07:00:00', 'hora_fin' => '09:00:00', 'peso1' => 30, 'peso2' => 30, 'peso3' => 40],
            ['sigla' => 'COM', 'nombre' => 'Computación', 'dias' => 'Miércoles, Viernes', 'dias_dictado' => ['miercoles', 'viernes'],        'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00', 'peso1' => 30, 'peso2' => 30, 'peso3' => 40],
            ['sigla' => 'ING', 'nombre' => 'Inglés',      'dias' => 'Lunes, Viernes',     'dias_dictado' => ['lunes', 'viernes'],            'hora_inicio' => '09:00:00', 'hora_fin' => '11:00:00', 'peso1' => 30, 'peso2' => 30, 'peso3' => 40],
        ];

        foreach ($materias as $m) {
            DB::table('materias')->updateOrInsert(
                ['sigla' => $m['sigla']],
                [
                    'nombre'        => $m['nombre'],
                    'dias'          => $m['dias'],
                    'dias_dictado'  => json_encode($m['dias_dictado']),
                    'hora_inicio'   => $m['hora_inicio'],
                    'hora_fin'      => $m['hora_fin'],
                    'cant_examenes' => 3,
                    'peso_examen1'  => $m['peso1'],
                    'peso_examen2'  => $m['peso2'],
                    'peso_examen3'  => $m['peso3'],
                    'activo'        => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }

        // ══════════════════════════════════════
        // 4. PROFESIONES (organizadas por materia + grado académico)
        // ══════════════════════════════════════
        $profesiones = [
            // Matemáticas
            ['nombre' => 'Licenciatura en Matemáticas',      'nivel_jerarquico' => 'Licenciatura', 'materia_sigla' => 'MAT'],
            ['nombre' => 'Maestría en Matemáticas',           'nivel_jerarquico' => 'Maestría',     'materia_sigla' => 'MAT'],
            ['nombre' => 'Ingeniería en Sistemas',             'nivel_jerarquico' => 'Ingeniería',   'materia_sigla' => 'MAT'],
            // Física
            ['nombre' => 'Licenciatura en Física',             'nivel_jerarquico' => 'Licenciatura', 'materia_sigla' => 'FIS'],
            ['nombre' => 'Maestría en Física',                 'nivel_jerarquico' => 'Maestría',     'materia_sigla' => 'FIS'],
            ['nombre' => 'Ingeniería en Física',               'nivel_jerarquico' => 'Ingeniería',   'materia_sigla' => 'FIS'],
            // Computación
            ['nombre' => 'Licenciatura en Informática',        'nivel_jerarquico' => 'Licenciatura', 'materia_sigla' => 'COM'],
            ['nombre' => 'Maestría en Informática',            'nivel_jerarquico' => 'Maestría',     'materia_sigla' => 'COM'],
            ['nombre' => 'Ingeniería Informática',             'nivel_jerarquico' => 'Ingeniería',   'materia_sigla' => 'COM'],
            ['nombre' => 'Ingeniería de Sistemas',             'nivel_jerarquico' => 'Ingeniería',   'materia_sigla' => 'COM'],
            // Inglés
            ['nombre' => 'Licenciatura en Inglés',             'nivel_jerarquico' => 'Licenciatura', 'materia_sigla' => 'ING'],
            ['nombre' => 'Maestría en Lingüística',            'nivel_jerarquico' => 'Maestría',     'materia_sigla' => 'ING'],
            ['nombre' => 'Licenciatura en Lingüística',        'nivel_jerarquico' => 'Licenciatura', 'materia_sigla' => 'ING'],
            // Telecomunicaciones
            ['nombre' => 'Ingeniería en Telecomunicaciones',   'nivel_jerarquico' => 'Ingeniería',   'materia_sigla' => 'TEC'],
        ];

        foreach ($profesiones as $p) {
            DB::table('profesiones')->updateOrInsert(
                ['nombre' => $p['nombre']],
                [
                    'nivel_jerarquico' => $p['nivel_jerarquico'],
                    'materia_sigla'    => $p['materia_sigla'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }

        // ══════════════════════════════════════
        // 5. DOCENTES
        // ══════════════════════════════════════
        $docentes = [
            ['ci' => '3456789', 'nombre' => 'Carlos Mendoza Vaca',      'sexo' => 'M', 'telefono' => '71234567', 'correo' => 'cmendoza@ficct.uagrm.edu.bo',  'profesion' => 'Licenciatura en Matemáticas', 'materia' => 'MAT', 'experiencia' => 12],
            ['ci' => '4567890', 'nombre' => 'Ana Flores Ribera',        'sexo' => 'F', 'telefono' => '72345678', 'correo' => 'aflores@ficct.uagrm.edu.bo',    'profesion' => 'Licenciatura en Física',       'materia' => 'FIS', 'experiencia' => 8],
            ['ci' => '5678901', 'nombre' => 'Roberto Suárez Pedraza',   'sexo' => 'M', 'telefono' => '73456789', 'correo' => 'rsuarez@ficct.uagrm.edu.bo',    'profesion' => 'Licenciatura en Inglés',       'materia' => 'ING', 'experiencia' => 15],
            ['ci' => '6789012', 'nombre' => 'Lucía Torrez Montaño',     'sexo' => 'F', 'telefono' => '74567890', 'correo' => 'ltorrez@ficct.uagrm.edu.bo',    'profesion' => 'Ingeniería de Sistemas',       'materia' => 'COM', 'experiencia' => 10],
            ['ci' => '7890123', 'nombre' => 'Miguel Vargas Justiniano', 'sexo' => 'M', 'telefono' => '75678901', 'correo' => 'mvargas@ficct.uagrm.edu.bo',    'profesion' => 'Ingeniería Informática',       'materia' => 'COM', 'experiencia' => 7],
            ['ci' => '8901234', 'nombre' => 'Patricia Hurtado Limpias', 'sexo' => 'F', 'telefono' => '76789012', 'correo' => 'phurtado@ficct.uagrm.edu.bo',   'profesion' => 'Licenciatura en Lingüística',  'materia' => 'ING', 'experiencia' => 9],
            ['ci' => '2345678', 'nombre' => 'Jorge Chávez Aliaga',      'sexo' => 'M', 'telefono' => '77123456', 'correo' => 'jchavez@ficct.uagrm.edu.bo',    'profesion' => 'Maestría en Matemáticas',      'materia' => 'MAT', 'experiencia' => 14],
            ['ci' => '1234567', 'nombre' => 'Mónica Saavedra Vega',     'sexo' => 'F', 'telefono' => '78123456', 'correo' => 'msaavedra@ficct.uagrm.edu.bo',  'profesion' => 'Maestría en Física',           'materia' => 'FIS', 'experiencia' => 11],
            ['ci' => '9012348', 'nombre' => 'Luis Peña Justiniano',     'sexo' => 'M', 'telefono' => '79123456', 'correo' => 'lpena@ficct.uagrm.edu.bo',      'profesion' => 'Maestría en Lingüística',      'materia' => 'ING', 'experiencia' => 13],
            ['ci' => '8023456', 'nombre' => 'Sandra Limpias Roca',      'sexo' => 'F', 'telefono' => '70123457', 'correo' => 'slimpias@ficct.uagrm.edu.bo',   'profesion' => 'Maestría en Informática',      'materia' => 'COM', 'experiencia' => 6],
            ['ci' => '7034567', 'nombre' => 'Eduardo Quiroga Paz',      'sexo' => 'M', 'telefono' => '71023456', 'correo' => 'equiroga@ficct.uagrm.edu.bo',   'profesion' => 'Ingeniería en Sistemas',       'materia' => 'MAT', 'experiencia' => 9],
            ['ci' => '6045678', 'nombre' => 'Silvia Mamani Quispe',     'sexo' => 'F', 'telefono' => '72023456', 'correo' => 'smamani@ficct.uagrm.edu.bo',    'profesion' => 'Ingeniería en Física',         'materia' => 'FIS', 'experiencia' => 8],
        ];

        foreach ($docentes as $d) {
            $personaExistente = DB::table('personas')->where('ci', $d['ci'])->first();
            if ($personaExistente) {
                $personaId = $personaExistente->id;
                DB::table('personas')->where('id', $personaId)->update([
                    'nombre'     => $d['nombre'],
                    'sexo'       => $d['sexo'],
                    'telefono'   => $d['telefono'],
                    'correo'     => $d['correo'],
                    'updated_at' => now(),
                ]);
            } else {
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
            }

            $profesionId = DB::table('profesiones')->where('nombre', $d['profesion'])->value('id');

            DB::table('docentes')->updateOrInsert(
                ['persona_id' => $personaId],
                [
                    'profesion_id'       => $profesionId,
                    'materia'            => $d['materia'],
                    'anios_experiencia'  => $d['experiencia'],
                    'certif_docente'     => null,
                    'certif_profesional' => null,
                    'activo'             => true,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );

            $docenteId = DB::table('docentes')->where('persona_id', $personaId)->value('id');
            DB::table('docente_materias')->updateOrInsert(
                ['docente_id' => $docenteId, 'materia_sigla' => $d['materia']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // ══════════════════════════════════════
        // 6. PERIODOS + FLUJO COMPLETO
        //    FlujoPeriodosSeeder limpia y recrea periodos #1, #2 (cerrados) y #3 (activo)
        // ══════════════════════════════════════
        $this->call(FlujoPeriodosSeeder::class);
    }
}

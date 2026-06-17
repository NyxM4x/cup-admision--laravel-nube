<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Genera 3 periodos con flujo completo:
 *  - Periodo 1 (CERRADO): 500 postulantes, notas, resultados, asignación de carrera.
 *  - Periodo 2 (CERRADO): 500 postulantes, distinto rendimiento para comparar estadísticas.
 *  - Periodo 3 (ACTIVO):  Sin postulantes, 2 grupos listos para el demo de pago/email.
 *
 * Renumera IDs de periodos desde 1.
 * Docentes y datos estructurales (materias, carreras, horarios, aulas) NO se tocan.
 */
class FlujoPeriodosSeeder extends Seeder
{
    private const CUPO_GRUPO    = 70;
    private const CUPO_CARRERA  = 80;
    private const NOTA_APROBADO = 51.00;

    // Pesos de los exámenes (en porcentaje, suman 100)
    private const PESO_EX1 = 30;
    private const PESO_EX2 = 30;
    private const PESO_EX3 = 40;

    // ─────────────────────────────────────────────────────────────
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info(' FlujoPeriodosSeeder — Iniciando...');
        $this->command->info('════════════════════════════════════════════════');

        DB::transaction(function () {
            $this->limpiarDatos();
            $base = $this->obtenerDatosBase();

            $this->crearPeriodoCompleto($base, 1, [
                'fecha_ini_inscripcion' => '2024-01-08',
                'fecha_fin_inscripcion' => '2024-02-29',
                'fecha_ini_curso'       => '2024-03-04',
                'fecha_fin_curso'       => '2024-06-28',
                'activo'                => false,
            ], 500, 10_000_001, 42, 0.60, 'Periodo 1/2024 — I Semestre');

            $this->crearPeriodoCompleto($base, 2, [
                'fecha_ini_inscripcion' => '2024-07-08',
                'fecha_fin_inscripcion' => '2024-08-30',
                'fecha_ini_curso'       => '2024-09-02',
                'fecha_fin_curso'       => '2024-11-29',
                'activo'                => false,
            ], 500, 20_000_001, 77, 0.66, 'Periodo 2/2024 — II Semestre');

            $this->crearPeriodo3($base);
        });

        $this->command->info('');
        $this->command->info('✅ FlujoPeriodosSeeder finalizado correctamente.');
        $this->command->info('════════════════════════════════════════════════');
    }

    // ─────────────────────────────────────────────────────────────
    // LIMPIEZA Y RESET DE SECUENCIAS
    // ─────────────────────────────────────────────────────────────
    private function limpiarDatos(): void
    {
        $this->command->info('');
        $this->command->info('⟳ Limpiando datos anteriores...');

        // Deshabilitar FK checks temporalmente para truncar en cualquier orden
        DB::statement('SET session_replication_role = replica');

        foreach ([
            'notas',
            'resultados_admision',
            'grupo_postulante',
            'postulacion_carreras',
            'inscripciones',
            'grupo_materias',
            'grupos',
            'cupo_carreras',
            'periodos',
            'postulantes',
        ] as $tabla) {
            DB::statement("TRUNCATE TABLE {$tabla} RESTART IDENTITY CASCADE");
        }

        DB::statement('SET session_replication_role = DEFAULT');

        // Eliminar solo las personas que NO son de docentes
        $docentePersonaIds = DB::table('docentes')->pluck('persona_id')->toArray();
        if (empty($docentePersonaIds)) {
            DB::table('personas')->delete();
        } else {
            DB::table('personas')->whereNotIn('id', $docentePersonaIds)->delete();
        }

        // Resetear la secuencia de personas al máximo existente + 1
        $maxPersonaId = DB::table('personas')->max('id') ?? 0;
        DB::statement("SELECT setval('personas_id_seq', " . ($maxPersonaId + 1) . ", false)");

        $this->command->info('   ✓ Tablas limpiadas, secuencias reseteadas.');
    }

    // ─────────────────────────────────────────────────────────────
    // DATOS BASE (materias, horarios, aulas, carreras, docentes)
    // ─────────────────────────────────────────────────────────────
    private function obtenerDatosBase(): array
    {
        $this->command->info('');
        $this->command->info('⟳ Obteniendo datos base (materias, horarios, docentes)...');

        $materias = DB::table('materias')
            ->where('activo', true)
            ->orderBy('sigla')
            ->get()
            ->keyBy('sigla');

        if ($materias->isEmpty()) {
            throw new \RuntimeException('No hay materias. Ejecuta DatabaseSeeder primero.');
        }

        // Horarios G-MAÑANA-1 y G-TARDE-1
        $horarios = DB::table('horarios')
            ->where('activo', true)
            ->orderBy('hora_inicio')
            ->get();

        if ($horarios->isEmpty()) {
            throw new \RuntimeException('No hay horarios activos. Ejecuta HorariosSeeder primero.');
        }

        // Carreras activas
        $carreras = DB::table('carreras')
            ->where('activo', true)
            ->get()
            ->keyBy('codigo');

        if ($carreras->isEmpty()) {
            throw new \RuntimeException('No hay carreras. Ejecuta DatabaseSeeder primero.');
        }

        // Docentes por materia principal (campo materia en docentes)
        $docentesPorMateria = DB::table('docentes')
            ->join('personas', 'personas.id', '=', 'docentes.persona_id')
            ->where('docentes.activo', true)
            ->whereNotNull('docentes.materia')
            ->select('docentes.id', 'docentes.materia', 'personas.nombre')
            ->get()
            ->groupBy('materia')
            ->map(fn ($grp) => $grp->pluck('id')->toArray())
            ->toArray();

        // Aulas disponibles (para asignar a grupos)
        $aulas = DB::table('aulas')
            ->where('activo', true)
            ->pluck('id')
            ->toArray();

        if (empty($aulas)) {
            // Crear aulas mínimas si no existen
            $aulas = [];
            foreach (['A-101' => 'A', 'A-102' => 'A', 'B-101' => 'B', 'B-102' => 'B'] as $cod => $edif) {
                $aulas[] = DB::table('aulas')->insertGetId([
                    'codigo'     => $cod,
                    'edificio'   => 'Edificio ' . $edif,
                    'capacidad'  => 70,
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('   ✓ Materias: ' . $materias->count());
        $this->command->info('   ✓ Horarios: ' . $horarios->count());
        $this->command->info('   ✓ Carreras: ' . $carreras->count());
        $this->command->info('   ✓ Docentes activos: ' . collect($docentesPorMateria)->flatten()->count());
        $this->command->info('   ✓ Aulas: ' . count($aulas));

        return compact('materias', 'horarios', 'carreras', 'docentesPorMateria', 'aulas');
    }

    // ─────────────────────────────────────────────────────────────
    // CREAR PERIODO CON FLUJO COMPLETO (cerrado)
    // ─────────────────────────────────────────────────────────────
    private function crearPeriodoCompleto(
        array $base,
        int   $numPeriodo,
        array $fechas,
        int   $cantPostulantes,
        int   $ciBase,
        int   $seed,
        float $tasaAprobacion,
        string $label
    ): void {
        $this->command->info('');
        $this->command->info("─── {$label} ────────────────────────────────");

        // 1. Crear periodo
        $periodoId = DB::table('periodos')->insertGetId(array_merge($fechas, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        $this->command->info("   ✓ Periodo #{$periodoId} creado (activo={$fechas['activo']})");

        // 2. Cupo por carrera
        foreach ($base['carreras'] as $carrera) {
            DB::table('cupo_carreras')->insert([
                'carrera_id' => $carrera->id,
                'periodo_id' => $periodoId,
                'cupo_max'   => self::CUPO_CARRERA,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Crear grupos
        $gruposInfo = $this->crearGrupos($periodoId, $base, $numPeriodo);
        $this->command->info('   ✓ ' . count($gruposInfo) . ' grupos creados con grupo_materias');

        // 4. Crear postulantes + inscripciones + preferencias de carrera
        $postulantesInfo = $this->crearPostulantes(
            $periodoId, $cantPostulantes, $ciBase,
            $base['carreras'], $fechas['fecha_ini_curso'], $seed
        );
        $this->command->info("   ✓ {$cantPostulantes} postulantes creados con inscripciones");

        // 5. Asignar postulantes a grupos (round-robin)
        $this->asignarPostulantesAGrupos($gruposInfo, $postulantesInfo['ids']);
        $this->command->info('   ✓ Postulantes asignados a grupos');

        // 6. Generar notas
        $this->generarNotas($gruposInfo, $postulantesInfo['ids'], $seed, $tasaAprobacion);
        $this->command->info('   ✓ Notas generadas (3 exámenes por postulante × 4 materias)');

        // 7. Calcular promedio_final y crear resultados_admision
        $aprobados = $this->calcularResultados($periodoId, $postulantesInfo['ids']);
        $this->command->info("   ✓ Resultados de admisión creados ({$aprobados} aprobados)");

        // 8. Asignación de carreras por mérito
        $admitidos = $this->ejecutarAsignacionCarreras($periodoId, $base['carreras']);
        $this->command->info("   ✓ Asignación de carreras ejecutada ({$admitidos} admitidos)");

        // 9. Actualizar estados de postulantes
        $this->actualizarEstadosPostulantes($periodoId);
        $this->command->info("   ✓ Estados de postulantes actualizados");
    }

    // ─────────────────────────────────────────────────────────────
    // CREAR GRUPOS Y GRUPO_MATERIAS
    // ─────────────────────────────────────────────────────────────
    private function crearGrupos(int $periodoId, array $base, int $numPeriodo): array
    {
        $materias = $base['materias'];
        $horarios = $base['horarios'];
        $docentesPorMateria = $base['docentesPorMateria'];
        $aulaIds = $base['aulas'];

        // Grupos por turno = ceil(500 / 70) = 8
        $gruposPorTurno = (int) ceil(500 / self::CUPO_GRUPO);

        // Horarios de bloques por turno
        $bloquesHorario = [
            'Mañana' => [
                'MAT' => ['07:00:00', '08:30:00'],
                'FIS' => ['08:30:00', '10:00:00'],
                'COM' => ['10:00:00', '11:00:00'],
                'ING' => ['11:00:00', '12:00:00'],
            ],
            'Tarde' => [
                'MAT' => ['13:00:00', '14:30:00'],
                'FIS' => ['14:30:00', '16:00:00'],
                'COM' => ['16:00:00', '17:00:00'],
                'ING' => ['17:00:00', '18:00:00'],
            ],
        ];

        // Días por materia
        $diasMateria = [
            'MAT' => 'Lunes',
            'FIS' => 'Martes',
            'COM' => 'Miercoles',
            'ING' => 'Jueves',
        ];

        $gruposInfo = [];
        $aulaIdx = 0;
        $grupoGlobal = 0;

        foreach ($horarios as $horario) {
            $turno = $horario->turno;
            $bloquesBase = $bloquesHorario[$turno] ?? $bloquesHorario['Mañana'];

            for ($n = 1; $n <= $gruposPorTurno; $n++) {
                $grupoGlobal++;
                $turnoAbrev = ($turno === 'Mañana') ? 'MAÑ' : 'TAR';
                $codigo = "P{$numPeriodo}-{$turnoAbrev}-{$n}";

                $grupoId = DB::table('grupos')->insertGetId([
                    'codigo'             => $codigo,
                    'periodo_id'         => $periodoId,
                    'horario_id'         => $horario->id,
                    'aula_id'            => $aulaIds[$aulaIdx % count($aulaIds)],
                    'cupo_max'           => self::CUPO_GRUPO,
                    'inscritos_actuales' => 0,
                    'activo'             => true,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
                $aulaIdx++;

                $grupoMateriaIds = [];
                $orden = 1;

                foreach (['MAT', 'FIS', 'COM', 'ING'] as $sigla) {
                    if (! isset($materias[$sigla])) continue;
                    $materia = $materias[$sigla];

                    // Asignar docente round-robin por materia y número de grupo
                    $docentes = $docentesPorMateria[$sigla] ?? [];
                    $docenteId = null;
                    if (! empty($docentes)) {
                        $docenteId = $docentes[($grupoGlobal - 1) % count($docentes)];
                    }

                    $bloques = $bloquesBase[$sigla] ?? ['07:00:00', '08:00:00'];

                    $gmId = DB::table('grupo_materias')->insertGetId([
                        'grupo_id'    => $grupoId,
                        'materia_id'  => $materia->id,
                        'docente_id'  => $docenteId,
                        'aula_id'     => $aulaIds[$aulaIdx % count($aulaIds)],
                        'hora_inicio' => $bloques[0],
                        'hora_fin'    => $bloques[1],
                        'dia_semana'  => $diasMateria[$sigla],
                        'orden'       => $orden++,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);

                    $grupoMateriaIds[$sigla] = [
                        'gm_id'      => $gmId,
                        'docente_id' => $docenteId,
                        'sigla'      => $sigla,
                    ];
                    $aulaIdx++;
                }

                $gruposInfo[] = [
                    'grupo_id'        => $grupoId,
                    'grupo_materias'  => $grupoMateriaIds,
                    'postulantes'     => [],
                ];
            }
        }

        return $gruposInfo;
    }

    // ─────────────────────────────────────────────────────────────
    // CREAR POSTULANTES + INSCRIPCIONES + PREFERENCIAS
    // ─────────────────────────────────────────────────────────────
    private function crearPostulantes(
        int   $periodoId,
        int   $cantidad,
        int   $ciBase,
        mixed $carreras,
        string $fechaBase,
        int   $seed
    ): array {
        mt_srand($seed * 100);

        $carreraIds = $carreras->pluck('id')->toArray();
        $carreraCodigos = $carreras->pluck('id', 'codigo')->toArray();

        $nombres = [
            'Juan','Pedro','María','Lucía','Carlos','Ana','Diego','Sofía','Luis','Camila',
            'Roberto','Daniela','Andrés','Valeria','Mateo','Renata','Fabián','Antonella',
            'Sebastián','Catalina','Joaquín','Isabella','Nicolás','Emma','Tomás','Martina',
            'Gabriel','Florencia','Lautaro','Agustina','Rodrigo','Valentina','Leandro',
            'Juliana','Fernando','Paula','Cristian','Natalia','Esteban','Lorena',
        ];
        $apellidos = [
            'García','Pérez','Soto','Vargas','Mendoza','Aliaga','Roca','Méndez','Suárez',
            'Rivera','Cuéllar','Justiniano','Sandoval','Saavedra','Banzer','Antelo',
            'Paz','Gutiérrez','Ortiz','Velasco','Hurtado','Salvatierra','Parada','Limpias',
            'Egüez','Cronenbold','Foianini','Pacheco','Vaca','Roda','Torrez','Quiroga',
            'Mamani','Quispe','Flores','Rojas','Morales','Chávez','Ramírez','López',
        ];
        $colegios = [
            'U.E. San Calixto','Colegio La Salle','Colegio Don Bosco','U.E. Marista',
            'Colegio Alemán','Colegio Americano','U.E. Nacional Florida','U.E. 6 de Agosto',
            "Colegio Saint Andrew's",'Colegio Anglo Americano','U.E. Bolivia',
            'U.E. Mariscal Sucre','U.E. Sagrado Corazón','U.E. Santa Ana',
        ];

        // Distribución de preferencias de carrera (índice 0-3 → prioridad 1)
        $prefDistribucion = [0, 0, 0, 1, 1, 1, 2, 2, 3, 3]; // INFO y SIST más populares

        $personasBatch     = [];
        $postulantesBatch  = [];
        $inscripcionesBatch = [];
        $preferencias      = [];
        $postulanteIds     = [];

        // Insertar en lotes para rendimiento
        $loteSize = 100;

        for ($i = 0; $i < $cantidad; $i++) {
            $ci     = (string) ($ciBase + $i);
            $nombre = $nombres[$i % count($nombres)] . ' '
                    . $apellidos[mt_rand(0, count($apellidos) - 1)] . ' '
                    . $apellidos[mt_rand(0, count($apellidos) - 1)];
            $sexo   = ($i % 3 === 0) ? 'F' : 'M';
            $año    = mt_rand(2004, 2007);
            $mes    = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
            $dia    = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);

            $personaId = DB::table('personas')->insertGetId([
                'ci'               => $ci,
                'nombre'           => $nombre,
                'fecha_nacimiento' => "{$año}-{$mes}-{$dia}",
                'sexo'             => $sexo,
                'direccion'        => 'Santa Cruz de la Sierra',
                'telefono'         => '7' . mt_rand(1000000, 9999999),
                'correo'           => 'p' . $ci . '@cup.test',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $postulanteId = DB::table('postulantes')->insertGetId([
                'persona_id' => $personaId,
                'colegio'    => $colegios[mt_rand(0, count($colegios) - 1)],
                'estado'     => 'inscrito',
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $diaInscripcion = date('Y-m-d', strtotime($fechaBase) - mt_rand(1, 50) * 86400);

            $inscripcionId = DB::table('inscripciones')->insertGetId([
                'postulante_id'     => $postulanteId,
                'periodo_id'        => $periodoId,
                'fecha_inscripcion' => $diaInscripcion,
                'estado'            => 'activa',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // Primera preferencia de carrera
            $prefIdx1 = $prefDistribucion[$i % count($prefDistribucion)];
            $carrera1Id = $carreraIds[$prefIdx1];
            DB::table('postulacion_carreras')->insert([
                'inscripcion_id' => $inscripcionId,
                'carrera_id'     => $carrera1Id,
                'prioridad'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Segunda preferencia (70% de postulantes)
            $carrera2Id = null;
            if (mt_rand(0, 9) < 7) {
                $prefIdx2 = ($prefIdx1 + 1 + mt_rand(0, 2)) % count($carreraIds);
                $carrera2Id = $carreraIds[$prefIdx2];
                if ($carrera2Id !== $carrera1Id) {
                    DB::table('postulacion_carreras')->insert([
                        'inscripcion_id' => $inscripcionId,
                        'carrera_id'     => $carrera2Id,
                        'prioridad'      => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                } else {
                    $carrera2Id = null;
                }
            }

            $postulanteIds[] = [
                'postulante_id' => $postulanteId,
                'inscripcion_id' => $inscripcionId,
                'carrera1_id'   => $carrera1Id,
                'carrera2_id'   => $carrera2Id,
                'indice'        => $i,
            ];
        }

        return ['ids' => $postulanteIds];
    }

    // ─────────────────────────────────────────────────────────────
    // ASIGNAR POSTULANTES A GRUPOS (round-robin)
    // ─────────────────────────────────────────────────────────────
    private function asignarPostulantesAGrupos(array &$gruposInfo, array $postulantesInfo): void
    {
        $totalGrupos = count($gruposInfo);
        $ahora = now();

        foreach ($postulantesInfo as $idx => $p) {
            $grupoIdx = $idx % $totalGrupos;
            $gruposInfo[$grupoIdx]['postulantes'][] = $p;
        }

        // Insertar en grupo_postulante y actualizar inscritos_actuales
        foreach ($gruposInfo as &$grupo) {
            $cantInscritos = count($grupo['postulantes']);
            if ($cantInscritos === 0) continue;

            $rows = array_map(fn ($p) => [
                'grupo_id'        => $grupo['grupo_id'],
                'postulante_id'   => $p['postulante_id'],
                'fecha_asignacion' => $ahora,
                'created_at'      => $ahora,
                'updated_at'      => $ahora,
            ], $grupo['postulantes']);

            // Insertar de a 50 para evitar límites de parámetros
            foreach (array_chunk($rows, 50) as $chunk) {
                DB::table('grupo_postulante')->insert($chunk);
            }

            DB::table('grupos')
                ->where('id', $grupo['grupo_id'])
                ->update(['inscritos_actuales' => $cantInscritos, 'updated_at' => $ahora]);
        }
        unset($grupo);
    }

    // ─────────────────────────────────────────────────────────────
    // GENERAR NOTAS (3 exámenes por postulante × 4 materias)
    // ─────────────────────────────────────────────────────────────
    private function generarNotas(
        array $gruposInfo,
        array $postulantesFlat,
        int   $seed,
        float $tasaAprobacion
    ): void {
        mt_srand($seed * 31 + 7);

        // Bonus de dificultad por materia (negativo = más difícil)
        $dificultadMateria = ['MAT' => -4, 'FIS' => -6, 'COM' => 3, 'ING' => 0];

        // Factor de efectividad por posición de docente (variación entre docentes)
        // Se aplicará según el índice round-robin del docente en su materia
        $efectividadDocente = [0, 5, -3, 7, -5, 4, -2, 8, -4, 3, 6, -1, 2, -6, 5, 0];

        $ahora = now();
        $totalNotas = 0;

        foreach ($gruposInfo as $grupoIdx => $grupo) {
            if (empty($grupo['postulantes'])) continue;

            foreach ($grupo['grupo_materias'] as $sigla => $gmData) {
                $gmId = $gmData['gm_id'];
                $difMateria = $dificultadMateria[$sigla] ?? 0;
                $docEfect = $efectividadDocente[$grupoIdx % count($efectividadDocente)];

                $notasBatch = [];

                foreach ($grupo['postulantes'] as $pos) {
                    $indice = $pos['indice'];

                    // Factor de habilidad individual (0.0 = muy bajo, 1.0 = excelente)
                    // Usamos una distribución que da ~65% aprobados
                    $abilityBase = (($indice * 37 + $seed * 13) % 100) / 100;
                    // Sesgar la distribución para acercarse a $tasaAprobacion
                    $ability = pow($abilityBase, 1.0 / max(0.1, $tasaAprobacion * 1.5));
                    $ability = max(0, min(1, $ability));

                    // Puntaje base: de 25 a 95
                    $base = 25 + ($ability * 70) + $difMateria + $docEfect;

                    // Variación por examen
                    $var = fn () => (mt_rand(0, 100) - 50) / 5; // -10 a +10

                    $ex1 = max(0, min(100, round($base + $var(), 2)));
                    $ex2 = max(0, min(100, round($base + $var(), 2)));
                    $ex3 = max(0, min(100, round($base + $var(), 2)));

                    $notaFinal = round(
                        ($ex1 * self::PESO_EX1 + $ex2 * self::PESO_EX2 + $ex3 * self::PESO_EX3) / 100,
                        2
                    );
                    $resultado = $notaFinal >= self::NOTA_APROBADO ? 'aprobado' : 'reprobado';

                    $notasBatch[] = [
                        'grupo_materia_id' => $gmId,
                        'postulante_id'    => $pos['postulante_id'],
                        'examen1'          => $ex1,
                        'examen2'          => $ex2,
                        'examen3'          => $ex3,
                        'nota_final'       => $notaFinal,
                        'resultado'        => $resultado,
                        'registrado_por'   => null,
                        'observacion'      => null,
                        'created_at'       => $ahora,
                        'updated_at'       => $ahora,
                    ];
                }

                foreach (array_chunk($notasBatch, 50) as $chunk) {
                    DB::table('notas')->insert($chunk);
                }
                $totalNotas += count($notasBatch);
            }
        }

        $this->command->line("      → {$totalNotas} registros de notas insertados");
    }

    // ─────────────────────────────────────────────────────────────
    // CALCULAR PROMEDIO_FINAL Y CREAR RESULTADOS_ADMISION
    // ─────────────────────────────────────────────────────────────
    private function calcularResultados(int $periodoId, array $postulantesInfo): int
    {
        $ahora = now();
        $aprobados = 0;
        $batch = [];

        foreach ($postulantesInfo as $p) {
            // Promedio final = AVG(nota_final) de las 4 materias del grupo asignado
            $promedioFinal = DB::table('notas as n')
                ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                ->join('grupos as g', 'g.id', '=', 'gm.grupo_id')
                ->where('n.postulante_id', $p['postulante_id'])
                ->where('g.periodo_id', $periodoId)
                ->whereNotNull('n.nota_final')
                ->avg('n.nota_final');

            if ($promedioFinal === null) {
                $promedioFinal = 0;
            }

            $promedioFinal = round((float) $promedioFinal, 2);
            $estadoAdmision = $promedioFinal >= self::NOTA_APROBADO ? 'aprobado' : 'reprobado';

            if ($estadoAdmision === 'aprobado') $aprobados++;

            $batch[] = [
                'postulante_id'          => $p['postulante_id'],
                'periodo_id'             => $periodoId,
                'promedio_final'         => $promedioFinal,
                'posicion_ranking_general' => null, // se asigna en la fase de asignación
                'carrera_asignada_id'    => null,
                'estado_admision'        => $estadoAdmision,
                'fecha_asignacion'       => null,
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ];
        }

        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('resultados_admision')->insert($chunk);
        }

        return $aprobados;
    }

    // ─────────────────────────────────────────────────────────────
    // ASIGNACIÓN DE CARRERAS POR MÉRITO (algoritmo en cascada)
    // ─────────────────────────────────────────────────────────────
    private function ejecutarAsignacionCarreras(int $periodoId, mixed $carreras): int
    {
        $ahora = now();

        // Cupos disponibles por carrera
        $cupos = [];
        foreach ($carreras as $carrera) {
            $cupos[$carrera->id] = self::CUPO_CARRERA;
        }

        // Postulantes aprobados ordenados por promedio DESC
        $resultados = DB::table('resultados_admision')
            ->where('periodo_id', $periodoId)
            ->where('estado_admision', 'aprobado')
            ->orderByDesc('promedio_final')
            ->get();

        // Cargar preferencias de cada postulante
        $preferencias = DB::table('postulacion_carreras as pc')
            ->join('inscripciones as i', 'i.id', '=', 'pc.inscripcion_id')
            ->where('i.periodo_id', $periodoId)
            ->orderBy('pc.prioridad')
            ->select('i.postulante_id', 'pc.carrera_id', 'pc.prioridad')
            ->get()
            ->groupBy('postulante_id')
            ->map(fn ($prefs) => $prefs->sortBy('prioridad')->values());

        $admitidos = 0;
        $posicion  = 1;

        foreach ($resultados as $resultado) {
            $prefs = $preferencias[$resultado->postulante_id] ?? collect();

            $estadoFinal = 'no_admitido_sin_cupo';
            $carreraAsignada = null;
            $estadoEnum = 'no_admitido_sin_cupo';

            foreach ($prefs as $pref) {
                if (isset($cupos[$pref->carrera_id]) && $cupos[$pref->carrera_id] > 0) {
                    $carreraAsignada = $pref->carrera_id;
                    $estadoEnum = ($pref->prioridad === 1) ? 'admitido_primera' : 'admitido_segunda';
                    $cupos[$pref->carrera_id]--;
                    $admitidos++;
                    break;
                }
            }

            DB::table('resultados_admision')
                ->where('postulante_id', $resultado->postulante_id)
                ->where('periodo_id', $periodoId)
                ->update([
                    'posicion_ranking_general' => $posicion++,
                    'carrera_asignada_id'      => $carreraAsignada,
                    'estado_admision'          => $estadoEnum,
                    'fecha_asignacion'         => $carreraAsignada ? $ahora : null,
                    'updated_at'               => $ahora,
                ]);
        }

        return $admitidos;
    }

    // ─────────────────────────────────────────────────────────────
    // ACTUALIZAR ESTADO DE POSTULANTES (aprobado/reprobado)
    // ─────────────────────────────────────────────────────────────
    private function actualizarEstadosPostulantes(int $periodoId): void
    {
        // Postulantes admitidos → estado 'aprobado'
        DB::table('postulantes')
            ->whereIn('id', function ($q) use ($periodoId) {
                $q->select('postulante_id')
                    ->from('resultados_admision')
                    ->where('periodo_id', $periodoId)
                    ->whereIn('estado_admision', ['admitido_primera', 'admitido_segunda']);
            })
            ->update(['estado' => 'aprobado', 'updated_at' => now()]);

        // Postulantes reprobados → estado 'reprobado'
        DB::table('postulantes')
            ->whereIn('id', function ($q) use ($periodoId) {
                $q->select('postulante_id')
                    ->from('resultados_admision')
                    ->where('periodo_id', $periodoId)
                    ->where('estado_admision', 'reprobado');
            })
            ->update(['estado' => 'reprobado', 'updated_at' => now()]);
    }

    // ─────────────────────────────────────────────────────────────
    // PERIODO 3: ACTIVO, VACÍO, 2 GRUPOS
    // ─────────────────────────────────────────────────────────────
    private function crearPeriodo3(array $base): void
    {
        $this->command->info('');
        $this->command->info('─── Periodo 3/2026 — Activo (vacío para demo) ──────────');

        $periodoId = DB::table('periodos')->insertGetId([
            'fecha_ini_inscripcion' => '2025-01-06',
            'fecha_fin_inscripcion' => '2025-02-28',
            'fecha_ini_curso'       => '2025-03-03',
            'fecha_fin_curso'       => '2025-06-27',
            'activo'                => true,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $this->command->info("   ✓ Periodo #{$periodoId} creado (activo=true)");

        // Cupos por carrera
        foreach ($base['carreras'] as $carrera) {
            DB::table('cupo_carreras')->insert([
                'carrera_id' => $carrera->id,
                'periodo_id' => $periodoId,
                'cupo_max'   => self::CUPO_CARRERA,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $bloquesHorario = [
            'Mañana' => [
                'MAT' => ['07:00:00', '08:30:00'],
                'FIS' => ['08:30:00', '10:00:00'],
                'COM' => ['10:00:00', '11:00:00'],
                'ING' => ['11:00:00', '12:00:00'],
            ],
            'Tarde' => [
                'MAT' => ['13:00:00', '14:30:00'],
                'FIS' => ['14:30:00', '16:00:00'],
                'COM' => ['16:00:00', '17:00:00'],
                'ING' => ['17:00:00', '18:00:00'],
            ],
        ];

        $diasMateria = [
            'MAT' => 'Lunes',
            'FIS' => 'Martes',
            'COM' => 'Miercoles',
            'ING' => 'Jueves',
        ];

        $aulaIds = $base['aulas'];
        $aulaIdx = 0;
        $materias = $base['materias'];
        $docentesPorMateria = $base['docentesPorMateria'];

        // Solo 1 grupo por turno para el periodo activo de demo
        $turnoIdx = 0;
        foreach ($base['horarios'] as $horario) {
            $turno = $horario->turno;
            $turnoAbrev = ($turno === 'Mañana') ? 'MAÑ' : 'TAR';
            $codigo = "P3-{$turnoAbrev}-1";
            $bloquesBase = $bloquesHorario[$turno] ?? $bloquesHorario['Mañana'];

            $grupoId = DB::table('grupos')->insertGetId([
                'codigo'             => $codigo,
                'periodo_id'         => $periodoId,
                'horario_id'         => $horario->id,
                'aula_id'            => $aulaIds[$aulaIdx % count($aulaIds)],
                'cupo_max'           => self::CUPO_GRUPO,
                'inscritos_actuales' => 0,
                'activo'             => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            $aulaIdx++;

            $orden = 1;
            foreach (['MAT', 'FIS', 'COM', 'ING'] as $sigla) {
                if (! isset($materias[$sigla])) continue;
                $materia = $materias[$sigla];

                $docentes = $docentesPorMateria[$sigla] ?? [];
                $docenteId = ! empty($docentes) ? $docentes[$turnoIdx % count($docentes)] : null;
                $bloques = $bloquesBase[$sigla] ?? ['07:00:00', '08:00:00'];

                DB::table('grupo_materias')->insert([
                    'grupo_id'    => $grupoId,
                    'materia_id'  => $materia->id,
                    'docente_id'  => $docenteId,
                    'aula_id'     => $aulaIds[$aulaIdx % count($aulaIds)],
                    'hora_inicio' => $bloques[0],
                    'hora_fin'    => $bloques[1],
                    'dia_semana'  => $diasMateria[$sigla],
                    'orden'       => $orden++,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $aulaIdx++;
            }

            $this->command->info("   ✓ Grupo {$codigo} creado (turno: {$turno})");
            $turnoIdx++;
        }
    }
}

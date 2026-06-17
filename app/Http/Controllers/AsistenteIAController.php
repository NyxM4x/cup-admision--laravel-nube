<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AsistenteIAController extends Controller
{
    private const ROLES_PERMITIDOS = ['Administrador', 'Coordinador CUP', 'Auditor'];
    private const MODEL             = 'llama-3.3-70b-versatile';
    private const MAX_TOKENS        = 900;

    public function consultar(Request $request): JsonResponse
    {
        // ── 1. Verificar rol ────────────────────────────────────────
        $rolNombre = Auth::user()->rol?->nombre;

        if (! in_array($rolNombre, self::ROLES_PERMITIDOS)) {
            BitacoraLogger::registrar(
                'ACCESO_DENEGADO',
                'AsistenteIA',
                'Intento de acceso al asistente IA — rol: ' . ($rolNombre ?? 'sin rol')
            );
            return response()->json(['error' => 'Sin autorización para usar el asistente IA.'], 403);
        }

        // ── 2. Validar mensaje ──────────────────────────────────────
        $request->validate(['mensaje' => 'required|string|max:500']);
        $pregunta = trim($request->mensaje);

        // ── 3. Recopilar contexto real de la BD ────────────────────
        $contexto = $this->recopilarContexto($pregunta);

        // ── 4. Llamar a Claude ─────────────────────────────────────
        $respuesta = $this->llamarClaude($pregunta, $contexto);

        // ── 5. Bitácora ────────────────────────────────────────────
        BitacoraLogger::registrar(
            'ASISTENTE_IA',
            'AsistenteIA',
            'Consulta (' . $rolNombre . '): ' . Str::limit($pregunta, 150)
        );

        return response()->json(['respuesta' => $respuesta]);
    }

    // ────────────────────────────────────────────────────────────────
    // Recopilar contexto real de la base de datos
    // ────────────────────────────────────────────────────────────────
    private function recopilarContexto(string $pregunta): string
    {
        $ctx = [];
        $ctx[] = 'Fecha/hora actual: ' . now()->format('d/m/Y H:i') . ' (Bolivia, UTC-4)';
        $ctx[] = '';

        // ── 1. PERÍODOS con estadísticas completas ──────────────────
        $ctx[] = '=== PERÍODOS ACADÉMICOS ===';
        $periodos = DB::table('periodos')->orderBy('id')->get();
        foreach ($periodos as $p) {
            $inscritos  = DB::table('inscripciones')->where('periodo_id', $p->id)->count();
            $ad1        = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('estado_admision', 'admitido_primera')->count();
            $ad2        = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('estado_admision', 'admitido_segunda')->count();
            $sinCupo    = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('estado_admision', 'no_admitido_sin_cupo')->count();
            $reprobados = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('estado_admision', 'reprobado')->count();
            $grupos     = DB::table('grupos')->where('periodo_id', $p->id)->count();
            $promGlobal = DB::table('resultados_admision')->where('periodo_id', $p->id)->avg('promedio_final');
            $estado     = $p->activo ? 'ACTIVO' : 'cerrado';

            $ctx[] = "Periodo #{$p->id} ({$estado})";
            $ctx[] = "  Inscripción: {$p->fecha_ini_inscripcion} al {$p->fecha_fin_inscripcion}";
            $ctx[] = "  Curso: {$p->fecha_ini_curso} al {$p->fecha_fin_curso}";
            $ctx[] = "  Postulantes inscritos: {$inscritos}";
            $ctx[] = "  Grupos: {$grupos}";
            $ctx[] = "  Resultados: admitidos 1ra opción={$ad1} | admitidos 2da opción={$ad2} | sin cupo={$sinCupo} | reprobados={$reprobados}";
            $ctx[] = "  Total admitidos: " . ($ad1 + $ad2);
            $ctx[] = "  Tasa aprobación: " . ($inscritos > 0 ? round(($ad1 + $ad2 + $sinCupo) * 100 / $inscritos, 1) : 0) . "% aprobaron el CUP";
            $ctx[] = "  Promedio global del periodo: " . ($promGlobal ? round($promGlobal, 2) : 'N/A');
        }
        $ctx[] = '';

        // ── 2. PROMEDIOS REALES POR MATERIA (de la tabla notas) ─────
        $ctx[] = '=== PROMEDIOS POR MATERIA (datos reales de exámenes) ===';
        foreach ($periodos as $p) {
            $ctx[] = "Periodo #{$p->id}:";
            $promedios = DB::table('notas as n')
                ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                ->join('grupos as g', 'g.id', '=', 'gm.grupo_id')
                ->join('materias as m', 'm.id', '=', 'gm.materia_id')
                ->where('g.periodo_id', $p->id)
                ->whereNotNull('n.nota_final')
                ->groupBy('m.sigla', 'm.nombre')
                ->selectRaw('m.sigla, m.nombre, ROUND(AVG(n.nota_final)::numeric,2) as promedio, COUNT(*) as total_notas, SUM(CASE WHEN n.nota_final >= 51 THEN 1 ELSE 0 END) as aprobados')
                ->get();
            foreach ($promedios as $pm) {
                $pct = $pm->total_notas > 0 ? round($pm->aprobados * 100 / $pm->total_notas, 1) : 0;
                $ctx[] = "  {$pm->sigla} ({$pm->nombre}): promedio={$pm->promedio} | {$pct}% aprobaron esta materia";
            }
        }
        $ctx[] = '';

        // ── 3. CARRERAS con admisiones por periodo ──────────────────
        $ctx[] = '=== CARRERAS Y ADMISIONES ===';
        $carreras = DB::table('carreras')->where('activo', true)->get();
        foreach ($carreras as $c) {
            $ctx[] = "{$c->codigo} — {$c->nombre}:";
            foreach ($periodos as $p) {
                $cupo = DB::table('cupo_carreras')
                    ->where('carrera_id', $c->id)->where('periodo_id', $p->id)->value('cupo_max') ?? 80;
                $ad1  = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('carrera_asignada_id', $c->id)->where('estado_admision', 'admitido_primera')->count();
                $ad2  = DB::table('resultados_admision')->where('periodo_id', $p->id)->where('carrera_asignada_id', $c->id)->where('estado_admision', 'admitido_segunda')->count();
                $ctx[] = "  Periodo #{$p->id}: cupo={$cupo} | admitidos=" . ($ad1 + $ad2) . " (1ra:{$ad1} 2da:{$ad2})";
            }
        }
        $ctx[] = '';

        // ── 4. DOCENTES con nombres y materias ──────────────────────
        $ctx[] = '=== DOCENTES ACTIVOS ===';
        $docentes = DB::table('docentes as d')
            ->join('personas as p', 'p.id', '=', 'd.persona_id')
            ->where('d.activo', true)
            ->select('d.id', 'p.nombre', 'd.materia', 'd.anios_experiencia')
            ->orderBy('d.materia')->orderBy('p.nombre')
            ->get();
        foreach ($docentes as $d) {
            $ctx[] = "  [{$d->materia}] {$d->nombre} ({$d->anios_experiencia} años exp.)";
        }
        $ctx[] = '';

        // ── 5. GRUPOS con docentes asignados ────────────────────────
        $ctx[] = '=== GRUPOS POR PERIODO ===';
        foreach ($periodos as $p) {
            $ctx[] = "Periodo #{$p->id}:";
            $grupos = DB::table('grupos as g')
                ->join('horarios as h', 'h.id', '=', 'g.horario_id')
                ->where('g.periodo_id', $p->id)
                ->select('g.id', 'g.codigo', 'g.cupo_max', 'g.inscritos_actuales', 'h.turno')
                ->orderBy('g.codigo')->get();
            foreach ($grupos as $gr) {
                // Promedios del grupo
                $promGrupo = DB::table('notas as n')
                    ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                    ->where('gm.grupo_id', $gr->id)->whereNotNull('n.nota_final')
                    ->avg('n.nota_final');
                $promStr = $promGrupo ? ' | promedio=' . round($promGrupo, 2) : '';
                // Docentes del grupo
                $docGrupo = DB::table('grupo_materias as gm')
                    ->join('materias as m', 'm.id', '=', 'gm.materia_id')
                    ->leftJoin('docentes as d', 'd.id', '=', 'gm.docente_id')
                    ->leftJoin('personas as p', 'p.id', '=', 'd.persona_id')
                    ->where('gm.grupo_id', $gr->id)
                    ->selectRaw("m.sigla, COALESCE(p.nombre, 'Sin asignar') as docente")
                    ->orderBy('m.sigla')->get()
                    ->map(fn ($r) => "{$r->sigla}:{$r->docente}")
                    ->implode(' | ');
                $ctx[] = "  {$gr->codigo} (turno {$gr->turno}): {$gr->inscritos_actuales}/{$gr->cupo_max} inscritos{$promStr}";
                if ($docGrupo) $ctx[] = "    Docentes: {$docGrupo}";
            }
        }
        $ctx[] = '';

        // ── 6. PAGOS del periodo activo ─────────────────────────────
        $activo = DB::table('periodos')->where('activo', true)->orderBy('id', 'desc')->first();
        if ($activo) {
            $ctx[] = "=== PAGOS PERIODO ACTIVO (#" . $activo->id . ") ===";
            $pagos = DB::table('pagos as pg')
                ->join('inscripciones as i', 'i.id', '=', 'pg.inscripcion_id')
                ->where('i.periodo_id', $activo->id)
                ->selectRaw('pg.estado, COUNT(*) as cnt, SUM(pg.monto) as total')
                ->groupBy('pg.estado')->get();
            if ($pagos->count()) {
                foreach ($pagos as $pg) {
                    $ctx[] = "  {$pg->estado}: {$pg->cnt} pago(s) | total=Bs " . number_format($pg->total, 2);
                }
            } else {
                $ctx[] = '  Sin registros de pago aún.';
            }
            $ctx[] = '';
        }

        // ── 7. MATERIAS del CUP ─────────────────────────────────────
        $ctx[] = '=== MATERIAS DEL CUP ===';
        DB::table('materias')->where('activo', true)->orderBy('sigla')->get()
            ->each(fn ($m) => $ctx[] = "  {$m->sigla} — {$m->nombre} | días: {$m->dias} | exámenes: {$m->cant_examenes} (pesos: {$m->peso_examen1}%/{$m->peso_examen2}%/{$m->peso_examen3}%)");
        $ctx[] = '';

        // ── 8. BÚSQUEDA POR CI (número solo, o precedido por "carnet", "CI", "C.I.", etc.) ─
        // Primero buscar con prefijo explícito, luego como número suelto
        $ciEncontrado = null;
        if (preg_match('/(?:carnet|ci|c\.i\.|carne)\s*[:#\-°nº]?\s*(\d{6,10})/i', $pregunta, $m)) {
            $ciEncontrado = $m[1];
        } elseif (preg_match('/\b(\d{6,10})\b/', $pregunta, $m)) {
            $ciEncontrado = $m[1];
        }

        if ($ciEncontrado) {
            $ci      = $ciEncontrado;
            $persona = DB::table('personas')->where('ci', $ci)->first();
            if ($persona) {
                $post = DB::table('postulantes')->where('persona_id', $persona->id)->first();
                if ($post) {
                    $ctx[] = "=== POSTULANTE ENCONTRADO (CI: {$ci}) ===";
                    $ctx[] = "Nombre: {$persona->nombre}";
                    $ctx[] = "Sexo: " . ($persona->sexo === 'M' ? 'Masculino' : 'Femenino');
                    $ctx[] = "Correo: {$persona->correo} | Teléfono: {$persona->telefono}";
                    $ctx[] = "Colegio: {$post->colegio} | Estado actual: {$post->estado}";

                    DB::table('inscripciones')->where('postulante_id', $post->id)
                        ->orderByDesc('periodo_id')->get()
                        ->each(function ($ins) use (&$ctx, $post) {
                            $prefs = DB::table('postulacion_carreras as pc')
                                ->join('carreras as c', 'c.id', '=', 'pc.carrera_id')
                                ->where('pc.inscripcion_id', $ins->id)
                                ->orderBy('pc.prioridad')
                                ->selectRaw('pc.prioridad, c.nombre')
                                ->get()
                                ->map(fn ($r) => "{$r->prioridad}ra: {$r->nombre}")
                                ->implode(' | ');

                            $pago = DB::table('pagos')->where('inscripcion_id', $ins->id)->orderByDesc('id')->first();
                            $res  = DB::table('resultados_admision')
                                ->where('postulante_id', $post->id)->where('periodo_id', $ins->periodo_id)->first();

                            $grupoAsig = DB::table('grupo_postulante as gp')
                                ->join('grupos as g', 'g.id', '=', 'gp.grupo_id')
                                ->join('horarios as h', 'h.id', '=', 'g.horario_id')
                                ->where('gp.postulante_id', $post->id)
                                ->where('g.periodo_id', $ins->periodo_id)
                                ->selectRaw("g.codigo, h.turno")->first();

                            $ctx[] = "";
                            $ctx[] = "Periodo #{$ins->periodo_id} (inscripción {$ins->estado}):";
                            $ctx[] = "  Preferencias: {$prefs}";
                            if ($grupoAsig) $ctx[] = "  Grupo asignado: {$grupoAsig->codigo} (turno {$grupoAsig->turno})";
                            if ($pago)      $ctx[] = "  Pago: estado={$pago->estado} | monto=Bs{$pago->monto}";
                            if ($res) {
                                $carrera = $res->carrera_asignada_id
                                    ? DB::table('carreras')->where('id', $res->carrera_asignada_id)->value('nombre')
                                    : 'ninguna';
                                $ctx[] = "  Resultado: promedio_final={$res->promedio_final} | estado={$res->estado_admision}";
                                $ctx[] = "  Carrera asignada: {$carrera} | Posición ranking: #{$res->posicion_ranking_general}";

                                // Notas por materia
                                $notas = DB::table('notas as n')
                                    ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                                    ->join('grupos as g', 'g.id', '=', 'gm.grupo_id')
                                    ->join('materias as m', 'm.id', '=', 'gm.materia_id')
                                    ->where('n.postulante_id', $post->id)
                                    ->where('g.periodo_id', $ins->periodo_id)
                                    ->whereNotNull('n.nota_final')
                                    ->select('m.sigla', 'n.examen1', 'n.examen2', 'n.examen3', 'n.nota_final', 'n.resultado')
                                    ->get();
                                if ($notas->count()) {
                                    $ctx[] = "  Notas por materia:";
                                    foreach ($notas as $nota) {
                                        $ctx[] = "    {$nota->sigla}: ex1={$nota->examen1} ex2={$nota->examen2} ex3={$nota->examen3} → final={$nota->nota_final} ({$nota->resultado})";
                                    }
                                }
                            }
                        });
                    $ctx[] = '';
                } else {
                    $ctx[] = "=== BÚSQUEDA CI: {$ci} ===";
                    $ctx[] = "Existe en el sistema como persona pero NO está registrada como postulante.";
                    $ctx[] = '';
                }
            } else {
                $ctx[] = "=== BÚSQUEDA CI: {$ci} ===";
                $ctx[] = "No se encontró ninguna persona con CI {$ci} en el sistema.";
                $ctx[] = '';
            }
        }

        // ── 9. TOTALES HISTÓRICOS ───────────────────────────────────
        $ctx[] = '=== TOTALES HISTÓRICOS DEL SISTEMA ===';
        $ctx[] = 'Total postulantes registrados: ' . DB::table('postulantes')->count();
        $ctx[] = 'Total admitidos histórico: '     . DB::table('postulantes')->where('estado', 'aprobado')->count();
        $ctx[] = 'Total reprobados histórico: '    . DB::table('postulantes')->where('estado', 'reprobado')->count();
        $ctx[] = 'Total docentes activos: '        . DB::table('docentes')->where('activo', true)->count();
        $ctx[] = 'Total grupos (todos los periodos): ' . DB::table('grupos')->count();
        $ctx[] = 'Total notas registradas: '       . DB::table('notas')->whereNotNull('nota_final')->count();

        return implode("\n", $ctx);
    }

    // ────────────────────────────────────────────────────────────────
    // Llamar a la API de Anthropic
    // ────────────────────────────────────────────────────────────────
    private function llamarClaude(string $pregunta, string $contexto): string
    {
        $apiKey = config('services.groq.key', '');

        if (empty($apiKey)) {
            return 'El asistente IA no está configurado. Agrega GROQ_API_KEY en el archivo .env y reinicia el servidor.';
        }

        $systemPrompt = <<<PROMPT
Eres el asistente oficial del sistema CUP-FICCT (Centro Universitario de Pregrado) de la Facultad de Ciencias Exactas y Tecnología (FICCT), UAGRM, Santa Cruz de la Sierra, Bolivia.
Tu función es asistir a administradores, coordinadores y auditores con información precisa sobre el sistema de admisión universitaria.

REGLAS OBLIGATORIAS:
1. SOLO respondes sobre el sistema CUP-FICCT. Ante cualquier otra consulta di exactamente: "Solo puedo responder consultas relacionadas al sistema CUP-FICCT."
2. NUNCA menciones, insinúes ni expongas contraseñas, tokens, claves API, credenciales ni datos sensibles de ningún tipo.
3. Responde SIEMPRE en español. Sé conciso (máximo 5-6 oraciones o usa viñetas cortas).
4. Basa tus respuestas EXCLUSIVAMENTE en los datos del contexto provisto. No inventes cifras ni nombres.
5. Si los datos necesarios no están en el contexto, di: "No tengo esa información disponible actualmente."
6. Para consultas de postulantes: cita el CI, nombre, estado y resultados exactamente como aparecen en el contexto.
7. Al comparar períodos: presenta los datos de forma estructurada con viñetas o tabla textual.
8. Para estadísticas: menciona el número exacto y el porcentaje cuando estén disponibles.
9. "Carnet" y "CI" son lo mismo: el número de Carnet de Identidad del postulante. Si el usuario dice "carnet 10000001" o "CI 10000001", busca en los datos del postulante con ese número.
10. Si el usuario busca un CI/carnet y no aparece en el contexto, indica que no se encontró en el sistema.

INTERPRETACIÓN DE ESTADOS DE ADMISIÓN:
- admitido_primera = Aprobó el CUP y fue asignado a su 1ra opción de carrera
- admitido_segunda = Aprobó el CUP y fue asignado a su 2da opción de carrera
- no_admitido_sin_cupo = Aprobó el CUP pero no hubo cupo disponible en sus opciones
- reprobado = No alcanzó el puntaje mínimo de 51/100 en el promedio final
- lista_espera = En espera de cupo disponible

NOTA MÍNIMA APROBATORIA: 51.00 puntos (de 100).
FÓRMULA PROMEDIO: (Examen1×30% + Examen2×30% + Examen3×40%) / 100

DATOS ACTUALES DEL SISTEMA (extraídos en tiempo real de la base de datos):
{$contexto}
PROMPT;

        $payload = [
            'model'       => self::MODEL,
            'max_tokens'  => self::MAX_TOKENS,
            'temperature' => 0.2,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $pregunta],
            ],
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ];

        // Intentar hasta 2 veces (1 reintento automático en caso de rate limit o error 5xx)
        for ($intento = 1; $intento <= 2; $intento++) {
            try {
                $response = Http::withHeaders($headers)
                    ->timeout(45)
                    ->post('https://api.groq.com/openai/v1/chat/completions', $payload);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content') ?? 'Sin respuesta del asistente.';
                }

                $status = $response->status();
                $body   = $response->body();
                Log::warning("AsistenteIA intento {$intento} — HTTP {$status}: {$body}");

                // Rate limit: esperar y reintentar
                if ($status === 429 && $intento < 2) {
                    sleep(2);
                    continue;
                }

                // Error de contexto demasiado largo
                if ($status === 400 && str_contains($body, 'context')) {
                    return 'La consulta generó demasiado contexto. Intenta una pregunta más específica.';
                }

                // Otros errores HTTP
                return match (true) {
                    $status === 429 => 'El asistente está recibiendo muchas consultas. Espera unos segundos e inténtalo de nuevo.',
                    $status >= 500  => 'El servicio de IA no está disponible ahora mismo. Inténtalo en unos segundos.',
                    default         => 'No pude obtener respuesta del asistente (código ' . $status . '). Inténtalo de nuevo.',
                };

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("AsistenteIA intento {$intento} — timeout/conexión: " . $e->getMessage());
                if ($intento < 2) { sleep(1); continue; }
                return 'Tiempo de espera agotado. El asistente tardó demasiado en responder. Inténtalo de nuevo.';
            } catch (\Exception $e) {
                Log::error('AsistenteIA excepción inesperada: ' . $e->getMessage());
                return 'Error inesperado al consultar el asistente. Inténtalo de nuevo.';
            }
        }

        return 'No se pudo obtener respuesta tras varios intentos. Inténtalo en unos segundos.';
    }
}

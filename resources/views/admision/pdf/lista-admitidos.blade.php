<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 11px; color: #1f2937; }
    h1 { font-size: 16px; color: #0d2c5e; margin: 0 0 2px; }
    .sub { color: #6b7280; font-size: 10px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0d2c5e; color: #fff; text-align: left; padding: 5px 6px; font-size: 10px; }
    td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; }
    .num { text-align: center; }
    .foot { margin-top: 14px; font-size: 9px; color: #6b7280; text-align: center; }
  </style>
</head>
<body>
  <h1>Lista Oficial de Admitidos — CUP FICCT-UAGRM</h1>
  <div class="sub">
    Periodo #{{ $periodo->id }}
    @if($periodo->fecha_ini_curso) · Curso: {{ \Illuminate\Support\Carbon::parse($periodo->fecha_ini_curso)->format('d/m/Y') }} @endif
    · Total admitidos: {{ $admitidos->count() }}
  </div>

  <table>
    <thead>
      <tr>
        <th class="num">Rank</th>
        <th>CI</th>
        <th>Nombre</th>
        <th>Carrera asignada</th>
        <th class="num">Promedio</th>
        <th class="num">Opción</th>
      </tr>
    </thead>
    <tbody>
      @forelse($admitidos as $r)
        <tr>
          <td class="num">{{ $r->posicion_ranking_general ?? '—' }}</td>
          <td>{{ $r->postulante->persona->ci ?? '' }}</td>
          <td>{{ $r->postulante->persona->nombre ?? '' }}</td>
          <td>{{ optional($r->carreraAsignada)->nombre ?? '—' }}</td>
          <td class="num">{{ number_format($r->promedio_final, 2) }}</td>
          <td class="num">{{ $r->estado_admision === 'admitido_primera' ? '1ra' : '2da' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;padding:12px">Sin admitidos. Ejecutá la asignación primero.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="foot">Documento generado por el Sistema CUP — UAGRM · FICCT</div>
</body>
</html>

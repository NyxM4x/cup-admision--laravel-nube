<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 10px; color: #1f2937; }
    h1 { font-size: 15px; color: #0d2c5e; margin: 0 0 2px; }
    .sub { color: #6b7280; font-size: 9px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0d2c5e; color: #fff; text-align: left; padding: 4px 5px; font-size: 9px; }
    td { padding: 3px 5px; border-bottom: 1px solid #e5e7eb; }
    .foot { margin-top: 12px; font-size: 8px; color: #6b7280; text-align: center; }
  </style>
</head>
<body>
  <h1>{{ $titulo }} — CUP FICCT-UAGRM</h1>
  <div class="sub">
    @if($periodo) Periodo #{{ $periodo->id }} · @endif
    {{ count($rows) }} registros · Generado {{ now()->format('d/m/Y H:i') }}
  </div>

  <table>
    <thead>
      <tr>@foreach($encabezados as $h)<th>{{ $h }}</th>@endforeach</tr>
    </thead>
    <tbody>
      @forelse($rows as $row)
        <tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
      @empty
        <tr><td colspan="{{ max(1, count($encabezados)) }}" style="text-align:center;padding:10px">Sin datos.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="foot">Sistema CUP — UAGRM · FICCT</div>
</body>
</html>

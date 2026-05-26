<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bitácora del Sistema</h2>
        <p class="text-sm text-gray-500">Registro inalterable de todas las acciones del sistema</p>
    </x-slot>

    @php
        // Color de badge segun la accion
        $colorAccion = function (string $accion): string {
            if (preg_match('/(_OK|_CREADO|_REACTIVADO)$/', $accion) && $accion !== 'LOGOUT_OK') {
                return 'bg-green-100 text-green-800';
            }
            if (in_array($accion, ['LOGIN_FAIL', 'LOGIN_INACTIVO', 'ACCESO_DENEGADO'])) {
                return 'bg-red-100 text-red-800';
            }
            if (in_array($accion, ['USUARIO_INACTIVADO', 'ROL_INACTIVADO', 'LOGOUT_OK'])) {
                return 'bg-yellow-100 text-yellow-800';
            }
            return 'bg-gray-100 text-gray-700';
        };
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Estadísticas --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Total registros</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['total']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Registros hoy</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['hoy']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Últimas 24h</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['ultimas_24h']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Eventos críticos 24h</div>
                    <div class="text-2xl font-bold {{ $estadisticas['eventos_criticos_24h'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($estadisticas['eventos_criticos_24h']) }}
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('bitacora.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 space-y-3">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Buscar en descripción</label>
                        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Buscar en descripción..."
                               class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Usuario</label>
                        <select name="user_id" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="">Todos los usuarios</option>
                            @foreach ($opciones['usuarios'] as $u)
                                <option value="{{ $u->id }}" @selected(($filtros['user_id'] ?? '') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Módulo</label>
                        <select name="modulo" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="">Todos los módulos</option>
                            @foreach ($opciones['modulos'] as $mod)
                                <option value="{{ $mod }}" @selected(($filtros['modulo'] ?? '') === $mod)>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Acción</label>
                        <select name="accion" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="">Todas las acciones</option>
                            @foreach ($opciones['acciones'] as $acc)
                                <option value="{{ $acc }}" @selected(($filtros['accion'] ?? '') === $acc)>{{ $acc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">IP</label>
                        <input type="text" name="ip" value="{{ $filtros['ip'] ?? '' }}" placeholder="IP..."
                               class="border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Desde</label>
                            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}"
                                   class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Hasta</label>
                            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}"
                                   class="border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">Filtrar</button>
                    <a href="{{ route('bitacora.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300">Limpiar</a>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha/Hora</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Módulo</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($registros as $r)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-500">{{ $r->id }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $r->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $r->user?->name ?? '— Sistema —' }}</td>
                                <td class="px-3 py-2 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $r->modulo }}</span>
                                </td>
                                <td class="px-3 py-2 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $colorAccion($r->accion) }}">{{ $r->accion }}</span>
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ Str::limit($r->descripcion, 80) }}</td>
                                <td class="px-3 py-2 text-sm text-gray-500">{{ $r->ip ?? '—' }}</td>
                                <td class="px-3 py-2 text-sm text-right">
                                    <a href="{{ route('bitacora.show', $r->id) }}" class="text-indigo-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No se encontraron registros con los filtros aplicados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $registros->links() }}</div>
        </div>
    </div>
</x-app-layout>

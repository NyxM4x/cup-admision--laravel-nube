<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Aulas</h2>
                <p class="text-sm text-gray-500">Catálogo de aulas disponibles para el CUP</p>
            </div>
            <a href="{{ route('aulas.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                + Nueva Aula
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Aulas activas</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['total_activas']) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Capacidad total</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['capacidad_total']) }} <span class="text-sm font-normal text-gray-500">estudiantes</span></div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">Edificios</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($estadisticas['edificios']) }}</div>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('aulas.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por código, edificio o equipamiento..."
                           class="border-gray-300 rounded-md shadow-sm w-full">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Edificio</label>
                    <select name="edificio" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todos los edificios</option>
                        @foreach ($edificios as $ed)
                            <option value="{{ $ed }}" @selected($edificio === $ed)>{{ $ed }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Estado</label>
                    <select name="estado" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm">
                        <option value="activos"   @selected($estado === 'activos')>Activas</option>
                        <option value="inactivos" @selected($estado === 'inactivos')>Inactivas</option>
                        <option value="todos"     @selected($estado === 'todos')>Todas</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">Filtrar</button>
                <a href="{{ route('aulas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-300">Limpiar</a>
            </form>

            {{-- Tabla --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Edificio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipamiento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($aulas as $aula)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $aula->codigo }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $aula->edificio }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $aula->capacidad }} <span class="text-gray-400">estudiantes</span></td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ Str::limit($aula->equipamiento, 60) ?: '—' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($aula->activo)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('aulas.edit', $aula->id) }}"
                                       class="inline-flex px-3 py-1 bg-amber-500 text-white text-xs rounded hover:bg-amber-600">Editar</a>
                                    @if ($aula->activo)
                                        <form method="POST" action="{{ route('aulas.destroy', $aula->id) }}" class="inline"
                                              onsubmit="return confirm('¿Inactivar el aula {{ $aula->codigo }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Inactivar</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('aulas.reactivar', $aula->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Reactivar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No se encontraron aulas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $aulas->links() }}</div>
        </div>
    </div>
</x-app-layout>

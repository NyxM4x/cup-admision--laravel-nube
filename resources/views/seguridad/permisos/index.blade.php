<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catálogo de Permisos del Sistema</h2>
            <a href="{{ route('permisos.matriz') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                Ver Matriz Rol-Permiso
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
                Los permisos del sistema están definidos por el código de la aplicación y no se crean dinámicamente.
                Para asignarlos a un rol, vaya a <a href="{{ route('roles.index') }}" class="underline font-semibold">Gestión de Roles</a>.
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('permisos.index') }}"
                  class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por código o descripción..."
                           class="border-gray-300 rounded-md shadow-sm w-full">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Módulo</label>
                    <select name="modulo" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($modulosDisponibles as $mod)
                            <option value="{{ $mod }}" @selected($modulo === $mod)>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">Filtrar</button>
            </form>

            @forelse ($permisos as $modulo => $permisosModulo)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <h3 class="font-semibold text-indigo-700">{{ $modulo }} <span class="text-gray-400 text-sm">({{ $permisosModulo->count() }})</span></h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase"># Roles con este permiso</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($permisosModulo as $permiso)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-900">{{ $permiso->codigo }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $permiso->descripcion }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700 text-center">{{ $permiso->roles_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-sm text-gray-500">No se encontraron permisos.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>

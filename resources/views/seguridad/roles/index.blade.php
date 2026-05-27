<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Roles</h2>
            <a href="{{ route('roles.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                + Nuevo Rol
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

            {{-- Filtros --}}
            <form method="GET" action="{{ route('roles.index') }}"
                  class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o descripción..."
                           class="border-gray-300 rounded-md shadow-sm w-full">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Estado</label>
                    <select name="estado" class="border-gray-300 rounded-md shadow-sm">
                        <option value="activos"   @selected($estado === 'activos')>Activos</option>
                        <option value="inactivos" @selected($estado === 'inactivos')>Inactivos</option>
                        <option value="todos"     @selected($estado === 'todos')>Todos</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">Filtrar</button>
            </form>

            {{-- Tabla --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"># Usuarios</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase"># Permisos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($roles as $rol)
                            @php $esSistema = $rol->nombre === 'Administrador'; @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $rol->nombre }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $rol->descripcion ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $rol->usuarios_count }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-center">{{ $rol->permisos_count }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($rol->activo)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('roles.edit', $rol->id) }}"
                                       class="inline-flex px-3 py-1 bg-amber-500 text-white text-xs rounded hover:bg-amber-600">Editar</a>

                                    @if ($rol->activo)
                                        @if ($esSistema)
                                            <button type="button" disabled title="Rol del sistema"
                                                    class="inline-flex px-3 py-1 bg-gray-300 text-gray-500 text-xs rounded cursor-not-allowed">Inactivar</button>
                                        @else
                                            <form method="POST" action="{{ route('roles.destroy', $rol->id) }}" class="inline"
                                                  onsubmit="return confirm('¿Inactivar el rol {{ $rol->nombre }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Inactivar</button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('roles.reactivar', $rol->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Reactivar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No se encontraron roles.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $roles->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>

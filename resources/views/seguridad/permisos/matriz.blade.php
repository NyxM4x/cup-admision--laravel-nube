<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Matriz Rol-Permiso</h2>
            <a href="{{ route('permisos.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">
                Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
                Vista de auditoría. Para modificar las asignaciones, editá el rol.
            </div>

            @php
                // Mapa rol_id => [permiso_id => true] para lookup rápido
                $mapa = [];
                foreach ($roles as $rol) {
                    $mapa[$rol->id] = $rol->permisos->pluck('id')->flip();
                }
                $moduloPrevio = null;
            @endphp

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50">Módulo / Permiso</th>
                            @foreach ($roles as $rol)
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ $rol->nombre }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($permisos as $permiso)
                            @if ($permiso->modulo !== $moduloPrevio)
                                <tr class="bg-indigo-50">
                                    <td colspan="{{ $roles->count() + 1 }}" class="px-4 py-2 font-semibold text-indigo-700">{{ $permiso->modulo }}</td>
                                </tr>
                                @php $moduloPrevio = $permiso->modulo; @endphp
                            @endif
                            <tr>
                                <td class="px-4 py-2 font-mono text-gray-800 sticky left-0 bg-white">{{ $permiso->codigo }}</td>
                                @foreach ($roles as $rol)
                                    <td class="px-3 py-2 text-center">
                                        @if (isset($mapa[$rol->id][$permiso->id]))
                                            <span class="text-green-600 font-bold">&#10003;</span>
                                        @else
                                            <span class="text-gray-300">&middot;</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

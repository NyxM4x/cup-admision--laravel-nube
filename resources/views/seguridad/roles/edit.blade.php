<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Rol</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @php
                    $esAdmin = $rolModel->nombre === 'Administrador';
                    $marcados = old('permisos', $permisosAsignados);
                @endphp

                @if ($esAdmin)
                    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
                        El rol Administrador siempre conserva todos los permisos del sistema.
                    </div>
                @endif

                <form method="POST" action="{{ route('roles.update', $rolModel->id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                                      :value="old('nombre', $rolModel->nombre)" required autofocus :readonly="$esAdmin" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="2"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $rolModel->descripcion) }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Permisos del rol</h3>
                        <x-input-error :messages="$errors->get('permisos')" class="mb-2" />
                        <div class="space-y-4">
                            @foreach ($permisos as $modulo => $permisosModulo)
                                <fieldset class="border border-gray-200 rounded-md p-4">
                                    <legend class="px-2 text-sm font-semibold text-indigo-700">{{ $modulo }}</legend>
                                    <div class="grid sm:grid-cols-2 gap-2">
                                        @foreach ($permisosModulo as $permiso)
                                            <label class="inline-flex items-start gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                                       @checked($esAdmin || in_array($permiso->id, $marcados))
                                                       @disabled($esAdmin)
                                                       class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span><strong>{{ $permiso->codigo }}</strong>: {{ $permiso->descripcion }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button>Actualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

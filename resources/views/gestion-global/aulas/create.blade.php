<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Aula</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('aulas.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="codigo" value="Código" />
                        <x-text-input id="codigo" name="codigo" type="text" class="block mt-1 w-full" :value="old('codigo')" placeholder="Ej: A-101" required autofocus />
                        <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edificio" value="Edificio" />
                        <x-text-input id="edificio" name="edificio" type="text" class="block mt-1 w-full" :value="old('edificio')" placeholder="Ej: Bloque A" required />
                        <x-input-error :messages="$errors->get('edificio')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="capacidad" value="Capacidad" />
                        <x-text-input id="capacidad" name="capacidad" type="number" min="1" max="500" class="block mt-1 w-full" :value="old('capacidad')" required />
                        <x-input-error :messages="$errors->get('capacidad')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="equipamiento" value="Equipamiento (opcional)" />
                        <textarea id="equipamiento" name="equipamiento" rows="2" placeholder="Proyector, pizarra..."
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('equipamiento') }}</textarea>
                        <x-input-error :messages="$errors->get('equipamiento')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('aulas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button>Guardar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

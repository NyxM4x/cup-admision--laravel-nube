<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Aula</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('aulas.update', $aulaModel->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="codigo" value="Código" />
                        <x-text-input id="codigo" name="codigo" type="text" class="block mt-1 w-full" :value="old('codigo', $aulaModel->codigo)" required autofocus />
                        <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="edificio" value="Edificio" />
                        <x-text-input id="edificio" name="edificio" type="text" class="block mt-1 w-full" :value="old('edificio', $aulaModel->edificio)" required />
                        <x-input-error :messages="$errors->get('edificio')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="capacidad" value="Capacidad" />
                        <x-text-input id="capacidad" name="capacidad" type="number" min="1" max="500" class="block mt-1 w-full" :value="old('capacidad', $aulaModel->capacidad)" required />
                        <x-input-error :messages="$errors->get('capacidad')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="equipamiento" value="Equipamiento (opcional)" />
                        <textarea id="equipamiento" name="equipamiento" rows="2"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('equipamiento', $aulaModel->equipamiento) }}</textarea>
                        <x-input-error :messages="$errors->get('equipamiento')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('aulas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button>Actualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

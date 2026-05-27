<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuario</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('usuarios.update', $user->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $user->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ci" value="CI (opcional)" />
                        <x-text-input id="ci" name="ci" type="text" class="block mt-1 w-full" :value="old('ci', $user->ci)" />
                        <x-input-error :messages="$errors->get('ci')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono (opcional)" />
                        <x-text-input id="telefono" name="telefono" type="text" class="block mt-1 w-full" :value="old('telefono', $user->telefono)" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="rol_id" value="Rol" />
                        <select id="rol_id" name="rol_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Seleccionar rol —</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" @selected(old('rol_id', $user->rol_id) == $rol->id)>{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('rol_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Contraseña (dejar vacío para mantener la actual)" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-primary-button>Actualizar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

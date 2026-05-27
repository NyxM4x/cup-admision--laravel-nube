<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de Registro de Bitácora #{{ $registro->id }}</h2>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('bitacora.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-md hover:bg-gray-800">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="divide-y divide-gray-100">
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->id }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Fecha y hora</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->created_at?->format('d/m/Y H:i:s') }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                        <dd class="text-sm text-gray-900 col-span-2">
                            @if ($registro->user)
                                {{ $registro->user->name }} &lt;{{ $registro->user->email }}&gt; (id: {{ $registro->user->id }})
                            @else
                                <span class="text-gray-500 italic">— Sistema/Anónimo —</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Acción</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->accion }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Módulo</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->modulo }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->descripcion }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">IP</dt>
                        <dd class="text-sm text-gray-900 col-span-2">{{ $registro->ip ?? '—' }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                        <dd class="text-sm text-gray-900 col-span-2 break-all">{{ $registro->user_agent ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>

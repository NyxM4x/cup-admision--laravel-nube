<x-guest-layout>

    <h1>Confirmar Contraseña</h1>
    <p class="auth-form-subtitle">
        Esta es una zona segura del sistema. Por favor confirmá tu
        contraseña antes de continuar.
    </p>

    @if ($errors->any())
        <div class="auth-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input id="password" type="password" name="password"
                       class="form-control"
                       required autofocus
                       placeholder="Tu contraseña actual">
            </div>
        </div>

        <button type="submit" class="btn-cup">
            <i class="bi bi-shield-check me-2"></i>
            Confirmar
        </button>

    </form>

</x-guest-layout>

<x-guest-layout>

    <h1>Restablecer Contraseña</h1>
    <p class="auth-form-subtitle">
        Ingresá tu nueva contraseña para acceder al sistema.
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email', $request->email) }}"
                       required autofocus readonly>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input id="password" type="password" name="password"
                       class="form-control"
                       required
                       placeholder="Mínimo 8 caracteres">
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock-fill input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control"
                       required
                       placeholder="Repetí la contraseña">
            </div>
        </div>

        <button type="submit" class="btn-cup">
            <i class="bi bi-check-circle me-2"></i>
            Restablecer Contraseña
        </button>

    </form>

</x-guest-layout>

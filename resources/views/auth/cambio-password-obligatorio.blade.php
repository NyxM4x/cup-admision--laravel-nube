<x-guest-layout>

    <h1>Cambiar Contraseña</h1>
    <p class="auth-form-subtitle">
        Por seguridad, debés cambiar la contraseña asignada por el administrador
        antes de continuar usando el sistema.
    </p>

    @if (session('warning'))
        <div class="auth-error" style="background:rgba(245,158,11,0.08);border-color:rgba(245,158,11,0.30);color:#92400e;">
            <i class="bi bi-info-circle-fill"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.cambio.store') }}">
        @csrf

        <div class="mb-3">
            <label for="current_password" class="form-label">Contraseña actual (la que te asignaron)</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input id="current_password" type="password" name="current_password"
                       class="form-control" required autofocus
                       placeholder="Tu contraseña inicial">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-shield-lock input-icon"></i>
                <input id="password" type="password" name="password"
                       class="form-control" required
                       placeholder="Mínimo 8 caracteres">
            </div>
            <small class="text-muted d-block mt-1" style="font-size:0.78rem;">
                <i class="bi bi-info-circle"></i>
                Debe contener: 8+ caracteres, 1 mayúscula, 1 minúscula y 1 número.
            </small>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-shield-check input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control" required
                       placeholder="Repetí la contraseña">
            </div>
        </div>

        <button type="submit" class="btn-cup">
            <i class="bi bi-check-circle me-2"></i>
            Cambiar Contraseña y Continuar
        </button>

        <div class="auth-form-footer">
            Una vez cambiada, no se te volverá a pedir.
        </div>

    </form>

</x-guest-layout>

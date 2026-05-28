<x-guest-layout>

    <h1>Crear Cuenta</h1>
    <p class="auth-form-subtitle">
        Completá tus datos para crear una cuenta en el sistema CUP.
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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre completo</label>
            <div class="auth-input-wrap">
                <i class="bi bi-person input-icon"></i>
                <input id="name" type="text" name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required autofocus
                       placeholder="Tu nombre completo">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required
                       placeholder="usuario@cup.uagrm.bo">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
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
            <i class="bi bi-person-plus me-2"></i>
            Crear Cuenta
        </button>

        <div class="auth-meta">
            <span style="color:var(--cup-muted)">¿Ya tenés cuenta?</span>
            <a href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Iniciar sesión
            </a>
        </div>

    </form>

</x-guest-layout>

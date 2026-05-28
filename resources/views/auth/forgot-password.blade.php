<x-guest-layout>

    <h1>Recuperar Contraseña</h1>
    <p class="auth-form-subtitle">
        ¿Olvidaste tu contraseña? Ingresá tu correo electrónico y te
        enviaremos un enlace para restablecerla.
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="auth-success">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Errores -->
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required autofocus
                       placeholder="usuario@cup.uagrm.bo">
            </div>
        </div>

        <button type="submit" class="btn-cup">
            <i class="bi bi-envelope-paper me-2"></i>
            Enviar Enlace de Recuperación
        </button>

        <div class="auth-meta">
            <span></span>
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left me-1"></i>
                Volver al login
            </a>
        </div>

        <div class="auth-form-footer">
            Si no recibís el correo en unos minutos, revisá tu carpeta de spam
            o contactá al Coordinador del CUP.
        </div>

    </form>

</x-guest-layout>

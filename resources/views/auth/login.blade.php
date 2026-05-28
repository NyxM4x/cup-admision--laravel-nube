<x-guest-layout>

    <h1>Iniciar Sesión</h1>
    <p class="auth-form-subtitle">
        Ingresá con tus credenciales institucionales para acceder al sistema.
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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email"
                       class="form-control"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="usuario@cup.uagrm.bo">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input id="password" type="password" name="password"
                       class="form-control"
                       required autocomplete="current-password"
                       placeholder="Ingresá tu contraseña">
            </div>
        </div>

        <!-- Remember -->
        <div class="form-check mb-3">
            <input type="checkbox" id="remember_me" name="remember" class="form-check-input">
            <label for="remember_me" class="form-check-label" style="font-size:0.88rem; color:var(--cup-muted);">
                Recordar mi sesión
            </label>
        </div>

        <button type="submit" class="btn-cup">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Iniciar Sesión
        </button>

        @if (Route::has('password.request'))
            <div class="auth-meta">
                <span></span>
                <a href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        @endif

        <div class="auth-form-footer">
            Para problemas de acceso, contactá al Coordinador del CUP.
        </div>

    </form>

</x-guest-layout>

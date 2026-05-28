<x-guest-layout>

    <h1>Verificar Correo Electrónico</h1>
    <p class="auth-form-subtitle">
        Gracias por registrarte. Antes de continuar, por favor verificá
        tu correo electrónico haciendo click en el enlace que te enviamos.
        Si no lo recibiste, podemos enviarte uno nuevo.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-success">
            <i class="bi bi-check-circle me-1"></i>
            Se envió un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-cup">
                <i class="bi bi-envelope-paper me-2"></i>
                Reenviar Correo de Verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none; border:none; color:var(--cup-muted); font-size:0.88rem; cursor:pointer; padding:0.5rem; width:100%;">
                <i class="bi bi-box-arrow-right me-1"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>

</x-guest-layout>

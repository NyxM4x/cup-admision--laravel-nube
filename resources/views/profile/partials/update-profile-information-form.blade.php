<p class="text-muted small mb-3">
  Actualizá tu nombre y correo electrónico institucional.
</p>

@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
  <div class="alert alert-cup-warning mb-3">
    <small>
      <i class="bi bi-exclamation-triangle me-1"></i>
      Tu dirección de correo no está verificada.
      <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">Reenviar verificación</button>
    </small>
  </div>

  @if (session('status') === 'verification-link-sent')
    <div class="alert alert-cup-success mb-3">
      <small><i class="bi bi-check-circle me-1"></i> Se envió un nuevo enlace de verificación a tu correo.</small>
    </div>
  @endif
@endif

<form id="send-verification" method="POST" action="{{ route('verification.send') }}">
  @csrf
</form>

<form method="POST" action="{{ route('profile.update') }}">
  @csrf
  @method('PATCH')

  <div class="mb-3">
    <label for="name" class="form-label">Nombre completo</label>
    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
           class="form-control" required autofocus autocomplete="name">
    @error('name')
      <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Correo electrónico</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
           class="form-control" required autocomplete="username">
    @error('email')
      <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
  </div>

  <div class="d-flex align-items-center gap-2">
    <button type="submit" class="btn btn-cup-primary">
      <i class="bi bi-check-circle me-1"></i> Guardar cambios
    </button>

    @if (session('status') === 'profile-updated')
      <small class="text-success">
        <i class="bi bi-check-circle me-1"></i> Guardado correctamente.
      </small>
    @endif
  </div>
</form>

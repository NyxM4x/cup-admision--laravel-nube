<p class="text-muted small mb-3">
  Asegurate de usar una contraseña larga y segura.
</p>

<form method="POST" action="{{ route('password.update') }}">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label for="update_password_current_password" class="form-label">Contraseña actual</label>
    <input id="update_password_current_password" type="password" name="current_password"
           class="form-control" autocomplete="current-password">
    @error('current_password', 'updatePassword')
      <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
  </div>

  <div class="mb-3">
    <label for="update_password_password" class="form-label">Nueva contraseña</label>
    <input id="update_password_password" type="password" name="password"
           class="form-control" autocomplete="new-password">
    @error('password', 'updatePassword')
      <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
  </div>

  <div class="mb-3">
    <label for="update_password_password_confirmation" class="form-label">Confirmar contraseña</label>
    <input id="update_password_password_confirmation" type="password" name="password_confirmation"
           class="form-control" autocomplete="new-password">
    @error('password_confirmation', 'updatePassword')
      <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
  </div>

  <div class="d-flex align-items-center gap-2">
    <button type="submit" class="btn btn-cup-primary">
      <i class="bi bi-shield-check me-1"></i> Cambiar contraseña
    </button>

    @if (session('status') === 'password-updated')
      <small class="text-success">
        <i class="bi bi-check-circle me-1"></i> Contraseña actualizada.
      </small>
    @endif
  </div>
</form>

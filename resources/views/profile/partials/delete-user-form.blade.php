<p class="text-muted small mb-3">
  Una vez eliminada tu cuenta, todos los recursos asociados se borrarán permanentemente.
  Antes de eliminar, por favor descargá cualquier información que quieras conservar.
</p>

<button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
  <i class="bi bi-trash me-1"></i> Eliminar mi cuenta
</button>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>Confirmar eliminación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>¿Estás seguro de que querés eliminar tu cuenta? Esta acción no se puede deshacer.</p>
          <p class="text-muted small">Por favor ingresá tu contraseña para confirmar.</p>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" name="password" class="form-control" required>
            @error('password', 'userDeletion')
              <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash me-1"></i> Eliminar cuenta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@if ($errors->userDeletion->isNotEmpty())
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    });
  </script>
@endif

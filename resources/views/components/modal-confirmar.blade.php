{{-- Modal global de confirmación CUP --}}
{{-- Se invoca via JS: cupConfirmar({ titulo, mensaje, subtexto, textoBoton, tipo, formSelector }) --}}
<div class="modal fade" id="cupModalConfirmar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
      <div class="modal-header" style="background: linear-gradient(135deg, var(--cup-primary, #0D2C5E) 0%, var(--cup-primary-light, #1E5FA8) 100%); color: white; border: none; padding: 20px 24px;">
        <h5 class="modal-title d-flex align-items-center gap-2" id="cupModalConfirmarTitulo">
          <i class="bi bi-exclamation-triangle-fill" id="cupModalConfirmarIcono"></i>
          <span id="cupModalConfirmarTituloTexto">Confirmar acción</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" style="padding: 24px;">
        <p id="cupModalConfirmarMensaje" class="mb-0" style="color: #374151; font-size: 15px; line-height: 1.6;">
          ¿Estás seguro de realizar esta acción?
        </p>
        <p id="cupModalConfirmarSubtexto" class="text-muted mb-0 mt-2" style="font-size: 13px;"></p>
      </div>
      <div class="modal-footer" style="border: none; padding: 16px 24px 24px; gap: 8px;">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x me-1"></i> Cancelar
        </button>
        <button type="button" class="btn" id="cupModalConfirmarBoton" style="background: var(--cup-warning, #F59E0B); color: white; border: none; padding: 8px 20px;">
          <i class="bi bi-check2 me-1"></i> <span id="cupModalConfirmarBotonTexto">Confirmar</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    let modalInstance = null;

    window.cupConfirmar = function (opciones) {
      const {
        titulo       = 'Confirmar acción',
        mensaje      = '¿Estás seguro?',
        subtexto     = '',
        textoBoton   = 'Confirmar',
        tipo         = 'warning',   // 'warning' | 'danger' | 'success'
        formSelector = null,         // selector del form a enviar
        onConfirmar  = null          // callback alternativo
      } = opciones;

      document.getElementById('cupModalConfirmarTituloTexto').textContent = titulo;
      document.getElementById('cupModalConfirmarMensaje').textContent = mensaje;
      document.getElementById('cupModalConfirmarSubtexto').textContent = subtexto;
      document.getElementById('cupModalConfirmarBotonTexto').textContent = textoBoton;

      const icono  = document.getElementById('cupModalConfirmarIcono');
      let   boton  = document.getElementById('cupModalConfirmarBoton');
      const header = document.querySelector('#cupModalConfirmar .modal-header');

      if (tipo === 'danger') {
        icono.className = 'bi bi-trash-fill';
        boton.style.background = '#DC2626';
        header.style.background = 'linear-gradient(135deg, #B91C1C 0%, #DC2626 100%)';
      } else if (tipo === 'success') {
        icono.className = 'bi bi-check-circle-fill';
        boton.style.background = '#198754';
        header.style.background = 'linear-gradient(135deg, #166534 0%, #198754 100%)';
      } else {
        icono.className = 'bi bi-exclamation-triangle-fill';
        boton.style.background = '#F59E0B';
        header.style.background = 'linear-gradient(135deg, #0D2C5E 0%, #1E5FA8 100%)';
      }

      // Limpiar listeners anteriores clonando el botón
      const newBoton = boton.cloneNode(true);
      boton.parentNode.replaceChild(newBoton, boton);
      boton = newBoton;

      boton.addEventListener('click', function () {
        if (formSelector) {
          const form = document.querySelector(formSelector);
          if (form) form.submit();
        }
        if (typeof onConfirmar === 'function') onConfirmar();
        if (modalInstance) modalInstance.hide();
      });

      if (!modalInstance) {
        modalInstance = new bootstrap.Modal(document.getElementById('cupModalConfirmar'));
      }
      modalInstance.show();
    };
  })();
</script>

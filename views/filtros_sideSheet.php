<div class="offcanvas offcanvas-end custom-offcanvas" tabindex="-1" id="filtroLateral" aria-labelledby="filtroLateralLabel" aria-modal="true" role="dialog">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="filtroLateralLabel">Filtrar Datos Por:</h5>
    <button type="button" class="btn btn-danger btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Cerrar">
      <i class="bi bi-x"></i>
    </button>
  </div>    

  <div class="offcanvas-body">
    <form id="formFiltros" method="POST" action="home.php?menu=tables&report=1">

      <!-- Finca -->
      <div class="mb-3">
        <label for="finca" class="form-label">Finca:</label>
        <select class="form-select select2" id="finca" name="finca[]" multiple></select>
      </div>

      <!-- Bloque -->
      <div class="mb-3">
        <label for="bloque" class="form-label">Bloque:</label>
        <select class="form-select select2" id="bloque" name="bloque[]" multiple></select>
      </div>

      <!-- Variedad -->
      <div class="mb-3">
        <label for="variedad" class="form-label">Variedad:</label>
        <select class="form-select select2" id="variedad" name="variedad[]" multiple></select>
      </div>

      <!-- Siembra -->
      <div class="mb-3">
        <label for="siembra" class="form-label">Siembra:</label>
        <select class="form-select select2" id="siembra" name="siembra[]" multiple></select>
      </div>

      <button type="submit" name="buscar" class="btn btn-success w-100" disabled>Buscar</button>
    </form>
  </div>
</div>
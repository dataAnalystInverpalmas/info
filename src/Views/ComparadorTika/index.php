<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
:root {
  --tika-bg: #f6f8f7;
  --tika-panel: #ffffff;
  --tika-fg: #17312d;
  --tika-muted: #5f6f6b;
  --tika-line: #dbe7e4;
  --tika-soft: #eef6f4;
  --tika-accent: #00796B;
  --tika-pos: #15803d;
  --tika-neg: #b91c1c;
  --tika-warn: #8a5a00;
  --tika-warn-bg: #fff4dc;
  --s1: #00796B;
  --s2: #eb6834;
  --s3: #2a78d6;
  --s4: #6250d6;
  --s5: #d55181;
  --tika-f-display: "Inter", "Segoe UI", system-ui, sans-serif;
  --tika-f-body: "Inter", "Segoe UI", system-ui, sans-serif;
}

.crud-page-header .crud-header-actions.report-actions-bar {
  margin-bottom: 0 !important;
  flex: 1 1 auto;
  justify-content: flex-end;
}
.crud-page-header .crud-page-subtitle {
  font-size: 0.85rem;
  margin-top: 0;
}

/* Selector superior de modelos en BD */
.tika-selector-bar {
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 14px;
  padding: 14px 16px;
  margin-bottom: 18px;
  box-shadow: 0 6px 20px rgba(0, 77, 64, 0.04);
}
.tika-selector-bar .select2-container {
  width: 100% !important;
}
.tika-selector-bar .select2-container .select2-selection--multiple {
  border: 1px solid var(--tika-line);
  border-radius: 8px;
  min-height: 38px;
  padding: 2px 6px;
}
.tika-selector-bar .select2-container--default .select2-selection--multiple .select2-selection__choice {
  background-color: var(--tika-soft);
  border: 1px solid #b2dfdb;
  color: var(--tika-fg);
  border-radius: 6px;
  font-size: 0.82rem;
  font-weight: 600;
  padding: 2px 8px;
}
.tika-selector-bar .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
  color: var(--tika-accent);
  margin-right: 5px;
}

/* Estilos encapsulados de plantilla_comparacion.html adaptados al tema Inverpalmas */
.tika-comp-wrap {
  color: var(--tika-fg);
  font: 14.5px/1.45 var(--tika-f-body);
  display: grid;
  gap: 18px;
  padding-bottom: 36px;
}
.tika-comp-wrap * {
  box-sizing: border-box;
}
.tika-comp-wrap h1,
.tika-comp-wrap h2,
.tika-comp-wrap h3 {
  font-family: var(--tika-f-display);
  font-weight: 700;
  margin: 0;
  color: var(--tika-fg);
}
.tika-comp-wrap h1 {
  font-size: 1.35rem;
  letter-spacing: -0.01em;
}
.tika-comp-wrap h2 {
  font-size: 1.05rem;
}
.tika-comp-wrap .eyebrow {
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--tika-muted);
  font-weight: 600;
}
.tika-comp-wrap .muted {
  color: var(--tika-muted);
}
.tika-comp-wrap .num {
  font-variant-numeric: tabular-nums;
}
.tika-comp-wrap .tika-header {
  display: grid;
  gap: 12px;
}
.tika-comp-wrap .head-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 20px;
  align-items: baseline;
  justify-content: space-between;
}
.tika-comp-wrap .modelos {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 10px;
}
.tika-comp-wrap .mod {
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 10px;
  padding: 10px 12px;
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 4px 10px;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 77, 64, 0.03);
}
.tika-comp-wrap .mod.off {
  opacity: 0.55;
}
.tika-comp-wrap .sw {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  display: inline-block;
}
.tika-comp-wrap .mod .id {
  font-family: var(--tika-f-display);
  font-weight: 700;
  font-size: 1.1rem;
  display: flex;
  gap: 8px;
  align-items: center;
}
.tika-comp-wrap .mod .esc {
  grid-column: 1 / -1;
  font-size: 0.78rem;
  color: var(--tika-muted);
  word-break: break-all;
  line-height: 1.3;
}
.tika-comp-wrap .mod .ctl {
  display: flex;
  gap: 14px;
  justify-content: flex-end;
  font-size: 0.85rem;
}
.tika-comp-wrap .mod label {
  display: flex;
  gap: 5px;
  align-items: center;
  cursor: pointer;
  margin-bottom: 0;
}
.tika-comp-wrap .filtros {
  position: sticky;
  top: 56px;
  z-index: 10;
  background: rgba(246, 248, 247, 0.96);
  backdrop-filter: blur(6px);
  border: 1px solid var(--tika-line);
  border-radius: 12px;
  padding: 10px 14px;
  display: flex;
  flex-wrap: wrap;
  gap: 10px 16px;
  align-items: end;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}
.tika-comp-wrap .f {
  display: grid;
  gap: 3px;
  margin-bottom: 0;
}
.tika-comp-wrap .f > span {
  font-size: 0.72rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--tika-muted);
  font-weight: 600;
}
.tika-comp-wrap select,
.tika-comp-wrap input[type=search],
.tika-comp-wrap input[type=text] {
  font: inherit;
  font-size: 0.9rem;
  color: var(--tika-fg);
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 6px;
  padding: 5px 8px;
  min-height: 34px;
}
.tika-comp-wrap input[list] {
  width: 190px;
}
.tika-comp-wrap .seg {
  display: inline-flex;
  border: 1px solid var(--tika-line);
  border-radius: 6px;
  overflow: hidden;
  background: var(--tika-panel);
}
.tika-comp-wrap .seg button {
  font: inherit;
  font-size: 0.86rem;
  border: 0;
  background: none;
  color: var(--tika-fg);
  padding: 6px 11px;
  cursor: pointer;
  min-height: 32px;
}
.tika-comp-wrap .seg button + button {
  border-left: 1px solid var(--tika-line);
}
.tika-comp-wrap .seg button[aria-pressed="true"] {
  background: var(--tika-accent);
  color: var(--tika-panel);
  font-weight: 600;
}
.tika-comp-wrap .btn-tika {
  font: inherit;
  font-size: 0.86rem;
  border: 1px solid var(--tika-line);
  background: var(--tika-panel);
  color: var(--tika-fg);
  border-radius: 6px;
  padding: 6px 11px;
  cursor: pointer;
  min-height: 34px;
  font-weight: 500;
}
.tika-comp-wrap .btn-tika:hover {
  background: var(--tika-soft);
}
.tika-comp-wrap .panel {
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 14px;
  padding: 16px;
  display: grid;
  gap: 12px;
  min-width: 0;
  box-shadow: 0 8px 24px rgba(0, 77, 64, 0.04);
}
.tika-comp-wrap .panel-head {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 16px;
  justify-content: space-between;
  align-items: center;
}
.tika-comp-wrap .scroll {
  overflow-x: auto;
  min-width: 0;
}
.tika-comp-wrap table {
  border-collapse: collapse;
  width: 100%;
  font-size: 0.88rem;
}
.tika-comp-wrap th,
.tika-comp-wrap td {
  padding: 6px 10px;
  text-align: right;
  border-bottom: 1px solid var(--tika-line);
  white-space: nowrap;
}
.tika-comp-wrap th:first-child,
.tika-comp-wrap td:first-child {
  text-align: left;
}
.tika-comp-wrap thead th {
  font-weight: 600;
  color: var(--tika-muted);
  font-size: 0.76rem;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  position: sticky;
  top: 0;
  background: var(--tika-panel);
  z-index: 2;
}
.tika-comp-wrap tbody tr:hover {
  background: var(--tika-soft);
}
.tika-comp-wrap td.sub {
  padding-left: 24px;
  color: var(--tika-muted);
}
.tika-comp-wrap .pos {
  color: var(--tika-pos);
  font-weight: 600;
}
.tika-comp-wrap .neg {
  color: var(--tika-neg);
  font-weight: 600;
}
.tika-comp-wrap .pct {
  font-size: 0.78rem;
  color: var(--tika-muted);
  margin-left: 4px;
  font-weight: 400;
}
.tika-comp-wrap .notas {
  display: grid;
  gap: 6px;
  font-size: 0.85rem;
}
.tika-comp-wrap .nota {
  background: var(--tika-warn-bg);
  color: var(--tika-warn);
  border-radius: 6px;
  padding: 6px 10px;
}
.tika-comp-wrap .tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  border-bottom: 1px solid var(--tika-line);
}
.tika-comp-wrap .tabs button {
  font: inherit;
  font-family: var(--tika-f-display);
  font-weight: 500;
  font-size: 0.96rem;
  background: none;
  border: 0;
  border-bottom: 3px solid transparent;
  color: var(--tika-muted);
  padding: 8px 14px;
  cursor: pointer;
}
.tika-comp-wrap .tabs button[aria-selected="true"] {
  color: var(--tika-accent);
  border-bottom-color: var(--tika-accent);
  font-weight: 700;
}
.tika-comp-wrap .legend {
  display: flex;
  flex-wrap: wrap;
  gap: 6px 16px;
  font-size: 0.86rem;
}
.tika-comp-wrap .legend span {
  display: inline-flex;
  gap: 6px;
  align-items: center;
}
.tika-comp-wrap .legend .sw {
  width: 14px;
  height: 3px;
  border-radius: 2px;
}
.tika-comp-wrap .chart {
  position: relative;
  min-width: 0;
}
.tika-comp-wrap .chart svg {
  display: block;
  width: 100%;
  height: auto;
  overflow: visible;
}
.tika-comp-wrap .chart text {
  fill: var(--tika-muted);
  font: 11px var(--tika-f-body);
  font-variant-numeric: tabular-nums;
}
.tika-comp-wrap .tip {
  position: absolute;
  pointer-events: none;
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 0.84rem;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
  min-width: 190px;
  z-index: 5;
}
.tika-comp-wrap .tip b {
  font-family: var(--tika-f-display);
}
.tika-comp-wrap .tip .r {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  align-items: center;
}
.tika-comp-wrap .tip .r span:first-child {
  display: inline-flex;
  gap: 6px;
  align-items: center;
}
.tika-comp-wrap details summary {
  cursor: pointer;
  color: var(--tika-accent);
  font-weight: 600;
  font-size: 0.88rem;
}
.tika-comp-wrap th.sort {
  cursor: pointer;
  user-select: none;
}
.tika-comp-wrap th.sort[aria-sort]::after {
  content: " ▾";
}
.tika-comp-wrap th.sort[aria-sort="ascending"]::after {
  content: " ▴";
}
.tika-comp-wrap tr.clic {
  cursor: pointer;
}
.tika-comp-wrap .tika-footer {
  font-size: 0.82rem;
  color: var(--tika-muted);
  display: grid;
  gap: 6px;
  background: var(--tika-panel);
  border: 1px solid var(--tika-line);
  border-radius: 12px;
  padding: 12px 16px;
}
</style>

<!-- Encabezado estándar de Reportes & Curvas -->
<div class="crud-page-header d-print-none no-print">
  <div class="crud-header-left">
    <div class="crud-header-icon">
      <span class="material-icons">compare</span>
    </div>
    <div>
      <div class="crud-page-subtitle">
        <span class="crud-page-badge">Reportes &amp; Curvas</span>
        <span class="text-muted d-none d-sm-inline">• Comparador de escenarios Tika (Esquejes, Producción, Guarde y Salvamento)</span>
      </div>
    </div>
  </div>
  <div class="crud-header-actions report-actions-bar mb-0 d-flex gap-2 align-items-center">
    <span class="small text-muted me-2" id="generado"></span>
    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center" id="btnCopiarEnlace" title="Copiar enlace de esta comparación">
      <span class="material-icons me-1" style="font-size:1rem;">share</span>
      Compartir enlace
    </button>
    <button type="button" class="btn btn-sm btn-teal d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalImportarTika">
      <span class="material-icons me-1" style="font-size:1rem;">cloud_upload</span>
      Importar modelo
    </button>
  </div>
</div>

<!-- Barra superior: Selector de modelos cargados en BD y botón Comparar -->
<div class="tika-selector-bar d-print-none">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-lg-9">
      <label for="tikaModelosSelect" class="form-label small fw-bold text-uppercase mb-1" style="color: var(--tika-muted); letter-spacing: 0.04em;">
        Modelos cargados para comparar
      </label>
      <select id="tikaModelosSelect" class="form-select" multiple="multiple">
        <?php foreach ($modelosDisponibles as $m): ?>
          <?php $sel = in_array((int)$m['id'], $modelosSeleccionados, true) ? 'selected' : ''; ?>
          <option value="<?php echo (int)$m['id']; ?>" <?php echo $sel; ?>>
            <?php echo (int)$m['id'] . ' — ' . htmlspecialchars((string)$m['escenario']) . ' (' . htmlspecialchars((string)$m['fecha']) . ')'; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-lg-3 d-flex gap-2">
      <button type="button" class="btn btn-teal flex-grow-1 fw-semibold d-flex align-items-center justify-content-center" id="btnCompararModelos" style="min-height: 38px;">
        <span class="material-icons me-1" style="font-size: 1.15rem;">sync_alt</span>
        Comparar
      </button>
    </div>
  </div>
  <div id="tikaEstadoCarga" class="small text-muted mt-1" style="display:none;"></div>
</div>

<!-- Contenedor principal del Comparador (plantilla_comparacion.html integrada) -->
<div class="tika-comp-wrap" id="tikaCompWrap">
  <div class="tika-header">
    <div class="modelos" id="modelos"></div>
  </div>

  <div class="filtros" id="filtros">
    <label class="f"><span>Semana desde</span><select id="sIni"></select></label>
    <label class="f"><span>Semana hasta</span><select id="sFin"></select></label>
    <div class="f"><span>Flor</span><div class="seg" id="flor"></div></div>
    <label class="f"><span>Finca</span><select id="finca"></select></label>
    <label class="f"><span>Variedad</span><input type="text" id="variedad" list="listaVar" placeholder="Todas" autocomplete="off"></label>
    <datalist id="listaVar"></datalist>
    <button class="btn-tika" id="limpiar" type="button">Quitar filtros</button>
  </div>

  <section class="panel">
    <div class="panel-head"><h2>Resumen</h2><span class="muted" id="resBase"></span></div>
    <div class="scroll"><table id="tResumen"></table></div>
    <div class="notas" id="notas"></div>
  </section>

  <section class="panel">
    <div class="tabs" role="tablist" id="tabs"></div>
    <div class="panel-head">
      <div class="legend" id="leyenda"></div>
      <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">
        <span id="subCtl"></span>
        <div class="seg" id="modo">
          <button type="button" data-v="val" aria-pressed="true">Valores</button>
          <button type="button" data-v="dif" aria-pressed="false">Diferencia vs base</button>
        </div>
      </div>
    </div>
    <div class="chart" id="chart"></div>
    <details><summary>Ver tabla por semana</summary><div class="scroll" style="max-height:420px;overflow-y:auto"><table id="tSemana"></table></div></details>
  </section>

  <section class="panel">
    <div class="panel-head">
      <h2 id="desTit">Desglose</h2>
      <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:end">
        <label class="f"><span>Agrupar por</span><select id="dim"></select></label>
        <label class="f"><span>Buscar</span><input type="search" id="buscar" placeholder="Filtrar filas"></label>
      </div>
    </div>
    <p class="muted" style="margin:0;font-size:.86rem">Usa la misma métrica y filtros de la pestaña activa. Haz clic en una variedad para filtrar toda la página.</p>
    <div class="scroll" style="max-height:560px;overflow-y:auto"><table id="tDes"></table></div>
  </section>

  <div class="tika-footer">
    <div class="d-flex flex-wrap align-items-center gap-2">
      <label class="btn-tika mb-0" style="display:inline-block">
        Agregar modelo desde carpeta local…
        <input type="file" id="carpeta" webkitdirectory multiple hidden>
      </label>
      <span id="cargaMsg"></span>
    </div>
    <div>Elige la carpeta de un modelo (por ejemplo <span class="num">modelos/547</span>) con esquejes_futuros, proy1-3, guarde y salvamento para previsualizar en tu navegador, o usa <b>Importar modelo</b> arriba para guardarlo en la base de datos.</div>
    <div>Producción = suma de proy1-3 (tallos por semana de producción); "Nuevas siembras" son las filas cuyo origen es el escenario y "Existentes" las de origen Planeado. Salvamento ignora el filtro de finca.</div>
  </div>
</div>

<!-- Modal Administración: Importar Modelo Tika -->
<div class="modal fade" id="modalImportarTika" tabindex="-1" aria-labelledby="modalImportarTikaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold d-flex align-items-center" id="modalImportarTikaLabel">
          <span class="material-icons text-success me-2">cloud_upload</span>
          Importar Modelos Tika a Base de Datos
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <!-- Navegación por pestañas: Red vs Subida de CSV -->
        <ul class="nav nav-tabs mb-3" id="importarTikaTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tab-red-btn" data-bs-toggle="tab" data-bs-target="#tabImportarRed" type="button" role="tab">
              <span class="material-icons align-middle me-1" style="font-size:1rem;">lan</span>
              Desde Carpeta de Red
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tab-csv-btn" data-bs-toggle="tab" data-bs-target="#tabImportarCsv" type="button" role="tab">
              <span class="material-icons align-middle me-1" style="font-size:1rem;">upload_file</span>
              Subir 6 archivos CSV
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- Pestaña 1: Ruta de Red -->
          <div class="tab-pane fade show active" id="tabImportarRed" role="tabpanel">
            <div class="alert <?php echo $redDisponible ? 'alert-success' : 'alert-warning'; ?> py-2 px-3 small d-flex align-items-center mb-3">
              <span class="material-icons me-2" style="font-size:1.2rem;"><?php echo $redDisponible ? 'check_circle' : 'warning'; ?></span>
              <div>
                Ruta de red configurada: <code><?php echo htmlspecialchars($rutaRed); ?>\&lt;ID&gt;</code>
                <?php if ($redDisponible): ?>
                  <span class="badge rounded-pill bg-success ms-1">Accesible</span>
                <?php else: ?>
                  <span class="badge rounded-pill bg-warning text-dark ms-1">No detectada (use la pestaña Subir 6 archivos CSV)</span>
                <?php endif; ?>
              </div>
            </div>

            <form id="formImportarRed">
              <div class="mb-3">
                <label class="form-label small fw-semibold" for="inputIdsRed">ID(s) de modelo a importar o reimportar</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="inputIdsRed" name="ids" placeholder="Ej: 547, 548, 499" required>
                  <button type="submit" class="btn btn-success fw-semibold d-flex align-items-center" id="btnSubmitRed">
                    <span class="material-icons me-1" style="font-size:1.1rem;">download_for_offline</span>
                    Importar desde Red
                  </button>
                </div>
                <div class="form-text">Puede ingresar uno o varios IDs numéricos separados por coma o espacio. Reimportar un modelo reemplaza sus datos dentro de una transacción.</div>
              </div>
            </form>
          </div>

          <!-- Pestaña 2: Formulario de Subida de los 6 CSV -->
          <div class="tab-pane fade" id="tabImportarCsv" role="tabpanel">
            <form id="formImportarCsv" enctype="multipart/form-data">
              <input type="hidden" name="modo" value="archivos">
              <div class="row g-3 mb-3">
                <div class="col-12 col-sm-4">
                  <label class="form-label small fw-semibold" for="inputModeloIdCsv">ID del Modelo (numérico)</label>
                  <input type="number" min="1" step="1" class="form-control" id="inputModeloIdCsv" name="modelo_id" placeholder="Ej: 547" required>
                </div>
                <div class="col-12 col-sm-8">
                  <label class="form-label small fw-semibold" for="inputArchivosMulti">Seleccionar los 6 archivos CSV juntos</label>
                  <input type="file" class="form-control" id="inputArchivosMulti" name="archivos[]" accept=".csv" multiple>
                  <div class="form-text">Seleccione <code>esquejes_futuros.csv</code>, <code>proy1.csv</code>, <code>proy2.csv</code>, <code>proy3.csv</code>, <code>guarde.csv</code> (o <code>granel.csv</code>) y <code>salvamento.csv</code>.</div>
                  <div id="csvFilesStatus" class="small mt-1" style="display:none;"></div>
                </div>
              </div>

              <details class="mb-3">
                <summary class="small fw-semibold text-teal" style="cursor:pointer;">O seleccionar cada archivo CSV individualmente</summary>
                <div class="row g-2 mt-2">
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">1. esquejes_futuros.csv</label>
                    <input type="file" class="form-control form-control-sm" name="csv_esquejes_futuros" accept=".csv">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">2. proy1.csv</label>
                    <input type="file" class="form-control form-control-sm" name="csv_proy1" accept=".csv">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">3. proy2.csv</label>
                    <input type="file" class="form-control form-control-sm" name="csv_proy2" accept=".csv">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">4. proy3.csv</label>
                    <input type="file" class="form-control form-control-sm" name="csv_proy3" accept=".csv">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">5. guarde.csv (o granel.csv)</label>
                    <input type="file" class="form-control form-control-sm" name="csv_guarde" accept=".csv">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label small mb-1">6. salvamento.csv</label>
                    <input type="file" class="form-control form-control-sm" name="csv_salvamento" accept=".csv">
                  </div>
                </div>
              </details>

              <button type="submit" class="btn btn-success fw-semibold d-flex align-items-center" id="btnSubmitCsv">
                <span class="material-icons me-1" style="font-size:1.1rem;">cloud_upload</span>
                Subir e Importar Modelo
              </button>
            </form>
          </div>
        </div>

        <!-- Resultado de validación de la importación recién ejecutada -->
        <div id="importResultBox" class="mt-3" style="display:none;"></div>

        <!-- Tabla de modelos ya cargados en BD con sus totales -->
        <hr class="my-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold mb-0">Modelos cargados en Base de Datos</h6>
          <span class="small text-muted">Totales sin filtros para validación</span>
        </div>
        <div class="table-responsive" style="max-height: 260px;">
          <table class="table table-sm table-hover align-middle mb-0" id="tablaModelosCargados">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Escenario</th>
                <th class="text-end">Esquejes</th>
                <th class="text-end">Producción</th>
                <th class="text-end">Guarde</th>
                <th class="text-end">Salvamento</th>
                <th>Cargado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($modelosDisponibles as $m): ?>
                <tr>
                  <td class="fw-bold"><?php echo (int)$m['id']; ?></td>
                  <td class="small text-truncate" style="max-width: 240px;" title="<?php echo htmlspecialchars((string)$m['escenario']); ?>">
                    <?php echo htmlspecialchars((string)$m['escenario']); ?>
                  </td>
                  <td class="text-end font-monospace small"><?php echo number_format((int)$m['esquejes'], 0, ',', '.'); ?></td>
                  <td class="text-end font-monospace small"><?php echo number_format((int)$m['produccion'], 0, ',', '.'); ?></td>
                  <td class="text-end font-monospace small"><?php echo number_format((int)$m['guarde'], 0, ',', '.'); ?></td>
                  <td class="text-end font-monospace small"><?php echo number_format((int)$m['salvamento'], 0, ',', '.'); ?></td>
                  <td class="small text-muted"><?php echo htmlspecialchars((string)$m['fecha']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  let DATOS = <?php echo !empty($datosIniciales) ? json_encode($datosIniciales, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : 'null'; ?>;
  const COLORES = ["--s1","--s2","--s3","--s4","--s5"];
  const fmt = new Intl.NumberFormat("es-CO",{maximumFractionDigits:0});
  const fmtPct = new Intl.NumberFormat("es-CO",{style:"percent",maximumFractionDigits:1,signDisplay:"exceptZero"});
  const $id = id => document.getElementById(id);
  const esc = s => String(s).replace(/[&<>"]/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[c]));
  const cssVar = v => getComputedStyle(document.documentElement).getPropertyValue(v).trim();

  const apiUrl = path => (typeof window.getEndpointUrl === "function") ? window.getEndpointUrl(path) : path;

  const st = {modelos:[], infoVar:{}, activos:new Set(), base:null, tab:"esq", modo:"val", flor:"", finca:"", variedad:"", sIni:null, sFin:null, sub:{}, dim:"Variedad", orden:{col:1,asc:false}};
  try{ const t=localStorage.getItem("tika.tab"); if(t) st.tab=t; }catch(e){}

  // Filas: esq [s,v,f,ciclo,q] · prod [s,v,f,nuevo,q] · guarde [s,v,f,nsg,q,perd] · salv [s,v,submercado,q]
  const DS = {
    esq:{label:"Esquejes", titulo:"Esquejes a sembrar por semana de siembra", unidad:"esquejes", finca:true,
      sub:{name:"Ciclo", opts:m=>["1","2"], test:(r,m,v)=>String(r[3])===v},
      dims:{Variedad:(r,m)=>m.vars[r[1]], Flor:(r,m)=>m.vflor[r[1]], Finca:(r,m)=>m.fincas[r[2]], Ciclo:r=>"Ciclo "+r[3]}},
    prod:{label:"Producción", titulo:"Producción proyectada (tallos) por semana de producción", unidad:"tallos", finca:true,
      sub:{name:"Origen", opts:()=>[["0","Siembras existentes"],["1","Nuevas siembras"]], test:(r,m,v)=>String(r[3])===v},
      dims:{Variedad:(r,m)=>m.vars[r[1]], Flor:(r,m)=>m.vflor[r[1]], Finca:(r,m)=>m.fincas[r[2]], Origen:r=>r[3]?"Nuevas siembras":"Siembras existentes"}},
    guarde:{label:"Guarde", titulo:"Inventario en guarde (tallos) por semana", unidad:"tallos", finca:true,
      sub:{name:"NSG", opts:m=>m.nsg, test:(r,m,v)=>m.nsg[r[3]]===v},
      dims:{Variedad:(r,m)=>m.vars[r[1]], Flor:(r,m)=>m.vflor[r[1]], Finca:(r,m)=>m.fincas[r[2]], NSG:(r,m)=>m.nsg[r[3]]}},
    salv:{label:"Salvamento", titulo:"Salvamento (tallos) por semana", unidad:"tallos", finca:false, qi:3,
      sub:{name:"Submercado", opts:m=>m.subs, test:(r,m,v)=>m.subs[r[2]]===v},
      dims:{Variedad:(r,m)=>m.vars[r[1]], Flor:(r,m)=>m.vflor[r[1]], Submercado:(r,m)=>m.subs[r[2]]}},
  };
  const qOf = (k,r) => k==="salv" ? r[3] : r[4];

  function prepararModelo(m){
    m.id = String(m.id);
    m.vflor = m.vars.map(v => (st.infoVar[v]||[])[0] || "SIN FLOR");
    m.cob = {};
    for(const k in DS){ let a=Infinity,b=-Infinity; for(const r of m[k]){ if(r[0]<a)a=r[0]; if(r[0]>b)b=r[0]; } m.cob[k]=[a,b]; }
    return m;
  }

  function agregarModelos(lista, info){
    Object.assign(st.infoVar, info||{});
    for(const m of lista){
      m.id = String(m.id);
      const i = st.modelos.findIndex(x=>x.id===m.id);
      if(i>=0) st.modelos[i]=m; else st.modelos.push(m);
      prepararModelo(m); st.activos.add(m.id);
    }
    st.modelos.forEach(prepararModelo);
    if(!st.base || !st.modelos.some(m=>m.id===st.base)) st.base = st.modelos[0]?.id;
    construirControles(); render();
  }
  window.agregarModelos = agregarModelos;

  const activos = () => st.modelos.filter(m=>st.activos.has(m.id));
  const colorDe = m => cssVar(COLORES[st.modelos.indexOf(m) % COLORES.length]);

  // ---------- filtros ----------
  function pasa(k, r, m, conSub){
    if(r[0]<st.sIni || r[0]>st.sFin) return false;
    if(st.flor && m.vflor[r[1]]!==st.flor) return false;
    if(st.variedad && m.vars[r[1]]!==st.variedad) return false;
    if(st.finca && DS[k].finca && m.fincas[r[2]]!==st.finca) return false;
    if(conSub && st.sub[k] && !DS[k].sub.test(r,m,st.sub[k])) return false;
    return true;
  }

  function semanasTodas(){
    const s=new Set(); for(const m of st.modelos) for(const k in DS) for(const r of m[k]) s.add(r[0]);
    return [...s].sort((a,b)=>a-b);
  }

  function construirControles(){
    $id("modelos").innerHTML = st.modelos.map(m=>`
      <div class="mod ${st.activos.has(m.id)?"":"off"}">
        <div class="id"><span class="sw" style="background:${colorDe(m)}"></span>${esc(m.id)}</div>
        <div class="ctl">
          <label><input type="checkbox" data-act="${esc(m.id)}" ${st.activos.has(m.id)?"checked":""}> Incluir</label>
          <label><input type="radio" name="base" data-base="${esc(m.id)}" ${st.base===m.id?"checked":""}> Base</label>
        </div>
        <div class="esc">${esc(m.escenario)}</div>
      </div>`).join("");
    $id("modelos").querySelectorAll("[data-act]").forEach(el=>el.onchange=()=>{
      el.checked ? st.activos.add(el.dataset.act) : st.activos.delete(el.dataset.act);
      if(!st.activos.size){ st.activos.add(el.dataset.act); el.checked=true; }
      if(!st.activos.has(st.base)) st.base=[...st.activos][0];
      construirControles(); render(); });
    $id("modelos").querySelectorAll("[data-base]").forEach(el=>el.onchange=()=>{ st.base=el.dataset.base; st.activos.add(st.base); construirControles(); render(); });

    const sem = semanasTodas();
    if(st.sIni==null || !sem.includes(st.sIni)){ st.sIni=sem[0]; }
    if(st.sFin==null || !sem.includes(st.sFin)){ st.sFin=sem[sem.length-1]; }
    const opt = v => sem.map(s=>`<option value="${s}" ${s===v?"selected":""}>${s}</option>`).join("");
    $id("sIni").innerHTML=opt(st.sIni); $id("sFin").innerHTML=opt(st.sFin);

    const flores=[...new Set(st.modelos.flatMap(m=>m.vflor))].sort();
    $id("flor").innerHTML = [["","Todas"],...flores.map(f=>[f,f[0]+f.slice(1).toLowerCase()])].map(([v,l])=>`<button type="button" data-v="${esc(v)}" aria-pressed="${st.flor===v}">${esc(l)}</button>`).join("");
    $id("flor").querySelectorAll("button").forEach(b=>b.onclick=()=>{ st.flor=b.dataset.v; construirControles(); render(); });

    const fincas=[...new Set(st.modelos.flatMap(m=>m.fincas))].sort();
    $id("finca").innerHTML = `<option value="">Todas</option>`+fincas.map(f=>`<option ${f===st.finca?"selected":""}>${esc(f)}</option>`).join("");
    $id("listaVar").innerHTML = [...new Set(st.modelos.flatMap(m=>m.vars))].sort().map(v=>`<option value="${esc(v)}">`).join("");
    $id("variedad").value = st.variedad;

    $id("tabs").innerHTML = Object.entries(DS).map(([k,d])=>`<button role="tab" type="button" data-k="${k}" aria-selected="${st.tab===k}">${d.label}</button>`).join("");
    $id("tabs").querySelectorAll("button").forEach(b=>b.onclick=()=>{ st.tab=b.dataset.k; try{localStorage.setItem("tika.tab",st.tab)}catch(e){} if(!DS[st.tab].dims[st.dim]) st.dim="Variedad"; construirControles(); render(); });

    const d=DS[st.tab];
    let subs=[...new Map(st.modelos.flatMap(m=>d.sub.opts(m).map(o=>Array.isArray(o)?o:[o,o]))).entries()];
    $id("subCtl").innerHTML = `<label class="f"><span>${d.sub.name}</span><select id="subSel"><option value="">Todos</option>${subs.map(([v,l])=>`<option value="${esc(v)}" ${st.sub[st.tab]===v?"selected":""}>${esc(l)}</option>`).join("")}</select></label>`;
    $id("subSel").onchange=e=>{ st.sub[st.tab]=e.target.value; render(); };
    $id("dim").innerHTML = Object.keys(d.dims).map(x=>`<option ${x===st.dim?"selected":""}>${x}</option>`).join("");
    $id("modo").querySelectorAll("button").forEach(b=>b.setAttribute("aria-pressed", b.dataset.v===st.modo));
  }

  $id("sIni").onchange=e=>{ st.sIni=+e.target.value; if(st.sIni>st.sFin){st.sFin=st.sIni; $id("sFin").value=st.sFin;} render(); };
  $id("sFin").onchange=e=>{ st.sFin=+e.target.value; if(st.sFin<st.sIni){st.sIni=st.sFin; $id("sIni").value=st.sIni;} render(); };
  $id("finca").onchange=e=>{ st.finca=e.target.value; render(); };
  $id("variedad").onchange=e=>{ const v=e.target.value.trim().toUpperCase(); st.variedad = st.modelos.some(m=>m.vars.includes(v)) ? v : ""; e.target.value=st.variedad; render(); };
  $id("limpiar").onclick=()=>{ const s=semanasTodas(); Object.assign(st,{flor:"",finca:"",variedad:"",sIni:s[0],sFin:s[s.length-1],sub:{}}); construirControles(); render(); };
  $id("modo").querySelectorAll("button").forEach(b=>b.onclick=()=>{ st.modo=b.dataset.v; construirControles(); renderSerie(); });
  $id("dim").onchange=e=>{ st.dim=e.target.value; renderDesglose(); };
  $id("buscar").oninput=()=>renderDesglose();

  // ---------- celdas de diferencia ----------
  function difCelda(v,b){
    const d=v-b; const cls=d>0?"pos":d<0?"neg":"";
    const p = b ? `<span class="pct">${fmtPct.format(d/b)}</span>` : "";
    return `<td class="num ${cls}">${d>0?"+":""}${fmt.format(d)}${p}</td>`;
  }

  // ---------- resumen ----------
  function renderResumen(){
    const ms=activos(), base=st.modelos.find(m=>m.id===st.base), otros=ms.filter(m=>m!==base);
    if(!base) return;
    const sum=(k,m,f)=>{ let t=0; for(const r of m[k]) if(pasa(k,r,m,false)&&(!f||f(r))) t+=qOf(k,r); return t; };
    const filas=[
      ["Esquejes a sembrar","esquejes",m=>sum("esq",m)],
      ["Producción proyectada","tallos",m=>sum("prod",m)],
      ["de siembras existentes","tallos",m=>sum("prod",m,r=>!r[3]),1],
      ["de nuevas siembras","tallos",m=>sum("prod",m,r=>r[3]),1],
      ["Guarde (inventario)","tallos",m=>sum("guarde",m)],
      ["pérdidas en guarde","tallos",m=>{let t=0; for(const r of m.guarde) if(pasa("guarde",r,m,false)) t+=r[5]; return t;},1],
      ["Salvamento","tallos",m=>sum("salv",m)],
      ["Variedades con esquejes","variedades",m=>{const s=new Set(); for(const r of m.esq) if(pasa("esq",r,m,false)&&r[4]>0) s.add(r[1]); return s.size;}],
    ];
    const cab = `<thead><tr><th>Indicador</th>${[base,...otros].map(m=>`<th><span class="sw" style="background:${colorDe(m)};margin-right:6px"></span>${esc(m.id)}${m===base?" (base)":""}</th>`).join("")}${otros.map(m=>`<th>Δ ${esc(m.id)} − ${esc(base.id)}</th>`).join("")}</tr></thead>`;
    const cuerpo = filas.map(([n,u,f,sub])=>{ const vb=f(base); const vs=otros.map(f);
      return `<tr><td class="${sub?"sub":""}">${n}</td><td class="num">${fmt.format(vb)}</td>${vs.map(v=>`<td class="num">${fmt.format(v)}</td>`).join("")}${vs.map(v=>difCelda(v,vb)).join("")}</tr>`; }).join("");
    $id("tResumen").innerHTML = cab+"<tbody>"+cuerpo+"</tbody>";
    $id("resBase").textContent = `Semanas ${st.sIni}–${st.sFin}`+(st.flor?` · ${st.flor}`:"")+(st.finca?` · ${st.finca}`:"")+(st.variedad?` · ${st.variedad}`:"");

    const notas=[];
    for(const k in DS){
      const cs=ms.map(m=>[m,Math.max(m.cob[k][0],st.sIni),Math.min(m.cob[k][1],st.sFin)]);
      const ini=Math.max(...cs.map(c=>c[1])), fin=Math.min(...cs.map(c=>c[2]));
      const dif=cs.filter(c=>c[1]!==ini||c[2]!==fin);
      if(dif.length || cs.some(c=>c[1]!==cs[0][1]||c[2]!==cs[0][2]))
        notas.push([`${DS[k].label}: ${cs.map(([m,a,b])=>`${m.id} tiene datos ${a}–${b}`).join(", ")}. Para comparar parejo usa ${ini}–${fin}.`, ini, fin]);
    }
    $id("notas").innerHTML = notas.map(([n,a,b])=>`<div class="nota">${esc(n)} <button class="btn-tika" type="button" data-r="${a},${b}" style="min-height:26px;padding:2px 8px;margin-left:6px">Usar ${a}–${b}</button></div>`).join("");
    $id("notas").querySelectorAll("[data-r]").forEach(b=>b.onclick=()=>{ [st.sIni,st.sFin]=b.dataset.r.split(",").map(Number); construirControles(); render(); });
  }

  // ---------- series semanales ----------
  function series(k){
    const ms=activos(), sem=new Set();
    const porModelo = ms.map(m=>{ const mp=new Map(); for(const r of m[k]) if(pasa(k,r,m,true)){ mp.set(r[0],(mp.get(r[0])||0)+qOf(k,r)); } return [m,mp]; });
    for(const m of ms) for(const r of m[k]) if(r[0]>=st.sIni&&r[0]<=st.sFin) sem.add(r[0]);
    const semanas=[...sem].sort((a,b)=>a-b);
    const ser = porModelo.map(([m,mp])=>({m, vals:semanas.map(s=> s<m.cob[k][0]||s>m.cob[k][1] ? null : (mp.get(s)||0))}));
    return {semanas, ser};
  }

  function nice(min,max){
    if(min===max){ max=min+1; }
    const span=max-min, step0=Math.pow(10,Math.floor(Math.log10(span/5))); const err=span/5/step0;
    const step=step0*(err>=7.5?10:err>=3.5?5:err>=1.5?2:1);
    const a=Math.floor(min/step)*step, b=Math.ceil(max/step)*step, t=[];
    for(let v=a; v<=b+step/2; v+=step) t.push(Math.round(v));
    return t;
  }
  const corto2 = v => { const a=Math.abs(v); const f=new Intl.NumberFormat("es-CO",{maximumFractionDigits:a>=1e6&&a<1e7?1:0}); return a>=1e6?f.format(v/1e6)+" M":a>=1e3?fmt.format(v/1e3)+" mil":fmt.format(v); };

  function renderSerie(){
    const k=st.tab, {semanas, ser}=series(k), base=ser.find(s=>s.m.id===st.base);
    if(!base) return;
    const dif = st.modo==="dif";
    const lineas = dif ? ser.filter(s=>s!==base).map(s=>({m:s.m, vals:s.vals.map((v,i)=>v==null||base.vals[i]==null?null:v-base.vals[i])})) : ser;
    $id("leyenda").innerHTML = (dif?`<span class="muted">${esc(DS[k].titulo)} · diferencia contra ${esc(st.base)}</span>`:`<span class="muted">${esc(DS[k].titulo)}</span>`) +
      lineas.map(s=>`<span><span class="sw" style="background:${colorDe(s.m)}"></span>${esc(s.m.id)}${dif?" − "+esc(st.base):""}</span>`).join("");
    const el=$id("chart"); const W=Math.max(320, el.clientWidth||900), H=Math.round(Math.min(380, Math.max(260, W*0.36)));
    const P={l:62,r:44,t:12,b:30};
    if(!semanas.length || !lineas.length){ el.innerHTML=`<p class="muted">${dif?"Incluye al menos otro modelo además de la base para ver diferencias.":"Sin datos para estos filtros."}</p>`; renderTablaSemana(semanas,ser,base); return; }
    const todos=lineas.flatMap(s=>s.vals).filter(v=>v!=null);
    const ticks=nice(Math.min(0,...todos), Math.max(0,...todos)); const y0=ticks[0], y1=ticks[ticks.length-1];
    const X=i=>P.l+(semanas.length===1?0.5:i/(semanas.length-1))*(W-P.l-P.r), Y=v=>P.t+(1-(v-y0)/(y1-y0))*(H-P.t-P.b);
    const paso=Math.max(1,Math.ceil(semanas.length/Math.floor((W-P.l-P.r)/48)));
    let g=`<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="${esc(DS[k].titulo)}">`;
    for(const t of ticks) g+=`<line x1="${P.l}" x2="${W-P.r}" y1="${Y(t)}" y2="${Y(t)}" stroke="${cssVar(t===0?"--tika-muted":"--tika-line")}" stroke-width="1"/><text x="${P.l-8}" y="${Y(t)+4}" text-anchor="end">${corto2(t)}</text>`;
    semanas.forEach((s,i)=>{ const anio=s%100===1; if(anio) g+=`<line x1="${X(i)}" x2="${X(i)}" y1="${P.t}" y2="${H-P.b}" stroke="${cssVar("--tika-line")}" stroke-dasharray="3 3"/>`;
      if(i%paso===0||anio&&semanas.length<80) g+=`<text x="${X(i)}" y="${H-P.b+18}" text-anchor="middle" ${anio?'style="font-weight:600"':''}>${s}</text>`; });
    const fin=[];
    for(const s of lineas){ let d="",pen=false; s.vals.forEach((v,i)=>{ if(v==null){pen=false;return;} d+=(pen?"L":"M")+X(i).toFixed(1)+","+Y(v).toFixed(1); pen=true; });
      g+=`<path d="${d}" fill="none" stroke="${colorDe(s.m)}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>`;
      const li=s.vals.map((v,i)=>v==null?-1:i).filter(i=>i>=0).pop(); if(li!=null&&li>=0){ g+=`<circle cx="${X(li)}" cy="${Y(s.vals[li])}" r="3.5" fill="${colorDe(s.m)}" stroke="${cssVar("--tika-panel")}" stroke-width="2"/>`; fin.push({x:X(li)+7,y:Y(s.vals[li])+4,id:s.m.id}); } }
    fin.sort((a,b)=>a.y-b.y).forEach((e,i,a)=>{ if(i&&e.y-a[i-1].y<13) e.y=a[i-1].y+13; g+=`<text x="${e.x}" y="${e.y}" style="fill:${cssVar("--tika-fg")};font-weight:600">${esc(e.id)}</text>`; });
    g+=`<line id="cruz" y1="${P.t}" y2="${H-P.b}" stroke="${cssVar("--tika-muted")}" stroke-width="1" visibility="hidden"/><g id="puntos"></g><rect x="${P.l}" y="${P.t}" width="${W-P.l-P.r}" height="${H-P.t-P.b}" fill="transparent" id="hit"/></svg><div class="tip" id="tip" hidden></div>`;
    el.innerHTML=g;
    const svg=el.querySelector("svg"), hit=$id("hit"), tip=$id("tip"), cruz=$id("cruz"), pts=$id("puntos");
    const mover=ev=>{ const rc=svg.getBoundingClientRect(), x=(ev.clientX-rc.left)*W/rc.width;
      const i=Math.max(0,Math.min(semanas.length-1,Math.round((x-P.l)/(W-P.l-P.r)*(semanas.length-1))));
      cruz.setAttribute("x1",X(i)); cruz.setAttribute("x2",X(i)); cruz.setAttribute("visibility","visible");
      pts.innerHTML=lineas.filter(s=>s.vals[i]!=null).map(s=>`<circle cx="${X(i)}" cy="${Y(s.vals[i])}" r="4.5" fill="${colorDe(s.m)}" stroke="${cssVar("--tika-panel")}" stroke-width="2"/>`).join("");
      const bv=base.vals[i];
      tip.innerHTML=`<b>Semana ${semanas[i]}</b>`+ser.map(s=>{ const v=s.vals[i]; const d=s!==base&&v!=null&&bv!=null?v-bv:null;
        return `<div class="r"><span><span class="sw" style="background:${colorDe(s.m)}"></span>${esc(s.m.id)}</span><span class="num">${v==null?"sin datos":fmt.format(v)}${d!=null?` <span class="${d>0?"pos":d<0?"neg":""}">(${d>0?"+":""}${fmt.format(d)})</span>`:""}</span></div>`; }).join("");
      tip.hidden=false; const px=X(i)*rc.width/W; const tw=tip.offsetWidth;
      tip.style.left=Math.min(rc.width-tw-4, Math.max(4, px+14 + (px+14+tw>rc.width? -tw-28:0)))+"px"; tip.style.top="8px"; };
    hit.addEventListener("pointermove",mover); hit.addEventListener("pointerdown",mover);
    hit.addEventListener("pointerleave",()=>{ tip.hidden=true; cruz.setAttribute("visibility","hidden"); pts.innerHTML=""; });
    renderTablaSemana(semanas,ser,base);
  }

  function renderTablaSemana(semanas,ser,base){
    const otros=ser.filter(s=>s!==base); const c=v=>v==null?`<td class="muted">–</td>`:`<td class="num">${fmt.format(v)}</td>`;
    const tot=s=>s.vals.reduce((a,v)=>a+(v||0),0);
    $id("tSemana").innerHTML=`<thead><tr><th>Semana</th>${[base,...otros].map(s=>`<th>${esc(s.m.id)}</th>`).join("")}${otros.map(s=>`<th>Δ ${esc(s.m.id)} − ${esc(base.m.id)}</th>`).join("")}</tr></thead><tbody>`+
      semanas.map((w,i)=>`<tr><td class="num">${w}</td>${[base,...otros].map(s=>c(s.vals[i])).join("")}${otros.map(s=>s.vals[i]==null||base.vals[i]==null?`<td class="muted">–</td>`:difCelda(s.vals[i],base.vals[i])).join("")}</tr>`).join("")+
      `<tr style="font-weight:600"><td>Total</td>${[base,...otros].map(s=>`<td class="num">${fmt.format(tot(s))}</td>`).join("")}${otros.map(s=>difCelda(tot(s),tot(base))).join("")}</tr></tbody>`;
  }

  // ---------- desglose ----------
  function renderDesglose(){
    const k=st.tab, d=DS[k], fn=d.dims[st.dim]||d.dims.Variedad, ms=activos(), base=st.modelos.find(m=>m.id===st.base), otros=ms.filter(m=>m!==base), orden=[base,...otros];
    if(!base) return;
    const mapa=new Map();
    orden.forEach((m,j)=>{ for(const r of m[k]) if(pasa(k,r,m,true)){ const key=fn(r,m); let a=mapa.get(key); if(!a){a=new Array(orden.length).fill(0); mapa.set(key,a);} a[j]+=qOf(k,r); } });
    const q=$id("buscar").value.trim().toUpperCase();
    let filas=[...mapa.entries()].filter(([key])=>!q||String(key).toUpperCase().includes(q)).map(([key,a])=>[key,...a,...a.slice(1).map(v=>v-a[0])]);
    const {col,asc}=st.orden; filas.sort((x,y)=>{ const a=x[col],b=y[col]; const r=typeof a==="string"?a.localeCompare(b):a-b; return asc?r:-r; });
    const esVar = st.dim==="Variedad";
    const extra = esVar ? ["Flor","Color"] : [];
    const cols=[st.dim,...orden.map(m=>m.id+(m===base?" (base)":"")),...otros.map(m=>`Δ ${m.id} − ${base.id}`)];
    $id("desTit").textContent=`${d.label} por ${st.dim.toLowerCase()}`;
    const th=cols.map((c,i)=>`<th class="sort" tabindex="0" data-c="${i}" ${i===col?`aria-sort="${asc?"ascending":"descending"}"`:""}>${esc(c)}</th>`);
    if(esVar) th.splice(1,0,...extra.map(x=>`<th>${x}</th>`));
    const totales=orden.map((_,j)=>filas.reduce((s,f)=>s+f[1+j],0));
    $id("tDes").innerHTML=`<thead><tr>${th.join("")}</tr></thead><tbody>`+filas.map(f=>{ const iv=st.infoVar[f[0]]||[];
      return `<tr ${esVar?`class="clic" tabindex="0" data-v="${esc(f[0])}"`:""}><td>${esc(f[0])}</td>${esVar?`<td class="muted" style="text-align:left">${esc(iv[0]||"")}</td><td class="muted" style="text-align:left">${esc(iv[1]||"")}</td>`:""}${orden.map((_,j)=>`<td class="num">${fmt.format(f[1+j])}</td>`).join("")}${otros.map((_,j)=>difCelda(f[1+j+1],f[1])).join("")}</tr>`; }).join("")+
      `<tr style="font-weight:600"><td>Total (${filas.length})</td>${esVar?"<td></td><td></td>":""}${totales.map(v=>`<td class="num">${fmt.format(v)}</td>`).join("")}${totales.slice(1).map(v=>difCelda(v,totales[0])).join("")}</tr></tbody>`;
    $id("tDes").querySelectorAll("th.sort").forEach(h=>{ const f=()=>{ const c=+h.dataset.c; st.orden={col:c,asc:st.orden.col===c?!st.orden.asc:c===0}; renderDesglose(); }; h.onclick=f; h.onkeydown=e=>{if(e.key==="Enter")f();}; });
    $id("tDes").querySelectorAll("tr.clic").forEach(tr=>{ const f=()=>{ st.variedad=tr.dataset.v; $id("variedad").value=st.variedad; render(); window.scrollTo({top:0,behavior:"smooth"}); }; tr.onclick=f; tr.onkeydown=e=>{if(e.key==="Enter")f();}; });
  }

  function render(){
    if(!st.modelos.length) return;
    $id("generado").textContent = DATOS?.generado ? `Datos consultados el ${DATOS.generado}` : "";
    renderResumen(); renderSerie(); renderDesglose();
  }
  let rz; window.addEventListener("resize",()=>{ clearTimeout(rz); rz=setTimeout(renderSerie,150); });

  // ---------- carga de una carpeta de modelo en el navegador ----------
  function parseCSV(txt){
    if(txt.charCodeAt(0)===0xFEFF) txt=txt.slice(1);
    const filas=[]; let f=[], c="", q=false;
    for(let i=0;i<txt.length;i++){ const ch=txt[i];
      if(q){ if(ch==='"'){ if(txt[i+1]==='"'){c+='"';i++;} else q=false; } else c+=ch; }
      else if(ch==='"') q=true; else if(ch===','){ f.push(c); c=""; }
      else if(ch==='\n'){ f.push(c.replace(/\r$/,"")); filas.push(f); f=[]; c=""; } else c+=ch; }
    if(c||f.length){ f.push(c); filas.push(f); }
    const h=filas.shift(); return {h, filas:filas.filter(r=>r.length>1&&r[0]!=="")};
  }
  const n = s => +String(s||"").replace(/[.,]/g,"") || 0;
  async function modeloDesdeArchivos(id, archivos){
    const leer = async (...nombres) => {
      const a = archivos.find(f => nombres.includes(f.name.toLowerCase()));
      if(!a) throw new Error(`falta ${nombres.join(" o ")}`);
      return parseCSV(await a.text());
    };
    const ix=(t,nombre)=>{ const i=t.h.indexOf(nombre); if(i<0) throw new Error(`no encuentro la columna ${nombre}`); return i; };
    const esq=await leer("esquejes_futuros.csv","equejes_futuros.csv"), gua=await leer("guarde.csv","granel.csv"), sal=await leer("salvamento.csv");
    const proy=[await leer("proy1.csv"),await leer("proy2.csv"),await leer("proy3.csv")];
    const info={}, vars=new Set(), fincas=new Set(), subs=new Set(), nsg=new Set();
    const E={v:ix(esq,"Variedad"),f:ix(esq,"Finca"),c:ix(esq,"Ciclo"),q:ix(esq,"Cantidad"),s:ix(esq,"Semana"),fl:ix(esq,"Flor"),co:ix(esq,"Color"),e:ix(esq,"Escenario")};
    const Pr=proy.map(p=>({s:ix(p,"SemanaProducion"),v:ix(p,"Variedad"),f:ix(p,"Finca"),o:ix(p,"Origen"),q:ix(p,"Cantidad"),fl:ix(p,"Flor"),co:ix(p,"Color")}));
    const S={s:ix(sal,"Semana"),v:ix(sal,"Variedad"),k:ix(sal,"Submercado"),q:ix(sal,"Cantidad")};
    esq.filas.forEach(r=>{ vars.add(r[E.v]); fincas.add(r[E.f]); info[r[E.v]]??=[r[E.fl],r[E.co]]; });
    proy.forEach((p,j)=>p.filas.forEach(r=>{ vars.add(r[Pr[j].v]); fincas.add(r[Pr[j].f]); info[r[Pr[j].v]]??=[r[Pr[j].fl],r[Pr[j].co]]; }));
    gua.filas.forEach(r=>{ vars.add(r[1]); fincas.add(r[2]); nsg.add(r[3]); });
    sal.filas.forEach(r=>{ if(r[S.v]){ vars.add(r[S.v]); subs.add(r[S.k]); } });
    const L=x=>[...x].sort(), V=L(vars), F=L(fincas), K=L(subs), G=L(nsg);
    const iV=new Map(V.map((v,i)=>[v,i])), iF=new Map(F.map((v,i)=>[v,i])), iK=new Map(K.map((v,i)=>[v,i])), iG=new Map(G.map((v,i)=>[v,i]));
    const agr=(lista, nq)=>{ const m=new Map(); for(const r of lista){ const key=r.slice(0,r.length-nq).join("|"); const a=m.get(key); if(a){ for(let j=0;j<nq;j++) a[a.length-nq+j]+=r[r.length-nq+j]; } else m.set(key,r); } return [...m.values()]; };
    const out={id:String(id), escenario:esq.filas[0]?.[E.e]||id, vars:V, fincas:F, subs:K, nsg:G,
      esq:agr(esq.filas.map(r=>[+r[E.s],iV.get(r[E.v]),iF.get(r[E.f]),+r[E.c],n(r[E.q])]),1),
      prod:agr(proy.flatMap((p,j)=>p.filas.map(r=>[+r[Pr[j].s],iV.get(r[Pr[j].v]),iF.get(r[Pr[j].f]),r[Pr[j].o]==="Planeado"?0:1,+r[Pr[j].q]||0])),1),
      guarde:agr(gua.filas.map(r=>[+r[0],iV.get(r[1]),iF.get(r[2]),iG.get(r[3]),n(r[4]),n(r[6])]),2),
      salv:agr(sal.filas.filter(r=>r[S.v]).map(r=>[+r[S.s],iV.get(r[S.v]),iK.get(r[S.k]),n(r[S.q])]),1)};
    return {out, info};
  }
  $id("carpeta").onchange=async e=>{
    const files=[...e.target.files]; const grupos=new Map();
    for(const f of files){ const p=(f.webkitRelativePath||f.name).split("/"); const dir=p.length>1?p[p.length-2]:"modelo"; if(!grupos.has(dir)) grupos.set(dir,[]); grupos.get(dir).push(f); }
    const ok=[], err=[];
    for(const [dir,arch] of grupos){ if(!arch.some(f=>f.name.toLowerCase()==="esquejes_futuros.csv")) continue;
      $id("cargaMsg").textContent=`Leyendo ${dir}…`;
      try{ const {out,info}=await modeloDesdeArchivos(dir,arch); agregarModelos([out],info); ok.push(dir); }catch(x){ err.push(`${dir}: ${x.message}`); } }
    $id("cargaMsg").textContent = ok.length ? `Agregado: ${ok.join(", ")}.` : "";
    if(err.length) $id("cargaMsg").textContent += ` No se pudo leer ${err.join("; ")}.`;
    if(!ok.length&&!err.length) $id("cargaMsg").textContent="Esa carpeta no tiene esquejes_futuros.csv. Elige la carpeta de un modelo.";
    e.target.value="";
  };

  // ---------- Carga desde API (/api/tika/comparador) y sincronización con URL ----------
  async function cargarComparacionDesdeApi(idsArray, pushHistory = true) {
    const cleanIds = (idsArray || []).map(x => String(x).trim()).filter(x => /^\d+$/.test(x));
    if (!cleanIds.length) {
      if (window.showAppToast) {
        window.showAppToast("Seleccione al menos un modelo cargado para comparar.", "warning", "Comparador Tika");
      }
      return;
    }

    const estadoEl = $id("tikaEstadoCarga");
    const btnComp = $id("btnCompararModelos");
    if (estadoEl) {
      estadoEl.style.display = "block";
      estadoEl.textContent = `Cargando modelos ${cleanIds.join(", ")} desde el servidor...`;
    }
    if (btnComp) btnComp.disabled = true;

    try {
      const queryIds = cleanIds.join(",");
      if (pushHistory && window.history && window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.set("modelos", queryIds);
        window.history.replaceState({}, "", url.toString());
      }

      const resp = await fetch(apiUrl(`api/tika/comparador.php?modelos=${encodeURIComponent(queryIds)}`), {
        headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
      });
      const datos = await resp.json();
      if (!resp.ok || datos.ok === false) {
        throw new Error(datos.message || `Error HTTP ${resp.status}`);
      }

      DATOS = datos;
      st.modelos = [];
      st.activos.clear();
      st.base = null;
      st.sIni = null;
      st.sFin = null;
      agregarModelos(datos.modelos || [], datos.infoVar || {});

      if (estadoEl) {
        estadoEl.style.display = "none";
      }
    } catch (err) {
      if (estadoEl) {
        estadoEl.style.display = "block";
        estadoEl.textContent = `Error al cargar modelos: ${err.message}`;
      }
      if (window.showAppToast) {
        window.showAppToast(err.message, "danger", "Error cargando modelos");
      }
    } finally {
      if (btnComp) btnComp.disabled = false;
    }
  }

  function actualizarCatalogoModelosUI(listaModelos, nuevosSeleccionados = []) {
    const $sel = $('#tikaModelosSelect');
    const actuales = ($sel.val() || []).map(String);
    const unir = new Set([...actuales, ...nuevosSeleccionados.map(String)]);

    $sel.empty();
    (listaModelos || []).forEach(m => {
      const idStr = String(m.id);
      const text = `${m.id} — ${m.escenario} (${m.fecha || ''})`;
      const opt = new Option(text, idStr, unir.has(idStr), unir.has(idStr));
      $sel.append(opt);
    });
    $sel.trigger('change');

    // Actualizar tabla en modal
    const tbody = document.querySelector('#tablaModelosCargados tbody');
    if (tbody) {
      tbody.innerHTML = (listaModelos || []).map(m => `
        <tr>
          <td class="fw-bold">${esc(m.id)}</td>
          <td class="small text-truncate" style="max-width: 240px;" title="${esc(m.escenario)}">${esc(m.escenario)}</td>
          <td class="text-end font-monospace small">${fmt.format(m.esquejes || 0)}</td>
          <td class="text-end font-monospace small">${fmt.format(m.produccion || 0)}</td>
          <td class="text-end font-monospace small">${fmt.format(m.guarde || 0)}</td>
          <td class="text-end font-monospace small">${fmt.format(m.salvamento || 0)}</td>
          <td class="small text-muted">${esc(m.fecha || '')}</td>
        </tr>
      `).join('');
    }
  }

  function mostrarResultadoImportacion(res) {
    const box = $id("importResultBox");
    if (!box) return;
    const importados = res.data?.importados || [];
    const errores = res.data?.errores || [];

    let html = "";
    if (importados.length) {
      html += `<div class="alert alert-success py-2 px-3 mb-2">
        <div class="fw-bold mb-1">✓ ${esc(res.message || 'Importación completada')} — Totales de validación:</div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered bg-white mb-0 small">
            <thead>
              <tr>
                <th>Modelo</th>
                <th>Escenario</th>
                <th class="text-end">Esquejes</th>
                <th class="text-end">Producción</th>
                <th class="text-end">Guarde</th>
                <th class="text-end">Salvamento</th>
              </tr>
            </thead>
            <tbody>
              ${importados.map(m => `
                <tr>
                  <td class="fw-bold">${esc(m.id)}</td>
                  <td class="text-truncate" style="max-width:220px;">${esc(m.escenario)}</td>
                  <td class="text-end fw-semibold">${fmt.format(m.totales.esquejes)}</td>
                  <td class="text-end fw-semibold">${fmt.format(m.totales.produccion)}</td>
                  <td class="text-end fw-semibold">${fmt.format(m.totales.guarde)}</td>
                  <td class="text-end fw-semibold">${fmt.format(m.totales.salvamento)}</td>
                </tr>
              `).join("")}
            </tbody>
          </table>
        </div>
      </div>`;
    }
    if (errores.length) {
      html += `<div class="alert alert-warning py-2 px-3 mb-0 small">
        <b>Advertencias:</b> ${errores.map(e => `Modelo ${esc(e.id)}: ${esc(e.message)}`).join(" · ")}
      </div>`;
    }
    box.innerHTML = html;
    box.style.display = "block";
  }

  $(document).ready(function() {
    const $sel = $('#tikaModelosSelect');
    $sel.select2({
      placeholder: "Seleccione dos o más modelos para comparar...",
      allowClear: true,
      width: '100%'
    });

    $id("btnCompararModelos").addEventListener("click", () => {
      cargarComparacionDesdeApi($sel.val() || [], true);
    });

    $id("btnCopiarEnlace").addEventListener("click", async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        if (window.showAppToast) {
          window.showAppToast("Enlace copiado al portapapeles.", "success", "Compartir");
        }
      } catch (e) {
        if (window.showAppToast) {
          window.showAppToast(window.location.href, "info", "Enlace para compartir");
        }
      }
    });

    // Formulario importar desde red
    $id("formImportarRed").addEventListener("submit", async (ev) => {
      ev.preventDefault();
      const btn = $id("btnSubmitRed");
      const rawIds = $id("inputIdsRed").value.trim();
      if (!rawIds) return;

      btn.disabled = true;
      const origHtml = btn.innerHTML;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Importando...`;
      $id("importResultBox").style.display = "none";

      try {
        const fd = new FormData();
        fd.append("modo", "red");
        fd.append("ids", rawIds);

        const resp = await fetch(apiUrl("api/tika/importar.php"), {
          method: "POST",
          body: fd,
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
        });
        const res = await resp.json();
        if (!resp.ok || res.ok === false) {
          throw new Error(res.message || `Error HTTP ${resp.status}`);
        }

        mostrarResultadoImportacion(res);
        const nuevosIds = (res.data?.importados || []).map(x => x.id);
        if (res.data?.modelos) {
          actualizarCatalogoModelosUI(res.data.modelos, nuevosIds);
        }
        if (nuevosIds.length) {
          cargarComparacionDesdeApi($('#tikaModelosSelect').val() || nuevosIds, true);
        }
        if (window.showAppToast) {
          window.showAppToast(res.message, "success", "Importación Tika");
        }
      } catch (err) {
        const box = $id("importResultBox");
        box.innerHTML = `<div class="alert alert-danger py-2 px-3 small mb-0"><b>Error:</b> ${esc(err.message)}</div>`;
        box.style.display = "block";
        if (window.showAppToast) {
          window.showAppToast(err.message, "danger", "Error al importar");
        }
      } finally {
        btn.disabled = false;
        btn.innerHTML = origHtml;
      }
    });

    // Formulario subir 6 CSV (con acumulación inteligente y soporte para granel.csv / guarde.csv)
    const canonicalSlotFromFileName = (rawName) => {
      const b = String(rawName || "").toLowerCase().trim();
      if (b.includes("esquejes_futuros") || b.includes("equejes_futuros")) return "csv_esquejes_futuros";
      if (/(^|[^a-z0-9])proy1([^0-9]|$)/.test(b)) return "csv_proy1";
      if (/(^|[^a-z0-9])proy2([^0-9]|$)/.test(b)) return "csv_proy2";
      if (/(^|[^a-z0-9])proy3([^0-9]|$)/.test(b)) return "csv_proy3";
      if (b.includes("guarde") || b.includes("granel")) return "csv_guarde";
      if (b.includes("salvamento")) return "csv_salvamento";
      return null;
    };

    const accumulatedCsvFiles = {};
    const slotLabels = {
      csv_esquejes_futuros: "esquejes_futuros.csv",
      csv_proy1: "proy1.csv",
      csv_proy2: "proy2.csv",
      csv_proy3: "proy3.csv",
      csv_guarde: "guarde.csv / granel.csv",
      csv_salvamento: "salvamento.csv"
    };

    const refreshCsvFilesStatus = () => {
      const formEl = $id("formImportarCsv");
      const statusEl = $id("csvFilesStatus");
      if (!formEl || !statusEl) return;

      const resolved = { ...accumulatedCsvFiles };
      const multiInput = $id("inputArchivosMulti");
      if (multiInput && multiInput.files) {
        for (const f of multiInput.files) {
          const slot = canonicalSlotFromFileName(f.name);
          if (slot) resolved[slot] = f;
        }
      }
      for (const slot of Object.keys(slotLabels)) {
        const inp = formEl.querySelector(`input[name="${slot}"]`);
        if (inp && inp.files && inp.files[0]) {
          resolved[slot] = inp.files[0];
        }
      }

      const count = Object.keys(resolved).length;
      if (count === 0) {
        statusEl.style.display = "none";
        return;
      }

      const badges = Object.entries(slotLabels).map(([slot, label]) => {
        const f = resolved[slot];
        return f
          ? `<span class="badge bg-success-subtle text-success border border-success-subtle me-1 mb-1">✓ ${esc(f.name)}</span>`
          : `<span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1 mb-1">Falta: ${esc(label)}</span>`;
      }).join("");

      statusEl.innerHTML = `<div class="mt-1">${badges}</div>`;
      statusEl.style.display = "block";
    };

    const multiInputEl = $id("inputArchivosMulti");
    if (multiInputEl) {
      multiInputEl.addEventListener("change", () => {
        if (multiInputEl.files) {
          for (const f of multiInputEl.files) {
            const slot = canonicalSlotFromFileName(f.name);
            if (slot) accumulatedCsvFiles[slot] = f;
          }
        }
        refreshCsvFilesStatus();
      });
    }
    $id("formImportarCsv").querySelectorAll('input[type="file"][name^="csv_"]').forEach(inp => {
      inp.addEventListener("change", () => {
        if (inp.files && inp.files[0]) {
          accumulatedCsvFiles[inp.name] = inp.files[0];
        }
        refreshCsvFilesStatus();
      });
    });

    $id("formImportarCsv").addEventListener("submit", async (ev) => {
      ev.preventDefault();
      const btn = $id("btnSubmitCsv");
      btn.disabled = true;
      const origHtml = btn.innerHTML;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Subiendo y procesando...`;
      $id("importResultBox").style.display = "none";

      try {
        const formEl = ev.target;
        const resolved = { ...accumulatedCsvFiles };
        const multiInp = $id("inputArchivosMulti");
        if (multiInp && multiInp.files) {
          for (const f of multiInp.files) {
            const slot = canonicalSlotFromFileName(f.name);
            if (slot) resolved[slot] = f;
          }
        }
        for (const slot of Object.keys(slotLabels)) {
          const inp = formEl.querySelector(`input[name="${slot}"]`);
          if (inp && inp.files && inp.files[0]) {
            resolved[slot] = inp.files[0];
          }
        }

        const fd = new FormData();
        fd.append("modo", "archivos");
        fd.append("modelo_id", ($id("inputModeloIdCsv")?.value || "").trim());
        for (const [slot, fileObj] of Object.entries(resolved)) {
          if (fileObj) {
            fd.append(slot, fileObj, fileObj.name);
          }
        }

        const resp = await fetch(apiUrl("api/tika/importar.php?modo=archivos"), {
          method: "POST",
          body: fd,
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
        });
        const res = await resp.json();
        if (!resp.ok || res.ok === false) {
          throw new Error(res.message || `Error HTTP ${resp.status}`);
        }

        mostrarResultadoImportacion(res);
        const nuevosIds = (res.data?.importados || []).map(x => x.id);
        if (res.data?.modelos) {
          actualizarCatalogoModelosUI(res.data.modelos, nuevosIds);
        }
        if (nuevosIds.length) {
          cargarComparacionDesdeApi($('#tikaModelosSelect').val() || nuevosIds, true);
        }
        if (window.showAppToast) {
          window.showAppToast(res.message, "success", "Importación CSV Tika");
        }
      } catch (err) {
        const box = $id("importResultBox");
        box.innerHTML = `<div class="alert alert-danger py-2 px-3 small mb-0"><b>Error:</b> ${esc(err.message)}</div>`;
        box.style.display = "block";
        if (window.showAppToast) {
          window.showAppToast(err.message, "danger", "Error al subir CSV");
        }
      } finally {
        btn.disabled = false;
        btn.innerHTML = origHtml;
      }
    });

    // Carga inicial: si DATOS viene embebido del servidor, renderizar de inmediato; si no, consultar API
    const iniciales = $sel.val() || [];
    if (DATOS && Array.isArray(DATOS.modelos) && DATOS.modelos.length > 0) {
      if (iniciales.length > 0 && window.history && window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.set("modelos", iniciales.join(","));
        window.history.replaceState({}, "", url.toString());
      }
      agregarModelos(DATOS.modelos, DATOS.infoVar || {});
    } else if (iniciales.length > 0) {
      cargarComparacionDesdeApi(iniciales, true);
    }

    <?php if (!empty($abrirModalImportar)): ?>
    const modalEl = document.getElementById('modalImportarTika');
    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
    <?php endif; ?>
  });
})();
</script>


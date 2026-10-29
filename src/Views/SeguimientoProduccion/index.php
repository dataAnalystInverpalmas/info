<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .crud-page-header .crud-header-actions.report-actions-bar {
        margin-bottom: 0 !important;
        flex: 1 1 auto;
        justify-content: flex-end;
    }

    .crud-page-header .crud-page-subtitle {
        font-size: 0.85rem;
        margin-top: 0;
    }

    .sgp-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin: -0.5rem -15px 1rem;
        padding: 0.9rem 15px;
    }
    .sgp-bar h1 { font-size: 1.05rem; font-weight: 700; margin: 0; color: #17312d; }
    .sgp-bar p { margin: 0.15rem 0 0; color: #5f6f6b; font-size: 0.8rem; }

    .sgp-filtro-sidebar { position: sticky; top: 1rem; }
    .sgp-filtro-sidebar .card { border: 1px solid #dbe7e4; border-radius: 14px; }
    .sgp-filtro-sidebar .card-header {
        background: linear-gradient(135deg, #00796B 0%, #00695c 100%);
        color: #fff;
        border-radius: 14px 14px 0 0 !important;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.6rem 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }
    .sgp-filtro-sidebar .card-header .material-icons.toggle-icon { font-size: 1.1rem; transition: transform 0.2s ease; }
    .sgp-filtro-sidebar .card-header.collapsed .toggle-icon { transform: rotate(-90deg); }
    .sgp-filtro-sidebar label { font-size: 0.78rem; font-weight: 600; color: #1f2937; letter-spacing: 0.02em; }
    .sgp-filtro-sidebar .select2-container { width: 100% !important; }
    .sgp-filtro-sidebar .select2-container .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: calc(1.5em + .75rem + 2px);
    }
    .sgp-filtros-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.25rem 0.75rem; }
    .sgp-filtros-grid-full { grid-column: 1 / -1; }
    .sgp-tipo-checks { display: flex; flex-wrap: wrap; gap: 0.35rem 1rem; padding-top: 0.15rem; }
    .sgp-tipo-checks .form-check { padding-left: 1.4rem; }
    .sgp-tipo-checks .form-check-label { font-size: 0.85rem; font-weight: 500; color: #1f2937; }

    .sgp-section-card {
        border: 1px solid #e1ebe9;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05);
        padding: 1rem;
        min-width: 0;
    }
    .sgp-section-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; color: #17312d; }
    .sgp-table-wrapper { max-height: 340px; overflow: auto; }
    .sgp-table thead th { position: sticky; top: 0; background: #f8fafc; z-index: 2; }
    .sgp-modo-group .btn.active { background-color: #00796B; border-color: #00796B; color: #fff; }

    @media (max-width: 767.98px) {
        .sgp-filtro-sidebar { position: static; margin-bottom: 1rem; }
    }
    @media (max-width: 575.98px) {
        .sgp-filtros-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>

<!-- Page Header Banner / Navbar estilo CRUD EMV -->
<div class="crud-page-header d-print-none no-print">
    <div class="crud-header-left">
        <div class="crud-header-icon">
            <span class="material-icons">trending_up</span>
        </div>
        <div>
            <!-- Título omitido según indicación del usuario (ya presente en el navbar/breadcrumb superior) -->
            <div class="crud-page-subtitle">
                <span class="crud-page-badge">Producción</span>
                <span class="text-muted d-none d-sm-inline">• Seguimiento detallado del ciclo y avance de producción</span>
            </div>
        </div>
    </div>
    <div class="crud-header-actions report-actions-bar mb-0">
        <!-- Espacio para los chips de filtros activos a medida que se agregan -->
        <div class="filtros-aplicados"></div>
        <button type="button" class="btn btn-sm btn-teal d-flex align-items-center" data-bs-toggle="offcanvas"
            data-bs-target="#sgpFiltros" aria-controls="sgpFiltros" title="Mostrar filtros">
            <span class="material-icons me-1" style="font-size:1rem;">filter_list</span>
            Filtros
            <span class="badge rounded-pill bg-light text-dark ms-1 d-none filtros-badge">0</span>
        </button>
    </div>
</div>

<!-- Barra lateral vertical de filtros (mismo patrón que Reporte Lonas) -->
<div class="offcanvas offcanvas-end filtros-sidebar no-print" tabindex="-1" id="sgpFiltros"
    data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="sgpFiltrosLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sgpFiltrosLabel">
            <span class="material-icons" style="font-size: 1.2rem; color: #00796B;">filter_list</span>
            Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <label class="filter-label" for="sgpFiltroFinca">Finca</label>
            <select id="sgpFiltroFinca" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($fincas as $finca): ?>
                    <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroBloque">Bloque</label>
            <select id="sgpFiltroBloque" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($bloques as $bloque): ?>
                    <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroFlor">Flor</label>
            <select id="sgpFiltroFlor" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($flores as $flor): ?>
                    <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroVariedad">Variedad</label>
            <select id="sgpFiltroVariedad" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($variedades as $variedad): ?>
                    <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroCosecha">Cosecha</label>
            <select id="sgpFiltroCosecha" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($cosechas as $cosecha): ?>
                    <option value="<?php echo htmlspecialchars($cosecha); ?>"><?php echo htmlspecialchars($cosecha); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroCiclo">Ciclo</label>
            <select id="sgpFiltroCiclo" class="form-control select2" multiple data-cuenta-filtro>
                <option value="1">Ciclo 1 (edad ≤ 35)</option>
                <option value="2">Ciclo 2 (edad &gt; 35)</option>
            </select>
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroSemanaDesde">Semana desde <small class="text-muted">(aaww)</small></label>
            <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="aaww" id="sgpFiltroSemanaDesde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($semanaDesdeDefault); ?>">
        </div>
        <div>
            <label class="filter-label" for="sgpFiltroSemanaHasta">Semana hasta <small class="text-muted">(aaww)</small></label>
            <input type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="aaww" id="sgpFiltroSemanaHasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($semanaHastaDefault); ?>">
        </div>
        <div>
            <label class="filter-label">Tipo (comparar hasta 3)</label>
            <div id="sgpFiltroTipo" class="d-flex flex-wrap gap-2 pt-1">
                <?php foreach ($tipos as $tipoOpt): ?>
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input sgp-tipo-check" type="checkbox" value="<?php echo htmlspecialchars($tipoOpt); ?>" id="sgpTipo_<?php echo htmlspecialchars($tipoOpt); ?>" <?php echo in_array($tipoOpt, $tiposPorDefecto, true) ? 'checked' : ''; ?>>
                        <label class="form-check-label" style="font-size:0.8rem;" for="sgpTipo_<?php echo htmlspecialchars($tipoOpt); ?>"><?php echo htmlspecialchars($tipoOpt); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="filtros-footer d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="SeguimientoProduccion.limpiarFiltros()" title="Limpiar todos los filtros">
            <span class="material-icons align-middle" style="font-size: 1rem;">restart_alt</span>
            Limpiar
        </button>
        <button type="button" class="btn btn-sm btn-teal flex-fill" data-filtros-aplicar onclick="SeguimientoProduccion.aplicarFiltros()" title="Consultar con los filtros seleccionados">
            <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
            Consultar
        </button>
    </div>
</div>

<div class="container-fluid px-0 pb-4">
    <div class="row g-3" id="sgpRow">
        <div class="col-12" id="sgpMainCol">

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div id="sgpEstadoCarga" class="text-muted small">Cargando información...</div>
                <div class="btn-group sgp-modo-group btn-group-sm mt-2 mt-md-0" role="group" aria-label="Agrupar por">
                    <button type="button" class="btn btn-outline-secondary active" data-modo="edad">Por Edad</button>
                    <button type="button" class="btn btn-outline-secondary" data-modo="aass">Por Año-Semana</button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="sgp-section-card">
                        <h5 class="sgp-section-title">Promedio F/M (tallos por mata)</h5>
                        <div style="position: relative; height: 340px;">
                            <canvas id="sgpChartFM"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="sgp-section-card">
                        <h5 class="sgp-section-title">Total Corte (tallos)</h5>
                        <div style="position: relative; height: 340px;">
                            <canvas id="sgpChartCorte"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="sgp-section-card">
                        <h5 class="sgp-section-title">Detalle numérico</h5>
                        <div class="sgp-table-wrapper">
                            <table class="table table-sm table-striped sgp-table mb-0">
                                <thead>
                                    <tr>
                                        <th id="sgpColX">Edad</th>
                                        <th>Tipo</th>
                                        <th class="text-end">Promedio F/M</th>
                                        <th class="text-end">Corte (suma)</th>
                                        <th class="text-end">Matas</th>
                                        <th class="text-end">Registros</th>
                                    </tr>
                                </thead>
                                <tbody id="sgpTablaDetalle">
                                    <tr><td colspan="6" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="sgp-section-card">
                        <h5 class="sgp-section-title">Acumulado por Ciclo</h5>
                        <div class="sgp-table-wrapper">
                            <table class="table table-sm table-striped sgp-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Ciclo</th>
                                        <th class="text-end">F/M acumulado</th>
                                        <th class="text-end">Corte (suma)</th>
                                        <th class="text-end">Matas (promedio)</th>
                                        <th class="text-end">Registros (suma)</th>
                                    </tr>
                                </thead>
                                <tbody id="sgpTablaAcumuladoCiclo">
                                    <tr><td colspan="6" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/seguimiento_produccion.js?v=5"></script>

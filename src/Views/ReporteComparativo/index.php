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

    .input-filter {
        height: 36px;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 0.82rem !important;
        padding: 0.375rem 0.65rem;
        color: #1e293b;
        background-color: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .input-filter:focus {
        border-color: #00796B !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 121, 107, 0.15) !important;
        outline: none;
    }

    /* Estilos Select2 Premium y sin Barras de Scroll */
    #comparativoFiltros .select2-container {
        width: 100% !important;
    }

    #comparativoFiltros .select2-container--default .select2-selection--multiple {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        min-height: 36px !important;
        max-height: none !important;
        overflow-y: visible !important;
        overflow-x: visible !important;
        padding: 2px 6px !important;
        cursor: text;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    #comparativoFiltros .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #00796B !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 121, 107, 0.15) !important;
        outline: 0;
    }

    #comparativoFiltros .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e8f5e9 !important;
        border: 1px solid #c8e6c9 !important;
        color: #1b5e20 !important;
        border-radius: 6px !important;
        padding: 1px 7px !important;
        font-size: 0.74rem !important;
        font-weight: 600 !important;
        margin: 2px 3px 2px 0 !important;
    }

    #comparativoFiltros .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #2e7d32 !important;
        margin-right: 4px !important;
        font-weight: bold;
        border: none !important;
    }

    #comparativoFiltros .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #c62828 !important;
        background: transparent !important;
    }

    #comparativoFiltros .select2-container--default .select2-search--inline {
        margin: 0 !important;
    }

    #comparativoFiltros .select2-container--default .select2-search--inline .select2-search__field {
        margin: 4px 0 0 3px !important;
        height: 24px !important;
        font-size: 0.8rem !important;
        font-family: inherit !important;
        color: #1e293b !important;
        line-height: 24px !important;
    }

    #comparativoFiltros .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-size: 0.8rem !important;
        margin-top: 5px !important;
        float: left;
    }

    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        font-size: 0.82rem !important;
        z-index: 1060 !important;
        overflow: hidden;
    }

    .select2-results__option {
        padding: 6px 10px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #00796B !important;
        color: #ffffff !important;
    }

    /* ========================================================
       TARJETAS DE KPIS (DASHBOARD CORPORATIVO)
       ======================================================== */
    .reportes-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .reportes-kpi {
        padding: 1.1rem 1.25rem;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .reportes-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 77, 64, 0.08);
    }

    .reportes-kpi-label {
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 700;
    }

    .reportes-kpi-icon-wrap {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reportes-kpi-value {
        display: block;
        margin-top: 0.35rem;
        font-size: 1.85rem;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }

    .reportes-kpi-help {
        display: block;
        margin-top: 0.35rem;
        color: #64748b;
        font-size: 0.78rem;
    }

    /* ========================================================
       GRÁFICOS Y TARJETAS DE CONTENIDO
       ======================================================== */
    .reportes-charts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .reportes-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        padding: 1.25rem;
        min-width: 0;
        margin-bottom: 1.25rem;
    }

    .reportes-card h4 {
        margin: 0 0 0.85rem;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
    }

    .reportes-card-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }

    .reportes-card-toggle h4 {
        margin: 0;
    }

    .reportes-card-toggle .toggle-icon {
        color: #64748b;
        transition: transform 0.2s ease;
    }

    .reportes-card-toggle.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    .reportes-chart-wrap {
        position: relative;
        height: 290px;
    }

    .reportes-empty-state {
        text-align: center;
        padding: 2.25rem 1rem;
        color: #64748b;
    }

    .reportes-empty-state .material-icons {
        font-size: 2.75rem;
        color: #94a3b8;
    }

    @media (max-width: 991.98px) {
        .reportes-kpi-grid,
        .reportes-charts-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .reportes-kpi-grid,
        .reportes-charts-grid {
            grid-template-columns: 1fr;
        }
        .reportes-chart-wrap {
            height: 240px;
        }
    }
</style>

<!-- Page Header Banner / Navbar estilo CRUD EMV -->
<div class="crud-page-header d-print-none no-print">
    <div class="crud-header-left">
        <div class="crud-header-icon">
            <span class="material-icons">query_stats</span>
        </div>
        <div>
            <!-- Título omitido según indicación del usuario (ya presente en el navbar/breadcrumb superior) -->
            <div class="crud-page-subtitle">
                <span class="crud-page-badge">Producción</span>
                <span class="text-muted d-none d-sm-inline">• Comparativo teórico vs real de siembras</span>
            </div>
        </div>
    </div>
    <div class="crud-header-actions report-actions-bar mb-0">
        <!-- Espacio para los chips de filtros activos a medida que se agregan -->
        <div class="filtros-aplicados"></div>
        <span class="report-banner-status me-2" id="comparativoEstado"></span>
        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center" id="comparativoFullscreenBtn" onclick="ReporteComparativo.toggleFullscreen()" title="Pantalla completa">
            <span class="material-icons" id="comparativoFullscreenIcon" style="font-size:1.15rem;">fullscreen</span>
        </button>
        <button type="button" class="btn btn-sm btn-teal d-flex align-items-center" data-bs-toggle="offcanvas"
            data-bs-target="#comparativoFiltros" aria-controls="comparativoFiltros" title="Mostrar filtros">
            <span class="material-icons me-1" style="font-size:1rem;">filter_list</span>
            Filtros
            <span class="badge rounded-pill bg-light text-dark ms-1 d-none filtros-badge">0</span>
        </button>
    </div>
</div>

<!-- Barra lateral vertical de filtros (mismo patrón que Reporte Lonas) -->
<div class="offcanvas offcanvas-end filtros-sidebar no-print" tabindex="-1" id="comparativoFiltros"
    data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="comparativoFiltrosLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="comparativoFiltrosLabel">
            <span class="material-icons" style="font-size: 1.2rem; color: #00796B;">filter_list</span>
            Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <!-- 1. Finca -->
        <div>
            <label class="filter-label" for="cFiltroFinca">Finca</label>
            <select id="cFiltroFinca" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todas">
                <?php foreach (($filtrosOpciones['fincas'] ?? []) as $finca): ?>
                    <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 2. Bloque -->
        <div>
            <label class="filter-label" for="cFiltroBloque">Bloque</label>
            <select id="cFiltroBloque" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todos">
                <?php foreach (($filtrosOpciones['bloques'] ?? []) as $bloque): ?>
                    <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 3. Variedad -->
        <div>
            <label class="filter-label" for="cFiltroVariedad">Variedad</label>
            <select id="cFiltroVariedad" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todas">
                <?php foreach (($filtrosOpciones['variedades'] ?? []) as $variedad): ?>
                    <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 4. Temporada -->
        <div>
            <label class="filter-label" for="cFiltroTemporada">Temporada</label>
            <select id="cFiltroTemporada" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todas">
                <?php foreach (($filtrosOpciones['temporadas'] ?? []) as $temporada): ?>
                    <option value="<?php echo htmlspecialchars($temporada); ?>"><?php echo htmlspecialchars($temporada); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 5. Flor -->
        <div>
            <label class="filter-label" for="cFiltroFlor">Flor</label>
            <select id="cFiltroFlor" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todas">
                <?php foreach (($filtrosOpciones['flores'] ?? []) as $flor): ?>
                    <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 6. Tipo Siembra -->
        <div>
            <label class="filter-label" for="cFiltroTipoSiembra">Tipo siembra</label>
            <select id="cFiltroTipoSiembra" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todos">
                <?php foreach (($filtrosOpciones['tipos_siembra'] ?? []) as $tipo): ?>
                    <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 7. Programa -->
        <div>
            <label class="filter-label" for="cFiltroPrograma">Programa</label>
            <select id="cFiltroPrograma" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todos">
                <?php foreach (($filtrosOpciones['programas'] ?? []) as $programa): ?>
                    <option value="<?php echo htmlspecialchars($programa); ?>"><?php echo htmlspecialchars($programa); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 8. Color -->
        <div>
            <label class="filter-label" for="cFiltroColor">Color</label>
            <select id="cFiltroColor" class="form-control select2-filter" multiple data-cuenta-filtro data-placeholder="Todos">
                <?php foreach (($filtrosOpciones['colores'] ?? []) as $color): ?>
                    <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 9. Semana Desde -->
        <div>
            <label class="filter-label" for="cFiltroFechaDesde">Semana desde <small class="text-muted">(yyww)</small></label>
            <input type="text" id="cFiltroFechaDesde" class="form-control input-filter" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="yyww" value="<?php echo htmlspecialchars($rangoSemanasDefault['desde'] ?? ''); ?>">
        </div>
        <!-- 10. Semana Hasta -->
        <div>
            <label class="filter-label" for="cFiltroFechaHasta">Semana hasta <small class="text-muted">(yyww)</small></label>
            <input type="text" id="cFiltroFechaHasta" class="form-control input-filter" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="yyww" value="<?php echo htmlspecialchars($rangoSemanasDefault['hasta'] ?? ''); ?>">
        </div>
    </div>
    <div class="filtros-footer d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="ReporteComparativo.limpiarFiltros()" title="Limpiar todos los filtros">
            <span class="material-icons align-middle" style="font-size: 1rem;">restart_alt</span>
            Limpiar
        </button>
        <button type="button" class="btn btn-sm btn-teal flex-fill" data-filtros-aplicar onclick="ReporteComparativo.aplicarFiltros()" title="Consultar con los filtros seleccionados">
            <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
            Consultar
        </button>
    </div>
</div>

<div class="container-fluid px-0 pb-4">
    <div class="row g-3" id="comparativoRow">
        <div class="col-12" id="comparativoMainCol">

            <!-- Protagonismo Inmediato de KPIs Corporativos -->
            <div class="reportes-kpi-grid" id="comparativoKpiGrid">
                <div class="reportes-kpi">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="reportes-kpi-label">Camas reales</span>
                        <div class="reportes-kpi-icon-wrap" style="background-color:#e0f2f1; color:#00796B;">
                            <span class="material-icons" style="font-size:1.25rem;">yard</span>
                        </div>
                    </div>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="reportes-kpi-label">Camas teóricas</span>
                        <div class="reportes-kpi-icon-wrap" style="background-color:#e3f2fd; color:#1565C0;">
                            <span class="material-icons" style="font-size:1.25rem;">assignment</span>
                        </div>
                    </div>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="reportes-kpi-label">Cumplimiento</span>
                        <div class="reportes-kpi-icon-wrap" style="background-color:#f3e5f5; color:#7B1FA2;">
                            <span class="material-icons" style="font-size:1.25rem;">trending_up</span>
                        </div>
                    </div>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="reportes-kpi-label">Fincas</span>
                        <div class="reportes-kpi-icon-wrap" style="background-color:#fff3e0; color:#E65100;">
                            <span class="material-icons" style="font-size:1.25rem;">domain</span>
                        </div>
                    </div>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
            </div>

            <!-- Gráficos Teórico vs Real -->
            <div class="reportes-charts-grid">
                <div class="reportes-card">
                    <h4>Teórica vs Real por finca</h4>
                    <div class="reportes-chart-wrap">
                        <canvas id="comparativoChart1"></canvas>
                    </div>
                </div>
                <div class="reportes-card">
                    <h4>Teórica vs Real por semana (yyww ISO)</h4>
                    <div class="reportes-chart-wrap">
                        <canvas id="comparativoChart2"></canvas>
                    </div>
                </div>
            </div>

            <!-- Resumen Dinámico Multi-Dimensión -->
            <div class="reportes-card mb-3">
                <h4>Resumen dinámico (real vs teórica)</h4>
                <p class="text-muted small mb-2">Elige una o varias dimensiones para agrupar; el resumen se recalcula automáticamente.</p>
                <div style="max-width: 640px;">
                    <select id="comparativoResumenPorSelect" class="form-control select2-filter" multiple data-placeholder="Seleccionar dimensiones...">
                        <option value="finca">Finca</option>
                        <option value="flor">Flor</option>
                        <option value="variedad">Variedad</option>
                        <option value="temporada">Temporada</option>
                        <option value="tipo_siembra">Tipo siembra</option>
                        <option value="programa">Programa</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div id="comparativoResumenEmptyState" class="reportes-empty-state">
                    <span class="material-icons">table_chart</span>
                    <p class="mb-0">Selecciona una o más dimensiones arriba para ver el resumen agrupado.</p>
                </div>
                <div class="table-responsive mt-3" id="comparativoResumenWrap" style="display:none;">
                    <table id="comparativoResumenTable" class="table table-striped w-100">
                        <thead><tr></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen por Semana (Acordeón colapsable) -->
            <div class="reportes-card mb-3">
                <div class="reportes-card-toggle" onclick="ReporteComparativo.toggleCard(this)">
                    <h4>Resumen por semana</h4>
                    <span class="material-icons toggle-icon">expand_more</span>
                </div>
                <div class="reportes-card-collapsible mt-3">
                    <div id="comparativoSemanalEmptyState" class="reportes-empty-state">
                        <span class="material-icons">calendar_view_week</span>
                        <p class="mb-0">No hay datos por semana para estos filtros.</p>
                    </div>
                    <div class="table-responsive" id="comparativoSemanalWrap" style="display:none;">
                        <table id="comparativoSemanalTable" class="table table-striped w-100">
                            <thead><tr></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detalle de Siembras (Acordeón colapsable) -->
            <div class="reportes-card mb-3">
                <div class="reportes-card-toggle" onclick="ReporteComparativo.toggleCard(this)">
                    <h4>Detalle de siembras</h4>
                    <span class="material-icons toggle-icon">expand_more</span>
                </div>
                <div class="reportes-card-collapsible mt-3">
                    <div id="comparativoTableEmptyState" class="reportes-empty-state">
                        <span class="material-icons">insights</span>
                        <p class="mb-0">No hay siembras que coincidan con estos filtros.<br>Ajusta la finca, variedad, temporada o el rango de fechas.</p>
                    </div>
                    <div class="table-responsive" id="comparativoTableWrap" style="display:none;">
                        <table id="comparativoTable" class="table table-striped w-100">
                            <thead><tr></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Gráfico por Semana de Fecha Pico -->
            <div class="reportes-card">
                <h4>Teórica vs Real por semana de fecha pico (yyww ISO)</h4>
                <div class="reportes-chart-wrap" style="height: 320px;">
                    <canvas id="comparativoChart3"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var ReporteComparativo = (function () {
    var chart1, chart2, chart3;
    var actualizandoFiltros = false;

    var SELECTS_FILTRO = {
        finca: { id: '#cFiltroFinca', opciones: 'fincas' },
        bloque: { id: '#cFiltroBloque', opciones: 'bloques' },
        variedad: { id: '#cFiltroVariedad', opciones: 'variedades' },
        temporada: { id: '#cFiltroTemporada', opciones: 'temporadas' },
        flor: { id: '#cFiltroFlor', opciones: 'flores' },
        tipo_siembra: { id: '#cFiltroTipoSiembra', opciones: 'tipos_siembra' },
        programa: { id: '#cFiltroPrograma', opciones: 'programas' },
        color: { id: '#cFiltroColor', opciones: 'colores' }
    };

    var KPI_CONFIG = {
        'Camas reales': { icon: 'yard', color: '#00796B', bg: '#e0f2f1' },
        'Camas teóricas': { icon: 'assignment', color: '#1565C0', bg: '#e3f2fd' },
        'Cumplimiento': { icon: 'trending_up', color: '#7B1FA2', bg: '#f3e5f5' },
        'Fincas': { icon: 'domain', color: '#E65100', bg: '#fff3e0' }
    };

    function init() {
        // Inicialización Select2 con autocompletado y búsqueda inteligente
        $('.select2-filter').each(function () {
            $(this).select2({
            width: '100%',
            placeholder: $(this).data('placeholder') || 'Todas',
            allowClear: true,
            // Dentro del panel lateral el desplegable debe vivir en el offcanvas
            dropdownParent: $(this).closest('.offcanvas, body'),
            language: {
                noResults: function () {
                    return "Sin resultados";
                },
                searching: function () {
                    return "Buscando...";
                }
            }
            });
        });

        $('#comparativoResumenPorSelect').on('change', cargarDatos);

        $('#cFiltroFinca, #cFiltroBloque, #cFiltroVariedad, #cFiltroTemporada, #cFiltroFlor, #cFiltroTipoSiembra, #cFiltroPrograma, #cFiltroColor')
            .on('change', onFiltroChange);
        $('#cFiltroFechaDesde, #cFiltroFechaHasta').on('change', onFiltroChange);

        var opcionesEjeYDesdeCero = {
            responsive: true,
            maintainAspectRatio: false,
            scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
        };

        chart1 = new Chart(document.getElementById('comparativoChart1').getContext('2d'), {
            type: 'bar',
            data: { labels: [], datasets: [] },
            options: opcionesEjeYDesdeCero
        });
        chart2 = new Chart(document.getElementById('comparativoChart2').getContext('2d'), {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: opcionesEjeYDesdeCero
        });
        chart3 = new Chart(document.getElementById('comparativoChart3').getContext('2d'), {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: opcionesEjeYDesdeCero
        });

        // Escuchar cambios de fullscreen
        document.addEventListener('fullscreenchange', function() {
            var icon = document.getElementById('comparativoFullscreenIcon');
            if (icon) {
                icon.textContent = document.fullscreenElement ? 'fullscreen_exit' : 'fullscreen';
            }
            setTimeout(function() {
                if (chart1) chart1.resize();
                if (chart2) chart2.resize();
                if (chart3) chart3.resize();
            }, 300);
        });

        cargarDatos();
    }

    function getCleanMultiVal(selector) {
        var val = $(selector).val();
        if (!val) return [];
        if (!Array.isArray(val)) val = [val];
        return val.filter(function (item) {
            return item !== null && item !== undefined && String(item).trim() !== '';
        });
    }

    function construirFiltros() {
        return {
            finca: getCleanMultiVal('#cFiltroFinca'),
            bloque: getCleanMultiVal('#cFiltroBloque'),
            variedad: getCleanMultiVal('#cFiltroVariedad'),
            temporada: getCleanMultiVal('#cFiltroTemporada'),
            flor: getCleanMultiVal('#cFiltroFlor'),
            tipo_siembra: getCleanMultiVal('#cFiltroTipoSiembra'),
            programa: getCleanMultiVal('#cFiltroPrograma'),
            color: getCleanMultiVal('#cFiltroColor'),
            desde: (document.getElementById('cFiltroFechaDesde').value || '').trim(),
            hasta: (document.getElementById('cFiltroFechaHasta').value || '').trim(),
            resumen_por: getCleanMultiVal('#comparativoResumenPorSelect')
        };
    }

    function setEstado(texto, tipo) {
        var el = document.getElementById('comparativoEstado');
        if (!el) return;
        el.textContent = texto || '';
        el.className = 'report-banner-status me-2' + (tipo ? ' text-' + tipo : ' text-muted');
    }

    function cargarDatos() {
        setEstado('Consultando...', 'muted');
        var filtros = construirFiltros();
        $.ajax({
            url: 'ajax/reporte_comparativo.php',
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (data) {
            setEstado('', '');
            pintarKpis(data.kpis || []);
            pintarGraficos(data.chart1 || null, data.chart2 || null, data.chart3 || null);
            pintarTabla('#comparativoSemanalTable', '#comparativoSemanalEmptyState', '#comparativoSemanalWrap', data.resumenSemanal || null);
            pintarTabla('#comparativoTable', '#comparativoTableEmptyState', '#comparativoTableWrap', data.table || null);
            pintarTabla('#comparativoResumenTable', '#comparativoResumenEmptyState', '#comparativoResumenWrap', data.resumen || null);
        }).fail(function () {
            setEstado('Error al consultar', 'danger');
            console.error('No se pudo cargar el reporte comparativo');
        });
    }

    function onFiltroChange() {
        if (actualizandoFiltros) { return; }
        actualizarOpcionesFiltros();
    }

    function actualizarOpcionesFiltros() {
        var filtros = construirFiltros();
        filtros.accion = 'opciones';
        $.ajax({
            url: 'ajax/reporte_comparativo.php',
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (opciones) {
            actualizandoFiltros = true;
            Object.keys(SELECTS_FILTRO).forEach(function (clave) {
                var cfg = SELECTS_FILTRO[clave];
                var $select = $(cfg.id);
                var seleccionActual = $select.val() || [];
                if (!Array.isArray(seleccionActual)) seleccionActual = [seleccionActual];
                var disponibles = (opciones[cfg.opciones] || []).map(String);
                var nuevaSeleccion = seleccionActual.filter(function (v) {
                    return disponibles.indexOf(String(v)) !== -1;
                });

                $select.empty();
                disponibles.forEach(function (valor) {
                    $select.append(new Option(valor, valor, false, nuevaSeleccion.indexOf(valor) !== -1));
                });
                $select.val(nuevaSeleccion).trigger('change.select2');
            });
            actualizandoFiltros = false;
        }).fail(function () {
            actualizandoFiltros = false;
            console.error('No se pudieron actualizar las opciones de filtro');
        });
    }

    function pintarKpis(kpis) {
        var grid = document.getElementById('comparativoKpiGrid');
        if (!grid || !kpis.length) { return; }
        grid.innerHTML = kpis.map(function (kpi) {
            var cfg = KPI_CONFIG[kpi.label] || { icon: 'analytics', color: '#00796B', bg: '#e0f2f1' };
            return '<div class="reportes-kpi">' +
                '<div class="d-flex align-items-center justify-content-between mb-2">' +
                    '<span class="reportes-kpi-label">' + kpi.label + '</span>' +
                    '<div class="reportes-kpi-icon-wrap" style="background-color:' + cfg.bg + '; color:' + cfg.color + ';">' +
                        '<span class="material-icons" style="font-size:1.25rem;">' + cfg.icon + '</span>' +
                    '</div>' +
                '</div>' +
                '<strong class="reportes-kpi-value">' + kpi.value + '</strong>' +
                '<span class="reportes-kpi-help">' + (kpi.help || '') + '</span>' +
            '</div>';
        }).join('');
    }

    function pintarGraficos(data1, data2, data3) {
        if (data1) {
            chart1.data.labels = data1.labels || [];
            chart1.data.datasets = data1.datasets || [];
            chart1.update();
        }
        if (data2) {
            chart2.data.labels = data2.labels || [];
            chart2.data.datasets = data2.datasets || [];
            chart2.update();
        }
        if (data3) {
            chart3.data.labels = data3.labels || [];
            chart3.data.datasets = data3.datasets || [];
            chart3.update();
        }
    }

    function pintarTabla(tableSel, emptyStateSel, wrapSel, table) {
        var emptyState = document.querySelector(emptyStateSel);
        var wrap = document.querySelector(wrapSel);
        if (!table || !table.columns || !table.columns.length) {
            if (emptyState) emptyState.style.display = 'block';
            if (wrap) wrap.style.display = 'none';
            return;
        }
        if (emptyState) emptyState.style.display = 'none';
        if (wrap) wrap.style.display = 'block';

        if ($.fn.DataTable.isDataTable(tableSel)) {
            $(tableSel).DataTable().destroy();
        }
        $(tableSel + ' thead').empty();
        $(tableSel + ' tbody').empty();

        $(tableSel).DataTable({
            data: table.rows || [],
            columns: table.columns.map(function (c) {
                var col = { data: c.data, title: c.title };
                if (c.data === 'camas_real' || c.data === 'camas_teorica' || c.data === 'diferencia') {
                    col.render = function (data) { return Math.round(Number(data) || 0); };
                }
                if (c.data === 'cumplimiento_pct') {
                    col.render = function (data) { return data === null || data === undefined ? 'N/D' : data + '%'; };
                }
                return col;
            }),
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
        });
    }

    function toggleCard(headerEl) {
        $(headerEl).toggleClass('collapsed');
        $(headerEl).next('.reportes-card-collapsible').slideToggle(180);
    }

    function toggleFullscreen() {
        var container = document.getElementById('comparativoMainCol') || document.getElementById('layoutSidenav_content') || document.documentElement;
        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    function limpiarFiltros() {
        actualizandoFiltros = true;
        $('#cFiltroFinca, #cFiltroBloque, #cFiltroVariedad, #cFiltroTemporada, #cFiltroFlor, #cFiltroTipoSiembra, #cFiltroPrograma, #cFiltroColor').val(null).trigger('change.select2');
        actualizandoFiltros = false;
        document.getElementById('cFiltroFechaDesde').value = '<?php echo htmlspecialchars($rangoSemanasDefault['desde'] ?? ''); ?>';
        document.getElementById('cFiltroFechaHasta').value = '<?php echo htmlspecialchars($rangoSemanasDefault['hasta'] ?? ''); ?>';
        actualizarOpcionesFiltros();
        cargarDatos();
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleFullscreen: toggleFullscreen,
        toggleCard: toggleCard,
        limpiarFiltros: limpiarFiltros
    };
})();

document.addEventListener('DOMContentLoaded', ReporteComparativo.init);
</script>

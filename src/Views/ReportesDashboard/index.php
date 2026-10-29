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

    .filtro-sidebar .select2-container { width: 100% !important; }
    .filtro-sidebar .select2-container .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: calc(1.5em + .75rem + 2px);
    }

    .reportes-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin: -0.5rem -15px 1rem;
        padding: 0.9rem 15px;
    }

    .reportes-bar h1 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        color: #17312d;
    }

    .reportes-bar p {
        margin: 0.15rem 0 0;
        color: #5f6f6b;
        font-size: 0.8rem;
    }

    .reportes-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .reportes-kpi {
        padding: 1rem;
        border-radius: 16px;
        background: linear-gradient(145deg, #f8fbfa 0%, #eef7f5 100%);
        border: 1px solid #dce9e6;
    }

    .reportes-kpi-label {
        color: #5e726d;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
    }

    .reportes-kpi-value {
        display: block;
        margin-top: 0.35rem;
        font-size: 1.85rem;
        line-height: 1;
        font-weight: 700;
        color: #17312d;
    }

    .reportes-kpi-help {
        display: block;
        margin-top: 0.45rem;
        color: #67807a;
        font-size: 0.8rem;
    }

    .reportes-charts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .reportes-card {
        background: #ffffff;
        border: 1px solid #e1ebe9;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05);
        padding: 1rem;
        min-width: 0;
    }

    .reportes-card h4 {
        margin: 0 0 0.75rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #17312d;
    }

    .reportes-card-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }

    .reportes-card-toggle h4 {
        margin: 0 0 0.75rem;
    }

    .reportes-card-toggle .toggle-icon {
        color: #6a7f79;
        transition: transform 0.2s ease;
        margin-bottom: 0.75rem;
    }

    .reportes-card-toggle.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    .reportes-chart-wrap {
        position: relative;
        height: 280px;
    }

    .reportes-empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: #6a7f79;
    }

    .reportes-empty-state .material-icons {
        font-size: 2.5rem;
        color: #9fc2bb;
    }

    /* Filtro-sidebar: mismo lenguaje visual usado en Proyectos/Tareas/Bitácora */
    .filtro-sidebar {
        position: sticky;
        top: 1rem;
    }

    .filtro-sidebar .card {
        border: 1px solid #dbe7e4;
        border-radius: 14px;
    }

    .filtro-sidebar .card-header {
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

    .filtro-sidebar .card-header .material-icons.toggle-icon {
        font-size: 1.1rem;
    }

    .filtro-sidebar label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: 0.02em;
    }

    .reportes-filtros-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.25rem 0.75rem;
    }

    @media (max-width: 991.98px) {
        .reportes-kpi-grid,
        .reportes-charts-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .reportes-filtros-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .filtro-sidebar {
            position: static;
            margin-bottom: 1rem;
        }

        .reportes-kpi-grid,
        .reportes-charts-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<!-- Page Header Banner / Navbar estilo CRUD EMV -->
<div class="crud-page-header d-print-none no-print">
    <div class="crud-header-left">
        <div class="crud-header-icon">
            <span class="material-icons">bar_chart</span>
        </div>
        <div>
            <!-- Título omitido según indicación del usuario (ya presente en el navbar/breadcrumb superior) -->
            <div class="crud-page-subtitle">
                <span class="crud-page-badge">Producción</span>
                <span class="text-muted d-none d-sm-inline">• Resumen y métricas de camas sembradas</span>
            </div>
        </div>
    </div>
    <div class="crud-header-actions report-actions-bar mb-0">
        <!-- Espacio para los chips de filtros activos a medida que se agregan -->
        <div class="filtros-aplicados"></div>
        <button type="button" class="btn btn-sm btn-teal d-flex align-items-center" data-bs-toggle="offcanvas"
            data-bs-target="#reportesFiltros" aria-controls="reportesFiltros" title="Mostrar filtros">
            <span class="material-icons me-1" style="font-size:1rem;">filter_list</span>
            Filtros
            <span class="badge rounded-pill bg-light text-dark ms-1 d-none filtros-badge">0</span>
        </button>
    </div>
</div>

<!-- Barra lateral vertical de filtros (mismo patrón que Reporte Lonas) -->
<div class="offcanvas offcanvas-end filtros-sidebar no-print" tabindex="-1" id="reportesFiltros"
    data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="reportesFiltrosLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="reportesFiltrosLabel">
            <span class="material-icons" style="font-size: 1.2rem; color: #00796B;">filter_list</span>
            Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <label class="filter-label" for="filtroFinca">Finca</label>
            <select id="filtroFinca" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['fincas'] ?? []) as $finca): ?>
                    <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroBloque">Bloque</label>
            <select id="filtroBloque" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['bloques'] ?? []) as $bloque): ?>
                    <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroVariedad">Variedad</label>
            <select id="filtroVariedad" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['variedades'] ?? []) as $variedad): ?>
                    <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroTemporada">Temporada</label>
            <select id="filtroTemporada" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['temporadas'] ?? []) as $temporada): ?>
                    <option value="<?php echo htmlspecialchars($temporada); ?>"><?php echo htmlspecialchars($temporada); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroFlor">Flor</label>
            <select id="filtroFlor" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['flores'] ?? []) as $flor): ?>
                    <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroColor">Color</label>
            <select id="filtroColor" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach (($filtrosOpciones['colores'] ?? []) as $color): ?>
                    <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="filtroFechaDesde">Siembra desde</label>
            <input type="date" id="filtroFechaDesde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['desde'] ?? ''); ?>">
        </div>
        <div>
            <label class="filter-label" for="filtroFechaHasta">Siembra hasta</label>
            <input type="date" id="filtroFechaHasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['hasta'] ?? ''); ?>">
        </div>
    </div>
    <div class="filtros-footer d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="ReportesDashboard.limpiarFiltros()" title="Limpiar todos los filtros">
            <span class="material-icons align-middle" style="font-size: 1rem;">restart_alt</span>
            Limpiar
        </button>
        <button type="button" class="btn btn-sm btn-teal flex-fill" data-filtros-aplicar id="btnAplicarFiltros" onclick="ReportesDashboard.aplicarFiltros()" title="Consultar con los filtros seleccionados">
            <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
            Consultar
        </button>
    </div>
</div>

<div class="container-fluid px-0 pb-4">
    <div class="row g-3" id="reportesRow">
        <div class="col-12" id="reportesMainCol">
            <div class="reportes-kpi-grid" id="reportesKpiGrid">
                <div class="reportes-kpi">
                    <span class="reportes-kpi-label">KPI 1</span>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <span class="reportes-kpi-label">KPI 2</span>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <span class="reportes-kpi-label">KPI 3</span>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
                <div class="reportes-kpi">
                    <span class="reportes-kpi-label">KPI 4</span>
                    <strong class="reportes-kpi-value">—</strong>
                    <span class="reportes-kpi-help">Esperando datos</span>
                </div>
            </div>

            <div class="reportes-charts-grid">
                <div class="reportes-card">
                    <h4>Camas por finca</h4>
                    <div class="reportes-chart-wrap">
                        <canvas id="reportesChart1"></canvas>
                    </div>
                </div>
                <div class="reportes-card">
                    <h4>Camas por semana de siembra (yyww ISO)</h4>
                    <div class="reportes-chart-wrap">
                        <canvas id="reportesChart2"></canvas>
                    </div>
                </div>
            </div>

            <div class="reportes-card mb-3">
                <h4>Resumen dinámico (valor: camas)</h4>
                <p class="text-muted small mb-2">Elige una o varias dimensiones para agrupar; el resumen se recalcula solo.</p>
                <select id="resumenPorSelect" class="form-control select2" multiple style="max-width: 640px;">
                    <option value="finca">Finca</option>
                    <option value="flor">Flor</option>
                    <option value="variedad">Variedad</option>
                    <option value="temporada">Temporada</option>
                    <option value="ciclo">Ciclo</option>
                    <option value="color">Color</option>
                </select>
                <div id="reportesResumenEmptyState" class="reportes-empty-state">
                    <span class="material-icons">table_chart</span>
                    <p class="mb-0">Selecciona una o más dimensiones arriba para ver el resumen.</p>
                </div>
                <div class="table-responsive mt-3" id="reportesResumenWrap" style="display:none;">
                    <table id="reportesResumenTable" class="table table-striped w-100">
                        <thead><tr></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="reportes-card">
                <div class="reportes-card-toggle" onclick="ReportesDashboard.toggleCard(this)">
                    <h4>Detalle</h4>
                    <span class="material-icons toggle-icon">expand_more</span>
                </div>
                <div class="reportes-card-collapsible">
                    <div id="reportesTableEmptyState" class="reportes-empty-state">
                        <span class="material-icons">insights</span>
                        <p class="mb-0">No hay siembras que coincidan con estos filtros.<br>Ajusta la finca, variedad, temporada o el rango de fechas.</p>
                    </div>
                    <div class="table-responsive" id="reportesTableWrap" style="display:none;">
                        <table id="reportesTable" class="table table-striped w-100">
                            <thead><tr></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="reportes-card mt-3" id="reportesEdadCard">
        <h4>Camas por edad de siembra</h4>
        <p class="text-muted small mb-2">Semanas transcurridas desde la fecha de siembra hasta hoy, según los filtros generales de arriba.</p>
        <div class="reportes-chart-wrap" style="height: 360px;">
            <canvas id="reportesChartEdad"></canvas>
        </div>
    </div>
</div>

<script>
var ReportesDashboard = (function () {
    var chart1, chart2, chartEdad;
    var actualizandoFiltros = false;

    var SELECTS_FILTRO = {
        finca: { id: '#filtroFinca', opciones: 'fincas' },
        bloque: { id: '#filtroBloque', opciones: 'bloques' },
        variedad: { id: '#filtroVariedad', opciones: 'variedades' },
        temporada: { id: '#filtroTemporada', opciones: 'temporadas' },
        flor: { id: '#filtroFlor', opciones: 'flores' },
        color: { id: '#filtroColor', opciones: 'colores' }
    };

    function init() {
        $('.select2').each(function () { $(this).select2({ width: '100%', placeholder: 'Todas', allowClear: true, dropdownParent: $(this).closest('.offcanvas, body') }); });
        $('#resumenPorSelect').on('change', cargarDatos);

        $('#filtroFinca, #filtroBloque, #filtroVariedad, #filtroTemporada, #filtroFlor, #filtroColor')
            .on('change', onFiltroChange);
        $('#filtroFechaDesde, #filtroFechaHasta').on('change', onFiltroChange);

        $('#reportesFiltrosToggle').on('click', toggleSidebar);

        var opcionesEjeYDesdeCero = {
            responsive: true,
            maintainAspectRatio: false,
            scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
        };

        chart1 = new Chart(document.getElementById('reportesChart1').getContext('2d'), {
            type: 'bar',
            data: { labels: [], datasets: [] },
            options: opcionesEjeYDesdeCero
        });
        chart2 = new Chart(document.getElementById('reportesChart2').getContext('2d'), {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: opcionesEjeYDesdeCero
        });
        chartEdad = new Chart(document.getElementById('reportesChartEdad').getContext('2d'), {
            type: 'bar',
            data: { labels: [], datasets: [] },
            options: $.extend(true, {}, opcionesEjeYDesdeCero, {
                scales: { xAxes: [{ scaleLabel: { display: true, labelString: 'Edad (semanas)' } }] }
            })
        });
        cargarDatos();
    }

    function construirFiltros() {
        return {
            finca: $('#filtroFinca').val() || [],
            bloque: $('#filtroBloque').val() || [],
            variedad: $('#filtroVariedad').val() || [],
            temporada: $('#filtroTemporada').val() || [],
            flor: $('#filtroFlor').val() || [],
            color: $('#filtroColor').val() || [],
            desde: document.getElementById('filtroFechaDesde').value,
            hasta: document.getElementById('filtroFechaHasta').value,
            resumen_por: $('#resumenPorSelect').val() || []
        };
    }

    function cargarDatos() {
        var filtros = construirFiltros();
        $.ajax({
            url: 'ajax/reportes_dashboard.php',
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (data) {
            pintarKpis(data.kpis || []);
            pintarGraficos(data.chart1 || null, data.chart2 || null, data.chartEdad || null);
            pintarTabla('#reportesTable', '#reportesTableEmptyState', '#reportesTableWrap', data.table || null);
            pintarTabla('#reportesResumenTable', '#reportesResumenEmptyState', '#reportesResumenWrap', data.resumen || null);
        }).fail(function () {
            console.error('No se pudo cargar el reporte');
        });
    }

    // Al cambiar un filtro, los demás selects se recalculan en cascada (solo
    // opciones compatibles con lo ya elegido); la recarga de datos sigue
    // disparándose con "Aplicar".
    function onFiltroChange() {
        if (actualizandoFiltros) { return; }
        actualizarOpcionesFiltros();
    }

    function actualizarOpcionesFiltros() {
        var filtros = construirFiltros();
        filtros.accion = 'opciones';
        $.ajax({
            url: 'ajax/reportes_dashboard.php',
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (opciones) {
            actualizandoFiltros = true;
            Object.keys(SELECTS_FILTRO).forEach(function (clave) {
                var cfg = SELECTS_FILTRO[clave];
                var $select = $(cfg.id);
                var seleccionActual = $select.val() || [];
                var disponibles = (opciones[cfg.opciones] || []).map(String);
                var nuevaSeleccion = seleccionActual.filter(function (v) {
                    return disponibles.indexOf(String(v)) !== -1;
                });

                $select.empty();
                disponibles.forEach(function (valor) {
                    $select.append(new Option(valor, valor, false, nuevaSeleccion.indexOf(valor) !== -1));
                });
                $select.val(nuevaSeleccion).trigger('change');
            });
            actualizandoFiltros = false;
        }).fail(function () {
            actualizandoFiltros = false;
            console.error('No se pudieron actualizar las opciones de filtro');
        });
    }

    function pintarKpis(kpis) {
        var grid = document.getElementById('reportesKpiGrid');
        if (!kpis.length) { return; }
        grid.innerHTML = kpis.map(function (kpi) {
            return '<div class="reportes-kpi">' +
                '<span class="reportes-kpi-label">' + kpi.label + '</span>' +
                '<strong class="reportes-kpi-value">' + kpi.value + '</strong>' +
                '<span class="reportes-kpi-help">' + (kpi.help || '') + '</span>' +
            '</div>';
        }).join('');
    }

    function pintarGraficos(data1, data2, dataEdad) {
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
        if (dataEdad) {
            chartEdad.data.labels = dataEdad.labels || [];
            chartEdad.data.datasets = dataEdad.datasets || [];
            chartEdad.update();
        }
    }

    function pintarTabla(tableSel, emptyStateSel, wrapSel, table) {
        var emptyState = document.querySelector(emptyStateSel);
        var wrap = document.querySelector(wrapSel);
        if (!table || !table.columns || !table.columns.length) {
            emptyState.style.display = 'block';
            wrap.style.display = 'none';
            return;
        }
        emptyState.style.display = 'none';
        wrap.style.display = 'block';

        if ($.fn.DataTable.isDataTable(tableSel)) {
            $(tableSel).DataTable().destroy();
        }
        $(tableSel + ' thead').empty();
        $(tableSel + ' tbody').empty();

        $(tableSel).DataTable({
            data: table.rows || [],
            columns: table.columns.map(function (c) {
                var col = { data: c.data, title: c.title };
                if (c.data === 'camas') {
                    col.render = function (data) { return Math.round(Number(data) || 0); };
                }
                return col;
            }),
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
        });
    }

    function toggleCard(headerEl) {
        $(headerEl).toggleClass('collapsed');
        $(headerEl).next('.reportes-card-collapsible').slideToggle(150);
    }

    function toggleSidebar() {
        var wrapper = document.getElementById('reportesFiltersWrapper');
        if (!wrapper) return;
        var btn = document.querySelector('[data-target="#reportesFiltersWrapper"]');
        var isExp = wrapper.classList.toggle('is-expanded');
        wrapper.classList.toggle('is-collapsed', !isExp);
        if (btn) {
            btn.classList.toggle('active', isExp);
            var textEl = btn.querySelector('.toggle-text');
            var iconEl = btn.querySelector('.toggle-icon');
            if (textEl) textEl.textContent = isExp ? 'Menos filtros' : 'Más filtros';
            if (iconEl) iconEl.textContent = isExp ? 'expand_less' : 'expand_more';
        }
        setTimeout(function () {
            if (chart1) { chart1.resize(); }
            if (chart2) { chart2.resize(); }
            if (chartEdad) { chartEdad.resize(); }
        }, 320);
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleSidebar: toggleSidebar,
        toggleCard: toggleCard,
        limpiarFiltros: function () {
            actualizandoFiltros = true;
            $('#filtroFinca, #filtroBloque, #filtroVariedad, #filtroTemporada, #filtroFlor, #filtroColor').val(null).trigger('change');
            actualizandoFiltros = false;
            document.getElementById('filtroFechaDesde').value = '<?php echo htmlspecialchars($rangoFechasDefault['desde'] ?? ''); ?>';
            document.getElementById('filtroFechaHasta').value = '<?php echo htmlspecialchars($rangoFechasDefault['hasta'] ?? ''); ?>';
            actualizarOpcionesFiltros();
            cargarDatos();
        }
    };
})();

document.addEventListener('DOMContentLoaded', ReportesDashboard.init);
</script>

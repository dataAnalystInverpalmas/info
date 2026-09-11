<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
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

<div class="reportes-bar">
    <h1>Reporte Sembrado</h1>
    <p>Consulta de siembras con filtros, indicadores, gráficos y tabla de detalle, para facilitar la búsqueda de siembras.</p>
</div>

<div class="container-fluid px-3 px-lg-4 pb-4">
    <div class="row" id="reportesRow">
        <div class="col-md-5 col-lg-4" id="reportesSidebarCol">
            <div class="filtro-sidebar mr-0 mr-lg-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header" id="reportesFiltrosToggle">
                        <span>
                            <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                            Filtros
                        </span>
                        <span class="material-icons toggle-icon">chevron_left</span>
                    </div>
                    <div class="card-body p-3" id="reportesFiltrosBody">
                        <div class="reportes-filtros-grid">
                            <div class="mb-2">
                                <label for="filtroFinca">Finca</label>
                                <select id="filtroFinca" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['fincas'] ?? []) as $finca): ?>
                                        <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroBloque">Bloque</label>
                                <select id="filtroBloque" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['bloques'] ?? []) as $bloque): ?>
                                        <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroVariedad">Variedad</label>
                                <select id="filtroVariedad" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['variedades'] ?? []) as $variedad): ?>
                                        <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroTemporada">Temporada</label>
                                <select id="filtroTemporada" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['temporadas'] ?? []) as $temporada): ?>
                                        <option value="<?php echo htmlspecialchars($temporada); ?>"><?php echo htmlspecialchars($temporada); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroFlor">Flor</label>
                                <select id="filtroFlor" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['flores'] ?? []) as $flor): ?>
                                        <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroColor">Color</label>
                                <select id="filtroColor" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['colores'] ?? []) as $color): ?>
                                        <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filtroFechaDesde">Siembra desde</label>
                                <input type="date" id="filtroFechaDesde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['desde'] ?? ''); ?>">
                            </div>
                            <div class="mb-2">
                                <label for="filtroFechaHasta">Siembra hasta</label>
                                <input type="date" id="filtroFechaHasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['hasta'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-brand-green btn-sm w-100 mb-2" id="btnAplicarFiltros" style="background-color:#00796B;border-color:#00796B;color:#fff;" onclick="ReportesDashboard.aplicarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">search</span> Aplicar
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="ReportesDashboard.limpiarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">restart_alt</span> Limpiar
                </button>
            </div>
        </div>

        <div class="col-md-7 col-lg-8" id="reportesMainCol">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btnMostrarFiltros" style="display:none;" onclick="ReportesDashboard.toggleSidebar()">
                <span class="material-icons" style="font-size:1rem;vertical-align:middle;">chevron_right</span> Mostrar filtros
            </button>
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
        $('.select2').select2({ width: '100%', placeholder: 'Todas', allowClear: true });
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
        var sidebarCol = document.getElementById('reportesSidebarCol');
        var mainCol = document.getElementById('reportesMainCol');
        var btnMostrar = document.getElementById('btnMostrarFiltros');
        var oculto = sidebarCol.classList.toggle('d-none');

        if (oculto) {
            mainCol.classList.remove('col-md-7', 'col-lg-8');
            mainCol.classList.add('col-12');
            btnMostrar.style.display = 'inline-flex';
        } else {
            mainCol.classList.remove('col-12');
            mainCol.classList.add('col-md-7', 'col-lg-8');
            btnMostrar.style.display = 'none';
        }

        // Los canvas de Chart.js no se redimensionan solos cuando su contenedor cambia de ancho.
        setTimeout(function () {
            if (chart1) { chart1.resize(); }
            if (chart2) { chart2.resize(); }
            if (chartEdad) { chartEdad.resize(); }
        }, 200);
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

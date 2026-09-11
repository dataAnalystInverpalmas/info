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

    @media (max-width: 575.98px) {
        .reportes-filtros-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="reportes-bar">
    <h1>Teórico vs Real Siembras</h1>
    <p>Consulta de siembras que compara lo sembrado realmente contra el programa teórico, para facilitar la búsqueda de siembras.</p>
</div>

<div class="container-fluid px-3 px-lg-4 pb-4">
    <div class="row" id="comparativoRow">
        <div class="col-md-5 col-lg-4" id="comparativoSidebarCol">
            <div class="filtro-sidebar mr-0 mr-lg-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header" id="comparativoFiltrosToggle">
                        <span>
                            <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                            Filtros
                        </span>
                        <span class="material-icons toggle-icon">chevron_left</span>
                    </div>
                    <div class="card-body p-3" id="comparativoFiltrosBody">
                        <div class="reportes-filtros-grid">
                            <div class="mb-2">
                                <label for="cFiltroFinca">Finca</label>
                                <select id="cFiltroFinca" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['fincas'] ?? []) as $finca): ?>
                                        <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroBloque">Bloque</label>
                                <select id="cFiltroBloque" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['bloques'] ?? []) as $bloque): ?>
                                        <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroVariedad">Variedad</label>
                                <select id="cFiltroVariedad" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['variedades'] ?? []) as $variedad): ?>
                                        <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroTemporada">Temporada</label>
                                <select id="cFiltroTemporada" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['temporadas'] ?? []) as $temporada): ?>
                                        <option value="<?php echo htmlspecialchars($temporada); ?>"><?php echo htmlspecialchars($temporada); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroFlor">Flor</label>
                                <select id="cFiltroFlor" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['flores'] ?? []) as $flor): ?>
                                        <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroTipoSiembra">Tipo siembra</label>
                                <select id="cFiltroTipoSiembra" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['tipos_siembra'] ?? []) as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroPrograma">Programa</label>
                                <select id="cFiltroPrograma" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['programas'] ?? []) as $programa): ?>
                                        <option value="<?php echo htmlspecialchars($programa); ?>"><?php echo htmlspecialchars($programa); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroColor">Color</label>
                                <select id="cFiltroColor" class="form-control select2" multiple>
                                    <?php foreach (($filtrosOpciones['colores'] ?? []) as $color): ?>
                                        <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroFechaDesde">Fecha desde</label>
                                <input type="date" id="cFiltroFechaDesde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['desde'] ?? ''); ?>">
                            </div>
                            <div class="mb-2">
                                <label for="cFiltroFechaHasta">Fecha hasta</label>
                                <input type="date" id="cFiltroFechaHasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($rangoFechasDefault['hasta'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-brand-green btn-sm w-100 mb-2" style="background-color:#00796B;border-color:#00796B;color:#fff;" onclick="ReporteComparativo.aplicarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">search</span> Aplicar
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="ReporteComparativo.limpiarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">restart_alt</span> Limpiar
                </button>
            </div>
        </div>

        <div class="col-md-7 col-lg-8" id="comparativoMainCol">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btnMostrarFiltrosComparativo" style="display:none;" onclick="ReporteComparativo.toggleSidebar()">
                <span class="material-icons" style="font-size:1rem;vertical-align:middle;">chevron_right</span> Mostrar filtros
            </button>

            <div class="reportes-kpi-grid" id="comparativoKpiGrid">
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

            <div class="reportes-card mb-3">
                <h4>Resumen dinámico (real vs teórica)</h4>
                <p class="text-muted small mb-2">Elige una o varias dimensiones para agrupar; el resumen se recalcula solo.</p>
                <select id="comparativoResumenPorSelect" class="form-control select2" multiple style="max-width: 640px;">
                    <option value="finca">Finca</option>
                    <option value="flor">Flor</option>
                    <option value="variedad">Variedad</option>
                    <option value="temporada">Temporada</option>
                    <option value="tipo_siembra">Tipo siembra</option>
                    <option value="programa">Programa</option>
                    <option value="color">Color</option>
                </select>
                <div id="comparativoResumenEmptyState" class="reportes-empty-state">
                    <span class="material-icons">table_chart</span>
                    <p class="mb-0">Selecciona una o más dimensiones arriba para ver el resumen.</p>
                </div>
                <div class="table-responsive mt-3" id="comparativoResumenWrap" style="display:none;">
                    <table id="comparativoResumenTable" class="table table-striped w-100">
                        <thead><tr></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="reportes-card mb-3">
                <div class="reportes-card-toggle" onclick="ReporteComparativo.toggleCard(this)">
                    <h4>Resumen por semana</h4>
                    <span class="material-icons toggle-icon">expand_more</span>
                </div>
                <div class="reportes-card-collapsible">
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

            <div class="reportes-card">
                <div class="reportes-card-toggle" onclick="ReporteComparativo.toggleCard(this)">
                    <h4>Detalle</h4>
                    <span class="material-icons toggle-icon">expand_more</span>
                </div>
                <div class="reportes-card-collapsible">
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

            <div class="reportes-card">
                <h4>Teórica vs Real por semana de fecha pico (yyww ISO)</h4>
                <div class="reportes-chart-wrap" style="height: 340px;">
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

    function init() {
        $('.select2').select2({ width: '100%', placeholder: 'Todas', allowClear: true });
        $('#comparativoResumenPorSelect').on('change', cargarDatos);

        $('#cFiltroFinca, #cFiltroBloque, #cFiltroVariedad, #cFiltroTemporada, #cFiltroFlor, #cFiltroTipoSiembra, #cFiltroPrograma, #cFiltroColor')
            .on('change', onFiltroChange);
        $('#cFiltroFechaDesde, #cFiltroFechaHasta').on('change', onFiltroChange);

        $('#comparativoFiltrosToggle').on('click', toggleSidebar);

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
        cargarDatos();
    }

    function construirFiltros() {
        return {
            finca: $('#cFiltroFinca').val() || [],
            bloque: $('#cFiltroBloque').val() || [],
            variedad: $('#cFiltroVariedad').val() || [],
            temporada: $('#cFiltroTemporada').val() || [],
            flor: $('#cFiltroFlor').val() || [],
            tipo_siembra: $('#cFiltroTipoSiembra').val() || [],
            programa: $('#cFiltroPrograma').val() || [],
            color: $('#cFiltroColor').val() || [],
            desde: document.getElementById('cFiltroFechaDesde').value,
            hasta: document.getElementById('cFiltroFechaHasta').value,
            resumen_por: $('#comparativoResumenPorSelect').val() || []
        };
    }

    function cargarDatos() {
        var filtros = construirFiltros();
        $.ajax({
            url: 'ajax/reporte_comparativo.php',
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (data) {
            pintarKpis(data.kpis || []);
            pintarGraficos(data.chart1 || null, data.chart2 || null, data.chart3 || null);
            pintarTabla('#comparativoSemanalTable', '#comparativoSemanalEmptyState', '#comparativoSemanalWrap', data.resumenSemanal || null);
            pintarTabla('#comparativoTable', '#comparativoTableEmptyState', '#comparativoTableWrap', data.table || null);
            pintarTabla('#comparativoResumenTable', '#comparativoResumenEmptyState', '#comparativoResumenWrap', data.resumen || null);
        }).fail(function () {
            console.error('No se pudo cargar el reporte comparativo');
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
        var grid = document.getElementById('comparativoKpiGrid');
        if (!kpis.length) { return; }
        grid.innerHTML = kpis.map(function (kpi) {
            return '<div class="reportes-kpi">' +
                '<span class="reportes-kpi-label">' + kpi.label + '</span>' +
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
        $(headerEl).next('.reportes-card-collapsible').slideToggle(150);
    }

    function toggleSidebar() {
        var sidebarCol = document.getElementById('comparativoSidebarCol');
        var mainCol = document.getElementById('comparativoMainCol');
        var btnMostrar = document.getElementById('btnMostrarFiltrosComparativo');
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

        setTimeout(function () {
            if (chart1) { chart1.resize(); }
            if (chart2) { chart2.resize(); }
            if (chart3) { chart3.resize(); }
        }, 200);
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleSidebar: toggleSidebar,
        toggleCard: toggleCard,
        limpiarFiltros: function () {
            actualizandoFiltros = true;
            $('#cFiltroFinca, #cFiltroBloque, #cFiltroVariedad, #cFiltroTemporada, #cFiltroFlor, #cFiltroTipoSiembra, #cFiltroPrograma, #cFiltroColor').val(null).trigger('change');
            actualizandoFiltros = false;
            document.getElementById('cFiltroFechaDesde').value = '<?php echo htmlspecialchars($rangoFechasDefault['desde'] ?? ''); ?>';
            document.getElementById('cFiltroFechaHasta').value = '<?php echo htmlspecialchars($rangoFechasDefault['hasta'] ?? ''); ?>';
            actualizarOpcionesFiltros();
            cargarDatos();
        }
    };
})();

document.addEventListener('DOMContentLoaded', ReporteComparativo.init);
</script>

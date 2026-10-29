<link rel="stylesheet" href="scripts/crud_tables.css?v=3">
<link rel="stylesheet" href="common/report-styles.css">

<!-- Encabezado unificado de reporte -->
<div class="report-page-header">
    <div class="report-header-left">
        <div class="report-header-icon">
            <span class="material-icons">local_florist</span>
        </div>
        <div>
            <h1 class="report-page-title">Reporte de Floreros</h1>
            <div class="report-page-subtitle">
                <span>Historial de evaluaciones de florero, apertura de tallos y descarte</span>
            </div>
        </div>
    </div>
    <div class="report-header-actions">
        <button type="button" class="btn btn-toggle-filters" id="btnToggleFiltrosRFv" onclick="toggleReportFvSidebar()" title="Mostrar u ocultar panel de filtros">
            <span class="material-icons">filter_alt</span>
            <span id="btnToggleFiltrosRFvText">Filtros</span>
        </button>
        <a href="index.php?report=florero_perfil" class="btn btn-outline-brand-green d-flex align-items-center">
            <span class="material-icons me-1" style="font-size:18px;">insights</span> Ver Perfil de Variedades
        </a>
    </div>
</div>

<div class="container-fluid px-0">
    <div class="row g-3">
        <!-- Panel de Filtros Lateral -->
        <div class="col-12 col-lg-3 col-xl-2 report-filtro-sidebar" id="rFvSidebarCol">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center" onclick="toggleReportFvSidebar()">
                    <span class="d-flex align-items-center">
                        <span class="material-icons me-1" style="font-size:18px;">tune</span> Filtros
                    </span>
                    <span class="material-icons toggle-icon" style="font-size:18px;">expand_less</span>
                </div>
                <div class="card-body">
                    <div class="filter-group mb-3">
                        <label class="filter-label" for="finicial">Fecha Inicial</label>
                        <input class="form-control form-control-sm" type="date" id="finicial">
                    </div>

                    <div class="filter-group mb-3">
                        <label class="filter-label" for="ffinal">Fecha Final</label>
                        <input class="form-control form-control-sm" type="date" id="ffinal">
                    </div>

                    <div class="filter-group mb-3">
                        <label class="filter-label" for="nflor">Tipo de Flor</label>
                        <select id="nflor" class="form-control form-control-sm"></select>
                    </div>

                    <div class="filter-group mb-3">
                        <label class="filter-label" for="fdesc">Descripción</label>
                        <input class="form-control form-control-sm" type="text" id="fdesc" placeholder="Contiene texto...">
                    </div>

                    <div class="filter-group mb-3">
                        <label class="filter-label" for="ngranel">Guarde Granel</label>
                        <select id="ngranel" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <button id="listar" type="button" onclick="listar()" class="btn btn-brand-green btn-sm w-100 d-flex align-items-center justify-content-center py-2">
                        <span class="material-icons me-1" style="font-size:16px;">search</span> Consultar
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna de Resultados / Tabla -->
        <div class="col-12 col-lg-9 col-xl-10" id="rFvMainCol">
            <div class="report-table-card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display nowrap" id="rFloreros" style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Variedad</th>
                                <th>Origen</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Viaje Simulado</th>
                                <th>Guarde Granel</th>
                                <th>Evaluación Calificación</th>
                                <th>Vida Florero</th>
                                <th>Consistencia Color</th>
                                <th>Pétalos</th>
                                <th>Velocidad Apertura</th>
                                <th>Forma Apertura</th>
                                <th>Tallo</th>
                                <th>Follaje</th>
                                <th>Final Óptimo %</th>
                                <th>No Conforme %</th>
                                <th>Inconformidades Causa % - Días</th>
                                <th>Observaciones</th>
                                <th>Fecha Florero</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th colspan="18"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleReportFvSidebar() {
    var sidebar = document.getElementById('rFvSidebarCol');
    var main = document.getElementById('rFvMainCol');
    var btnText = document.getElementById('btnToggleFiltrosRFvText');
    if (!sidebar || !main) return;

    if (sidebar.style.display === 'none') {
        sidebar.style.display = '';
        main.className = 'col-12 col-lg-9 col-xl-10';
        if (btnText) btnText.textContent = 'Filtros';
    } else {
        sidebar.style.display = 'none';
        main.className = 'col-12';
        if (btnText) btnText.textContent = 'Mostrar Filtros';
    }

    if (window.jQuery && $.fn.DataTable.isDataTable('#rFloreros')) {
        $('#rFloreros').DataTable().columns.adjust();
    }
}
</script>

<script src="scripts/scripts_reportsFV.js"></script>
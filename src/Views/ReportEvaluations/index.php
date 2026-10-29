<link rel="stylesheet" href="scripts/crud_tables.css?v=3">
<link rel="stylesheet" href="common/report-styles.css">

<!-- Encabezado unificado de reporte -->
<div class="report-page-header">
    <div class="report-header-left">
        <div class="report-header-icon">
            <span class="material-icons">assessment</span>
        </div>
        <div>
            <h1 class="report-page-title">Reporte de Evaluaciones</h1>
            <div class="report-page-subtitle">
                <span>Variables técnicas, mediciones agronómicas y observaciones de variedades</span>
            </div>
        </div>
    </div>
    <div class="report-header-actions">
        <button type="button" class="btn btn-toggle-filters" id="btnToggleFiltrosREval" onclick="toggleReportEvalSidebar()" title="Mostrar u ocultar panel de filtros">
            <span class="material-icons">filter_alt</span>
            <span id="btnToggleFiltrosREvalText">Filtros</span>
        </button>
    </div>
</div>

<div class="container-fluid px-0">
    <div class="row g-3">
        <!-- Panel de Filtros Lateral -->
        <div class="col-12 col-lg-3 col-xl-2 report-filtro-sidebar" id="rEvalSidebarCol">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center" onclick="toggleReportEvalSidebar()">
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

                    <button id="listar" type="button" onclick="listarEvaluaciones()" class="btn btn-brand-green btn-sm w-100 d-flex align-items-center justify-content-center py-2">
                        <span class="material-icons me-1" style="font-size:16px;">search</span> Consultar
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna de Resultados / Tabla -->
        <div class="col-12 col-lg-9 col-xl-10" id="rEvalMainCol">
            <div class="report-table-card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle display nowrap" id="rEvaluaciones" style="width:100%">
                        <thead>
                            <tr>
                                <th>Variedad</th>
                                <th>Evaluador</th>
                                <th>Comentario</th>
                                <th>Color</th>
                                <th>Ciclo</th>
                                <th>Productividad</th>
                                <th>tCabeza</th>
                                <th>pFuerte</th>
                                <th>fApertura</th>
                                <th>gTallo</th>
                                <th>Longitud</th>
                                <th>rFusarium</th>
                                <th>sEnfermedades</th>
                                <th>Follaje</th>
                                <th>pSpray</th>
                                <th>pMini</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleReportEvalSidebar() {
    var sidebar = document.getElementById('rEvalSidebarCol');
    var main = document.getElementById('rEvalMainCol');
    var btnText = document.getElementById('btnToggleFiltrosREvalText');
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

    if (window.jQuery && $.fn.DataTable.isDataTable('#rEvaluaciones')) {
        $('#rEvaluaciones').DataTable().columns.adjust();
    }
}
</script>

<script src="scripts/scriptsEvaluations.js"></script>
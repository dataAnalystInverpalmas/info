<link rel="stylesheet" href="scripts/crud_tables.css?v=3">

<div class="crud-page-header">
    <div class="crud-header-left">
        <div class="crud-header-icon">
            <span class="material-icons">grading</span>
        </div>
        <div>
            <h1 class="crud-page-title">Registro de Evaluaciones</h1>
            <div class="crud-page-subtitle">
                <span>Control y calificación técnica de variedades en campo</span>
                <span class="crud-page-badge">Evaluaciones</span>
            </div>
        </div>
    </div>
    <div class="crud-header-actions">
        <button type="button" class="btn btn-toggle-filters" id="btnToggleFiltros" onclick="toggleEvaluacionesSidebar()" title="Mostrar u ocultar panel de filtros">
            <span class="material-icons">filter_alt</span>
            <span id="btnToggleFiltrosText">Filtros</span>
        </button>
        <button id="btnNuevo" type="button" class="btn btn-crud-new">
            <span class="material-icons">library_add</span> Nueva Evaluación
        </button>
    </div>
</div>

<div class="container-fluid px-0">
    <div class="row g-3">

        <!-- ===== Panel lateral de filtros ===== -->
        <div class="col-12 col-lg-3 col-xl-2 crud-filtro-sidebar" id="evaluacionesSidebarCol">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center" onclick="toggleEvaluacionesSidebar()">
                    <span class="d-flex align-items-center">
                        <span class="material-icons me-1" style="font-size:18px;">tune</span> Filtros
                    </span>
                    <span class="material-icons toggle-icon" style="font-size:18px;">expand_less</span>
                </div>
                <div class="card-body">
                    <div class="filter-group mb-3">
                        <label class="filter-label" for="fecha_ini">Fecha Inicial</label>
                        <input class="form-control form-control-sm" type="date" id="fecha_ini">
                    </div>

                    <div class="filter-group mb-3">
                        <label class="filter-label" for="fecha_fin">Fecha Final</label>
                        <input class="form-control form-control-sm" type="date" id="fecha_fin">
                    </div>

                    <button type="button" onclick="listar()" class="btn btn-brand-green btn-sm w-100 d-flex align-items-center justify-content-center py-2">
                        <span class="material-icons me-1" style="font-size:16px;">search</span> Consultar
                    </button>
                </div>
            </div>
        </div>
        <!-- ===== Fin panel de filtros ===== -->

        <!-- ===== Contenido principal con tabla ===== -->
        <div class="col-12 col-lg-9 col-xl-10" id="evaluacionesMainCol">
            <div class="crud-table-card shadow-sm">
                <div class="table-responsive">
                    <table id="tableOrders" class="table table-hover align-middle display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Id</th>
                                <th>Fecha</th>
                                <th>Evaluador</th>
                                <th style="width: 140px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ===== Fin contenido principal ===== -->

    </div>
</div>

<!-- ============================================================
     Modal cabecera (Nuevo / Editar)
     ============================================================ -->
<div class="modal fade crud-modal" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-icon">
                    <span class="material-icons">event_note</span>
                </div>
                <div>
                    <h5 class="modal-title" id="exampleModalLabel">Evaluación</h5>
                    <p class="modal-subtitle">Ingrese los datos generales de la evaluación</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formOrders">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="fecha">Fecha</label>
                        <input type="date" class="form-control" id="fecha" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="evaluador">Evaluador</label>
                        <select name="evaluador" id="evaluador" class="form-control" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn btn-brand-green">
                        <span class="material-icons me-1" style="font-size:16px;vertical-align:middle">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal detalles y calificaciones
     ============================================================ -->
<div class="modal fade bs-example-modal-lg crud-modal" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelDetails" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-icon">
                    <span class="material-icons">assignment</span>
                </div>
                <div>
                    <h5 class="modal-title" id="exampleModalLabelDetails">Detalles de la Evaluación</h5>
                    <p class="modal-subtitle">Calificación técnica de variables agronómicas y comentarios</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <form id="formOrdersDetails">
                    <input type="hidden" id="order_id">
                    
                    <!-- Selección de Flor y Variedad -->
                    <div class="card border mb-3 bg-light p-3 rounded-3">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Tipo de Flor *</label>
                                <select id="flor" class="form-control" onchange="fetch_varieties(this.value)" required></select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-muted">Variedad *</label>
                                <select id="variedad" class="form-control">
                                    <option value="">Seleccione una variedad...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Puntajes -->
                    <div id="puntajes" class="card border mb-3 p-3 rounded-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <span class="material-icons me-1 text-success" style="font-size:18px;">star_rate</span>
                                Calificaciones Técnicas (Escala 1 a 3 - Opcional)
                            </h6>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Color</label>
                                <input placeholder="Color" id="color" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Ciclo</label>
                                <input placeholder="Ciclo" id="ciclo" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Productividad</label>
                                <input placeholder="Produc." id="productividad" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Tamaño Cabeza</label>
                                <input placeholder="tCabeza" id="tCabeza" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Porte Fuerte</label>
                                <input placeholder="pFuerte" id="pFuerte" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Forma Apertura</label>
                                <input placeholder="fApertura" id="fApertura" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Grosor Tallo</label>
                                <input placeholder="gTallo" id="gTallo" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Longitud</label>
                                <input placeholder="Longitud" id="longitud" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Resist. Fusarium</label>
                                <input placeholder="rFusarium" id="rFusarium" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Suscep. Enfermedad</label>
                                <input placeholder="sEnfermedad" id="sEnfermedades" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">Follaje</label>
                                <input placeholder="Follaje" id="follaje" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">P. Spray</label>
                                <input placeholder="pSpray" id="pSpray" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-6 col-sm-4 col-md">
                                <label class="small text-muted mb-1 d-block">S. Mini</label>
                                <input placeholder="sMini" id="sMini" class="form-control form-control-sm text-center" maxlength="1" type="number" min="1" max="3">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="small text-muted mb-1 d-block">Comentario de Calificación</label>
                                <textarea placeholder="Observaciones técnicas de la variedad..." id="comentario" class="form-control form-control-sm" rows="1"></textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-brand-green btnAgregarDetalles w-100 py-2 d-flex align-items-center justify-content-center">
                            <span class="material-icons me-1">add_circle</span> Agregar Calificación
                        </button>
                    </div>

                    <!-- Tabla de Detalles Agregados -->
                    <div class="table-responsive mb-4">
                        <table id="tableDetails" class="table table-bordered table-sm align-middle" style="width:100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Id</th>
                                    <th>order_id</th>
                                    <th>Variedad</th>
                                    <th>Item</th>
                                    <th>Valor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>

                <!-- Sección de Comentarios Generales -->
                <div class="card border p-3 rounded-3 bg-light">
                    <h6 class="fw-bold text-dark d-flex align-items-center mb-2">
                        <span class="material-icons me-1 text-primary" style="font-size:18px;">chat</span> Comentarios Adicionales
                    </h6>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-9">
                            <textarea id="texto_comentario" class="form-control" rows="2" placeholder="Escriba su comentario aquí..."></textarea>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-stretch">
                            <button type="button" class="btn btn-brand-green w-100 btnAgregarComentario d-flex align-items-center justify-content-center">
                                <span class="material-icons me-1">add_comment</span> Guardar Comentario
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tableComments" class="table table-bordered table-sm align-middle" style="width:100%">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Id</th>
                                    <th>Variedad</th>
                                    <th>Comentario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEvaluacionesSidebar() {
    var sidebar = document.getElementById('evaluacionesSidebarCol');
    var main = document.getElementById('evaluacionesMainCol');
    var btnText = document.getElementById('btnToggleFiltrosText');
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

    if (window.jQuery && $.fn.DataTable.isDataTable('#tableOrders')) {
        $('#tableOrders').DataTable().columns.adjust().responsive.recalc();
    }
}
</script>

<script src="scripts/load_varieties.js"></script>
<script src="scripts/orders.js?v=<?= file_exists('scripts/orders.js') ? filemtime('scripts/orders.js') : time() ?>"></script>

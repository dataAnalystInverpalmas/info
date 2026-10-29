<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<div class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">settings</span>
            </div>
            <div>
                <h4 class="crud-page-title">CRUD Arrangement</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Tabla arrangement</span>
                    <span class="text-muted d-none d-sm-inline">• Catálogo maestro de tipos, secciones y aplicaciones</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <button type="button" class="btn btn-toggle-filters" id="btnToggleSidebar" title="Mostrar/Ocultar barra de filtros">
                <span class="material-icons">filter_list</span>
                <span id="btnToggleSidebarText">Ocultar filtros</span>
            </button>
            <button id="btnNewArrangement" class="btn btn-crud-new">
                <span class="material-icons">add</span> Nuevo Arrangement
            </button>
        </div>
    </div>

    <div class="row" id="crudRow">
        <!-- Sidebar Filtros -->
        <div class="col-md-4 col-lg-3" id="crudSidebarCol">
            <div class="crud-filtro-sidebar">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosToggle" data-bs-toggle="collapse" data-bs-target="#crudFiltrosBody" data-toggle="collapse" data-target="#crudFiltrosBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosBody">
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_aa_tipo">Tipo</label>
                            <input type="text" id="f_aa_tipo" class="form-control form-control-sm f-col" placeholder="Filtrar por tipo...">
                        </div>
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_aa_aplicar">Aplicar</label>
                            <input type="text" id="f_aa_aplicar" class="form-control form-control-sm f-col" placeholder="Filtrar por aplicación...">
                        </div>
                        <div class="d-flex mt-3" style="gap: 8px;">
                            <button id="btnFilterArrangement" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                            </button>
                            <button id="btnClearArrangement" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8 col-lg-9" id="crudMainCol">
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="arrangementTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Tipo</th>
                                <th>Aplicar</th>
                                <th style="width: 80px;">Sección</th>
                                <th style="width: 80px;">Orden</th>
                                <th style="width: 120px;" class="text-center">Calc. Con Ciclo</th>
                                <th class="text-center" style="width: 145px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear / Editar -->
<div class="modal fade crud-modal" id="arrangementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3" id="modalHeaderIconContainer">
                    <span class="material-icons text-brand-green" id="modalArrangementIcon">settings</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="modalArrangementTitle">Registro Arrangement</h5>
                    <p class="modal-subtitle text-muted" id="modalArrangementSubtitle">Definición de tipo y orden de aplicación</p>
                </div>
                <button type="button" class="btn-close close ms-auto ml-auto" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="arrangementForm">
                    <input type="hidden" name="id" id="aa_id">
                    <input type="hidden" name="old_tipo" id="aa_old_tipo">
                    <input type="hidden" name="old_aplicar" id="aa_old_aplicar">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aa_tipo">Tipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tipo" id="aa_tipo" placeholder="Ej. INSECTICIDA, FUNGICIDA..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aa_aplicar">Aplicar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="aplicar" id="aa_aplicar" placeholder="Ej. DRENCH, FOLIAR..." required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="aa_seccion">Sección <span class="text-muted font-weight-normal small">(opcional)</span></label>
                            <input type="number" class="form-control" name="seccion" id="aa_seccion" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="aa_orden">Orden <span class="text-muted font-weight-normal small">(opcional)</span></label>
                            <input type="number" class="form-control" name="orden" id="aa_orden" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="aa_calc_conciclo">Calc. Con Ciclo</label>
                            <select class="form-control" name="calc_conciclo" id="aa_calc_conciclo">
                                <option value="0">No (0)</option>
                                <option value="1">Sí (1)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" data-dismiss="modal">
                    <span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">close</span> Cancelar
                </button>
                <button type="button" id="saveArrangement" class="btn btn-brand-green btn-sm px-4">
                    <span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/arrangement_only_crud.js?v=5"></script>
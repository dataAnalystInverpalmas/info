<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<div class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">tune</span>
            </div>
            <div>
                <h4 class="crud-page-title">CRUD Arrangements</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Tabla arrangements</span>
                    <span class="text-muted d-none d-sm-inline">• Configuración de labores, mezclas y aplicaciones</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <button type="button" class="btn btn-toggle-filters" id="btnToggleSidebar" title="Mostrar/Ocultar barra de filtros">
                <span class="material-icons">filter_list</span>
                <span id="btnToggleSidebarText">Ocultar filtros</span>
            </button>
            <button id="btnNewArrangements" class="btn btn-crud-new">
                <span class="material-icons">add</span> Nuevo Arrangement
            </button>
        </div>
    </div>

    <div class="row" id="crudRow">
        <!-- Sidebar Filtros -->
        <div class="col-md-4 col-lg-3" id="crudSidebarCol">
            <div class="crud-filtro-sidebar">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosToggle" data-toggle="collapse" data-target="#crudFiltrosBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosBody">
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_arr_variedad">Variedad</label>
                            <select id="f_arr_variedad" class="form-control form-control-sm f-col" data-placeholder="Todas las variedades">
                                <option value="">Todas las variedades</option>
                            </select>
                        </div>
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_arr_finca">Finca</label>
                            <select id="f_arr_finca" class="form-control form-control-sm f-col" data-placeholder="Todas las fincas">
                                <option value="">Todas las fincas</option>
                            </select>
                        </div>
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_arr_tipo">Tipo</label>
                            <input type="text" id="f_arr_tipo" class="form-control form-control-sm f-col" placeholder="Filtrar por tipo...">
                        </div>
                        <div class="d-flex mt-3" style="gap: 8px;">
                            <button id="btnFilterArrangements" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                            </button>
                            <button id="btnClearArrangements" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8 col-lg-9" id="crudMainCol">
            <!-- Herramienta de Copiar Variedad -->
            <div class="arr-copy-card">
                <div class="arr-copy-title">
                    <span class="material-icons text-brand-green" style="font-size: 1.25rem;">content_copy</span>
                    <span>Herramienta: Copiar Configuración de Variedad</span>
                </div>
                <div class="row align-items-end">
                    <div class="col-md-5 col-sm-6 mb-2">
                        <label for="copy_variedad_origen" class="small font-weight-bold text-secondary mb-1">Variedad Origen</label>
                        <select id="copy_variedad_origen" class="form-control form-control-sm">
                            <option value="">Seleccionar variedad origen...</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-sm-6 mb-2">
                        <label for="copy_variedad_destino" class="small font-weight-bold text-secondary mb-1">Variedad Destino</label>
                        <select id="copy_variedad_destino" class="form-control form-control-sm">
                            <option value="">Seleccionar variedad destino...</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-2">
                        <button id="btnCopyArrangements" type="button" class="btn btn-brand-green btn-sm btn-block d-flex align-items-center justify-content-center" style="height: 38px;">
                            <span class="material-icons mr-1" style="font-size: 1.05rem;">copy_all</span> Copiar
                        </button>
                    </div>
                </div>
                <div class="arr-copy-desc">
                    <span class="material-icons" style="font-size: 0.95rem;">info</span>
                    <span>Duplica todos los registros de la variedad origen hacia la variedad destino (sin duplicar existentes).</span>
                </div>
            </div>

            <!-- Tabla DataCard -->
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="arrangementsTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Variedad</th>
                                <th>Finca</th>
                                <th>Tipo</th>
                                <th>Aplicar</th>
                                <th>Medida</th>
                                <th style="width: 80px;">Valor</th>
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
<div class="modal fade crud-modal" id="arrangementsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3" id="modalHeaderIconContainer">
                    <span class="material-icons text-brand-green" id="modalArrIcon">tune</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="modalArrTitle">Registro Arrangement</h5>
                    <p class="modal-subtitle text-muted" id="modalArrSubtitle">Configuración de insumo y aplicación</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="arrangementsForm">
                    <input type="hidden" name="id" id="ar_id">
                    <input type="hidden" name="old_variedad" id="ar_old_variedad">
                    <input type="hidden" name="old_finca" id="ar_old_finca">
                    <input type="hidden" name="old_tipo" id="ar_old_tipo">
                    <input type="hidden" name="old_aplicar" id="ar_old_aplicar">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_variedad">Variedad <span class="text-danger">*</span></label>
                            <select class="form-control" name="variedad" id="ar_variedad" required>
                                <option value="">Seleccionar variedad...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_finca">Finca <span class="text-danger">*</span></label>
                            <select class="form-control" name="finca" id="ar_finca" required>
                                <option value="">Seleccionar finca...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_tipo">Tipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tipo" id="ar_tipo" placeholder="Ej. FERTILIZACION, COBRE..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_aplicar">Aplicar <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="aplicar" id="ar_aplicar" placeholder="Ej. DRENCH, FOLIAR..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_medidat">Medida <span class="text-muted font-weight-normal small">(opcional)</span></label>
                            <input type="text" class="form-control" name="medidat" id="ar_medidat" placeholder="Ej. CC, GR, LT...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="ar_valor">Valor / Dosis <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="valor" id="ar_valor" placeholder="0.00" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                </button>
                <button type="button" id="saveArrangements" class="btn btn-brand-green btn-sm px-4">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/arrangements_only_crud.js?v=4"></script>

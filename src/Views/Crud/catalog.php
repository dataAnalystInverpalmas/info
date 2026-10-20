<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<div class="container-fluid" style="padding-top:6px;">
    <div id="catalogCrudRoot"
         data-table="<?php echo htmlspecialchars($table); ?>"
         data-endpoint="<?php echo htmlspecialchars($endpoint); ?>"
         data-title="<?php echo htmlspecialchars($title); ?>"
         <?php if (!empty($selects)): ?>data-selects='<?php echo $selects; ?>'<?php endif; ?>
         <?php if (!empty($display)): ?>data-display='<?php echo $display; ?>'<?php endif; ?>
         <?php if (!empty($filters)): ?>data-filters='<?php echo $filters; ?>'<?php endif; ?>
         <?php if (!empty($bulkField)): ?>data-bulk-field="<?php echo htmlspecialchars($bulkField); ?>"<?php endif; ?>>

        <!-- Page Header Banner -->
        <div class="crud-page-header">
            <div class="crud-header-left">
                <div class="crud-header-icon">
                    <span class="material-icons" id="catalogHeaderIcon">table_chart</span>
                </div>
                <div>
                    <h4 class="crud-page-title" id="catalogTitle"><?php echo htmlspecialchars($title); ?></h4>
                    <div class="crud-page-subtitle">
                        <span class="crud-page-badge" id="catalogSubtitle">Tabla <?php echo htmlspecialchars($table); ?></span>
                        <span class="text-muted d-none d-sm-inline">• Catálogo del Sistema</span>
                    </div>
                </div>
            </div>
            <div class="crud-header-actions">
                <button type="button" class="btn btn-toggle-filters" id="btnToggleSidebar" title="Mostrar/Ocultar barra de filtros">
                    <span class="material-icons">filter_list</span>
                    <span id="btnToggleSidebarText">Ocultar filtros</span>
                </button>

                <?php if (!empty($bulkField)): ?>
                <div class="bulk-toolbar">
                    <span class="badge badge-pill badge-info px-2 py-1" id="bulkCount">0 seleccionados</span>
                    <div class="input-group input-group-sm" style="width:auto; flex-wrap:nowrap;">
                        <div class="input-group-prepend"><span class="input-group-text"><?php echo htmlspecialchars(ucfirst($bulkField)); ?></span></div>
                        <input type="number" id="bulkLongitud" class="form-control form-control-sm" step="0.01" min="0" placeholder="0.00" style="width:90px;" title="Valor con 2 decimales">
                    </div>
                    <button id="btnApplyLongitud" class="btn btn-brand-green btn-sm" title="Aplicar valor a registros seleccionados">
                        <span class="material-icons" style="font-size:1rem;vertical-align:middle;">check</span> Aplicar
                    </button>
                </div>
                <?php endif; ?>

                <button id="btnCrudNew" class="btn btn-crud-new">
                    <span class="material-icons">add</span> Nuevo Registro
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
                            <div id="crudFilters"></div>
                            <div class="d-flex mt-3" style="gap: 8px;">
                                <button id="btnCrudFilter" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                    <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                                </button>
                                <button id="btnCrudClear" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
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
                        <table id="catalogTable" class="display table table-striped table-hover" style="width:100%">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear / Editar -->
<div class="modal fade crud-modal" id="catalogModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3" id="modalHeaderIconContainer">
                    <span class="material-icons text-brand-green" id="modalIcon">add_circle</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="modalActionTitle">Registro</h5>
                    <p class="modal-subtitle text-muted" id="modalActionSubtitle">Gestión de catálogo</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="crudModalFields"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                </button>
                <button type="button" id="btnCrudSave" class="btn btn-brand-green btn-sm px-4">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/catalog_crud.js?v=8"></script>

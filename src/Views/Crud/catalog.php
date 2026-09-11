<style>
    /* ── CRUD Sidebar Filtros ── */
    .crud-filtro-sidebar .card { border: 1px solid #dbe7e4; border-radius: 14px; }
    .crud-filtro-sidebar .card-header {
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
    .crud-filtro-sidebar .card-header .toggle-icon { font-size: 1.1rem; transition: transform 0.2s; }
    .crud-filtro-sidebar .card-header.collapsed .toggle-icon { transform: rotate(180deg); }
    .crud-filtro-sidebar .card-body { padding: 0.75rem; }
    .crud-filtro-sidebar label { font-size: 0.78rem; font-weight: 600; color: #1f2937; letter-spacing: 0.02em; }
    .crud-filtro-sidebar .f-col { margin-bottom: 0.5rem; }
    .crud-filtro-sidebar .btn + .btn { margin-left: 0.4rem; }

    /* ── CRUD Main Content ── */
    .crud-main-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .crud-main-bar h4 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #17312d; }
    .crud-main-bar small { color: #6a7f79; font-size: 0.8rem; }

    #catalogTable td { vertical-align: middle; }
    .catsel-ico { display:inline-block; width:1.5em; text-align:center; color:#28a745; }
    .catsel-ico.muted { color:#adb5bd; }
    .catsel-ph { opacity:.7; }

    /* Selección múltiple en filas */
    #catalogTable tr.crud-row-selected td { background-color: #d5edea !important; }
    #catalogTable .gh-row-select { width: 16px; height: 16px; vertical-align: middle; }
    #selectAllCells { width: 16px; height: 16px; vertical-align: middle; }

    @media (max-width: 767.98px) {
        .crud-filtro-sidebar { position: static; margin-bottom: 1rem; }
    }
</style>

<div class="container-fluid" style="padding-top:2px;">
    <div id="catalogCrudRoot"
         data-table="<?php echo htmlspecialchars($table); ?>"
         data-endpoint="<?php echo htmlspecialchars($endpoint); ?>"
         data-title="<?php echo htmlspecialchars($title); ?>"
         <?php if (!empty($selects)): ?>data-selects='<?php echo $selects; ?>'<?php endif; ?>
         <?php if (!empty($display)): ?>data-display='<?php echo $display; ?>'<?php endif; ?>
         <?php if (!empty($filters)): ?>data-filters='<?php echo $filters; ?>'<?php endif; ?>
         <?php if (!empty($bulkField)): ?>data-bulk-field="<?php echo htmlspecialchars($bulkField); ?>"<?php endif; ?>>

        <div class="row" id="crudRow">
            <!-- Sidebar Filtros -->
            <div class="col-md-4 col-lg-3" id="crudSidebarCol">
                <div class="crud-filtro-sidebar">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header" id="crudFiltrosToggle" data-toggle="collapse" data-target="#crudFiltrosBody" aria-expanded="true">
                            <span>
                                <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                                Filtros
                            </span>
                            <span class="material-icons toggle-icon">expand_less</span>
                        </div>
                        <div class="card-body collapse show" id="crudFiltrosBody">
                            <div id="crudFilters"></div>
                            <div class="d-flex mt-2">
                                <button id="btnCrudFilter" class="btn btn-brand-green btn-sm">Filtrar</button>
                                <button id="btnCrudClear" class="btn btn-outline-secondary btn-sm">Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-8 col-lg-9" id="crudMainCol">
                <button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="btnMostrarFiltrosCrud" style="display:none;">
                    <span class="material-icons" style="font-size:1rem;vertical-align:middle;">chevron_right</span> Mostrar filtros
                </button>
                <div class="crud-main-bar">
                    <div>
                        <h4 id="catalogTitle"><?php echo htmlspecialchars($title); ?></h4>
                        <small id="catalogSubtitle">Tabla <?php echo htmlspecialchars($table); ?></small>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:0.5rem;">
                        <?php if (!empty($bulkField)): ?>
                        <div class="input-group input-group-sm" style="width:auto;flex-wrap:nowrap;">
                            <div class="input-group-prepend"><span class="input-group-text"><?php echo htmlspecialchars(ucfirst($bulkField)); ?></span></div>
                            <input type="number" id="bulkLongitud" class="form-control" step="0.01" min="0" placeholder="0.00" style="width:110px;" title="Valor con 2 decimales">
                        </div>
                        <button id="btnApplyLongitud" class="btn btn-outline-brand-green btn-sm" title="Aplicar longitud a los registros seleccionados">
                            <span class="material-icons" style="font-size:1rem;vertical-align:middle;">check</span> Aplicar a selección
                        </button>
                        <span id="bulkCount" class="badge badge-pill badge-info" style="font-size:0.75rem;">0 seleccionados</span>
                        <?php endif; ?>
                        <button id="btnCrudNew" class="btn btn-brand-green btn-sm">
                            <span class="material-icons" style="font-size:1rem;vertical-align:middle;">add</span> Nuevo
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="catalogTable" class="display table table-striped" style="width:100%">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="catalogModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="crudModalFields"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnCrudSave" class="btn btn-brand-green">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('btnMostrarFiltrosCrud');
    var sidebarCol = document.getElementById('crudSidebarCol');
    var mainCol = document.getElementById('crudMainCol');
    var toggle = document.getElementById('crudFiltrosToggle');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebarCol.classList.remove('d-none');
            mainCol.classList.remove('col-12');
            mainCol.classList.add('col-md-8', 'col-lg-9');
            toggleBtn.style.display = 'none';
        });
    }
    if (toggle) {
        toggle.addEventListener('click', function() {
            var body = document.getElementById('crudFiltrosBody');
            if (body.classList.contains('show')) {
                toggle.classList.add('collapsed');
            } else {
                toggle.classList.remove('collapsed');
            }
        });
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/catalog_crud.js?v=7"></script>

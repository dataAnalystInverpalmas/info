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

    /* ── CRUD Main ── */
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

    .arr-copy-panel { border:1px solid #e9ecef; border-radius:8px; padding:12px; margin-bottom:14px; background:#fafafa; }
    .arr-copy-panel .form-control { max-width:260px; }

    #arrangementsTable td { vertical-align:middle; }
    #arrangementsTable .btn { margin-right:6px; white-space:nowrap; }
    .arr-modal .form-label { font-weight:500; font-size:0.92rem; }
    .arr-modal .select2-container { width:100% !important; }
    .catsel-ico { display:inline-block; width:1.5em; text-align:center; color:#28a745; }
    .catsel-ico.muted { color:#adb5bd; }
    .catsel-ph { opacity:.7; }

    @media (max-width: 767.98px) {
        .crud-filtro-sidebar { position: static; margin-bottom: 1rem; }
    }
</style>

<div class="container-fluid" style="padding-top:2px;">
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
                        <div class="mb-2">
                            <label for="f_arr_variedad">Variedad</label>
                            <select id="f_arr_variedad" class="form-control form-control-sm f-col" data-placeholder="Filtrar variedad">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="f_arr_finca">Finca</label>
                            <select id="f_arr_finca" class="form-control form-control-sm f-col" data-placeholder="Filtrar finca">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="f_arr_tipo">Tipo</label>
                            <input type="text" id="f_arr_tipo" class="form-control form-control-sm f-col" placeholder="Filtrar tipo">
                        </div>
                        <div class="d-flex mt-2">
                            <button id="btnFilterArrangements" class="btn btn-brand-green btn-sm">Filtrar</button>
                            <button id="btnClearArrangements" class="btn btn-outline-secondary btn-sm">Limpiar</button>
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
                    <h4>CRUD Arrangements</h4>
                    <small>Tabla arrangements</small>
                </div>
                <button id="btnNewArrangements" class="btn btn-brand-green btn-sm">
                    <span class="material-icons" style="font-size:1rem;vertical-align:middle;">add</span> Nuevo
                </button>
            </div>

            <div class="arr-copy-panel">
                <div class="d-flex flex-wrap align-items-end" style="gap:8px;">
                    <div class="form-group mb-0">
                        <label for="copy_variedad_origen" class="form-label mb-1">Variedad origen</label>
                        <select id="copy_variedad_origen" class="form-control form-control-sm"><option value="">Seleccionar variedad</option></select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="copy_variedad_destino" class="form-label mb-1">Variedad destino</label>
                        <select id="copy_variedad_destino" class="form-control form-control-sm"><option value="">Seleccionar variedad</option></select>
                    </div>
                    <button id="btnCopyArrangements" type="button" class="btn btn-outline-brand-green btn-sm">Copiar</button>
                </div>
                <small class="text-muted d-block mt-2">Copia todos los registros de la variedad origen hacia la variedad destino (sin duplicar existentes).</small>
            </div>

            <div class="table-responsive">
                <table id="arrangementsTable" class="display table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Variedad</th>
                            <th>Finca</th>
                            <th>Tipo</th>
                            <th>Aplicar</th>
                            <th>Medida</th>
                            <th>Valor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade arr-modal" id="arrangementsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registro Arrangements</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="arrangementsForm">
                    <input type="hidden" name="id" id="ar_id">
                    <input type="hidden" name="old_variedad" id="ar_old_variedad">
                    <input type="hidden" name="old_finca" id="ar_old_finca">
                    <input type="hidden" name="old_tipo" id="ar_old_tipo">
                    <input type="hidden" name="old_aplicar" id="ar_old_aplicar">
                    <div class="form-group">
                        <label class="form-label">Variedad</label>
                        <select class="form-control" name="variedad" id="ar_variedad" required><option value="">Seleccionar variedad</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Finca</label>
                        <select class="form-control" name="finca" id="ar_finca" required><option value="">Seleccionar finca</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="tipo" id="ar_tipo" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aplicar</label>
                        <input type="text" class="form-control" name="aplicar" id="ar_aplicar" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Medida</label>
                        <input type="text" class="form-control" name="medidat" id="ar_medidat">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Valor</label>
                        <input type="number" step="0.01" class="form-control" name="valor" id="ar_valor" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" id="saveArrangements" class="btn btn-brand-green">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('btnMostrarFiltrosCrud');
    var sidebarCol = document.getElementById('crudSidebarCol');
    var mainCol = document.getElementById('crudMainCol');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebarCol.classList.remove('d-none');
            mainCol.classList.remove('col-12');
            mainCol.classList.add('col-md-8', 'col-lg-9');
            toggleBtn.style.display = 'none';
        });
    }
});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/arrangements_only_crud.js?v=3"></script>

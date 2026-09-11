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

    #arrangementTable td { vertical-align:middle; }
    #arrangementTable .btn { margin-right:6px; white-space:nowrap; }
    .arr-modal .form-label { font-weight:500; font-size:0.92rem; }

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
                            <label for="f_aa_tipo">Tipo</label>
                            <input type="text" id="f_aa_tipo" class="form-control form-control-sm f-col" placeholder="Filtrar tipo">
                        </div>
                        <div class="mb-2">
                            <label for="f_aa_aplicar">Aplicar</label>
                            <input type="text" id="f_aa_aplicar" class="form-control form-control-sm f-col" placeholder="Filtrar aplicar">
                        </div>
                        <div class="d-flex mt-2">
                            <button id="btnFilterArrangement" class="btn btn-brand-green btn-sm">Filtrar</button>
                            <button id="btnClearArrangement" class="btn btn-outline-secondary btn-sm">Limpiar</button>
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
                    <h4>CRUD Arrangement</h4>
                    <small>Tabla arrangement</small>
                </div>
                <button id="btnNewArrangement" class="btn btn-brand-green btn-sm">
                    <span class="material-icons" style="font-size:1rem;vertical-align:middle;">add</span> Nuevo
                </button>
            </div>
            <div class="table-responsive">
                <table id="arrangementTable" class="display table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Aplicar</th>
                            <th>Seccion</th>
                            <th>Orden</th>
                            <th>Calc.ConCiclo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade arr-modal" id="arrangementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registro Arrangement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="arrangementForm">
                    <input type="hidden" name="id" id="aa_id">
                    <input type="hidden" name="old_tipo" id="aa_old_tipo">
                    <input type="hidden" name="old_aplicar" id="aa_old_aplicar">
                    <div class="form-group">
                        <label class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="tipo" id="aa_tipo" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aplicar</label>
                        <input type="text" class="form-control" name="aplicar" id="aa_aplicar" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Seccion</label>
                        <input type="number" class="form-control" name="seccion" id="aa_seccion">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Orden</label>
                        <input type="number" class="form-control" name="orden" id="aa_orden">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Calc.ConCiclo</label>
                        <input type="number" class="form-control" name="calc_conciclo" id="aa_calc_conciclo">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" id="saveArrangement" class="btn btn-brand-green">Guardar</button>
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

<script src="scripts/arrangement_only_crud.js?v=2"></script>
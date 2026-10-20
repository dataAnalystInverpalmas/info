<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .filtersf .select2-container { width: 100% !important; }
    .filtersf .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; }
    .filtersf .row-compact { display:flex; gap:8px; }
    .filtersf .row-compact .form-section { flex:1; min-width:0; }
    #programfTable .btn { margin-right:6px; }
    #programfTable td { vertical-align: middle; }
    #programfTable td .btn { display: inline-block; white-space: nowrap; margin-right: 6px; }
    #programfTable th:last-child, #programfTable td:last-child { white-space: nowrap; width: 1%; }
    .app-content-f.filters-hidden #filtersColF { display: none; }
    .app-content-f.filters-hidden #tableColF { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content-f" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">holiday_village</span>
            </div>
            <div>
                <h4 class="crud-page-title">Programa de Siembras por Finca</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Planificación detallada de siembras por finca y bloque</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <button id="btnToggleFiltersF" class="btn btn-toggle-filters">
                <span class="material-icons">filter_list</span>
                <span>Filtros</span>
            </button>
            <button id="btnNewF" class="btn btn-crud-new">
                <span class="material-icons">add</span> Nuevo Registro
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filtros -->
        <div id="filtersColF" class="col-md-4 col-lg-3 mb-3">
            <div class="crud-filtro-sidebar filtersf">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosProgFToggle" data-toggle="collapse" data-target="#crudFiltrosProgFBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosProgFBody">
                        <div class="row-compact mb-2">
                            <div class="form-section">
                                <label class="filter-label" for="ff_programa">Programa</label>
                                <select id="ff_programa" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($programas as $p): ?>
                                        <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-section">
                                <label class="filter-label" for="ff_estado">Estado</label>
                                <select id="ff_estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="0">0</option>
                                    <option value="1" selected>1</option>
                                </select>
                            </div>
                        </div>

                        <div class="row-compact mb-2">
                            <div class="form-section">
                                <label class="filter-label" for="ff_ciclo">Ciclo</label>
                                <select id="ff_ciclo" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="form-section">
                                <label class="filter-label" for="ff_bloque">Bloque</label>
                                <select id="ff_bloque" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($bloques as $b): ?>
                                        <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-section">
                                <label class="filter-label" for="ff_adicional">Adic.</label>
                                <select id="ff_adicional" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="ff_producto">Producto</label>
                            <select id="ff_producto" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($productos as $pr): ?>
                                    <option value="<?php echo htmlspecialchars($pr); ?>"><?php echo htmlspecialchars($pr); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="ff_variedad">Variedad</label>
                            <select id="ff_variedad" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($variedades as $v): ?>
                                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="ff_temporada">Temporadas</label>
                            <select id="ff_temporada" class="form-control" multiple="multiple" data-placeholder="ej: FE2707, FE2708"></select>
                        </div>

                        <div class="row-compact mb-2">
                            <div class="form-section">
                                <label class="filter-label" for="ff_fecha_inicio">Fecha inicio</label>
                                <input type="date" id="ff_fecha_inicio" class="form-control">
                            </div>
                            <div class="form-section">
                                <label class="filter-label" for="ff_fecha_fin">Fecha fin</label>
                                <input type="date" id="ff_fecha_fin" class="form-control">
                            </div>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="ff_semana_siembra">Semana Siembra (YYWW)</label>
                            <input type="text" id="ff_semana_siembra" class="form-control" placeholder="ej: 2601" maxlength="4">
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="ff_color">Color</label>
                            <select id="ff_color" class="form-control">
                                <option value="">Todos</option>
                            </select>
                        </div>

                        <div class="filter-group mb-3">
                            <label class="filter-label" for="ff_finca">Finca</label>
                            <select id="ff_finca" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($fincas as $f): ?>
                                    <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex" style="gap: 8px;">
                            <button id="btnFilterF" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                            </button>
                            <button id="btnClearFilterF" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Column -->
        <div id="tableColF" class="col-md-8 col-lg-9 mb-3">
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="programfTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Programa</th>
                                <th>Producto</th>
                                <th>Variedad</th>
                                <th>Temporada</th>
                                <th>Finca</th>
                                <th>Bloque</th>
                                <th>Ncamas</th>
                                <th>Ciclo</th>
                                <th>Fecha Siembra</th>
                                <th>Ferradica</th>
                                <th>Adicional</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" style="text-align:right">Totales:</th>
                                <th id="totalNcamas" class="text-brand-green font-weight-bold"></th>
                                <th colspan="6"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade crud-modal" id="programfModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3">
                    <span class="material-icons text-brand-green">holiday_village</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold">Registro Programa por Finca</h5>
                    <p class="modal-subtitle text-muted">Asignación detallada de siembra por finca y bloque</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="programfForm">
                    <input type="hidden" name="id" id="pf_id">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Programa</label>
                                <input type="number" class="form-control" name="programa" id="pf_programa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Producto</label>
                                <input type="text" class="form-control" name="producto" id="pf_producto">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Variedad</label>
                                <input type="text" class="form-control" name="variedad" id="pf_variedad" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Temporada</label>
                                <input type="text" class="form-control" name="temporada_obj" id="pf_temporada_obj">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Finca</label>
                                <input type="text" class="form-control" name="finca" id="pf_finca">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Bloque</label>
                                <input type="number" class="form-control" name="bloque" id="pf_bloque">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ncamas</label>
                                <input type="number" class="form-control" name="ncamas" id="pf_ncamas">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ciclo</label>
                                <input type="number" class="form-control" name="ciclo" id="pf_ciclo">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Fecha Siembra</label>
                                <input type="date" class="form-control" name="fecha_siembra" id="pf_fecha_siembra" oninput="if(window.updateProgramfIsoWeek){window.updateProgramfIsoWeek(this.value);}">
                                <small id="pf_fecha_siembra_iso" class="form-text text-muted">Semana ISO: -</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Estado</label>
                                <select class="form-control" name="estado" id="pf_estado">
                                    <option value="1">1</option>
                                    <option value="0">0</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ferradica</label>
                                <input type="text" class="form-control" name="ferradica" id="pf_ferradica">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Adicional</label>
                                <input type="text" class="form-control" name="adicional" id="pf_adicional">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                </button>
                <button type="button" id="saveProgramf" class="btn btn-brand-green btn-sm px-4">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/programsf.js?v=5"></script>

<script>
$(function(){
    var $btn = $('#btnToggleFiltersF');
    var $panel = $('.filtersf');
    var $container = $('.app-content-f');
    if (!$btn.length) return;
    $btn.on('click', function(){
        var hidden = $container.hasClass('filters-hidden');
        if (hidden) {
            $container.removeClass('filters-hidden');
            $panel.show(180);
            $btn.html('<span class="material-icons">filter_list</span> <span>Ocultar filtros</span>');
        } else {
            $container.addClass('filters-hidden');
            $panel.hide(180);
            $btn.html('<span class="material-icons">filter_list</span> <span>Mostrar filtros</span>');
        }
    });
    $btn.html($container.hasClass('filters-hidden') ? '<span class="material-icons">filter_list</span> <span>Mostrar filtros</span>' : '<span class="material-icons">filter_list</span> <span>Ocultar filtros</span>');
});
</script>


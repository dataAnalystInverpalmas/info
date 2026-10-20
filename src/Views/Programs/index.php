<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 ajustes para filtros */
    .filters .select2-container { width: 100% !important; }
    .filters .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; }
    .filters .row-compact { display:flex; gap:8px; }
    .filters .row-compact .form-section { flex:1; min-width:0; }
    #programsTable .btn { margin-right:6px; }
    #programsTable td { vertical-align: middle; }
    #programsTable td .btn { display: inline-block; white-space: nowrap; margin-right: 6px; }
    #programsTable th:last-child, #programsTable td:last-child { white-space: nowrap; width: 1%; }
    .app-content.filters-hidden #filtersCol { display: none; }
    .app-content.filters-hidden #tableCol { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">calendar_today</span>
            </div>
            <div>
                <h4 class="crud-page-title">Programa de Siembras</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Planificación teórica global de siembras y camas</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <button id="btnToggleFilters" class="btn btn-toggle-filters">
                <span class="material-icons">filter_list</span>
                <span>Filtros</span>
            </button>
            <button id="btnNew" class="btn btn-crud-new">
                <span class="material-icons">add</span> Nuevo Registro
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filtros -->
        <div id="filtersCol" class="col-md-4 col-lg-3 mb-3">
            <div class="crud-filtro-sidebar filters">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosProgToggle" data-toggle="collapse" data-target="#crudFiltrosProgBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosProgBody">
                        <div class="form-section mb-2">
                            <div class="row-compact">
                                <div class="form-section">
                                    <label class="filter-label" for="f_programa">Programa</label>
                                    <select id="f_programa" class="form-control">
                                        <option value="">Todos</option>
                                        <?php foreach ($programas as $p): ?>
                                            <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-section">
                                    <label class="filter-label" for="f_estado">Estado</label>
                                    <select id="f_estado" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="0">0</option>
                                        <option value="1" selected>1</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row-compact mb-2">
                            <div class="form-section">
                                <label class="filter-label" for="f_ciclo">Ciclo</label>
                                <select id="f_ciclo" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                            <div class="form-section">
                                <label class="filter-label" for="f_adicional">Adicional</label>
                                <select id="f_adicional" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_variedad">Variedad</label>
                            <select id="f_variedad" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($variedades as $v): ?>
                                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_temporada">Temporada</label>
                            <select id="f_temporada" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($temporadas as $t): ?>
                                    <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_producto">Flor (Producto)</label>
                            <select id="f_producto" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="f_color">Color</label>
                            <select id="f_color" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($colores as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group mb-3">
                            <label class="filter-label" for="f_semana_siembra">Semana Siembra (YYWW)</label>
                            <input type="text" id="f_semana_siembra" class="form-control" placeholder="ej: 2601" maxlength="4">
                        </div>

                        <div class="d-flex" style="gap: 8px;">
                            <button id="btnFilter" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                            </button>
                            <button id="btnClearFilter" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Column -->
        <div id="tableCol" class="col-md-8 col-lg-9 mb-3">
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="programsTable" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Programa</th>
                                <th>Variedad</th>
                                <th>Ciclo</th>
                                <th>Fecha Siembra</th>
                                <th>Temporada</th>
                                <th>Pico</th>
                                <th>Ncamas</th>
                                <th>Casa</th>
                                <th>Raiz</th>
                                <th>PM</th>
                                <th>Ferradica</th>
                                <th>Estado</th>
                                <th>Adicional</th>
                                <th>Cant.Pedida</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" style="text-align:right">Total Ncamas:</th>
                                <th id="totalNcamas" class="text-brand-green font-weight-bold"></th>
                                <th colspan="8"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade crud-modal" id="programModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3">
                    <span class="material-icons text-brand-green">calendar_today</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold">Registro de Programa</h5>
                    <p class="modal-subtitle text-muted">Edición y configuración de siembra teórica</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="programForm">
                    <input type="hidden" name="id" id="p_id">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Programa</label>
                                <input type="number" class="form-control" name="programa" id="p_programa">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Variedad</label>
                                <input type="text" class="form-control" name="variedad" id="p_variedad" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Temporada</label>
                                <input type="text" class="form-control" name="temporada_obj" id="p_temporada_obj">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ciclo</label>
                                <input type="number" class="form-control" name="ciclo" id="p_ciclo">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Fecha Siembra</label>
                                <input type="date" class="form-control" name="fecha_siembra" id="p_fecha_siembra" oninput="if(window.updateProgramIsoWeek){window.updateProgramIsoWeek(this.value);}">
                                <small id="p_fecha_siembra_iso" class="form-text text-muted">Semana ISO: -</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ncamas</label>
                                <input type="number" class="form-control" name="ncamas" id="p_ncamas">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Casa ID</label>
                                <input type="number" class="form-control" name="casa_id" id="p_casa_id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Pico</label>
                                <input type="text" class="form-control" name="pico" id="p_pico">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Raiz</label>
                                <select class="form-control" name="raiz" id="p_raiz">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">PM</label>
                                <select class="form-control" name="pm" id="p_pm">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Ferradica</label>
                                <input type="text" class="form-control" name="ferradica" id="p_ferradica">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Estado</label>
                                <input type="text" class="form-control" name="estado" id="p_estado">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Adicional</label>
                                <select class="form-control" name="adicional" id="p_adicional">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark">Cantidad Pedida</label>
                                <input type="number" class="form-control" name="cantidad_pedida" id="p_cantidad_pedida">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                </button>
                <button type="button" id="saveProgram" class="btn btn-brand-green btn-sm px-4">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/programs.js?v=2"></script>

<script>
// Sugerir cantidad_pedida = ncamas * 960 cuando cambie ncamas
$(function(){
    $('#p_ncamas').on('input change', function(){
        var ncamas = parseFloat($(this).val()) || 0;
        $('#p_cantidad_pedida').val(Math.round(ncamas * 960));
    });
});
</script>

<script>
// Toggle para mostrar/ocultar filtros horizontalmente y expandir la tabla
$(function(){
    var $btn = $('#btnToggleFilters');
    var $panel = $('.filters');
    var $container = $('.app-content');
    if(!$btn.length) return;
    $btn.on('click', function(){
        var hidden = $container.hasClass('filters-hidden');
        if(hidden){
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


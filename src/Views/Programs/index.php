<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Estilo sencillo y claro para el formulario (fragmento integrado en layout) */
    .card-panel { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; }
    /* Select2 ajustes para filtros */
    .filters .select2-container { width: 100% !important; }
    .filters .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .25rem; }
    .filters h5 { margin-top:0; }
    .filters .row-compact { display:flex; gap:8px; }
    .filters .row-compact .form-section { flex:1; min-width:0; }
    .modal-title { font-weight:600; }
    .form-section { margin-bottom:12px; }
    .form-label { font-weight:500; font-size:0.92rem; }
    #programsTable .btn { margin-right:6px; }
    .app-content { padding-top: 20px; }
    #programsTable td { vertical-align: middle; }
    #programsTable td .btn { display: inline-block; white-space: nowrap; margin-right: 6px; }
    #programsTable th:last-child, #programsTable td:last-child { white-space: nowrap; width: 1%; }
    .app-content.filters-hidden #filtersCol { display: none; }
    .app-content.filters-hidden #tableCol { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content">
    <div class="row">
        <div id="filtersCol" class="col-sm-3">
            <div class="card-panel filters">
                <h5>Filtros</h5>

                <div class="form-group form-section">
                    <div class="row-compact">
                        <div class="form-group form-section">
                            <label class="form-label" for="f_programa">Programa</label>
                            <select id="f_programa" class="form-control">
                                <option value="">Todos</option>
                                <?php foreach ($programas as $p): ?>
                                    <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group form-section">
                            <label class="form-label" for="f_estado">Estado</label>
                            <select id="f_estado" class="form-control">
                                <option value="">Todos</option>
                                <option value="0">0</option>
                                <option value="1" selected>1</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row-compact">
                    <div class="form-group form-section">
                        <label class="form-label" for="f_ciclo">Ciclo</label>
                        <select id="f_ciclo" class="form-control">
                            <option value="">Todos</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="form-group form-section">
                        <label class="form-label" for="f_adicional">Adicional</label>
                        <select id="f_adicional" class="form-control">
                            <option value="">Todos</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="f_variedad">Variedad</label>
                    <select id="f_variedad" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($variedades as $v): ?>
                            <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="f_temporada">Temporada</label>
                    <select id="f_temporada" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($temporadas as $t): ?>
                            <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="f_semana_siembra">Semana Siembra (YYWW)</label>
                    <input type="text" id="f_semana_siembra" class="form-control" placeholder="ej: 2601" maxlength="4">
                </div>

                <div class="d-flex gap-2">
                    <button id="btnFilter" class="btn btn-sm btn-success">Filtrar</button>
                    <button id="btnClearFilter" class="btn btn-sm btn-secondary">Limpiar</button>
                </div>
            </div>
        </div>
        <div id="tableCol" class="col-sm-9">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="m-0">Programa de siembras</h4>
                    <div>
                        <button id="btnToggleFilters" class="btn btn-outline-secondary mr-2">Filtros</button>
                        <button id="btnNew" class="btn btn-primary">Nuevo registro</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="programsTable" class="display table table-striped" style="width:100%">
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" style="text-align:right">Total Ncamas:</th>
                                <th id="totalNcamas"></th>
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
<div class="modal fade" id="programModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registro Program</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="programForm">
            <input type="hidden" name="id" id="p_id">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Programa</label>
                        <input type="number" class="form-control" name="programa" id="p_programa">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Variedad</label>
                        <input type="text" class="form-control" name="variedad" id="p_variedad" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Temporada</label>
                        <input type="text" class="form-control" name="temporada_obj" id="p_temporada_obj">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Ciclo</label>
                        <input type="number" class="form-control" name="ciclo" id="p_ciclo">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Fecha Siembra</label>
                        <input type="date" class="form-control" name="fecha_siembra" id="p_fecha_siembra" oninput="if(window.updateProgramIsoWeek){window.updateProgramIsoWeek(this.value);}">
                        <small id="p_fecha_siembra_iso" class="form-text text-muted">Semana ISO: -</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Ncamas</label>
                        <input type="number" class="form-control" name="ncamas" id="p_ncamas">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Casa ID</label>
                        <input type="number" class="form-control" name="casa_id" id="p_casa_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Pico</label>
                        <input type="text" class="form-control" name="pico" id="p_pico">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Raiz</label>
                        <select class="form-control" name="raiz" id="p_raiz">
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">PM</label>
                        <select class="form-control" name="pm" id="p_pm">
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Ferradica</label>
                        <input type="text" class="form-control" name="ferradica" id="p_ferradica">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <input type="text" class="form-control" name="estado" id="p_estado">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Adicional</label>
                        <select class="form-control" name="adicional" id="p_adicional">
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Cantidad Pedida</label>
                        <input type="number" class="form-control" name="cantidad_pedida" id="p_cantidad_pedida">
                    </div>
                </div>
            </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
        <button type="button" id="saveProgram" class="btn btn-dark">Guardar</button>
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
            $btn.text('Ocultar filtros');
        } else {
            $container.addClass('filters-hidden');
            $panel.hide(180);
            $btn.text('Mostrar filtros');
        }
    });
    $btn.text( $container.hasClass('filters-hidden') ? 'Mostrar filtros' : ($panel.is(':visible') ? 'Ocultar filtros' : 'Mostrar filtros') );
});
</script>

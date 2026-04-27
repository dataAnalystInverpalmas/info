<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card-panel { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; }
    .filtersf .select2-container { width: 100% !important; }
    .filtersf .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .25rem; }
    .filtersf h5 { margin-top:0; }
    .modal-title { font-weight:600; }
    .form-section { margin-bottom:12px; }
    .form-label { font-weight:500; font-size:0.92rem; }
    .filtersf .row-compact { display:flex; gap:8px; }
    .filtersf .row-compact .form-section { flex:1; min-width:0; }
    #programfTable .btn { margin-right:6px; }
    .app-content-f { padding-top: 20px; }
    #programfTable td { vertical-align: middle; }
    #programfTable td .btn { display: inline-block; white-space: nowrap; margin-right: 6px; }
    #programfTable th:last-child, #programfTable td:last-child { white-space: nowrap; width: 1%; }
    .app-content-f.filters-hidden #filtersColF { display: none; }
    .app-content-f.filters-hidden #tableColF { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content-f">
    <div class="row">
        <div id="filtersColF" class="col-sm-3">
            <div class="card-panel filtersf">
                <h5>Filtros</h5>

                <div class="row-compact">
                    <div class="form-group form-section">
                        <label class="form-label" for="ff_programa">Programa</label>
                        <select id="ff_programa" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($programas as $p): ?>
                                <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group form-section">
                        <label class="form-label" for="ff_estado">Estado</label>
                        <select id="ff_estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="0">0</option>
                            <option value="1" selected>1</option>
                        </select>
                    </div>
                </div>

                <div class="row-compact">
                    <div class="form-group form-section">
                        <label class="form-label" for="ff_ciclo">Ciclo</label>
                        <select id="ff_ciclo" class="form-control">
                            <option value="">Todos</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="form-group form-section">
                        <label class="form-label" for="ff_bloque">Bloque</label>
                        <select id="ff_bloque" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($bloques as $b): ?>
                                <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group form-section">
                        <label class="form-label" for="ff_adicional">Adicional</label>
                        <select id="ff_adicional" class="form-control">
                            <option value="">Todos</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="ff_producto">Producto</label>
                    <select id="ff_producto" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($productos as $pr): ?>
                            <option value="<?php echo htmlspecialchars($pr); ?>"><?php echo htmlspecialchars($pr); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="ff_variedad">Variedad</label>
                    <select id="ff_variedad" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($variedades as $v): ?>
                            <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="ff_temporada">Temporada</label>
                    <select id="ff_temporada" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($temporadas as $t): ?>
                            <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="ff_semana_siembra">Semana Siembra (YYWW)</label>
                    <input type="text" id="ff_semana_siembra" class="form-control" placeholder="ej: 2601" maxlength="4">
                </div>

                <div class="form-group form-section">
                    <label class="form-label" for="ff_finca">Finca</label>
                    <select id="ff_finca" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($fincas as $f): ?>
                            <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button id="btnFilterF" class="btn btn-sm btn-success">Filtrar</button>
                    <button id="btnClearFilterF" class="btn btn-sm btn-secondary">Limpiar</button>
                </div>
            </div>
        </div>

        <div id="tableColF" class="col-sm-9">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="m-0">Programa de siembras por finca</h4>
                    <div>
                        <button id="btnToggleFiltersF" class="btn btn-outline-secondary mr-2">Filtros</button>
                        <button id="btnNewF" class="btn btn-primary">Nuevo registro</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="programfTable" class="display table table-striped" style="width:100%">
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" style="text-align:right">Totales:</th>
                                <th id="totalNcamas"></th>
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
<div class="modal fade" id="programfModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registro Programf</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="programfForm">
                    <input type="hidden" name="id" id="pf_id">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Programa</label>
                                <input type="number" class="form-control" name="programa" id="pf_programa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Producto</label>
                                <input type="text" class="form-control" name="producto" id="pf_producto">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">Variedad</label>
                                <input type="text" class="form-control" name="variedad" id="pf_variedad" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Temporada</label>
                                <input type="text" class="form-control" name="temporada_obj" id="pf_temporada_obj">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Finca</label>
                                <input type="text" class="form-control" name="finca" id="pf_finca">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Bloque</label>
                                <input type="number" class="form-control" name="bloque" id="pf_bloque">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Ncamas</label>
                                <input type="number" class="form-control" name="ncamas" id="pf_ncamas">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Ciclo</label>
                                <input type="number" class="form-control" name="ciclo" id="pf_ciclo">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Fecha Siembra</label>
                                <input type="date" class="form-control" name="fecha_siembra" id="pf_fecha_siembra">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control" name="estado" id="pf_estado">
                                    <option value="1">1</option>
                                    <option value="0">0</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Ferradica</label>
                                <input type="text" class="form-control" name="ferradica" id="pf_ferradica">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Adicional</label>
                                <input type="text" class="form-control" name="adicional" id="pf_adicional">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" id="saveProgramf" class="btn btn-dark">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/programsf.js?v=4"></script>

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
            $btn.text('Ocultar filtros');
        } else {
            $container.addClass('filters-hidden');
            $panel.hide(180);
            $btn.text('Mostrar filtros');
        }
    });
    $btn.text($container.hasClass('filters-hidden') ? 'Mostrar filtros' : ($panel.is(':visible') ? 'Ocultar filtros' : 'Mostrar filtros'));
});
</script>

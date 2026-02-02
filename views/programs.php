<?php
// incluir conexion como en otros views
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
}else{
    include ("../funciones/conexion.php");
}
?>
<style>
    /* Estilo sencillo y claro para el formulario (fragmento integrado en layout) */
    .card-panel { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; }
    .filters h5 { margin-top:0; }
    .modal-title { font-weight:600; }
    .form-section { margin-bottom:12px; }
    .form-label { font-weight:500; font-size:0.92rem; }
    #programsTable .btn { margin-right:6px; }
    /* padding-top to avoid fixed menu overlap; ajusta si tu layout usa otra altura */
    /* Se redujo ligeramente para subir el fragmento respecto al menú */
    .app-content { padding-top: 20px; }

    /* Alineación de acciones en la tabla: mantener los botones en una sola fila */
    #programsTable td { vertical-align: middle; }
    #programsTable td .btn { display: inline-block; white-space: nowrap; margin-right: 6px; }
    #programsTable th:last-child, #programsTable td:last-child { white-space: nowrap; width: 1%; }

    /* Cuando los filtros estén ocultos, expande la columna de la tabla al 100% */
    .app-content.filters-hidden #filtersCol { display: none; }
    .app-content.filters-hidden #tableCol { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content">
    <div class="row">
        <div id="filtersCol" class="col-sm-3">
            <div class="card-panel filters">
                <h5>Filtros</h5>
                <?php
                // Opciones para los selects de filtro: programa, variedad, temporada
                $programas = [];
                $variedades = [];
                $temporadas = [];

                if (isset($conexion) && $conexion) {
                    // Programa (distinct)
                    $qr = $conexion->query("SELECT DISTINCT programa FROM program ORDER BY programa DESC");
                    if ($qr) {
                        while ($r = $qr->fetch_object()) { $programas[] = $r->programa; }
                        $qr->free();
                    }
                    // Variedad (distinct)
                    $qr = $conexion->query("SELECT DISTINCT variedad FROM program WHERE variedad IS NOT NULL AND variedad <> '' ORDER BY variedad");
                    if ($qr) {
                        while ($r = $qr->fetch_object()) { $variedades[] = $r->variedad; }
                        $qr->free();
                    }
                    // Temporada (distinct)
                    $qr = $conexion->query("SELECT DISTINCT temporada_obj FROM program WHERE temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj");
                    if ($qr) {
                        while ($r = $qr->fetch_object()) { $temporadas[] = $r->temporada_obj; }
                        $qr->free();
                    }
                }
                ?>

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
                        <input type="date" class="form-control" name="fecha_siembra" id="p_fecha_siembra">
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

<!-- include page script (relativo a la raíz de la aplicación) -->
<script src="scripts/programs.js"></script>

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
            // mostrar filtros
            $container.removeClass('filters-hidden');
            $panel.show(180);
            $btn.text('Ocultar filtros');
        } else {
            // ocultar filtros y expandir tabla
            $container.addClass('filters-hidden');
            $panel.hide(180);
            $btn.text('Mostrar filtros');
        }
    });
    // estado inicial
    $btn.text( $container.hasClass('filters-hidden') ? 'Mostrar filtros' : ($panel.is(':visible') ? 'Ocultar filtros' : 'Mostrar filtros') );
});
</script>

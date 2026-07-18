<style>
    .gestion-title {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .kpi-card {
        border: 1px solid #dbe3ea;
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        background: #f8fbff;
    }

    .kpi-label {
        font-size: 0.78rem;
        color: #667085;
        margin-bottom: 0.2rem;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
    }

    .resumen-chip {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #d8dee8;
        background: #fff;
        color: #374151;
    }

    .tareas-detalle {
        font-size: 0.82rem;
        line-height: 1.35;
        white-space: normal;
    }

    .tareas-detalle .tarea-item {
        display: block;
        margin-bottom: 0.15rem;
    }

    .tareas-detalle .tarea-num {
        display: inline-block;
        min-width: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .tareas-detalle .tarea-orden {
        font-weight: 700;
        color: #2563eb;
    }
</style>

<?php
if (!function_exists('renderTareasDetalle')) {
    function renderTareasDetalle($detalle) {
        $texto = trim((string)$detalle);
        if ($texto === '') {
            return '<span class="text-muted">Sin tareas</span>';
        }

        $items = explode(' | ', $texto);
        $html = '<div class="tareas-detalle">';

        foreach ($items as $index => $item) {
            $textoItem = trim((string)$item);
            if ($textoItem === '') {
                continue;
            }

            $html .= '<span class="tarea-item"><span class="tarea-num">' . ($index + 1) . '.</span> <span class="tarea-orden">' . htmlspecialchars($textoItem, ENT_QUOTES, 'UTF-8') . '</span></span>';
        }

        $html .= '</div>';
        return $html;
    }
}
?>

<div class="container-fluid">
    <h4 class="gestion-title">Resumen Ejecutivo de Proyectos</h4>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <div class="row mb-3 align-items-end">
        <div class="col-6 col-md-2">
            <label class="small text-muted mb-0">Año</label>
            <input type="number" min="2020" max="2099" step="1" id="fAnio" class="form-control form-control-sm" value="<?php echo (int)$anio; ?>">
        </div>
        <div class="col-12 col-md-4">
            <label class="small text-muted mb-0">Categoría</label>
            <select id="fCategoria" class="form-control form-control-sm">
                <option value="">Todas</option>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-12 col-md-4">
            <label class="small text-muted mb-0">Estado</label>
            <select id="fEstado" class="form-control form-control-sm">
                <option value="">Todos</option>
                <option value="activo">Activo</option>
                <option value="pausado">Pausado</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <div class="col-12 col-md-2 text-right mt-2 mt-md-0">
            <button class="btn btn-sm btn-primary" onclick="cargarResumenEjecutivo()">Actualizar</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="kpi-card">
                <div class="kpi-label">Proyectos reportados</div>
                <div id="kpiProyectos" class="kpi-value">0</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2 mb-md-0">
            <div class="kpi-card">
                <div class="kpi-label">Avance promedio</div>
                <div id="kpiAvance" class="kpi-value">0%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-label">Logros del periodo</div>
                <div id="kpiLogros" class="kpi-value">0</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-label">Riesgos abiertos</div>
                <div id="kpiRiesgos" class="kpi-value">0</div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="tablaResumenEjecutivo" class="table display compact" style="width:100%">
            <thead>
                <tr>
                    <td>Proyecto</td>
                    <td>Objetivo / Alcance</td>
                    <td>Responsable</td>
                    <td>% Avance</td>
                    <td>Estado</td>
                    <td>Principales Logros</td>
                    <td>Riesgos / Pendientes</td>
                    <td>Tareas / Avance / Orden</td>
                    <td>Actividades del Año</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resumen)): ?>
                    <?php foreach ($resumen as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row->nombre ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->objetivo_alcance ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->responsable ?? ''); ?></td>
                            <td><?php echo (int)($row->avance_proyecto ?? 0); ?>%</td>
                            <td><span class="resumen-chip"><?php echo htmlspecialchars($row->estado ?? ''); ?></span></td>
                            <td><?php echo htmlspecialchars($row->logros_principales ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->riesgos_pendientes ?? ''); ?></td>
                            <td><?php echo renderTareasDetalle($row->tareas_detalle ?? ''); ?></td>
                            <td>
                                Total: <?php echo (int)($row->actividades_mes ?? 0); ?><br>
                                Completadas: <?php echo (int)($row->completadas_mes ?? 0); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    var dtResumenEjecutivo = null;
    var resumenInicial = <?php echo json_encode($resumen ?? [], JSON_UNESCAPED_UNICODE); ?>;

    $(document).ready(function () {
        dtResumenEjecutivo = $('#tablaResumenEjecutivo').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Descargar Excel',
                    title: 'Resumen_Ejecutivo_Proyectos_' + $('#fAnio').val(),
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });
        recalcularKpis(resumenInicial);
    });

    function cargarResumenEjecutivo() {
        var query = $.param({
            anio: $('#fAnio').val(),
            categoria: $('#fCategoria').val(),
            estado: $('#fEstado').val()
        });

        $.getJSON('ajax/resumen_ejecutivo_mensual.php?' + query, function (resp) {
            if (!resp || !resp.success) {
                alert((resp && resp.mensaje) ? resp.mensaje : 'No fue posible cargar el resumen ejecutivo');
                return;
            }

            dtResumenEjecutivo.clear();

            (resp.data || []).forEach(function (r) {
                dtResumenEjecutivo.row.add([
                    escapeHtml(r.nombre || ''),
                    escapeHtml(r.objetivo_alcance || ''),
                    escapeHtml(r.responsable || ''),
                    parseInt(r.avance_proyecto || 0, 10) + '%',
                    '<span class="resumen-chip">' + escapeHtml(r.estado || '') + '</span>',
                    escapeHtml(r.logros_principales || ''),
                    escapeHtml(r.riesgos_pendientes || ''),
                    renderTareasDetalle(r.tareas_detalle || ''),
                    'Total: ' + parseInt(r.actividades_mes || 0, 10) + '<br>Completadas: ' + parseInt(r.completadas_mes || 0, 10)
                ]);
            });

            dtResumenEjecutivo.draw();
            recalcularKpis(resp.data || []);
        }).fail(function () {
            alert('Error de conexion al cargar el resumen ejecutivo');
        });
    }

    function recalcularKpis(data) {
        var filas = Array.isArray(data) ? data : obtenerFilasDesdeDataTable();
        var totalProyectos = filas.length;
        var sumaAvance = 0;
        var sumaLogros = 0;
        var sumaRiesgos = 0;

        filas.forEach(function (r) {
            sumaAvance += parseInt(r.avance_proyecto || 0, 10);
            sumaLogros += parseInt(r.logros_total || 0, 10);
            sumaRiesgos += parseInt(r.riesgos_abiertos || 0, 10);
        });

        var promedioAvance = totalProyectos > 0 ? Math.round(sumaAvance / totalProyectos) : 0;

        $('#kpiProyectos').text(totalProyectos);
        $('#kpiAvance').text(promedioAvance + '%');
        $('#kpiLogros').text(sumaLogros);
        $('#kpiRiesgos').text(sumaRiesgos);
    }

    function obtenerFilasDesdeDataTable() {
        var rows = [];
        if (!dtResumenEjecutivo) {
            return rows;
        }

        dtResumenEjecutivo.rows().every(function () {
            var row = this.data();
            rows.push({
                avance_proyecto: parseInt((row[3] || '0').toString().replace('%', ''), 10) || 0,
                logros_total: 0,
                riesgos_abiertos: 0
            });
        });

        return rows;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderTareasDetalle(detalle) {
        var texto = String(detalle || '').trim();
        if (!texto) {
            return '<span class="text-muted">Sin tareas</span>';
        }

        var items = texto.split(' | ');
        var html = '<div class="tareas-detalle">';

        items.forEach(function (item, index) {
            var textoItem = item.trim();
            if (!textoItem) {
                return;
            }
            html += '<span class="tarea-item"><span class="tarea-num">' + (index + 1) + '.</span> <span class="tarea-orden">' + escapeHtml(textoItem) + '</span></span>';
        });

        html += '</div>';
        return html;
    }
</script>

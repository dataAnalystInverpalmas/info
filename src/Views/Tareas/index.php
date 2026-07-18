<!-- Vista Tareas -->
<style>
    .gestion-title {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .modal-dialog.modal-half {
        width: 70vw;
        max-width: 70vw;
    }

    #modalTarea #tDesc {
        min-height: 160px;
        resize: vertical;
    }

    @media (max-width: 991.98px) {
        .modal-dialog.modal-half {
            width: 95vw;
            max-width: 95vw;
            margin: 0.75rem auto;
        }
    }
</style>

<?php
$gestionActive = 'tareas';
$gestionTitle = 'Gestión de Tareas';
$gestionSubtitle = 'Consulta pendientes, prioriza ejecución y salta al tablero Kanban sin perder el hilo operativo.';
$gestionProyectoFiltro = trim((string)($_GET['proyecto'] ?? ''));
$gestionProyectoIdInicial = (int)($_GET['proyecto_id'] ?? 0);
$gestionQuickActions = [
    [
        'label' => 'Panel',
        'href' => 'index.php?report=205',
        'class' => 'btn-outline-secondary',
        'icon' => 'space_dashboard'
    ],
    [
        'label' => 'Nueva Tarea',
        'onclick' => 'abrirModalTarea()',
        'class' => 'btn-primary',
        'icon' => 'add_task'
    ],
    [
        'label' => 'Ir a Kanban',
        'href' => 'index.php?report=203',
        'class' => 'btn-outline-success',
        'icon' => 'view_kanban'
    ],
    [
        'label' => 'Ver Proyectos',
        'href' => 'index.php?report=200',
        'class' => 'btn-outline-secondary',
        'icon' => 'folder_open'
    ],
];
require __DIR__ . '/../Shared/gestion_header.php';
?>

<div class="container-fluid">
    <?php if ($gestionProyectoFiltro !== ''): ?>
        <div class="alert alert-light border d-flex align-items-center justify-content-between mb-3">
            <div>
                <strong>Contexto activo:</strong> mostrando tareas del proyecto <strong><?php echo htmlspecialchars($gestionProyectoFiltro); ?></strong>.
            </div>
            <a class="btn btn-sm btn-outline-secondary" href="index.php?report=201">Limpiar filtro</a>
        </div>
    <?php endif; ?>
    <div class="row mb-2">
        <div class="col-auto">
            <select id="filtroTipo" class="form-control form-control-sm" onchange="filtrarTareas()">
                <option value="">-- Todos los tipos --</option>
                <option value="prevista">Prevista</option>
                <option value="imprevista">Imprevista</option>
            </select>
        </div>
        <div class="col-auto">
            <select id="filtroProyectoTarea" class="form-control form-control-sm" onchange="filtrarTareas()">
                <option value="">-- Todos los proyectos --</option>
                <option value="Sin proyecto">Sin proyecto</option>
                <?php if (!empty($proyectos)): ?>
                    <?php foreach ($proyectos as $p): ?>
                        <option value="<?php echo htmlspecialchars($p->nombre); ?>" <?php echo $gestionProyectoFiltro !== '' && $gestionProyectoFiltro === (string)$p->nombre ? 'selected' : ''; ?>><?php echo htmlspecialchars(($p->categoria ? $p->categoria . ' / ' : '') . $p->nombre); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table id="tablaTareas" class="table display compact" style="width:100%">
            <thead>
                <tr>
                    <td style="width: 30px; text-align: center;" title="Arrastrar para reordenar">⋮</td>
                    <td>ID</td>
                    <td>Tipo</td>
                    <td>Nombre</td>
                    <td>Proyecto</td>
                    <td>% Avance</td>
                    <td>Responsable</td>
                    <td>Quien Solicita</td>
                    <td>Estado</td>
                    <td>Prioridad</td>
                    <td>Orden</td>
                    <td>Inicio</td>
                    <td>Vencimiento</td>
                    <td>Acciones</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tareas)): ?>
                    <?php foreach ($tareas as $row): ?>
                        <tr data-id="<?php echo (int)$row->id; ?>" data-proyecto="<?php echo (int)($row->proyecto_id ?? 0); ?>">
                            <td style="text-align: center; cursor: move; color: #999;" class="sortable-handle">⋮⋮</td>
                            <td><?php echo (int)$row->id; ?></td>
                            <td><span class="badge badge-<?php 
                                $tipo = $row->tipo ?? 'prevista';
                                echo ($tipo === 'prevista') ? 'info' : 'warning';
                            ?>">▪</span> <?php echo htmlspecialchars($tipo); ?></td>
                            <td><?php echo htmlspecialchars($row->nombre); ?></td>
                            <td><?php echo htmlspecialchars($row->proyecto_nombre ?? 'Sin proyecto'); ?></td>
                            <td><?php echo (int)($row->porcentaje_avance ?? 0); ?>%</td>
                            <td><?php echo htmlspecialchars($row->responsable ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->quien_solicita ?? ''); ?></td>
                            <td>
                                <span class="badge badge-<?php
                                    $estado = $row->estado;
                                    echo ($estado === 'completada') ? 'success' : (($estado === 'en_progreso') ? 'primary' : (($estado === 'cancelada') ? 'danger' : 'secondary'));
                                ?>">●</span>
                                <?php echo htmlspecialchars($estado); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php
                                    $prioridad = $row->prioridad;
                                    echo ($prioridad === 'urgente') ? 'danger' : (($prioridad === 'alta') ? 'warning' : (($prioridad === 'media') ? 'info' : 'light'));
                                ?>">●</span>
                                <?php echo htmlspecialchars($prioridad); ?>
                            </td>
                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars(isset($row->orden_ejecucion) ? (string)$row->orden_ejecucion : '-'); ?></span></td>
                            <td><?php echo htmlspecialchars($row->fecha_inicio ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_vencimiento ?? '-'); ?></td>
                            <td>
                                <button class="btn btn-xs btn-warning" onclick='editarTarea(<?php echo json_encode([
                                    "id" => (int)$row->id,
                                    "tipo" => $row->tipo ?? "prevista",
                                    "nombre" => $row->nombre,
                                    "descripcion" => $row->descripcion ?? "",
                                    "proyecto_id" => $row->proyecto_id ?? "",
                                    "responsable" => $row->responsable ?? "",
                                    "quien_solicita" => $row->quien_solicita ?? "",
                                    "estado" => $row->estado,
                                    "porcentaje_avance" => (int)($row->porcentaje_avance ?? 0),
                                    "prioridad" => $row->prioridad,
                                    "orden_ejecucion" => isset($row->orden_ejecucion) ? (int)$row->orden_ejecucion : "",
                                    "fecha_inicio" => $row->fecha_inicio ?? "",
                                    "fecha_vencimiento" => $row->fecha_vencimiento ?? ""
                                ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>✏</button>
                                <button class="btn btn-xs btn-info" title="Imágenes / Bitácora" onclick="abrirPanelTarea(<?php echo (int)$row->id; ?>, '<?php echo addslashes($row->nombre); ?>')">📎</button>
                                <button class="btn btn-xs btn-danger" onclick="eliminarTarea(<?php echo (int)$row->id; ?>)">🗑</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTarea" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-half" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tarea</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tId">
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Tipo</label>
                        <select id="tTipo" class="form-control mb-2">
                            <option value="prevista">Prevista</option>
                            <option value="imprevista">Imprevista</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">Proyecto (opcional)</label>
                        <select id="tProyecto" class="form-control mb-2">
                            <option value="">-- Sin Proyecto --</option>
                            <?php if (!empty($proyectos)): ?>
                                <?php foreach ($proyectos as $p): ?>
                                    <option value="<?php echo (int)$p->id; ?>"><?php echo htmlspecialchars(($p->categoria ? $p->categoria . ' / ' : '') . $p->nombre); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <label class="small text-muted mb-0">Nombre *</label>
                <input type="text" id="tNombre" class="form-control mb-2" placeholder="Nombre de la tarea">
                <label class="small text-muted mb-0">Descripción</label>
                <textarea id="tDesc" class="form-control mb-2" rows="6" placeholder="Descripción (puede superar 100 caracteres)"></textarea>
                <label class="small text-muted mb-0">Responsable</label>
                <input type="text" id="tResponsable" class="form-control mb-2" placeholder="¿Quién ejecuta?">
                <label class="small text-muted mb-0">Quien Solicita</label>
                <input type="text" id="tSolicita" class="form-control mb-2" placeholder="¿Quién lo solicita? (opcional)">
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Estado</label>
                        <select id="tEstado" class="form-control mb-2">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">% Avance</label>
                        <input type="number" id="tAvance" class="form-control mb-2" min="0" max="100" step="1" value="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Prioridad</label>
                        <select id="tPrioridad" class="form-control mb-2">
                            <option value="baja">Baja</option>
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">Orden de ejecución</label>
                        <input type="number" id="tOrden" class="form-control mb-2" min="1" step="1" placeholder="1, 2, 3...">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Fecha inicio</label>
                        <input type="date" id="tInicio" class="form-control mb-2">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">Vencimiento</label>
                        <input type="date" id="tVencimiento" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarTarea()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal imágenes + bitácora de tarea ──────────────────────────── -->
<div class="modal fade" id="modalPanelTarea" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="panelTareaTitulo">Tarea</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="tabsPanel">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabImagenes">📎 Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabBitacora">📋 Bitácora</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Tab imágenes -->
                    <div class="tab-pane fade show active" id="tabImagenes">
                        <div class="mb-2">
                            <label class="btn btn-sm btn-outline-primary mb-0">
                                📤 Subir imagen
                                <input type="file" id="inputImagen" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none" onchange="subirImagen()">
                            </label>
                            <small class="text-muted ml-2">JPG, PNG, GIF, WEBP — máx 5 MB</small>
                        </div>
                        <div id="galeriaImagenes" class="d-flex flex-wrap gap-2"></div>
                        <p id="sinImagenes" class="text-muted small">Sin imágenes adjuntas.</p>
                    </div>
                    <!-- Tab bitácora -->
                    <div class="tab-pane fade" id="tabBitacora">
                        <div id="listaBitacora"></div>
                        <p id="sinBitacora" class="text-muted small">Sin registros.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox simple -->
<div id="lightbox" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;cursor:zoom-out" onclick="cerrarLightbox()">
    <img id="lightboxImg" src="" style="max-width:90%;max-height:90%;border-radius:4px;box-shadow:0 0 30px #000">
</div>

<!-- SortableJS para drag-drop de tareas -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Inicializar drag-drop en tabla de tareas
    document.addEventListener('DOMContentLoaded', function() {
        var tableBody = document.querySelector('#tablaTareas tbody');
        if (tableBody) {
            Sortable.create(tableBody, {
                handle: '.sortable-handle',
                ghostClass: 'sortable-ghost',
                animation: 150,
                onEnd: function(evt) {
                    reordenarTareas();
                }
            });
        }
    });

    function reordenarTareas() {
        var rows = document.querySelectorAll('#tablaTareas tbody tr');
        var reorden = [];
        
        rows.forEach(function(row, indice) {
            var tareaId = parseInt(row.getAttribute('data-id'), 10);
            var proyectoId = parseInt(row.getAttribute('data-proyecto'), 10);
            if (!isNaN(tareaId)) {
                reorden.push({
                    id: tareaId,
                    proyecto_id: proyectoId,
                    orden_ejecucion: indice + 1
                });
            }
        });

        if (reorden.length === 0) return;

        // Enviar al servidor
        $.ajax({
            url: 'ajax/tareas.php?accion=reordenar',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                tareas: reorden
            }),
            dataType: 'json',
            success: function(resp) {
                if (!resp.success) {
                    alert('Error al reordenar: ' + (resp.mensaje || 'Error desconocido'));
                    location.reload();
                }
                console.log('Tareas reordenadas exitosamente');
            },
            error: function(xhr) {
                console.error('Error de AJAX:', xhr);
                alert('Error al reordenar tareas');
                location.reload();
            }
        });
    }
</script>
<style>
    .sortable-ghost {
        opacity: 0.5;
        background-color: #f0f0f0;
    }
    .sortable-handle {
        user-select: none;
        -webkit-user-select: none;
    }
    #tablaTareas tbody tr:hover .sortable-handle {
        color: #333;
        font-weight: bold;
    }
</style>

<script>
var usuarioActual = <?php echo json_encode($_SESSION['usuario'] ?? ''); ?>;
</script>
<script src="scripts/tareas.js?v=<?php echo @filemtime(__DIR__ . '/../../../scripts/tareas.js'); ?>"></script>
<script src="scripts/tarea_panel.js"></script>
<script>
window.gestionProyectoInicial = <?php echo json_encode($gestionProyectoFiltro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
window.gestionProyectoIdInicial = <?php echo json_encode($gestionProyectoIdInicial); ?>;
</script>

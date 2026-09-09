<?php
$gestionActive = 'solicitudes_extra';
$gestionTitle = 'Solicitudes Operativas';
$gestionSubtitle = 'Registra trabajo diario fuera del portafolio formal de proyectos.';
$gestionQuickActions = [];
require __DIR__ . '/../Shared/gestion_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 col-lg-2">
            <div class="filtro-sidebar">
                <div class="card mb-3">
                    <div class="card-header">
                        <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                        Filtros
                    </div>
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <label>Solicitante</label>
                            <input type="text" id="fSolicitante" class="form-control form-control-sm" placeholder="Buscar...">
                        </div>
                        <div class="mb-2">
                            <label>Tipo</label>
                            <select id="fTipo" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="Soporte">Soporte</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Consulta">Consulta</option>
                                <option value="Corrección">Corrección</option>
                                <option value="Capacitación">Capacitación</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Fecha inicio</label>
                            <input type="date" id="fFechaInicio" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label>Fecha fin</label>
                            <input type="date" id="fFechaFin" class="form-control form-control-sm">
                        </div>
                        <div class="d-grid gap-1 mt-3">
                            <button class="btn btn-sm btn-primary" onclick="aplicarFiltros()">
                                <span class="material-icons" style="font-size:0.9rem;">search</span> Filtrar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltros()">Limpiar</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm w-100" onclick="abrirSolicitud()">
                    <span class="material-icons" style="font-size:1rem;">add_task</span> Nueva solicitud
                </button>
            </div>
        </div>

        <div class="col-md-10 col-lg-10">
            <div class="table-responsive">
                <table id="tablaSolicitudes" class="table display compact" style="width:100%">
                    <thead><tr><td>Fecha</td><td>Solicitante</td><td>Área</td><td>Solicitud</td><td>Tipo</td><td>Horas</td><td>Responsable</td><td>Estado</td><td>Acciones</td></tr></thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($solicitud->fecha ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->solicitante ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->area ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->descripcion); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->tipo ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->tiempo_invertido_horas ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->responsable ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->estado); ?></td>
                                <td>
                                    <?php if (empty($solicitud->tarea_origen_id) && empty($solicitud->tarea_convertida_id)): ?>
                                        <button class="btn btn-xs btn-success" title="Convertir en tarea Kanban" onclick="convertirEnTarea(<?php echo (int)$solicitud->id; ?>)">Kanban</button>
                                    <?php else: ?>
                                        <span class="badge badge-info">En Kanban</span>
                                    <?php endif; ?>
                                    <button class="btn btn-xs btn-warning" onclick='editarSolicitud(<?php echo json_encode($solicitud, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>✏</button>
                                    <button class="btn btn-xs btn-danger" onclick="eliminarSolicitud(<?php echo (int)$solicitud->id; ?>)">🗑</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSolicitud" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Solicitud operativa</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
            <input type="hidden" id="sId">
            <div class="row"><div class="col-md-4"><label>Fecha</label><input type="date" id="sFecha" class="form-control"></div><div class="col-md-4"><label>Solicitante</label><input type="text" id="sSolicitante" class="form-control"></div><div class="col-md-4"><label>Área</label><input type="text" id="sArea" class="form-control"></div></div>
            <div class="row mt-2"><div class="col-md-6"><label>Tipo</label><select id="sTipo" class="form-control"><option value="">Seleccionar...</option><option value="Soporte">Soporte</option><option value="Mantenimiento">Mantenimiento</option><option value="Consulta">Consulta</option><option value="Corrección">Corrección</option><option value="Capacitación">Capacitación</option><option value="Otro">Otro</option></select></div><div class="col-md-3"><label>Horas</label><input type="number" id="sHoras" class="form-control" min="0" step="0.25"></div><div class="col-md-3"><label>Estado</label><select id="sEstado" class="form-control"><option value="pendiente">Pendiente</option><option value="en_progreso">En progreso</option><option value="completada">Completada</option><option value="cancelada">Cancelada</option></select></div></div>
            <label class="mt-2">Descripción *</label><textarea id="sDescripcion" class="form-control" rows="4"></textarea>
            <label class="mt-2">Responsable</label><input type="text" id="sResponsable" class="form-control">
            <label class="mt-2">Observaciones</label><textarea id="sObservaciones" class="form-control" rows="3"></textarea>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" onclick="guardarSolicitud()">Guardar</button></div>
    </div></div>
</div>

<script>
    var fFechaInicioDefault = '<?php echo date('Y-m-d', strtotime('monday this week')); ?>';
    var fFechaFinDefault = '<?php echo date('Y-m-d', strtotime('saturday this week')); ?>';
</script>
<script src="scripts/solicitudes_extra.js?v=<?php echo time(); ?>"></script>

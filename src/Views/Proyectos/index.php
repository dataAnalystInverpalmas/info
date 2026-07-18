<!-- Vista Proyectos -->
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

    #modalProyecto #pDesc {
        min-height: 140px;
        resize: vertical;
    }

    #modalNotasProyecto #notaDescripcion {
        min-height: 130px;
        resize: vertical;
    }

    #modalLogrosProyecto #logroDescripcion,
    #modalRiesgosProyecto #riesgoDescripcion,
    #modalRiesgosProyecto #riesgoPlan {
        min-height: 100px;
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
$gestionActive = 'proyectos';
$gestionTitle = 'Gestión de Proyectos';
$gestionSubtitle = 'Centraliza iniciativas, revisa avances y abre rápido los frentes operativos relacionados.';
$gestionQuickActions = [
    [
        'label' => 'Panel',
        'href' => 'index.php?report=205',
        'class' => 'btn-outline-secondary',
        'icon' => 'space_dashboard'
    ],
    [
        'label' => 'Resumen Ejecutivo',
        'href' => 'index.php?report=204',
        'class' => 'btn-outline-secondary',
        'icon' => 'insights'
    ],
    [
        'label' => 'Nuevo Proyecto',
        'onclick' => 'abrirModalProyecto()',
        'class' => 'btn-primary',
        'icon' => 'add_circle'
    ],
    [
        'label' => 'Ver Tareas',
        'href' => 'index.php?report=201',
        'class' => 'btn-outline-success',
        'icon' => 'assignment'
    ],
];
require __DIR__ . '/../Shared/gestion_header.php';
?>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-auto">
            <select id="filtroCategoria" class="form-control form-control-sm" onchange="filtrarProyectos()">
                <option value="">-- Todas las categorías --</option>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table id="tablaProyectos" class="table display compact" style="width:100%">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Categoría</td>
                    <td>Nombre</td>
                    <td>% Avance</td>
                    <td>Descripcion</td>
                    <td>Estado</td>
                    <td>Inicio</td>
                    <td>Fin</td>
                    <td>Acciones</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($proyectos)): ?>
                    <?php foreach ($proyectos as $row): ?>
                        <tr>
                            <td><?php echo (int)$row->id; ?></td>
                            <td><?php echo htmlspecialchars($row->categoria ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->nombre); ?></td>
                            <td>
                                <?php $avanceProyecto = (int)($row->avance_proyecto ?? 0); ?>
                                <div class="progress" style="height: 8px; min-width: 110px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $avanceProyecto; ?>%;" aria-valuenow="<?php echo $avanceProyecto; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted"><?php echo $avanceProyecto; ?>%</small>
                            </td>
                            <td><?php echo htmlspecialchars($row->descripcion ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->estado); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_inicio ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_fin ?? '-'); ?></td>
                            <td>
                                <a class="btn btn-xs btn-outline-primary" title="Ver tareas del proyecto" href="index.php?report=201&proyecto=<?php echo rawurlencode($row->nombre); ?>">📋</a>
                                <a class="btn btn-xs btn-outline-info" title="Nueva tarea para este proyecto" href="index.php?report=201&proyecto=<?php echo rawurlencode($row->nombre); ?>&proyecto_id=<?php echo (int)$row->id; ?>&nueva=1">➕</a>
                                <a class="btn btn-xs btn-outline-success" title="Ver tablero Kanban" href="index.php?report=203">🗂</a>
                                <a class="btn btn-xs btn-outline-dark" title="Ver bitácora del proyecto" href="index.php?report=202&proyecto=<?php echo rawurlencode($row->nombre); ?>">🕘</a>
                                <button class="btn btn-xs btn-warning" onclick='editarProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->categoria ?? '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($row->descripcion ?? "", JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($row->estado, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($row->fecha_inicio ?? "", JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($row->fecha_fin ?? "", JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>✏</button>
                                <button class="btn btn-xs btn-info" title="Notas del proyecto" onclick='abrirNotasProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>📝</button>
                                <button class="btn btn-xs btn-success" title="Logros" onclick='abrirLogrosProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>🏁</button>
                                <button class="btn btn-xs btn-secondary" title="Riesgos" onclick='abrirRiesgosProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>⚠</button>
                                <button class="btn btn-xs btn-danger" onclick="eliminarProyecto(<?php echo (int)$row->id; ?>)">🗑</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalProyecto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-half" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pId">
                <label class="small text-muted mb-0">Categoría</label>
                <input type="text" id="pCategoria" class="form-control mb-2" placeholder="Ej: Tika, Estadística" list="listaCategorias">
                <datalist id="listaCategorias">
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </datalist>
                <label class="small text-muted mb-0">Nombre *</label>
                <input type="text" id="pNombre" class="form-control mb-2" placeholder="Nombre del proyecto">
                <label class="small text-muted mb-0">Descripción</label>
                <textarea id="pDesc" class="form-control mb-2" rows="5" placeholder="Descripción (puede superar 100 caracteres)"></textarea>
                <label class="small text-muted mb-0">Estado</label>
                <select id="pEstado" class="form-control mb-2">
                    <option value="activo">Activo</option>
                    <option value="pausado">Pausado</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Fecha inicio</label>
                        <input type="date" id="pInicio" class="form-control mb-2">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">Fecha fin</label>
                        <input type="date" id="pFin" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarProyecto()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNotasProyecto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notasProyectoTitulo">Notas del proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="notaProyectoId">
                <div class="alert alert-light border small mb-2">
                    Guarda aquí decisiones, opiniones o cambios relevantes sin convertirlos en tarea.
                </div>
                <label class="small text-muted mb-0">Nueva nota</label>
                <textarea id="notaDescripcion" class="form-control mb-2" rows="4" placeholder="Escribe la anotación del proyecto"></textarea>
                <div class="text-right mb-3">
                    <button class="btn btn-sm btn-primary" onclick="guardarNotaProyecto()">Guardar nota</button>
                </div>
                <div id="listaNotasProyecto"></div>
                <p id="sinNotasProyecto" class="text-muted small mb-0">Sin notas registradas.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogrosProyecto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logrosProyectoTitulo">Logros del proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="logroProyectoId">
                <input type="hidden" id="logroId">

                <label class="small text-muted mb-0">Descripcion del logro</label>
                <textarea id="logroDescripcion" class="form-control mb-2" rows="3" placeholder="Que se logro en el proyecto"></textarea>

                <div class="row">
                    <div class="col-md-5">
                        <label class="small text-muted mb-0">Impacto</label>
                        <input type="text" id="logroImpacto" class="form-control mb-2" placeholder="Impacto o resultado">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-0">Fecha</label>
                        <input type="date" id="logroFecha" class="form-control mb-2">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Estado</label>
                        <select id="logroEstado" class="form-control mb-2">
                            <option value="registrado">Registrado</option>
                            <option value="validado">Validado</option>
                        </select>
                    </div>
                </div>

                <div class="text-right mb-3">
                    <button class="btn btn-sm btn-primary" onclick="guardarLogroProyecto()">Guardar logro</button>
                </div>

                <div id="listaLogrosProyecto"></div>
                <p id="sinLogrosProyecto" class="text-muted small mb-0">Sin logros registrados.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRiesgosProyecto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="riesgosProyectoTitulo">Riesgos del proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="riesgoProyectoId">
                <input type="hidden" id="riesgoId">

                <label class="small text-muted mb-0">Descripcion del riesgo</label>
                <textarea id="riesgoDescripcion" class="form-control mb-2" rows="3" placeholder="Riesgo o tema pendiente"></textarea>

                <div class="row">
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Probabilidad</label>
                        <select id="riesgoProbabilidad" class="form-control mb-2">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="muy_alta">Muy alta</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Impacto</label>
                        <select id="riesgoImpacto" class="form-control mb-2">
                            <option value="bajo">Bajo</option>
                            <option value="medio" selected>Medio</option>
                            <option value="alto">Alto</option>
                            <option value="critico">Critico</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Estado</label>
                        <select id="riesgoEstado" class="form-control mb-2">
                            <option value="abierto" selected>Abierto</option>
                            <option value="en_seguimiento">En seguimiento</option>
                            <option value="mitigado">Mitigado</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Responsable</label>
                        <input type="text" id="riesgoResponsable" class="form-control mb-2" placeholder="Responsable del seguimiento">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Fecha compromiso</label>
                        <input type="date" id="riesgoFechaCompromiso" class="form-control mb-2">
                    </div>
                </div>

                <label class="small text-muted mb-0">Plan de mitigacion</label>
                <textarea id="riesgoPlan" class="form-control mb-2" rows="3" placeholder="Acciones para mitigar"></textarea>

                <div class="text-right mb-3">
                    <button class="btn btn-sm btn-primary" onclick="guardarRiesgoProyecto()">Guardar riesgo</button>
                </div>

                <div id="listaRiesgosProyecto"></div>
                <p id="sinRiesgosProyecto" class="text-muted small mb-0">Sin riesgos registrados.</p>
            </div>
        </div>
    </div>
</div>

<script>var usuarioActual = <?php echo json_encode($_SESSION['usuario'] ?? ''); ?>;</script>
<script src="scripts/proyectos.js"></script>

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

    /* Acciones de fila: mismo tamaño e icono para todas, color solo al interactuar */
    .action-icons {
        display: flex;
        align-items: center;
        gap: 2px;
        flex-wrap: nowrap;
    }

    .action-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: transparent;
        color: #6c757d;
        line-height: 1;
        cursor: pointer;
        transition: background-color .15s ease, color .15s ease;
    }

    .action-icon-btn .material-icons {
        font-size: 17px;
        line-height: 1;
    }

    .action-icon-btn:hover,
    .action-icon-btn:focus {
        background-color: #eef1f4;
        color: #495057;
        text-decoration: none;
        outline: none;
    }

    .action-icon-btn[data-variant="danger"]:hover,
    .action-icon-btn[data-variant="danger"]:focus {
        background-color: #fdecea;
        color: #dc3545;
    }
</style>

<?php
$gestionActive = 'proyectos';
$gestionTitle = 'Gestión de Proyectos';
$gestionSubtitle = 'Centraliza iniciativas, revisa avances y abre rápido los frentes operativos relacionados.';
$gestionQuickActions = [];
require __DIR__ . '/../Shared/gestion_header.php';
?>

<div class="container-fluid px-3 px-lg-4 pt-2 pb-4">
    <div class="row">
        <div class="col-md-2 col-lg-2">
            <div class="filtro-sidebar mr-0 mr-lg-3">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                        Filtros
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label for="filtroCategoria">Categoría</label>
                            <select id="filtroCategoria" class="form-control form-control-sm" onchange="filtrarProyectos()">
                                <option value="">Todas</option>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button class="btn btn-brand-green btn-sm w-100 mb-2" onclick="abrirModalProyecto()">
                    <span class="material-icons" style="font-size:1rem;">add_circle</span> Nuevo Proyecto
                </button>
                <a class="btn btn-outline-secondary btn-sm w-100 mb-2" href="ajax/proyectos.php?accion=exportar_excel">
                    <span class="material-icons" style="font-size:1rem;">file_download</span> Exportar Excel
                </a>
                <a class="btn btn-outline-secondary btn-sm w-100" href="index.php?report=206">
                    <span class="material-icons" style="font-size:1rem;">assignment_late</span> Solicitudes
                </a>
            </div>
        </div>

        <div class="col-md-10 col-lg-10">
            <div class="table-responsive">
        <table id="tablaProyectos" class="table display compact" style="width:100%">
            <thead>
                <tr>
                    <td>Categoría</td>
                    <td>Nombre</td>
                    <td>% Avance</td>
                    <td>Responsable</td>
                    <td>Prioridad</td>
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
                            <td><?php echo htmlspecialchars($row->categoria ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->nombre); ?></td>
                            <td>
                                <?php $avanceProyecto = (int)($row->avance_proyecto ?? 0); ?>
                                <div class="progress" style="height: 8px; min-width: 110px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $avanceProyecto; ?>%;" aria-valuenow="<?php echo $avanceProyecto; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted"><?php echo $avanceProyecto; ?>%</small>
                            </td>
                            <td><?php echo htmlspecialchars($row->responsable_proyecto ?? ''); ?></td>
                            <td><?php
                                $p = $row->prioridad ?? 'media';
                                $pb = ($p === 'urgente') ? 'danger' : (($p === 'alta') ? 'warning' : (($p === 'media') ? 'info' : 'light'));
                                echo '<span class="badge badge-' . $pb . '">●</span> ' . htmlspecialchars($p);
                            ?></td>
                            <td><?php echo htmlspecialchars($row->estado); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_inicio ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_fin ?? '-'); ?></td>
                            <td>
                                <div class="action-icons">
                                    <a class="action-icon-btn" data-variant="view" title="Ver tareas del proyecto" href="index.php?report=201&proyecto=<?php echo rawurlencode($row->nombre); ?>"><span class="material-icons">list_alt</span></a>
                                    <button class="action-icon-btn" data-variant="edit" title="Editar proyecto" onclick="editarProyecto(<?php echo (int)$row->id; ?>)"><span class="material-icons">edit</span></button>
                                    <button class="action-icon-btn" data-variant="notes" title="Notas del proyecto" onclick='abrirNotasProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'><span class="material-icons">notes</span></button>
                                    <button class="action-icon-btn" data-variant="logros" title="Logros" onclick='abrirLogrosProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'><span class="material-icons">flag</span></button>
                                    <button class="action-icon-btn" data-variant="riesgos" title="Riesgos" onclick='abrirRiesgosProyecto(<?php echo (int)$row->id; ?>, <?php echo json_encode($row->nombre, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'><span class="material-icons">warning</span></button>
                                    <button class="action-icon-btn" data-variant="danger" title="Eliminar proyecto" onclick="eliminarProyecto(<?php echo (int)$row->id; ?>)"><span class="material-icons">delete</span></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
            </div>
        </div>
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
                <div class="row">
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Categoría</label>
                        <input type="text" id="pCategoria" class="form-control mb-2" placeholder="Ej: Tika, Estadística" list="listaCategorias">
                        <datalist id="listaCategorias">
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Tipo</label>
                        <input type="text" id="pTipo" class="form-control mb-2" placeholder="Proyecto, Mantenimiento...">
                    </div>
                </div>
                <label class="small text-muted mb-0">Nombre *</label>
                <input type="text" id="pNombre" class="form-control mb-2" placeholder="Nombre del proyecto">
                <label class="small text-muted mb-0">Descripción / objetivo</label>
                <textarea id="pDesc" class="form-control mb-2" rows="4" placeholder="¿Qué se busca lograr y por qué?"></textarea>
                <div class="row">
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Responsable</label>
                        <input type="text" id="pResponsable" class="form-control mb-2">
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-0">Área solicitante</label>
                        <input type="text" id="pArea" class="form-control mb-2">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Prioridad</label>
                        <select id="pPrioridad" class="form-control mb-2"><option value="baja">Baja</option><option value="media">Media</option><option value="alta">Alta</option><option value="urgente">Urgente</option></select>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Estado</label>
                        <select id="pEstado" class="form-control mb-2">
                            <option value="activo">Activo</option>
                            <option value="pausado">Pausado</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-0">Stakeholders</label>
                        <input type="text" id="pStakeholders" class="form-control mb-2">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="small text-muted mb-0">Fecha inicio</label>
                        <input type="date" id="pInicio" class="form-control mb-2">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted mb-0">Fecha fin</label>
                        <input type="date" id="pFin" class="form-control mb-2">
                    </div>
                </div>
                <label class="small text-muted mb-0">Link de evidencias</label>
                <input type="url" id="pEvidencias" class="form-control mb-3">
                <div class="border-top pt-2">
                    <a data-toggle="collapse" href="#seccionPeriodo" class="small text-muted d-block mb-2" style="cursor:pointer">
                        ▸ Actualización del periodo <span class="text-info">(entregable, riesgo, hito, criterio)</span>
                    </a>
                    <div class="collapse" id="seccionPeriodo">
                        <label class="small text-muted mb-0">Entregable clave del periodo</label>
                        <textarea id="pEntregable" class="form-control mb-2" rows="2"></textarea>
                        <label class="small text-muted mb-0">Riesgo principal</label>
                        <textarea id="pRiesgo" class="form-control mb-2" rows="2"></textarea>
                        <label class="small text-muted mb-0">Próximo hito</label>
                        <textarea id="pHito" class="form-control mb-2" rows="2"></textarea>
                        <label class="small text-muted mb-0">Criterio de éxito</label>
                        <textarea id="pExito" class="form-control mb-2" rows="2"></textarea>
                    </div>
                </div>
                <!-- Campos heredados enviados como ocultos para no romper la exportación Excel -->
                <input type="hidden" id="pProblema">
                <input type="hidden" id="pObjetivo">
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

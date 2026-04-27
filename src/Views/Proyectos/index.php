<!-- Vista Proyectos -->
<style>
    .modal-dialog.modal-half {
        width: 70vw;
        max-width: 70vw;
    }

    #modalProyecto #pDesc {
        min-height: 140px;
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
        <div class="col text-end">
            <button class="btn btn-sm btn-primary" onclick="abrirModalProyecto()">+ Nuevo</button>
        </div>
    </div>
    <div class="table-responsive">
        <table id="tablaProyectos" class="table display compact" style="width:100%">
            <thead>
                <tr>
                    <td>ID</td>
                    <td>Categoría</td>
                    <td>Nombre</td>
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
                            <td><?php echo htmlspecialchars($row->descripcion ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row->estado); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_inicio ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row->fecha_fin ?? '-'); ?></td>
                            <td>
                                <button class="btn btn-xs btn-warning" onclick="editarProyecto(<?php echo (int)$row->id; ?>, '<?php echo addslashes($row->categoria ?? ''); ?>', '<?php echo addslashes($row->nombre); ?>', '<?php echo addslashes($row->descripcion ?? ''); ?>', '<?php echo addslashes($row->estado); ?>', '<?php echo addslashes($row->fecha_inicio ?? ''); ?>', '<?php echo addslashes($row->fecha_fin ?? ''); ?>')">✏</button>
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

<script src="scripts/proyectos.js"></script>

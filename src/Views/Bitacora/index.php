<!-- Vista Bitacora -->
<div class="container-fluid">
    <div class="row">
        <div class="table-responsive">
            <table id="tablaBitacora" class="table display compact" style="width:100%">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Proyecto</td>
                        <td>Tarea</td>
                        <td>Tipo</td>
                        <td>Registro</td>
                        <td>Campos cambiados</td>
                        <td>Autor</td>
                        <td>Fecha</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registros)): ?>
                        <?php foreach ($registros as $row): ?>
                            <tr>
                                <td><?php echo (int)$row->id; ?></td>
                                <td><?php echo htmlspecialchars($row->proyecto_nombre ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($row->tarea_nombre ?? 'General'); ?></td>
                                <td><?php echo htmlspecialchars($row->tipo_registro); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row->descripcion); ?>
                                </td>
                                <td>
                                    <?php if (!empty($row->cambios_json)): ?>
                                        <?php $cambios = json_decode($row->cambios_json, true); ?>
                                        <?php if (!empty($cambios)): ?>
                                            <details>
                                                <summary class="small text-muted"><?php echo count($cambios); ?> campo(s)</summary>
                                                <table class="table table-sm table-bordered mb-0 small mt-1">
                                                    <thead><tr><th>Campo</th><th>Antes</th><th>Después</th></tr></thead>
                                                    <tbody>
                                                        <?php foreach ($cambios as $campo => $val): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($campo); ?></td>
                                                                <td class="text-danger"><?php echo htmlspecialchars($val['antes'] ?? '—'); ?></td>
                                                                <td class="text-success"><?php echo htmlspecialchars($val['despues'] ?? '—'); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </details>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row->autor); ?></td>
                                <td><?php echo htmlspecialchars($row->fecha_registro); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#tablaBitacora').DataTable({ responsive: true, order: [[0, 'desc']] });
});
</script>

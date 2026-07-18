<!-- Vista Bitacora -->
<style>
    .gestion-title {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
</style>
<?php
$gestionActive = 'bitacora';
$gestionTitle = 'Gestión de Bitácora';
$gestionSubtitle = 'Sigue cambios recientes y consulta el historial operativo del módulo completo.';
$gestionProyectoFiltro = trim((string)($_GET['proyecto'] ?? ''));
$gestionProyectosBitacora = [];
foreach (($registros ?? []) as $registroBitacora) {
    $nombreProyecto = trim((string)($registroBitacora->proyecto_nombre ?? ''));
    if ($nombreProyecto !== '') {
        $gestionProyectosBitacora[$nombreProyecto] = true;
    }
}
$gestionProyectosBitacora = array_keys($gestionProyectosBitacora);
sort($gestionProyectosBitacora, SORT_NATURAL | SORT_FLAG_CASE);
$gestionQuickActions = [
    [
        'label' => 'Panel',
        'href' => 'index.php?report=205',
        'class' => 'btn-outline-secondary',
        'icon' => 'space_dashboard'
    ],
    [
        'label' => 'Ver Tareas',
        'href' => 'index.php?report=201',
        'class' => 'btn-outline-secondary',
        'icon' => 'assignment'
    ],
    [
        'label' => 'Abrir Kanban',
        'href' => 'index.php?report=203',
        'class' => 'btn-outline-success',
        'icon' => 'view_kanban'
    ],
];
require __DIR__ . '/../Shared/gestion_header.php';
?>
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col-auto">
            <select id="filtroProyectoBitacora" class="form-control form-control-sm">
                <option value="">-- Todos los proyectos --</option>
                <?php foreach ($gestionProyectosBitacora as $gestionProyectoNombre): ?>
                    <option value="<?php echo htmlspecialchars($gestionProyectoNombre); ?>" <?php echo $gestionProyectoFiltro === $gestionProyectoNombre ? 'selected' : ''; ?>><?php echo htmlspecialchars($gestionProyectoNombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($gestionProyectoFiltro !== ''): ?>
            <div class="col-auto">
                <span class="small text-muted">Contexto activo: <strong><?php echo htmlspecialchars($gestionProyectoFiltro); ?></strong></span>
            </div>
            <div class="col-auto">
                <a class="btn btn-sm btn-outline-secondary" href="index.php?report=202">Limpiar filtro</a>
            </div>
        <?php endif; ?>
    </div>
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
    var tablaBitacora = $('#tablaBitacora').DataTable({ responsive: true, order: [[0, 'desc']] });
    var filtroInicial = <?php echo json_encode($gestionProyectoFiltro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    if (filtroInicial) {
        tablaBitacora.column(1).search(filtroInicial).draw();
    }

    $('#filtroProyectoBitacora').on('change', function () {
        tablaBitacora.column(1).search($(this).val()).draw();
    });
});
</script>

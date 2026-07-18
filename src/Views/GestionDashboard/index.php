<?php
$gestionActive = 'panel';
$gestionTitle = 'Centro de Gestión';
$gestionSubtitle = 'Entrada rápida para proyectos, tareas, tablero Kanban y seguimiento operativo por proyecto.';
$gestionQuickActions = [
    [
        'label' => 'Nuevo Proyecto',
        'href' => 'index.php?report=200',
        'class' => 'btn-outline-secondary',
        'icon' => 'create_new_folder'
    ],
    [
        'label' => 'Nueva Tarea',
        'href' => 'index.php?report=201&nueva=1',
        'class' => 'btn-primary',
        'icon' => 'add_task'
    ],
    [
        'label' => 'Abrir Kanban',
        'href' => 'index.php?report=203',
        'class' => 'btn-outline-success',
        'icon' => 'view_kanban'
    ],
];
require __DIR__ . '/../Shared/gestion_header.php';

$resumen = $dashboard['resumen'] ?? [];
$proyectosDestacados = $dashboard['proyectos_destacados'] ?? [];
$actividadReciente = $dashboard['actividad_reciente'] ?? [];
$alertas = $dashboard['alertas'] ?? [];
?>
<style>
    .gestion-dashboard-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
    }

    .gestion-card {
        background: #ffffff;
        border: 1px solid #e1ebe9;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05);
        padding: 1rem;
        min-width: 0;
    }

    .gestion-kpi-grid {
        grid-column: span 12;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .gestion-kpi {
        padding: 1rem;
        border-radius: 16px;
        background: linear-gradient(145deg, #f8fbfa 0%, #eef7f5 100%);
        border: 1px solid #dce9e6;
    }

    .gestion-kpi-label {
        color: #5e726d;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
    }

    .gestion-kpi-value {
        display: block;
        margin-top: 0.35rem;
        font-size: 1.85rem;
        line-height: 1;
        font-weight: 700;
        color: #17312d;
    }

    .gestion-kpi-help {
        display: block;
        margin-top: 0.45rem;
        color: #67807a;
        font-size: 0.82rem;
    }

    .gestion-main-column {
        grid-column: span 8;
    }

    .gestion-side-column {
        grid-column: span 4;
    }

    .gestion-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }

    .gestion-section-title h4 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #17312d;
    }

    .gestion-section-title p {
        margin: 0.25rem 0 0;
        font-size: 0.84rem;
        color: #6a7f79;
    }

    .gestion-project-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .gestion-project-card {
        border: 1px solid #e2ece9;
        border-radius: 16px;
        padding: 1rem;
        background: #fbfdfd;
    }

    .gestion-project-card h5 {
        margin: 0 0 0.2rem;
        font-size: 1rem;
        font-weight: 700;
        color: #17312d;
    }

    .gestion-project-meta {
        font-size: 0.82rem;
        color: #6c817c;
        margin-bottom: 0.75rem;
    }

    .gestion-project-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.6rem;
        margin-bottom: 0.8rem;
    }

    .gestion-project-stat {
        background: #f2f7f6;
        border-radius: 12px;
        padding: 0.65rem;
        text-align: center;
    }

    .gestion-project-stat strong {
        display: block;
        font-size: 1rem;
        color: #17312d;
    }

    .gestion-project-stat span {
        display: block;
        margin-top: 0.2rem;
        font-size: 0.74rem;
        color: #647873;
    }

    .gestion-project-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .gestion-project-actions .btn {
        flex: 1 1 140px;
    }

    .gestion-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .gestion-list-item {
        border: 1px solid #e2ece9;
        border-radius: 14px;
        padding: 0.85rem 0.9rem;
        background: #fcfdfd;
    }

    .gestion-list-item-title {
        font-weight: 600;
        color: #17312d;
        margin-bottom: 0.2rem;
    }

    .gestion-list-item-meta {
        font-size: 0.8rem;
        color: #6c817c;
    }

    .gestion-badge-alert {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.28rem 0.55rem;
    }

    .gestion-badge-alert.danger {
        background: #fdecea;
        color: #b8322c;
    }

    .gestion-badge-alert.warning {
        background: #fff4dd;
        color: #9a6700;
    }

    @media (max-width: 1199.98px) {
        .gestion-main-column,
        .gestion-side-column {
            grid-column: span 12;
        }
    }

    @media (max-width: 991.98px) {
        .gestion-kpi-grid,
        .gestion-project-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .gestion-kpi-grid,
        .gestion-project-grid,
        .gestion-project-stats {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="container-fluid">
    <div class="gestion-dashboard-grid">
        <div class="gestion-kpi-grid">
            <div class="gestion-kpi">
                <span class="gestion-kpi-label">Proyectos activos</span>
                <strong class="gestion-kpi-value"><?php echo (int)($resumen['proyectos_activos'] ?? 0); ?></strong>
                <span class="gestion-kpi-help"><?php echo (int)($resumen['total_proyectos'] ?? 0); ?> proyecto(s) en total</span>
            </div>
            <div class="gestion-kpi">
                <span class="gestion-kpi-label">Tareas abiertas</span>
                <strong class="gestion-kpi-value"><?php echo (int)(($resumen['tareas_pendientes'] ?? 0) + ($resumen['tareas_en_progreso'] ?? 0)); ?></strong>
                <span class="gestion-kpi-help"><?php echo (int)($resumen['tareas_en_progreso'] ?? 0); ?> en progreso y <?php echo (int)($resumen['tareas_pendientes'] ?? 0); ?> pendientes</span>
            </div>
            <div class="gestion-kpi">
                <span class="gestion-kpi-label">Avance promedio</span>
                <strong class="gestion-kpi-value"><?php echo (int)($resumen['avance_promedio'] ?? 0); ?>%</strong>
                <span class="gestion-kpi-help"><?php echo (int)($resumen['tareas_completadas'] ?? 0); ?> tarea(s) completadas</span>
            </div>
            <div class="gestion-kpi">
                <span class="gestion-kpi-label">Alertas</span>
                <strong class="gestion-kpi-value"><?php echo (int)($resumen['tareas_atrasadas'] ?? 0); ?></strong>
                <span class="gestion-kpi-help"><?php echo (int)($resumen['tareas_proximas'] ?? 0); ?> vencen esta semana</span>
            </div>
        </div>

        <div class="gestion-card gestion-main-column">
            <div class="gestion-section-title">
                <div>
                    <h4>Proyectos con más movimiento</h4>
                    <p>Accede directo al contexto operativo de cada frente de trabajo.</p>
                </div>
                <a class="btn btn-sm btn-outline-secondary" href="index.php?report=200">Ver todos los proyectos</a>
            </div>

            <div class="gestion-project-grid">
                <?php if (empty($proyectosDestacados)): ?>
                    <div class="gestion-project-card" style="grid-column: 1 / -1;">
                        <h5>Sin proyectos destacados todavía</h5>
                        <div class="gestion-project-meta">Crea tu primer proyecto o registra tareas para que el panel empiece a priorizar contexto operativo.</div>
                        <div class="gestion-project-actions">
                            <a class="btn btn-sm btn-outline-secondary" href="index.php?report=200">Ir a Proyectos</a>
                            <a class="btn btn-sm btn-primary" href="index.php?report=201&nueva=1">Crear una tarea</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($proyectosDestacados as $proyecto): ?>
                        <div class="gestion-project-card">
                            <h5><?php echo htmlspecialchars($proyecto['nombre']); ?></h5>
                            <div class="gestion-project-meta">
                                <?php echo htmlspecialchars($proyecto['categoria'] !== '' ? $proyecto['categoria'] : 'Sin categoría'); ?> ·
                                Estado: <?php echo htmlspecialchars($proyecto['estado']); ?> ·
                                Avance: <?php echo (int)$proyecto['avance']; ?>%
                            </div>

                            <div class="gestion-project-stats">
                                <div class="gestion-project-stat">
                                    <strong><?php echo (int)$proyecto['pendientes']; ?></strong>
                                    <span>Pendientes</span>
                                </div>
                                <div class="gestion-project-stat">
                                    <strong><?php echo (int)$proyecto['en_progreso']; ?></strong>
                                    <span>En progreso</span>
                                </div>
                                <div class="gestion-project-stat">
                                    <strong><?php echo (int)$proyecto['atrasadas']; ?></strong>
                                    <span>Atrasadas</span>
                                </div>
                            </div>

                            <div class="gestion-project-actions">
                                <a class="btn btn-sm btn-outline-primary" href="index.php?report=201&proyecto=<?php echo rawurlencode($proyecto['nombre']); ?>">Ver tareas</a>
                                <a class="btn btn-sm btn-outline-success" href="index.php?report=201&proyecto=<?php echo rawurlencode($proyecto['nombre']); ?>&proyecto_id=<?php echo (int)$proyecto['id']; ?>&nueva=1">Nueva tarea</a>
                                <a class="btn btn-sm btn-outline-dark" href="index.php?report=202&proyecto=<?php echo rawurlencode($proyecto['nombre']); ?>">Bitácora</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="gestion-card gestion-side-column">
            <div class="gestion-section-title">
                <div>
                    <h4>Alertas operativas</h4>
                    <p>Prioriza lo vencido y lo que necesita atención esta semana.</p>
                </div>
            </div>

            <div class="gestion-list">
                <?php if (empty($alertas)): ?>
                    <div class="gestion-list-item">
                        <div class="gestion-list-item-title">Sin alertas críticas</div>
                        <div class="gestion-list-item-meta">No hay tareas vencidas o próximas por atender.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($alertas as $alerta): ?>
                        <div class="gestion-list-item">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="gestion-list-item-title"><?php echo htmlspecialchars($alerta['nombre']); ?></div>
                                    <div class="gestion-list-item-meta"><?php echo htmlspecialchars($alerta['proyecto']); ?> · vence <?php echo htmlspecialchars($alerta['fecha_vencimiento']); ?></div>
                                </div>
                                <span class="gestion-badge-alert <?php echo $alerta['atrasada'] ? 'danger' : 'warning'; ?>">
                                    <?php echo $alerta['atrasada'] ? 'Atrasada' : 'Próxima'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="gestion-card gestion-main-column">
            <div class="gestion-section-title">
                <div>
                    <h4>Actividad reciente</h4>
                    <p>Últimos movimientos registrados en el módulo.</p>
                </div>
                <a class="btn btn-sm btn-outline-secondary" href="index.php?report=202">Abrir Bitácora</a>
            </div>

            <div class="gestion-list">
                <?php if (empty($actividadReciente)): ?>
                    <div class="gestion-list-item">
                        <div class="gestion-list-item-title">Aún no hay actividad</div>
                        <div class="gestion-list-item-meta">Cuando se creen o actualicen elementos, aparecerán aquí.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($actividadReciente as $actividad): ?>
                        <div class="gestion-list-item">
                            <div class="gestion-list-item-title"><?php echo htmlspecialchars($actividad->descripcion ?? 'Registro'); ?></div>
                            <div class="gestion-list-item-meta">
                                <?php echo htmlspecialchars($actividad->proyecto_nombre ?? 'Sin proyecto'); ?> ·
                                <?php echo htmlspecialchars($actividad->tarea_nombre ?? 'General'); ?> ·
                                <?php echo htmlspecialchars($actividad->autor ?? 'Sistema'); ?> ·
                                <?php echo htmlspecialchars($actividad->fecha_registro ?? ''); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="gestion-card gestion-side-column">
            <div class="gestion-section-title">
                <div>
                    <h4>Resumen rápido</h4>
                    <p>Métricas cortas para decidir el siguiente movimiento.</p>
                </div>
            </div>

            <div class="gestion-list">
                <div class="gestion-list-item">
                    <div class="gestion-list-item-title"><?php echo (int)($resumen['tareas_sin_proyecto'] ?? 0); ?> tarea(s) sin proyecto</div>
                    <div class="gestion-list-item-meta">Útil para identificar trabajo operativo que aún no está asociado a un frente.</div>
                </div>
                <div class="gestion-list-item">
                    <div class="gestion-list-item-title"><?php echo (int)($resumen['imprevistas'] ?? 0); ?> tarea(s) imprevistas</div>
                    <div class="gestion-list-item-meta">Mide cuánta ejecución reactiva está entrando al módulo.</div>
                </div>
                <div class="gestion-list-item">
                    <div class="gestion-list-item-title"><?php echo (int)($resumen['tareas_canceladas'] ?? 0); ?> tarea(s) canceladas</div>
                    <div class="gestion-list-item-meta">Sirve para revisar limpieza del backlog y cambios de prioridad.</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$gestionActive = $gestionActive ?? '';
$gestionTitle = $gestionTitle ?? 'Gestión';
$gestionSubtitle = $gestionSubtitle ?? 'Organiza el trabajo y navega rápido entre módulos relacionados.';
$gestionQuickActions = is_array($gestionQuickActions ?? null) ? $gestionQuickActions : [];

$gestionSections = [
    'panel' => [
        'label' => 'Panel',
        'href' => 'index.php?report=205',
        'icon' => 'space_dashboard',
        'description' => 'Vista general y atajos'
    ],
    'proyectos' => [
        'label' => 'Proyectos',
        'href' => 'index.php?report=200',
        'icon' => 'folder_open',
        'description' => 'Portafolio y avance'
    ],
    'tareas' => [
        'label' => 'Tareas',
        'href' => 'index.php?report=201',
        'icon' => 'task_alt',
        'description' => 'Ejecución operativa'
    ],
    'kanban' => [
        'label' => 'Kanban',
        'href' => 'index.php?report=203',
        'icon' => 'view_kanban',
        'description' => 'Flujo por estado'
    ],
    'bitacora' => [
        'label' => 'Bitácora',
        'href' => 'index.php?report=202',
        'icon' => 'history_edu',
        'description' => 'Seguimiento y cambios'
    ],
];
?>
<style>
    /* Barra compacta, a todo el ancho del viewport (igual que el navbar) */
    .gestion-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin: -0.5rem -15px 1rem;
        padding: 0.6rem 15px 0;
    }

    .gestion-bar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding-bottom: 0.5rem;
    }

    .gestion-bar-title-group {
        display: flex;
        align-items: baseline;
        gap: 0.6rem;
        min-width: 0;
    }

    .gestion-bar-title {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        color: #17312d;
        white-space: nowrap;
    }

    .gestion-bar-subtitle {
        margin: 0;
        color: #5f6f6b;
        font-size: 0.8rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .gestion-bar-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .gestion-bar-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    /* Pestañas de navegación: tira horizontal compacta, sin descripciones */
    .gestion-tabs {
        display: flex;
        gap: 0.25rem;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .gestion-tabs::-webkit-scrollbar {
        display: none;
    }

    .gestion-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none !important;
        color: #5f6f6b;
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
        padding: 0.5rem 0.7rem;
        border-bottom: 2px solid transparent;
        transition: color 0.15s ease, border-color 0.15s ease;
    }

    .gestion-tab .material-icons {
        font-size: 1.05rem;
    }

    .gestion-tab:hover,
    .gestion-tab:focus {
        color: #00796B;
        text-decoration: none;
    }

    .gestion-tab.active {
        color: #00796B;
        border-bottom-color: #00796B;
    }

    /* Botón primario de marca (acciones principales: Aplicar, Nuevo, Guardar) */
    .btn-brand-green {
        background-color: #00796B !important;
        border: 1px solid #00796B !important;
        color: #ffffff !important;
    }

    .btn-brand-green:hover,
    .btn-brand-green:focus,
    .btn-brand-green:active {
        background-color: #004D40 !important;
        border-color: #004D40 !important;
        color: #ffffff !important;
    }

    .btn-brand-green:focus {
        box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.25) !important;
    }

    @media (max-width: 767.98px) {
        .gestion-bar {
            margin: -0.5rem -0.9375rem 1rem;
            padding: 0.6rem 0.9375rem 0;
        }

        .gestion-bar-row {
            justify-content: flex-start;
        }

        .gestion-bar-subtitle {
            display: none;
        }

        .gestion-bar-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }

    /* Sidebar layout */
    .filtro-sidebar {
        position: sticky;
        top: 1rem;
    }
    .filtro-sidebar .card {
        border: 1px solid #dbe7e4;
        border-radius: 14px;
    }
    .filtro-sidebar .card-header {
        background: linear-gradient(135deg, #00796B 0%, #00695c 100%);
        color: #fff;
        border-radius: 14px 14px 0 0 !important;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.6rem 0.9rem;
    }
    .filtro-sidebar label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1f2937;
        letter-spacing: 0.02em;
    }
    @media (max-width: 767.98px) {
        .filtro-sidebar {
            position: static;
            margin-bottom: 1rem;
        }
    }

    /* DataTables del módulo de gestión: mismo lenguaje visual que el sidebar de filtros */
    .gestion-table-card.table-responsive {
        border: 1px solid #dbe7e4 !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 15px rgba(0, 77, 64, 0.05) !important;
        padding: 1rem 1.1rem !important;
        margin-bottom: 1rem !important;
    }

    .gestion-table-card .dataTables_wrapper > .row:first-child {
        margin-bottom: 0.85rem;
    }

    .gestion-table-card .dataTables_length select {
        border-radius: 8px !important;
    }

    .gestion-table-card .dataTables_filter input {
        border-radius: 999px !important;
        padding: 0.4rem 0.9rem 0.4rem 2.1rem !important;
        min-width: 220px;
        background-color: #f7faf9 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2300796B' stroke-width='2'%3e%3ccircle cx='11' cy='11' r='7'/%3e%3cline x1='21' y1='21' x2='16.65' y2='16.65'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: 0.75rem center !important;
        background-size: 14px !important;
    }

    .gestion-table-card table.dataTable thead th,
    .gestion-table-card table.dataTable thead td {
        background-color: #f2f8f7 !important;
        color: #17312d !important;
        border-bottom: 2px solid #cfe3df !important;
    }

    .gestion-table-card table.dataTable tbody tr:hover {
        background-color: #f3f8f7 !important;
    }

    .gestion-table-card .dataTables_paginate .paginate_button.current,
    .gestion-table-card .dataTables_paginate .paginate_button.current:hover {
        background: #00796B !important;
        border-color: #00796B !important;
    }

    .gestion-table-card .dataTables_paginate .paginate_button:hover {
        border-color: #93c7bd !important;
    }

    /* Insignias suaves: fondo tenue + texto de color, sin bloques sólidos */
    .gestion-table-card .badge {
        border-radius: 999px !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
    }

    .gestion-table-card .badge-primary { background: rgba(0, 121, 107, 0.12) !important; color: #00796B !important; }
    .gestion-table-card .badge-info { background: rgba(49, 151, 149, 0.12) !important; color: #2c7a7b !important; }
    .gestion-table-card .badge-success { background: rgba(56, 161, 105, 0.14) !important; color: #2f855a !important; }
    .gestion-table-card .badge-warning { background: rgba(214, 158, 46, 0.16) !important; color: #b7791f !important; }
    .gestion-table-card .badge-danger { background: rgba(220, 53, 69, 0.12) !important; color: #c53030 !important; }
    .gestion-table-card .badge-secondary { background: rgba(113, 128, 150, 0.12) !important; color: #4a5568 !important; }
    .gestion-table-card .badge-light { background: #eef2f1 !important; color: #5f6f6b !important; }
</style>

<div class="gestion-bar">
    <div class="gestion-bar-row">
        <div class="gestion-bar-title-group">
            <h1 class="gestion-bar-title"><?php echo htmlspecialchars($gestionTitle); ?></h1>
            <p class="gestion-bar-subtitle" title="<?php echo htmlspecialchars($gestionSubtitle); ?>"><?php echo htmlspecialchars($gestionSubtitle); ?></p>
        </div>
        <?php if (!empty($gestionQuickActions)): ?>
            <div class="gestion-bar-actions">
                <?php foreach ($gestionQuickActions as $action): ?>
                    <?php
                    $label = (string)($action['label'] ?? 'Acción');
                    $class = (string)($action['class'] ?? 'btn-outline-secondary');
                    $icon = trim((string)($action['icon'] ?? ''));
                    $href = trim((string)($action['href'] ?? ''));
                    $onClick = trim((string)($action['onclick'] ?? ''));
                    ?>
                    <?php if ($href !== ''): ?>
                        <a class="btn btn-sm <?php echo htmlspecialchars($class); ?>" href="<?php echo htmlspecialchars($href); ?>">
                            <?php if ($icon !== ''): ?><span class="material-icons" style="font-size: 1rem;"><?php echo htmlspecialchars($icon); ?></span><?php endif; ?>
                            <span><?php echo htmlspecialchars($label); ?></span>
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-sm <?php echo htmlspecialchars($class); ?>" onclick="<?php echo htmlspecialchars($onClick); ?>">
                            <?php if ($icon !== ''): ?><span class="material-icons" style="font-size: 1rem;"><?php echo htmlspecialchars($icon); ?></span><?php endif; ?>
                            <span><?php echo htmlspecialchars($label); ?></span>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <nav class="gestion-tabs" role="navigation" aria-label="Navegación interna de gestión">
        <?php foreach ($gestionSections as $sectionKey => $section): ?>
            <a class="gestion-tab <?php echo $gestionActive === $sectionKey ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($section['href']); ?>">
                <span class="material-icons"><?php echo htmlspecialchars($section['icon']); ?></span>
                <span><?php echo htmlspecialchars($section['label']); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
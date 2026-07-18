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
    .gestion-shell {
        background: linear-gradient(135deg, #ffffff 0%, #f3f8f7 100%);
        border: 1px solid #dbe7e4;
        border-radius: 18px;
        padding: 1rem 1rem 0.9rem;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.06);
        margin-bottom: 1rem;
    }

    .gestion-shell-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
    }

    .gestion-shell-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #00796B;
        background: rgba(0, 121, 107, 0.08);
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        margin-bottom: 0.55rem;
    }

    .gestion-shell-title {
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0;
        color: #17312d;
    }

    .gestion-shell-subtitle {
        margin: 0.35rem 0 0;
        color: #5f6f6b;
        font-size: 0.92rem;
        max-width: 760px;
    }

    .gestion-shell-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 0.55rem;
    }

    .gestion-shell-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .gestion-nav-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .gestion-nav-card {
        display: block;
        text-decoration: none !important;
        color: #26403b;
        background: #ffffff;
        border: 1px solid #dfe8e6;
        border-radius: 14px;
        padding: 0.85rem 0.9rem;
        min-width: 0;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .gestion-nav-card:hover,
    .gestion-nav-card:focus {
        transform: translateY(-1px);
        border-color: #93c7bd;
        box-shadow: 0 8px 20px rgba(0, 77, 64, 0.08);
        color: #16312c;
    }

    .gestion-nav-card.active {
        background: linear-gradient(135deg, #00796B 0%, #00695c 100%);
        border-color: #00796B;
        color: #ffffff;
        box-shadow: 0 10px 26px rgba(0, 121, 107, 0.22);
    }

    .gestion-nav-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.35rem;
    }

    .gestion-nav-label {
        font-weight: 700;
        font-size: 0.96rem;
    }

    .gestion-nav-card .material-icons {
        font-size: 1.15rem;
    }

    .gestion-nav-description {
        display: block;
        font-size: 0.8rem;
        color: inherit;
        opacity: 0.82;
    }

    @media (max-width: 991.98px) {
        .gestion-shell-header {
            flex-direction: column;
        }

        .gestion-shell-actions {
            justify-content: flex-start;
        }

        .gestion-nav-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .gestion-shell {
            padding: 0.9rem 0.9rem 0.85rem;
            border-radius: 14px;
        }

        .gestion-shell-title {
            font-size: 1.18rem;
        }

        .gestion-nav-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .gestion-shell-actions {
            width: 100%;
        }

        .gestion-shell-actions .btn {
            justify-content: center;
            width: 100%;
        }
    }
</style>

<div class="gestion-shell">
    <div class="gestion-shell-header">
        <div>
            <div class="gestion-shell-kicker">
                <span class="material-icons">dashboard_customize</span>
                Centro de gestión
            </div>
            <h3 class="gestion-shell-title"><?php echo htmlspecialchars($gestionTitle); ?></h3>
            <p class="gestion-shell-subtitle"><?php echo htmlspecialchars($gestionSubtitle); ?></p>
        </div>
        <?php if (!empty($gestionQuickActions)): ?>
            <div class="gestion-shell-actions">
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

    <div class="gestion-nav-grid" role="navigation" aria-label="Navegación interna de gestión">
        <?php foreach ($gestionSections as $sectionKey => $section): ?>
            <a class="gestion-nav-card <?php echo $gestionActive === $sectionKey ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($section['href']); ?>">
                <div class="gestion-nav-topline">
                    <span class="gestion-nav-label"><?php echo htmlspecialchars($section['label']); ?></span>
                    <span class="material-icons"><?php echo htmlspecialchars($section['icon']); ?></span>
                </div>
                <span class="gestion-nav-description"><?php echo htmlspecialchars($section['description']); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
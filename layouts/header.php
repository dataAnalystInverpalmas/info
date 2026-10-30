<?php
require("funciones/conexion.php");

$directorio = $GLOBALS['src'] ?? '';
$role = \App\Helpers\Auth::check() ? \App\Helpers\Auth::role() : 0;
$usuario = \App\Helpers\Auth::check() ? (\App\Helpers\Auth::user() ?? '') : '';

if ($role > 0):
    $can = function (string $dir) use ($conexion): bool {
        return \App\Helpers\Auth::can($dir, $conexion);
    };

    // Current report / table for active states
    $currentReport = $_GET['report'] ?? '';
    $currentTable = $_GET['table'] ?? '';

    // Helper to test if item is active
    $isItemActive = function ($item) use ($currentReport, $currentTable): bool {
        if (!empty($item['report']) && $item['report'] === $currentReport)
            return true;
        if (!empty($item['table']) && $item['table'] === $currentTable)
            return true;
        return false;
    };

    // ── DEFINICIÓN Y FILTRADO DE MENÚS ──
    $menuTablasGeneral = [
        ['href' => 'index.php?report=arrangements_crud', 'report' => 'arrangements_crud', 'dir' => 'views/arrangements_crud.php', 'icon' => 'tune', 'label' => 'CRUD Arrangements'],
        ['href' => 'index.php?report=arrangement_crud', 'report' => 'arrangement_crud', 'dir' => 'views/arrangement_crud.php', 'icon' => 'settings', 'label' => 'CRUD Arrangement'],
        ['href' => 'index.php?report=crud_breeders', 'report' => 'crud_breeders', 'dir' => 'views/breeders_crud.php', 'icon' => 'biotech', 'label' => 'CRUD Breeders'],
        ['href' => 'index.php?report=crud_supplies', 'report' => 'crud_supplies', 'dir' => 'views/supplies_crud.php', 'icon' => 'inventory_2', 'label' => 'CRUD Supplies'],
        ['href' => 'index.php?report=crud_varieties', 'report' => 'crud_varieties', 'dir' => 'views/varieties_crud.php', 'icon' => 'local_florist', 'label' => 'CRUD Varieties'],
        ['href' => 'index.php?report=crud_seasons', 'report' => 'crud_seasons', 'dir' => 'views/seasons_crud.php', 'icon' => 'date_range', 'label' => 'CRUD Seasons'],
        ['href' => 'index.php?report=crud_greenhouses', 'report' => 'crud_greenhouses', 'dir' => 'views/greenhouses_crud.php', 'icon' => 'deck', 'label' => 'CRUD Greenhouses'],
        ['href' => 'index.php?report=tika_calendario', 'report' => 'tika_calendario', 'dir' => 'views/tika_calendario.php', 'icon' => 'calendar_month', 'label' => 'CRUD Calendario Tika'],
    ];
    $visTablasGeneral = array_filter($menuTablasGeneral, function ($i) use ($can) {
        return $can($i['dir']);
    });

    $menuTablasFloreros = [
        ['href' => 'index.php?report=crud_fv_kinds', 'report' => 'crud_fv_kinds', 'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'category', 'label' => 'CRUD Tipos'],
        ['href' => 'index.php?report=crud_fv_origens', 'report' => 'crud_fv_origens', 'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'place', 'label' => 'CRUD Orígenes'],
        ['href' => 'index.php?report=crud_fv_causes', 'report' => 'crud_fv_causes', 'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'report_problem', 'label' => 'CRUD Causas'],
        ['href' => 'index.php?report=crud_fv_evaluations', 'report' => 'crud_fv_evaluations', 'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'grading', 'label' => 'CRUD Ítems de Evaluación'],
    ];
    $visTablasFloreros = array_filter($menuTablasFloreros, function ($i) use ($can) {
        return $can($i['dir']);
    });

    $hasAdminSec = ($role === 1);
    $menuTablasSeguridad = [];
    if ($hasAdminSec) {
        $menuTablasSeguridad = [
            ['href' => 'index.php?report=crud_users', 'report' => 'crud_users', 'icon' => 'people_alt', 'label' => 'Usuarios'],
            ['href' => 'index.php?report=roles_matrix', 'report' => 'roles_matrix', 'icon' => 'grid_view', 'label' => 'Matriz de Permisos'],
            ['href' => 'index.php?report=crud_roles', 'report' => 'crud_roles', 'icon' => 'security', 'label' => 'Permisos por Rol'],
            ['href' => 'index.php?report=login_logs', 'report' => 'login_logs', 'icon' => 'manage_history', 'label' => 'Bitácora de Accesos'],
        ];
    }
    $showTablas = !empty($visTablasGeneral) || !empty($visTablasFloreros) || !empty($menuTablasSeguridad);

    // Cultivo
    $menuCultivo = [
        ['href' => 'index.php?report=hoja_bautizo', 'report' => 'hoja_bautizo', 'dir' => 'views/formBautizo.php', 'icon' => 'description', 'label' => 'Hoja de Bautizo Clavel'],
        ['href' => 'index.php?report=aplicaciones', 'report' => 'aplicaciones', 'dir' => 'views/applications.php', 'icon' => 'science', 'label' => 'Aplicaciones de Insumos'],
        ['href' => 'index.php?report=reporte_cosecha_lonas', 'report' => 'reporte_cosecha_lonas', 'dir' => 'views/reporte_cosecha_lonas.php', 'icon' => 'shopping_bag', 'label' => 'Reporte Lonas Semanal'],
        ['href' => 'index.php?report=evaluacion_cosechas', 'report' => 'evaluacion_cosechas', 'dir' => 'views/growplanting.php', 'icon' => 'fact_check', 'label' => 'Evaluación de Cosechas'],
    ];
    $visCultivo = array_filter($menuCultivo, function ($i) use ($can) {
        return $can($i['dir']);
    });

    // Siembras
    $menuSiembras = [
        ['href' => 'index.php?report=cargar_archivos', 'report' => 'cargar_archivos', 'dir' => 'views/loadfiles.php', 'icon' => 'cloud_upload', 'label' => 'Cargar Archivos'],
        ['href' => 'index.php?report=labores_siembra', 'report' => 'labores_siembra', 'dir' => 'views/labors_form.php', 'icon' => 'edit_calendar', 'label' => 'Registra Labores de Siembra'],
        ['href' => 'index.php?report=crud_emv', 'report' => 'crud_emv', 'dir' => 'views/entrada_material_vegetal.php', 'icon' => 'forest', 'label' => 'Entrada Material Vegetal'],
        ['href' => 'index.php?report=programs', 'report' => 'programs', 'dir' => 'views/programs.php', 'icon' => 'event_note', 'label' => 'Editar Programa Siembras'],
        ['href' => 'index.php?report=programsf', 'report' => 'programsf', 'dir' => 'views/programsf.php', 'icon' => 'domain', 'label' => 'Editar Programa por Finca'],
        ['href' => 'index.php?table=14', 'table' => '14', 'dir' => 'tables/viewReportsP.php', 'icon' => 'summarize', 'label' => 'Generar Informes Siembras'],
        ['href' => 'index.php?report=imprimir_siembras', 'report' => 'imprimir_siembras', 'dir' => 'views/print.php', 'icon' => 'print', 'label' => 'Programa Siembras (Imprimir)'],
        ['href' => 'index.php?report=imprimir_ensartes', 'report' => 'imprimir_ensartes', 'dir' => 'views/growroot.php', 'icon' => 'content_paste', 'label' => 'Programa Ensartes (Imprimir)'],
    ];
    $visSiembras = array_filter($menuSiembras, function ($i) use ($can) {
        return $can($i['dir']);
    });

    // PowerBI
    $menuPowerBI = [
        ['href' => 'index.php?report=plano_siembras', 'report' => 'plano_siembras', 'dir' => 'views/report_pb_pto_vs_real.php', 'icon' => 'map', 'label' => 'Plano de Siembras'],
        ['href' => 'index.php?report=informe_clavel_picos', 'report' => 'informe_clavel_picos', 'dir' => 'views/report_pb_clavel.php', 'icon' => 'signal_cellular_alt', 'label' => 'Informe Clavel por Picos'],
        ['href' => 'index.php?report=proyecciones_curvas', 'report' => 'proyecciones_curvas', 'dir' => 'views/report_pb_curvas.php', 'icon' => 'show_chart', 'label' => 'Proyecciones Real - Quipus'],
        ['href' => 'index.php?report=causas_nacional', 'report' => 'causas_nacional', 'dir' => 'views/report_pb_nacional.php', 'icon' => 'pie_chart', 'label' => 'Causas Nacional'],
        ['href' => 'index.php?report=compara_produccion', 'report' => 'compara_produccion', 'dir' => 'views/report_pb_compara_prod.php', 'icon' => 'compare_arrows', 'label' => 'Compara Producción'],
    ];
    $visPowerBI = array_filter($menuPowerBI, function ($i) use ($can) {
        return $can($i['dir']);
    });

    // Reportes
    $menuReportes = [
        ['href' => 'index.php?report=reportes_produccion', 'report' => 'reportes_produccion', 'dir' => 'views/reportes_dashboard.php', 'icon' => 'bar_chart', 'label' => 'Reporte Sembrado'],
        ['href' => 'index.php?report=reportes_real_vs_teorico', 'report' => 'reportes_real_vs_teorico', 'dir' => 'views/reporte_comparativo.php', 'icon' => 'query_stats', 'label' => 'Teórico vs Real Siembras'],
        ['href' => 'index.php?report=curvas_clavel_produccion', 'report' => 'curvas_clavel_produccion', 'dir' => 'views/curvas_clavel_produccion.php', 'icon' => 'timeline', 'label' => 'Curvas Producción Clavel'],
        ['href' => 'index.php?report=seguimiento_produccion', 'report' => 'seguimiento_produccion', 'dir' => 'views/seguimiento_produccion.php', 'icon' => 'trending_up', 'label' => 'Seguimiento de Producción'],
        ['href' => 'index.php?report=demanda_tika', 'report' => 'demanda_tika', 'dir' => 'views/demanda_tika.php', 'icon' => 'difference', 'label' => 'Demanda Semanal Tika'],
        ['href' => 'index.php?report=compara_escenarios', 'report' => 'compara_escenarios', 'dir' => 'views/compara_escenarios.php', 'icon' => 'dynamic_feed', 'label' => 'Comparar Escenarios'],
        ['href' => 'index.php?report=comparador_tika', 'report' => 'comparador_tika', 'dir' => 'views/comparador_tika.php', 'icon' => 'compare', 'label' => 'Comparador Escenarios Tika'],
    ];
    $visReportes = array_filter($menuReportes, function ($i) use ($can) {
        return $can($i['dir']);
    });

    // Floreros
    $menuFloreros = [
        ['href' => 'index.php?report=registra_floreros', 'report' => 'registra_floreros', 'dir' => 'views/flowervase.php', 'icon' => 'add_circle_outline', 'label' => 'Registra Floreros'],
        ['href' => 'index.php?report=floreros_diario', 'report' => 'floreros_diario', 'dir' => 'views/florero_diario.php', 'icon' => 'smartphone', 'label' => 'Revisión Diaria (Móvil)'],
        ['href' => 'index.php?report=florero_perfil', 'report' => 'florero_perfil', 'dir' => 'views/florero_perfil.php', 'icon' => 'stars', 'label' => 'Perfil de Variedades', 'badge' => 'Nuevo'],
        ['href' => 'index.php?report=reporte_floreros', 'report' => 'reporte_floreros', 'dir' => 'views/report_fv.php', 'icon' => 'table_view', 'label' => 'Reporte Floreros (Tabular)'],
        ['href' => 'index.php?report=orders', 'report' => 'orders', 'dir' => 'views/evaluaciones_crud.php', 'icon' => 'rate_review', 'label' => 'Registra Evaluaciones'],
        ['href' => 'index.php?report=reporte_evaluaciones', 'report' => 'reporte_evaluaciones', 'dir' => 'views/report_evaluations.php', 'icon' => 'grading', 'label' => 'Reporte Evaluaciones'],
    ];
    $visFloreros = array_filter($menuFloreros, function ($i) use ($can) {
        return $can($i['dir']);
    });

    // Gestión PM (Role 1)
    $menuGestion = [];
    if ($role === 1) {
        $menuGestion = [
            ['href' => 'index.php?report=gestion_dashboard', 'report' => 'gestion_dashboard', 'icon' => 'dashboard', 'label' => 'Centro de Gestión'],
            ['href' => 'index.php?report=proyectos', 'report' => 'proyectos', 'icon' => 'folder_special', 'label' => 'Proyectos y Frentes'],
            ['href' => 'index.php?report=tareas', 'report' => 'tareas', 'icon' => 'task_alt', 'label' => 'Tareas Operativas'],
            ['href' => 'index.php?report=kanban', 'report' => 'kanban', 'icon' => 'view_kanban', 'label' => 'Kanban de Ejecución'],
            ['href' => 'index.php?report=bitacora', 'report' => 'bitacora', 'icon' => 'history_edu', 'label' => 'Bitácora y Seguimiento'],
        ];
    }

    // Determine Active Category and Item for Breadcrumbs and Open Accordion
    $activeSectionTitle = 'Inicio';
    $activeItemTitle = 'Panel Integral';

    $isTablasActive = false;
    foreach (array_merge($visTablasGeneral, $visTablasFloreros, $menuTablasSeguridad) as $item) {
        if ($isItemActive($item)) {
            $isTablasActive = true;
            $activeSectionTitle = 'Tablas';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isCultivoActive = false;
    foreach ($visCultivo as $item) {
        if ($isItemActive($item)) {
            $isCultivoActive = true;
            $activeSectionTitle = 'Cultivo';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isSiembrasActive = false;
    foreach ($visSiembras as $item) {
        if ($isItemActive($item)) {
            $isSiembrasActive = true;
            $activeSectionTitle = 'Siembras';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isPowerBIActive = false;
    foreach ($visPowerBI as $item) {
        if ($isItemActive($item)) {
            $isPowerBIActive = true;
            $activeSectionTitle = 'PowerBI';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isReportesActive = false;
    foreach ($visReportes as $item) {
        if ($isItemActive($item)) {
            $isReportesActive = true;
            $activeSectionTitle = 'Reportes';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isFlorerosActive = false;
    foreach ($visFloreros as $item) {
        if ($isItemActive($item)) {
            $isFlorerosActive = true;
            $activeSectionTitle = 'Floreros';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $isGestionActive = false;
    foreach ($menuGestion as $item) {
        if ($isItemActive($item)) {
            $isGestionActive = true;
            $activeSectionTitle = 'Gestión PM';
            $activeItemTitle = $item['label'];
            break;
        }
    }

    $GLOBALS['appActiveBreadcrumb'] = [
        'section' => $activeSectionTitle,
        'item' => $activeItemTitle
    ];
    ?>

    <!-- =========================================================================
     MODERN APP SIDEBAR NAVIGATION (INVERPALMAS)
     ========================================================================= -->
    <aside id="appSidebar" class="app-sidebar" aria-label="Menú principal">
        <!-- Header: Brand & Close -->
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <img src="img/logo.png" alt="Inverpalmas">
                <span class="sidebar-brand-badge">2026</span>
            </a>
            <button id="sidebarCloseBtn" class="btn-sidebar-close d-lg-none" type="button" title="Cerrar menú">
                <span class="material-icons">close</span>
            </button>
        </div>

        <!-- Live Instant Filter Input -->
        <div class="sidebar-search-box">
            <div class="sidebar-search-wrapper">
                <span class="material-icons sidebar-search-icon">search</span>
                <input type="text" id="sidebarSearchInput" class="sidebar-search-input"
                    placeholder="Buscar módulo o CRUD..." autocomplete="off">
                <button type="button" id="sidebarSearchClear" class="sidebar-search-clear"
                    title="Limpiar búsqueda">&times;</button>
            </div>
        </div>

        <!-- Navigation Groups -->
        <div class="sidebar-nav-container">
            <div class="sidebar-section-title">Módulos del Sistema</div>

            <!-- 1. Tablas & Catálogos -->
            <?php if ($showTablas): ?>
                <div class="sidebar-group <?php echo $isTablasActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isTablasActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">grid_view</span>
                            <span>Tablas & Catálogos</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visTablasGeneral as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <?php if (!empty($visTablasFloreros)): ?>
                            <li class="sidebar-section-title text-muted mt-2 mb-1" style="font-size: 0.65rem;">Floreros</li>
                            <?php foreach ($visTablasFloreros as $item): ?>
                                <?php $act = $isItemActive($item); ?>
                                <li class="sidebar-subitem">
                                    <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                        <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                        <span><?php echo $item['label']; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($menuTablasSeguridad)): ?>
                            <li class="sidebar-section-title text-danger mt-2 mb-1" style="font-size: 0.65rem;">Seguridad y Accesos
                            </li>
                            <?php foreach ($menuTablasSeguridad as $item): ?>
                                <?php $act = $isItemActive($item); ?>
                                <li class="sidebar-subitem">
                                    <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                        <span
                                            class="material-icons sidebar-sublink-icon text-danger"><?php echo $item['icon']; ?></span>
                                        <span
                                            class="<?php echo $item['report'] === 'roles_matrix' ? 'fw-bold text-success' : ''; ?>"><?php echo $item['label']; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 2. Cultivo -->
            <?php if (!empty($visCultivo)): ?>
                <div class="sidebar-group <?php echo $isCultivoActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isCultivoActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">eco</span>
                            <span>Cultivo</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visCultivo as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 3. Siembras -->
            <?php if (!empty($visSiembras)): ?>
                <div class="sidebar-group <?php echo $isSiembrasActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isSiembrasActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">agriculture</span>
                            <span>Siembras</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visSiembras as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 4. PowerBI -->
            <?php if (!empty($visPowerBI)): ?>
                <div class="sidebar-group <?php echo $isPowerBIActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isPowerBIActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">analytics</span>
                            <span>PowerBI & Analítica</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visPowerBI as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 5. Reportes -->
            <?php if (!empty($visReportes)): ?>
                <div class="sidebar-group <?php echo $isReportesActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isReportesActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">assessment</span>
                            <span>Reportes & Curvas</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visReportes as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 6. Floreros -->
            <?php if (!empty($visFloreros)): ?>
                <div class="sidebar-group <?php echo $isFlorerosActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isFlorerosActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">filter_vintage</span>
                            <span>Floreros & Calidad</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($visFloreros as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                    <?php if (!empty($item['badge'])): ?>
                                        <span class="sidebar-sublink-badge bg-success text-white"><?php echo $item['badge']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 7. Gestión PM (Admin) -->
            <?php if ($role === 1 && !empty($menuGestion)): ?>
                <div class="sidebar-group <?php echo $isGestionActive ? 'is-open' : ''; ?>">
                    <button type="button" class="sidebar-group-btn <?php echo $isGestionActive ? 'active-group' : ''; ?>">
                        <span class="d-flex align-items-center">
                            <span class="material-icons sidebar-group-icon">assignment</span>
                            <span>Gestión PM</span>
                        </span>
                        <span class="material-icons sidebar-group-arrow">expand_more</span>
                    </button>
                    <ul class="sidebar-submenu">
                        <?php foreach ($menuGestion as $item): ?>
                            <?php $act = $isItemActive($item); ?>
                            <li class="sidebar-subitem">
                                <a href="<?php echo $item['href']; ?>" class="sidebar-sublink <?php echo $act ? 'active' : ''; ?>">
                                    <span class="material-icons sidebar-sublink-icon"><?php echo $item['icon']; ?></span>
                                    <span><?php echo $item['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>

        <!-- Footer: User Badge & Quick Logout -->
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="sidebar-user-avatar">
                    <?php echo strtoupper(substr($usuario ?: 'U', 0, 1)); ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name" title="<?php echo htmlspecialchars($usuario); ?>">
                        <?php echo htmlspecialchars($usuario); ?>
                    </div>
                    <div class="sidebar-user-role">
                        <?php echo htmlspecialchars(\App\Helpers\Auth::roleName()); ?>
                    </div>
                </div>
                <a href="logout.php" class="btn-sidebar-logout" title="Cerrar sesión">
                    <span class="material-icons" style="font-size: 1.15rem;">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Mobile Overlay Backdrop -->
    <div id="appSidebarBackdrop" class="app-sidebar-backdrop"></div>

    <?php
endif; // $role > 0

/**
 * Topbar renderer helper for layout.php
 */
function renderAppTopBar()
{
    $breadcrumb = $GLOBALS['appActiveBreadcrumb'] ?? ['section' => 'Inverpalmas', 'item' => 'Panel Integral'];
    $usuario = \App\Helpers\Auth::check() ? (\App\Helpers\Auth::user() ?? '') : '';
    ?>
    <header id="appTopBar" class="app-topbar sticky-top">
        <div class="d-flex align-items-center">
            <!-- Sidebar Collapse/Expand Button -->
            <button id="sidebarToggleBtn" class="btn-toggle-sidebar" type="button"
                title="Ocultar/Mostrar menú lateral (Ctrl+B)">
                <span class="material-icons">menu_open</span>
            </button>

            <!-- Brand Icon visible when sidebar is collapsed on desktop -->
            <a href="index.php" class="topbar-brand-collapsed">
                <img src="img/logo.png" alt="Inverpalmas">
            </a>

            <!-- Breadcrumb / Contextual Location -->
            <div class="app-topbar-breadcrumb d-none d-sm-flex align-items-center">
                <span class="crumb-parent"><?php echo htmlspecialchars($breadcrumb['section']); ?></span>
                <span class="material-icons crumb-separator">chevron_right</span>
                <span class="crumb-active"><?php echo htmlspecialchars($breadcrumb['item']); ?></span>
            </div>
        </div>

        <!-- Topbar Right Section -->
        <div class="app-topbar-actions">
            <!-- Fullscreen Toggle -->
            <button id="fullscreenToggleBtn" class="btn-topbar-icon" type="button" title="Pantalla completa">
                <span class="material-icons">fullscreen</span>
            </button>
        </div>
    </header>
    <?php
}

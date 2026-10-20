<?php
require("funciones/conexion.php");

$directorio = $GLOBALS['src'];
?>
<style>
  /* Modern styling for Navbar */
  .navbar-custom {
    background-color: #ffffff !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border-bottom: 1px solid #eef2f5;
    padding: 0.5rem 1.5rem;
    z-index: 1060 !important;
  }

  .navbar-custom .navbar-brand img {
    height: 38px;
    object-fit: contain;
    transition: transform 0.3s ease;
  }

  .navbar-custom .navbar-brand:hover img {
    transform: scale(1.02);
  }

  .nav-link-btn {
    font-weight: 500;
    font-size: 0.9rem;
    color: #495057 !important;
    border-radius: 8px;
    padding: 0.4rem 0.85rem !important;
    margin: 0 0.15rem;
    transition: all 0.2s ease-in-out;
  }

  .nav-link-btn:hover,
  .nav-link-btn:focus {
    background-color: #f1f5f3;
    color: #00796B !important;
  }

  .dropdown-custom .dropdown-menu {
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    padding: 0.6rem;
    margin-top: 10px;
    animation: navFadeIn 0.2s ease-out;
  }

  @keyframes navFadeIn {
    from {
      opacity: 0;
      transform: translateY(5px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .dropdown-custom .dropdown-item {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    color: #495057;
    font-weight: 400;
    transition: all 0.15s ease-in-out;
  }

  .dropdown-custom .dropdown-item:hover {
    background-color: #e8f5e9;
    color: #016241;
  }

  .dropdown-custom .dropdown-divider {
    margin: 0.5rem 0;
    border-top: 1px solid #f1f3f5;
  }

  /* Compact spacing for buttons on mobile */
  @media (max-width: 767.98px) {
    .navbar-custom {
      padding: 0.5rem 1rem;
    }

    .navbar-collapse {
      background: #ffffff;
      padding: 1rem 0;
      border-top: 1px solid #f1f3f5;
      margin-top: 0.5rem;
    }

    .nav-link-btn {
      margin: 0.25rem 0;
      width: 100%;
      text-align: left;
    }

    .dropdown-custom .dropdown-menu {
      box-shadow: none;
      border: 1px solid #eef2f5;
      margin-top: 0;
      padding-left: 1rem;
    }

    .navbar-text {
      margin-bottom: 0.5rem;
    }
  }
</style>

<nav class="navbar navbar-expand-md navbar-light navbar-custom fixed-top">
  <!-- Brand Logo -->
  <a class="navbar-brand" href="<?php echo $directorio; ?>/index.php">
    <img src="img/logo.png" alt="Inverpalmas Logo">
  </a>

  <?php
  if (empty($_SESSION['usuario'])) {
    $role = 0;
  } else {
    $role = $_SESSION['role'];
  }
  ?>

  <?php if ($role > 0): ?>
    <!-- Hamburger Toggler for Mobile -->
    <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collapsibleNavbar"
      aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <?php
    $can = function(string $dir) use ($conexion): bool {
        return \App\Helpers\Auth::can($dir, $conexion);
    };

    // ── DEFINICIÓN Y FILTRADO DINÁMICO DE MENÚS ──
    $menuTablasGeneral = [
        ['href' => 'index.php?report=arrangements_crud', 'dir' => 'views/arrangements_crud.php', 'icon' => 'tune', 'label' => 'CRUD Arrangements'],
        ['href' => 'index.php?report=arrangement_crud',  'dir' => 'views/arrangement_crud.php',  'icon' => 'settings', 'label' => 'CRUD Arrangement'],
        ['href' => 'index.php?report=crud_breeders',     'dir' => 'views/breeders_crud.php',     'icon' => 'biotech', 'label' => 'CRUD Breeders'],
        ['href' => 'index.php?report=crud_supplies',     'dir' => 'views/supplies_crud.php',     'icon' => 'inventory_2', 'label' => 'CRUD Supplies'],
        ['href' => 'index.php?report=crud_varieties',    'dir' => 'views/varieties_crud.php',    'icon' => 'local_florist', 'label' => 'CRUD Varieties'],
        ['href' => 'index.php?report=crud_seasons',      'dir' => 'views/seasons_crud.php',      'icon' => 'date_range', 'label' => 'CRUD Seasons'],
        ['href' => 'index.php?report=crud_greenhouses',  'dir' => 'views/greenhouses_crud.php',  'icon' => 'deck', 'label' => 'CRUD Greenhouses'],
        ['href' => 'index.php?report=tika_calendario',   'dir' => 'views/tika_calendario.php',   'icon' => 'calendar_month', 'label' => 'CRUD Calendario Tika'],
    ];
    $visTablasGeneral = array_filter($menuTablasGeneral, function($i) use ($can) { return $can($i['dir']); });

    $menuTablasFloreros = [
        ['href' => 'index.php?report=crud_fv_kinds',       'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'category', 'label' => 'CRUD Tipos'],
        ['href' => 'index.php?report=crud_fv_origens',     'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'place', 'label' => 'CRUD Orígenes'],
        ['href' => 'index.php?report=crud_fv_causes',      'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'report_problem', 'label' => 'CRUD Causas'],
        ['href' => 'index.php?report=crud_fv_evaluations', 'dir' => 'views/fv_catalogs_crud.php', 'icon' => 'grading', 'label' => 'CRUD Ítems de Evaluación'],
    ];
    $visTablasFloreros = array_filter($menuTablasFloreros, function($i) use ($can) { return $can($i['dir']); });
    $hasAdminSec = ($role == 1);
    $showTablas = !empty($visTablasGeneral) || !empty($visTablasFloreros) || $hasAdminSec;

    // Cultivo
    $menuCultivo = [
        ['href' => 'index.php?report=hoja_bautizo', 'dir' => 'views/formBautizo.php', 'label' => 'Hoja de Bautizo Clavel'],
        ['href' => 'index.php?report=aplicaciones', 'dir' => 'views/applications.php', 'label' => 'Aplicaciones de insumos por Semana'],
        ['href' => 'index.php?report=reporte_cosecha_lonas', 'dir' => 'views/reporte_cosecha_lonas.php', 'label' => 'Reporte Lonas Semanal'],
        ['href' => 'index.php?report=presupuesto_siembras', 'dir' => 'views/program.php', 'label' => 'Presupuesto de Siembras'],
        ['href' => 'index.php?report=presupuesto_area', 'dir' => 'views/programView.php', 'label' => 'Presupuesto con Asignación de área'],
        ['href' => 'index.php?report=evaluacion_cosechas', 'dir' => 'views/growplanting.php', 'label' => 'Evaluación de Cosechas'],
    ];
    $visCultivo = array_filter($menuCultivo, function($i) use ($can) { return $can($i['dir']); });

    // Siembras
    $menuSiembras = [
        ['href' => 'index.php?report=cargar_archivos', 'dir' => 'views/loadfiles.php', 'label' => 'Cargar Archivos', 'divider_after' => true],
        ['href' => 'index.php?report=labores_siembra', 'dir' => 'views/labors_form.php', 'label' => 'Registra Labores de Siembra'],
        ['href' => 'index.php?report=crud_emv', 'dir' => 'views/entrada_material_vegetal.php', 'label' => 'Entrada Material Vegetal', 'divider_after' => true],
        ['href' => 'index.php?report=programs', 'dir' => 'views/programs.php', 'label' => 'Editar programa siembras'],
        ['href' => 'index.php?report=programsf', 'dir' => 'views/programsf.php', 'label' => 'Editar programa por finca'],
        ['href' => 'index.php?report=plano_consulta', 'dir' => 'views/plano_consulta.php', 'label' => 'Plano Consulta', 'divider_after' => true],
        ['href' => 'index.php?table=14', 'dir' => 'tables/viewReportsP.php', 'label' => 'Generar Informes de Propagación y Siembras'],
        ['href' => 'index.php?report=imprimir_siembras', 'dir' => 'views/print.php', 'label' => 'Programa de Siembras - Imprimir'],
        ['href' => 'index.php?report=imprimir_ensartes', 'dir' => 'views/growroot.php', 'label' => 'Programa de Ensartes - Imprimir'],
    ];
    $visSiembras = array_filter($menuSiembras, function($i) use ($can) { return $can($i['dir']); });

    // PowerBI
    $menuPowerBI = [
        ['href' => 'index.php?report=plano_siembras', 'dir' => 'views/report_pb_pto_vs_real.php', 'label' => 'Plano de siembras'],
        ['href' => 'index.php?report=informe_clavel_picos', 'dir' => 'views/report_pb_clavel.php', 'label' => 'Informe de Clavel por picos'],
        ['href' => 'index.php?report=proyecciones_curvas', 'dir' => 'views/report_pb_curvas.php', 'label' => 'Proyecciones real - quipus - inver'],
        ['href' => 'index.php?report=causas_nacional', 'dir' => 'views/report_pb_nacional.php', 'label' => 'Causas Nacional'],
        ['href' => 'index.php?report=compara_produccion', 'dir' => 'views/report_pb_compara_prod.php', 'label' => 'Compara Producción'],
    ];
    $visPowerBI = array_filter($menuPowerBI, function($i) use ($can) { return $can($i['dir']); });

    // Reportes
    $menuReportes = [
        ['href' => 'index.php?report=reportes_produccion', 'dir' => 'views/reportes_dashboard.php', 'label' => 'Reporte Sembrado'],
        ['href' => 'index.php?report=reportes_real_vs_teorico', 'dir' => 'views/reporte_comparativo.php', 'label' => 'Teórico vs Real Siembras'],
        ['href' => 'index.php?report=curvas_clavel_produccion', 'dir' => 'views/curvas_clavel_produccion.php', 'label' => 'Curvas Producción Clavel'],
        ['href' => 'index.php?report=seguimiento_produccion', 'dir' => 'views/seguimiento_produccion.php', 'label' => 'Seguimiento de producción'],
        ['href' => 'index.php?report=demanda_tika', 'dir' => 'views/demanda_tika.php', 'label' => 'Demanda Semanal'],
        ['href' => 'index.php?report=compara_escenarios', 'dir' => 'views/compara_escenarios.php', 'label' => 'Comparar Escenarios'],
    ];
    $visReportes = array_filter($menuReportes, function($i) use ($can) { return $can($i['dir']); });

    // Floreros
    $menuFloreros = [
        ['href' => 'index.php?report=registra_floreros', 'dir' => 'views/flowervase.php', 'label' => 'Registra Floreros'],
        ['href' => 'index.php?report=floreros_diario', 'dir' => 'views/florero_diario.php', 'label' => 'Revisión diaria (móvil)'],
        ['href' => 'index.php?report=florero_perfil', 'dir' => 'views/florero_perfil.php', 'label' => 'Perfil de Variedades (Nuevo)', 'bold' => true],
        ['href' => 'index.php?report=reporte_floreros', 'dir' => 'views/report_fv.php', 'label' => 'Reporte Floreros (Tabular)', 'divider_after' => true],
        ['href' => 'index.php?report=orders', 'dir' => 'views/evaluaciones_crud.php', 'label' => 'Registra Evaluaciones'],
        ['href' => 'index.php?report=reporte_evaluaciones', 'dir' => 'views/report_evaluations.php', 'label' => 'Reporte Evaluaciones'],
    ];
    $visFloreros = array_filter($menuFloreros, function($i) use ($can) { return $can($i['dir']); });
    ?>

    <!-- Unified Collapsible Menu -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav mr-auto">

        <!-- Tablas Dropdown -->
        <?php if ($showTablas): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuTablas" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Tablas
            </a>
            <div class="dropdown-menu" aria-labelledby="menuTablas">
              <?php foreach ($visTablasGeneral as $item): ?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo $item['href']; ?>">
                  <span class="material-icons mr-2 text-muted" style="font-size:1.1rem;"><?php echo $item['icon']; ?></span> <?php echo $item['label']; ?>
                </a>
              <?php endforeach; ?>

              <?php if (!empty($visTablasFloreros)): ?>
                <?php if (!empty($visTablasGeneral)): ?><div class="dropdown-divider"></div><?php endif; ?>
                <h6 class="dropdown-header">Floreros</h6>
                <?php foreach ($visTablasFloreros as $item): ?>
                  <a class="dropdown-item d-flex align-items-center" href="<?php echo $item['href']; ?>">
                    <span class="material-icons mr-2 text-muted" style="font-size:1.1rem;"><?php echo $item['icon']; ?></span> <?php echo $item['label']; ?>
                  </a>
                <?php endforeach; ?>
              <?php endif; ?>

              <?php if ($hasAdminSec): ?>
                <?php if (!empty($visTablasGeneral) || !empty($visTablasFloreros)): ?><div class="dropdown-divider"></div><?php endif; ?>
                <h6 class="dropdown-header text-danger font-weight-bold">Seguridad y Accesos</h6>
                <a class="dropdown-item d-flex align-items-center" href="index.php?report=crud_users"><span class="material-icons mr-2 text-danger" style="font-size:1.1rem;">people_alt</span> Usuarios</a>
                <a class="dropdown-item d-flex align-items-center font-weight-bold" href="index.php?report=roles_matrix" style="color: #00796B !important;"><span class="material-icons mr-2" style="font-size:1.1rem; color:#00796B;">grid_view</span> Matriz de Permisos</a>
                <a class="dropdown-item d-flex align-items-center text-muted" href="index.php?report=crud_roles"><span class="material-icons mr-2 text-secondary" style="font-size:1.1rem;">security</span> Permisos por Rol (Lista)</a>
                <a class="dropdown-item d-flex align-items-center" href="index.php?report=login_logs"><span class="material-icons mr-2 text-danger" style="font-size:1.1rem;">manage_history</span> Bitácora de Accesos</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- Cultivo Dropdown -->
        <?php if (!empty($visCultivo)): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuCultivo" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Cultivo
            </a>
            <div class="dropdown-menu" aria-labelledby="menuCultivo">
              <?php foreach ($visCultivo as $item): ?>
                <a class="dropdown-item" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- Siembras Dropdown -->
        <?php if (!empty($visSiembras)): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuSiembras" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Siembras
            </a>
            <div class="dropdown-menu" aria-labelledby="menuSiembras">
              <?php foreach ($visSiembras as $item): ?>
                <a class="dropdown-item" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
                <?php if (!empty($item['divider_after'])): ?><div class="dropdown-divider"></div><?php endif; ?>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- PowerBI Dropdown -->
        <?php if (!empty($visPowerBI)): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuPowerBI" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              PowerBI
            </a>
            <div class="dropdown-menu" aria-labelledby="menuPowerBI">
              <?php foreach ($visPowerBI as $item): ?>
                <a class="dropdown-item" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- Reportes Dropdown -->
        <?php if (!empty($visReportes)): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuReportes" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Reportes
            </a>
            <div class="dropdown-menu" aria-labelledby="menuReportes">
              <?php foreach ($visReportes as $item): ?>
                <a class="dropdown-item" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- Floreros Dropdown -->
        <?php if (!empty($visFloreros)): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuFloreros" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Floreros
            </a>
            <div class="dropdown-menu" aria-labelledby="menuFloreros">
              <?php foreach ($visFloreros as $item): ?>
                <a class="dropdown-item<?php echo !empty($item['bold']) ? ' font-weight-bold text-success' : ''; ?>" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
                <?php if (!empty($item['divider_after'])): ?><div class="dropdown-divider"></div><?php endif; ?>
              <?php endforeach; ?>
            </div>
          </li>
        <?php endif; ?>

        <!-- Gestión (Solo Admin) Dropdown -->
        <?php if ($role == 1): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuGestion" role="button" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              Gestión PM
            </a>
            <div class="dropdown-menu" aria-labelledby="menuGestion">
              <a class="dropdown-item font-weight-bold" href="index.php?report=gestion_dashboard"
                style="color: #00796B !important;">Centro de gestión</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="index.php?report=proyectos">Proyectos y frentes</a>
              <a class="dropdown-item" href="index.php?report=tareas">Tareas operativas</a>
              <a class="dropdown-item" href="index.php?report=kanban">Kanban de ejecución</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="index.php?report=bitacora">Bitácora y seguimiento</a>
            </div>
          </li>
        <?php endif; ?>

      </ul>

      <!-- User Greeting & Logout (Right Align) -->
      <div class="navbar-nav ml-auto align-items-center flex-column flex-md-row pt-2 pt-md-0">
        <span class="navbar-text mr-md-4 mb-2 mb-md-0 font-weight-bold text-dark d-flex align-items-center"
          style="font-size: 0.9rem;">
          <span class="material-icons mr-2" style="font-size: 1.25rem; color: #00796B;">account_circle</span>
          <span><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
          <span class="badge badge-pill badge-light border ml-2 text-dark font-weight-normal py-1 px-2" style="font-size: 0.72rem; background: #eef2f5;">
            <?php echo htmlspecialchars(\App\Helpers\Auth::roleName()); ?>
          </span>
        </span>
        <a class="btn btn-sm btn-outline-danger px-3 d-flex align-items-center justify-content-center"
          href="<?php echo $directorio; ?>/logout.php"
          style="border-radius: 20px; font-weight: 500; font-size: 0.85rem; border-width: 1.5px; transition: all 0.2s;">
          <span class="material-icons mr-1" style="font-size: 1rem;">logout</span>
          Salir
        </a>
      </div>

    </div>
  <?php endif; ?>
</nav>
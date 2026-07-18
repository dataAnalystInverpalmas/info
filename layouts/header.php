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

  .nav-link-btn:hover, .nav-link-btn:focus {
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
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
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
  if (empty($_SESSION['usuario'])){
    $role = 0;
  } else {
    $role = $_SESSION['role']; 
  }
  ?>

  <?php if ($role > 0): ?>
    <!-- Hamburger Toggler for Mobile -->
    <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Unified Collapsible Menu -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav mr-auto">
        
        <!-- Tablas Dropdown -->
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuTablas" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Tablas
          </a>
          <div class="dropdown-menu" aria-labelledby="menuTablas">
            <a class="dropdown-item" href="index.php?report=arrangements_crud">CRUD Arrangements</a>
            <a class="dropdown-item" href="index.php?report=arrangement_crud">CRUD Arrangement</a>
            <a class="dropdown-item" href="index.php?report=crud_breeders">CRUD Breeders</a>
            <a class="dropdown-item" href="index.php?report=crud_supplies">CRUD Supplies</a>
            <a class="dropdown-item" href="index.php?report=crud_varieties">CRUD Varieties</a>
            <a class="dropdown-item" href="index.php?report=crud_seasons">CRUD Seasons</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?report=crud_users">CRUD Users</a>
            <a class="dropdown-item" href="index.php?report=crud_roles">CRUD Roles</a>
          </div>
        </li>

        <!-- Cultivo Dropdown -->
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuCultivo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Cultivo
          </a>
          <div class="dropdown-menu" aria-labelledby="menuCultivo">
            <a class="dropdown-item" href="index.php?report=1">Hoja de Bautizo Clavel</a>
            <a class="dropdown-item" href="index.php?report=2">Aplicaciones de insumos por Semana</a>
            <a class="dropdown-item" href="index.php?report=4">Presupuesto de Siembras</a>
            <a class="dropdown-item" href="index.php?report=5">Presupuesto con Asignación de área</a>
            <a class="dropdown-item" href="index.php?report=8">Evaluación de Cosechas</a>
          </div>
        </li>

        <!-- Informes Dropdown 
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Informes
          </a>
          <div class="dropdown-menu" aria-labelledby="menuInformes">
            <a class="dropdown-item" href="index.php?report=102">Info de bloques</a>
          </div>
        </li> -->

        <!-- Siembras Dropdown -->
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuSiembras" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Siembras
          </a>
          <div class="dropdown-menu" aria-labelledby="menuSiembras">
            <a class="dropdown-item" href="index.php?report=1005">Cargar Archivos</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?report=14">Registra Labores de Siembra</a>
            <a class="dropdown-item" href="index.php?report=crud_emv">Entrada Material Vegetal</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?report=programs">Editar programa siembras</a>
            <a class="dropdown-item" href="index.php?report=programsf">Editar programa por finca</a>
            <a class="dropdown-item" href="index.php?report=plano_consulta">Plano Consulta</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?table=14">Generar Informes de Propagación y Siembras</a>
            <a class="dropdown-item" href="index.php?report=6">Programa de Siembras - Imprimir</a>
            <a class="dropdown-item" href="index.php?report=7">Programa de Ensartes - Imprimir</a>
          </div>
        </li>

        <!-- PowerBI Dropdown -->
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuPowerBI" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            PowerBI
          </a>
          <div class="dropdown-menu" aria-labelledby="menuPowerBI">
            <a class="dropdown-item" href="index.php?report=108">Plano de siembras</a>
            <a class="dropdown-item" href="index.php?report=103">Informe de Clavel por picos</a>
            <a class="dropdown-item" href="index.php?report=104">Proyecciones real - quipus - inver</a>
            <a class="dropdown-item" href="index.php?report=106">Demandas</a>
            <a class="dropdown-item" href="index.php?report=107">Compara Producción</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?report=109">Proyecciones JL</a>
            <a class="dropdown-item" href="index.php?report=curvas">Curvas Clavel</a>
          </div>
        </li>

        <!-- Floreros Dropdown -->
        <li class="nav-item dropdown dropdown-custom">
          <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuFloreros" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Floreros
          </a>
          <div class="dropdown-menu" aria-labelledby="menuFloreros">
            <a class="dropdown-item" href="index.php?report=12">Registra Floreros</a>
            <a class="dropdown-item" href="index.php?report=52">Reporte Floreros</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="index.php?report=orders">Registra Evaluaciones</a>
            <a class="dropdown-item" href="index.php?report=53">Reporte Evaluaciones</a>
          </div>
        </li>

        <!-- Gestión (Solo Admin) Dropdown -->
        <?php if ($role == 1): ?>
          <li class="nav-item dropdown dropdown-custom">
            <a class="nav-link dropdown-toggle nav-link-btn" href="#" id="menuGestion" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Gestión PM
            </a>
            <div class="dropdown-menu" aria-labelledby="menuGestion">
              <a class="dropdown-item font-weight-bold" href="index.php?report=205" style="color: #00796B !important;">Centro de gestión</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="index.php?report=200">Proyectos y frentes</a>
              <a class="dropdown-item" href="index.php?report=201">Tareas operativas</a>
              <a class="dropdown-item" href="index.php?report=203">Kanban de ejecución</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="index.php?report=202">Bitácora y seguimiento</a>
            </div>
          </li>
        <?php endif; ?>

      </ul>

      <!-- User Greeting & Logout (Right Align) -->
      <div class="navbar-nav ml-auto align-items-center flex-column flex-md-row pt-2 pt-md-0">
        <span class="navbar-text mr-md-4 mb-2 mb-md-0 font-weight-bold text-dark d-flex align-items-center" style="font-size: 0.9rem;">
          <span class="material-icons mr-2" style="font-size: 1.25rem; color: #00796B;">account_circle</span>
          <?php echo htmlspecialchars($_SESSION['usuario']); ?>
        </span>
        <a class="btn btn-sm btn-outline-danger px-3 d-flex align-items-center justify-content-center" href="<?php echo $directorio; ?>/logout.php" style="border-radius: 20px; font-weight: 500; font-size: 0.85rem; border-width: 1.5px; transition: all 0.2s;">
          <span class="material-icons mr-1" style="font-size: 1rem;">logout</span>
          Salir
        </a>
      </div>
      
    </div>
  <?php endif; ?>
</nav>


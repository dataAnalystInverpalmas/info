<?php
require("funciones/conexion.php");

$directorio = $GLOBALS['src'];
?>
<style media="screen"></style>
<nav class="navbar navbar-expand-md bg-light navbar-light fixed-top"> <!--fixed-top-->
<?php
    if (empty($_SESSION['usuario'])){
    ?>  
  	 <a class="navbar-brand" href="<?php echo $directorio; ?>/index.php">
    <?php  
    }
    else {
    ?>  
      <a class="navbar-brand" href="<?php echo $directorio; ?>/index.php">
    <?php    
      }
    ?>        
    <img src="img/logo.png" alt="Logo" style="width:160px;"></a>  
      <?php
      if (empty($_SESSION['usuario'])){
          $role=0;
        }else{
      ?>
          <!-- <a class="navbar-brand" href="http://172.10.18.133/info/home.php">
          <span class="btn btn-sm btn-outline-primary my-2 my-sm-0"> -->
      <?php
          /* echo $_SESSION['usuario'];*/
          $role=$_SESSION['role']; 
      ?>
        </span></a>
      <?php  
        }
      ?>

    <?php if ($role<>'0'){ ?>
    <a class="navbar-brand" href="<?php echo $directorio; ?>/logout.php">
    <span style="color:red">X</span></a>
    <?php } ?>
<?php if ($role > 0)
{
  //ob_start(); 
?>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
</button>

  <div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
	<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
      Tablas
  </button>
	<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
      <!--<a class="dropdown-item" href="index.php?table=1">Cargar Plano de Siembra</a> -->
      <!--<a class="dropdown-item" href="index.php?table=2">Cargar Variedades</a>-->
      <!--<a class="dropdown-item" href="index.php?table=3">Cargar Presupuesto de Siembras</a>-->
      <!--<a class="dropdown-item" href="index.php?table=4">Cargar Temporadas</a>-->
      <!--<a class="dropdown-item" href="index.php?table=12">Cargar Presupuesto de Siembras con Asignaciones de Area</a>-->
      <!--<a class="dropdown-item" href="index.php?table=generateViewBudget">Generar Datos Pto Siembras</a>-->
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
  </div>

    <div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
	<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
      Cultivo
    </button>
	<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
      <a class="dropdown-item" href="index.php?report=1">Hoja de Bautizo Clavel</a>
      <a class="dropdown-item" href="index.php?report=2">Aplicaciones de insumos por Semana</a>
      <!--<a class="dropdown-item" href="index.php?report=3">Presupuesto de Siembras Adicional</a>-->
      <a class="dropdown-item" href="index.php?report=4">Presupuesto de Siembras</a>
      <a class="dropdown-item" href="index.php?report=5">Presupuesto con AsignaciÃ³n de Ã¡rea</a>
			<a class="dropdown-item" href="index.php?report=8">EvaluaciÃ³n de Cosechas</a>
    </div>
  </div>

	<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
		Informes
	</button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
		<!-- <a class="dropdown-item" href="index.php?report=51">Reporte Ensartes y Cosechas</a> -->
    <a class="dropdown-item" href="index.php?report=102">Info de bloques</a>
    
	</div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  Siembras
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="index.php?report=1005">Cargar Archivos</a>
    <div class="dropdown-divider"></div>
    <!-- <a class="dropdown-item" href="index.php?report=101">Registra InformaciÃ³n</a>
    <a class="dropdown-item" href="index.php?report=9">Ubicaciones</a>
    <a class="dropdown-item" href="index.php?report=10">Indicadores</a> -->
    <a class="dropdown-item" href="index.php?report=14">Registra Labores de Siembra</a>
    <a class="dropdown-item" href="index.php?report=crud_emv">Entrada Material Vegetal</a>
    <!--<a class="dropdown-item" href="index.php?report=months">Registra InformaciÃ³n Reportes</a>-->
    <div class="dropdown-divider"></div>
    <!-- SubmenÃº adicional para administraciÃ³n del programa de siembras -->
    <a class="dropdown-item" href="index.php?report=programs">Editar programa siembras</a>
    <a class="dropdown-item" href="index.php?report=programsf">Editar programa por finca</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="index.php?table=14">Generar Informes de PropagaciÃ³n y Siembras</a>
    <a class="dropdown-item" href="index.php?report=6">Programa de Siembras - Imprimir</a>
		<a class="dropdown-item" href="index.php?report=7">Programa de Ensartes - Imprimir</a>
  </div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  PowerBI
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="index.php?report=108">Plano de siembras</a>
    <a class="dropdown-item" href="index.php?report=103">Informe de Clavel por picos</a>
    <a class="dropdown-item" href="index.php?report=104">Proyecciones real - quipus - inver</a>
    <a class="dropdown-item" href="index.php?report=106">Demandas</a>
    <a class="dropdown-item" href="index.php?report=107">Compara ProducciÃ³n</a>
  </div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  Floreros
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="index.php?report=12">Registra Floreros</a>
    <a class="dropdown-item" href="index.php?report=52">Reporte Floreros</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="index.php?report=orders">Registra Evaluaciones</a>
    <a class="dropdown-item" href="index.php?report=53">Reporte Evaluaciones</a>
  </div>
</div>

<?php if ($role == 1): ?>
<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-dark my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
    Gestión
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="index.php?report=200">Proyectos</a>
    <a class="dropdown-item" href="index.php?report=201">Tareas</a>
    <a class="dropdown-item" href="index.php?report=202">Bitácora</a>
    Kanban:
    <a class="dropdown-item" href="index.php?report=203">Tablero</a>
  </div>
</div>
<?php endif; ?>

<?php } ?>
</nav>


<?php
require("funciones/conexion.php");

$directorio = $_GLOBALS['src'];
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
      <a class="navbar-brand" href="<?php echo $directorio; ?>/home.php">
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
	<div class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenuOffset">
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=loadEmployees">Cargar Empleados</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=1">Cargar Plano de Siembra</a> -->
      <a class="dropdown-item" href="home.php?menu=tables&table=2">Cargar Variedades</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=3">Cargar Presupuesto de Siembras</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=4">Cargar Temporadas</a>
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=5">Cargar Datos Fusarium</a>-->
      <a class="dropdown-item" href="home.php?menu=tables&table=6">Cargar Arreglos</a>
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=7">Cargar Empresas</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=8">Cargar Fincas</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=9">Cargar Productos</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=10">Cargar Variedades Adicionales</a> -->
			<!-- <a class="dropdown-item" href="home.php?menu=tables&table=11">Cargar Areas de Producción</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=12">Cargar Presupuesto de Siembras con Asignaciones de Area</a> -->
      <a class="dropdown-item" href="home.php?menu=tables&table=13">Cargar Plano Historico de Siembras</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=14">Generar Informes de Propagación y Siembras</a>
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=16">Cargar Labores Presiembra</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=17">Cargar Presupuesto Adicionales con Asignación</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=18">Cargar Planos de Bloques</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=19">Cargar Presupuesto Adicionales</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=loadQualities">Cargar Tabla Calidades</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=loadEvaluations">Cargar Tabla Evaluaciones</a>
      <a class="dropdown-item" href="home.php?menu=tables&table=loadNalcauses">Cargar Tabla Causas Nacional</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=loadComments">Cargar Tabla Comentarios</a> -->
      <a class="dropdown-item" href="home.php?menu=tables&table=loadCurves">Cargar Tabla Curvas</a>
      <!--<a class="dropdown-item" href="home.php?menu=tables&table=loadFeatures">Cargar Tabla Caracteristicas</a> -->
      <a class="dropdown-item" href="home.php?menu=tables&table=loadSupervisors">Cargar Tabla Supervisores</a>
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=loadAssistances">Cargar Tabla Asistencias</a> -->
      <!-- <a class="dropdown-item" href="home.php?menu=tables&table=withoutdatacovid">Verificar personal sin datos COVID</a> -->
      <a class="dropdown-item" href="home.php?menu=tables&table=generateViewBudget">Generar Datos Pto Siembras</a>
    </div>
  </div>

    <div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
	<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
      Siembras
    </button>
	<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
      <a class="dropdown-item" href="home.php?menu=tables&report=1">Hoja de Bautizo Clavel</a>
      <a class="dropdown-item" href="home.php?menu=tables&report=2">Aplicaciones de insumos por Semana</a>
      <a class="dropdown-item" href="home.php?menu=tables&report=3">Presupuesto de Siembras Adicional</a>
      <a class="dropdown-item" href="home.php?menu=tables&report=4">Presupuesto de Siembras</a>
      <a class="dropdown-item" href="home.php?menu=tables&report=5">Presupuesto con Asignación de área</a>
			<a class="dropdown-item" href="home.php?menu=tables&report=6">Programa de Siembras - Imprimir</a>
			<a class="dropdown-item" href="home.php?menu=tables&report=7">Programa de Ensartes - Imprimir</a>
			<a class="dropdown-item" href="home.php?menu=tables&report=8">Evaluación de Cosechas</a>
    </div>
  </div>

	<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
		Informes
	</button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
		<!-- <a class="dropdown-item" href="home.php?menu=tables&report=50">Reporte Fusarium</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=51">Reporte Ensartes y Cosechas</a> -->
    <a class="dropdown-item" href="home.php?menu=tables&report=102">Info de bloques</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=13">Reporte Calidades</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=53">Reporte Evaluaciones</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=105">Trazabilidad</a>
	</div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  Información
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="home.php?menu=tables&report=1005">Cargar Archivos</a>
    <!-- <a class="dropdown-item" href="home.php?menu=tables&report=101">Registra Información</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=9">Ubicaciones</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=10">Indicadores</a> -->
    <a class="dropdown-item" href="home.php?menu=tables&report=11">Registra Clasificación</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=14">Registra Labores de Siembra</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=orders">Registra Evaluaciones</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=months">Registra Información Reportes</a>
  </div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  PowerBI
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="home.php?menu=tables&report=103">Informe de Clavel</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=104">Proyecciones real - quipus - inver</a>
  </div>
</div>

<div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
  <button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  Floreros
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
    <a class="dropdown-item" href="home.php?menu=tables&report=12">Registra Floreros</a>
    <a class="dropdown-item" href="home.php?menu=tables&report=52">Reporte Floreros</a>
  </div>
</div>

<!-- <div class="btn-group collapse navbar-collapse" id="collapsibleNavbar">
<button type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
  COVID-19
</button>
<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
  <a class="dropdown-item" href="home.php?menu=tables&report=1001">Configuración</a>
  <a class="dropdown-item" href="home.php?menu=tables&report=1000">Ingreso de Temperaturas</a>
  <a class="dropdown-item" href="home.php?menu=tables&report=1002">Reporte</a>
  <a class="dropdown-item" href="home.php?menu=tables&report=1004">¿Quienes No Registran?</a>
</div>
</div> -->

<?php } ?>
</nav>

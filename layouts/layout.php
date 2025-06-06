<!DOCTYPE html>
<html lang="es">
<head>
<title>Inverpalmas</title>
    <!-- Required meta tags -->
<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


<link rel="stylesheet" type="text/css" href="scripts/stylesSP.css">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="scripts/print.css" rel="stylesheet">
    <link href="scripts/myStyles.css" rel="stylesheet" media="screen" type="text/css">
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<!-- DataTables FixedColumns CSS -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="DataTables/SearchBuilder-1.0.1/css/searchBuilder.dataTables.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="DataTables/DataTables-1.10.23/css/dataTables.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
		<!-- jQuery -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- DataTables JS -->
	<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<!-- DataTables FixedColumns JS -->
	<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="bootstrap/dist/js/JsBarcode.all.min.js"></script>

<!-- Polyfill para ClipboardItem -->
<script>
    if (typeof ClipboardItem === 'undefined') {
      window.ClipboardItem = function (items) {
        return items;
      };
    }
</script>

<!-- Custom JS file --> 
<!-- Loading Javascript -->
<?php setlocale(LC_TIME,"es_CO"); ?>
</head>
<body>
	<style>      
	  @media screen
		{
			body 
			{
				padding-top: 5rem;
			}
		}
	</style>
<header>
	<?php
		require_once('header.php');
	?>
</header>
<section>
	<div class="container-fluid">
	<?php
			// carga el archivo routing.php para direccionar a la página .php que se incrustará entre la header y el footer
			require_once('routing.php');
	 ?>
	</div>
</section>
<!--<script  src="dist/jquery-3.5.1.js">-->
</script>
<!-- Cargar scripts de DataTables y Bootstrap al final 
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>-->
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <!--<script type="text/javascript" src="/DataTables/datatables.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
    <script src="/DataTables/SearchBuilder-1.0.1/js/dataTables.searchBuilder.js"></script>
<!---footer-->
<footer>
	<?php
		include_once('footer.php');
	?>
</footer>
</body>
</html>

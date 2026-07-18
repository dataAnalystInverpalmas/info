<!DOCTYPE html>
<html lang="es">
<head>
<title>Labores Cultivo</title>
    <!-- Required meta tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/x-icon" href="img/icon.ico">


<link rel="stylesheet" type="text/css" href="scripts/stylesSP.css">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="scripts/print.css" rel="stylesheet">
    <link href="scripts/myStyles.css" rel="stylesheet" media="screen" type="text/css">
    <!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="vendor/datatables/datatables/media/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css">
	<!-- DataTables FixedColumns CSS -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.3.4/css/searchBuilder.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<!-- jQuery local para evitar timeout de CDN -->
	<script type="text/javascript" src="vendor/components/jquery/jquery.min.js"></script>
	<!-- DataTables JS local -->
	<script type="text/javascript" src="vendor/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
	<!-- DataTables FixedColumns JS -->
	<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="scripts/JsBarcode.all.min.js"></script>

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
	  :root {
		--navbar-fixed-offset: 4rem;
	  }

	  body {
		font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
		background-color: #f6f8fb !important;
		color: #2D3748 !important;
		padding-top: 4rem !important;
	  }

	  /* Global professional overrides for cards, tables, forms, buttons */
	  .card {
		border: none !important;
		border-radius: 12px !important;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
		background-color: #ffffff !important;
	  }

	  .form-control {
		border-radius: 8px !important;
		border: 1px solid #E2E8F0 !important;
		font-size: 0.9rem !important;
		padding: 0.6rem 0.75rem !important;
		height: auto !important;
		color: #4A5568 !important;
		transition: all 0.2s ease-in-out !important;
	  }

	  .form-control:focus {
		border-color: #00796B !important;
		box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.15) !important;
		outline: none !important;
	  }

	  .btn {
		border-radius: 8px !important;
		font-weight: 500 !important;
		font-size: 0.875rem !important;
		padding: 0.5rem 1rem !important;
		transition: all 0.2s ease !important;
	  }

	  .btn-success {
		background-color: #00796B !important;
		border-color: #00796B !important;
		color: #ffffff !important;
	  }

	  .btn-success:hover, .btn-success:focus, .btn-success:active {
		background-color: #004D40 !important;
		border-color: #004D40 !important;
		color: #ffffff !important;
	  }

	  .btn-outline-success {
		color: #00796B !important;
		border-color: #00796B !important;
	  }

	  .btn-outline-success:hover {
		background-color: #00796B !important;
		color: #ffffff !important;
		border-color: #00796B !important;
	  }

	  /* Soft custom background for alerts */
	  .alert {
		border: none !important;
		border-radius: 8px !important;
	  }

	  /* MODERN TABLES & DATATABLES UX OVERRIDES */
	  .table-responsive {
		border-radius: 12px !important;
		background: #ffffff !important;
		padding: 1rem !important;
		border: 1px solid #E2E8F0 !important;
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
		margin-bottom: 2rem !important;
	  }

	  table.table, table.dataTable {
		border-collapse: separate !important;
		border-spacing: 0 !important;
		width: 100% !important;
		margin: 0 !important;
		border: none !important;
	  }

	  /* Style table headers */
	  table.table thead th, table.dataTable thead th, table.dataTable thead td {
		background-color: #F8FAFC !important;
		color: #4A5568 !important;
		font-weight: 600 !important;
		font-size: 0.75rem !important;
		letter-spacing: 0.5px;
		text-transform: uppercase !important;
		padding: 14px 16px !important;
		border-bottom: 2px solid #E2E8F0 !important;
		border-top: none !important;
		outline: none !important;
	  }

	  /* Style table cells */
	  table.table tbody td, table.dataTable tbody td {
		padding: 12px 16px !important;
		font-size: 0.85rem !important;
		color: #2D3748 !important;
		border-bottom: 1px solid #EDF2F7 !important;
		border-top: none !important;
		vertical-align: middle !important;
	  }

	  /* Subtle stripe on tables */
	  table.table-striped tbody tr:nth-of-type(odd) {
		background-color: #F8FAFC !important;
	  }
	  table.table-striped tbody tr:nth-of-type(even) {
		background-color: #ffffff !important;
	  }

	  /* Modern hovered row */
	  table.table tbody tr:hover, table.dataTable tbody tr:hover {
		background-color: #F1F5F9 !important;
		transition: background-color 0.15s ease-in-out;
	  }

	  /* DataTables elements skinning */
	  .dataTables_wrapper .dataTables_length,
	  .dataTables_wrapper .dataTables_filter,
	  .dataTables_wrapper .dataTables_info,
	  .dataTables_wrapper .dataTables_paginate {
		font-size: 0.825rem !important;
		color: #718096 !important;
		margin-bottom: 1.25rem !important;
	  }

	  .dataTables_wrapper .dataTables_length select {
		border-radius: 6px !important;
		border: 1px solid #CBD5E1 !important;
		padding: 4px 8px !important;
		outline: none !important;
		margin: 0 4px !important;
		background-color: #fff !important;
	  }

	  .dataTables_wrapper .dataTables_filter input {
		border-radius: 8px !important;
		border: 1px solid #CBD5E1 !important;
		padding: 6px 12px !important;
		outline: none !important;
		margin-left: 8px !important;
		background-color: #fff !important;
		transition: all 0.2s ease !important;
	  }

	  .dataTables_wrapper .dataTables_filter input:focus {
		border-color: #00796B !important;
		box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.15) !important;
	  }

	  /* DataTables Paginate buttons override */
	  .dataTables_wrapper .dataTables_paginate .paginate_button {
		border-radius: 6px !important;
		border: 1px solid #E2E8F0 !important;
		background: #ffffff !important;
		color: #4A5568 !important;
		padding: 5px 12px !important;
		margin: 0 2px !important;
		transition: all 0.2s ease !important;
		vertical-align: middle !important;
		display: inline-block !important;
		line-height: normal !important;
	  }

	  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
		background: #EDF2F7 !important;
		color: #1A202C !important;
		border-color: #CBD5E0 !important;
	  }

	  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
	  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
		background: #00796B !important;
		color: #ffffff !important;
		border-color: #00796B !important;
		font-weight: 600 !important;
	  }

	  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
	  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
		background: #F8FAFC !important;
		color: #A0AEC0 !important;
		border-color: #EDF2F7 !important;
		cursor: not-allowed !important;
	  }

	  /* FORM GROUPS & LABELS UX OVERRIDES */
	  .form-group label, label.form-label {
		font-weight: 600 !important;
		font-size: 0.775rem !important;
		color: #4A5568 !important;
		text-transform: uppercase !important;
		letter-spacing: 0.5px !important;
		margin-bottom: 0.45rem !important;
		display: inline-block !important;
	  }

	  select.form-control {
		padding-right: 2rem !important;
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234a5568' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
		background-repeat: no-repeat !important;
		background-position: right 0.75rem center !important;
		background-size: 16px 12px !important;
		appearance: none !important;
		-webkit-appearance: none !important;
		-moz-appearance: none !important;
	  }

	  textarea.form-control {
		border-radius: 10px !important;
		padding: 0.75rem !important;
		line-height: 1.5 !important;
	  }

	  /* MODERN BOOTSTRAP MODALS OVERRIDES */
	  .modal-content {
		border: none !important;
		border-radius: 14px !important;
		box-shadow: 0 10px 45px rgba(0, 0, 0, 0.08) !important;
		overflow: hidden !important;
	  }

	  .modal-header {
		background-color: #F8FAFC !important;
		border-bottom: 1px solid #E2E8F0 !important;
		padding: 1.25rem 1.5rem !important;
	  }

	  .modal-header .modal-title {
		font-size: 1.05rem !important;
		font-weight: 600 !important;
		color: #1A202C !important;
	  }

	  .modal-header .close {
		padding: 1.25rem 1.5rem !important;
		margin: -1.25rem -1.5rem -1.25rem auto !important;
		outline: none !important;
		color: #4A5568 !important;
		opacity: 0.8 !important;
		transition: opacity 0.2s ease !important;
	  }

	  .modal-header .close:hover {
		opacity: 1 !important;
	  }

	  .modal-body {
		padding: 1.5rem !important;
		background-color: #ffffff !important;
	  }

	  .modal-footer {
		background-color: #F8FAFC !important;
		border-top: 1px solid #E2E8F0 !important;
		padding: 1rem 1.5rem !important;
	  }

	  /* Keep modal header/actions visible below the fixed navbar */
	  .modal.show {
		padding-top: var(--navbar-fixed-offset) !important;
	  }

	  .modal.show .modal-dialog {
		margin-top: 0 !important;
	  }

	  .modal.show .modal-dialog.modal-dialog-centered {
		min-height: calc(100% - var(--navbar-fixed-offset)) !important;
		align-items: flex-start !important;
	  }

	  @media (max-width: 767.98px) {
		:root {
		  --navbar-fixed-offset: 4.75rem;
		}
	  }

	  @media screen
		{
			body 
			{
				padding-top: 5.5rem;
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

<!-- Cargar scripts de DataTables y Bootstrap al final 
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>-->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <!--<script type="text/javascript" src="/DataTables/datatables.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/searchbuilder/1.3.4/js/dataTables.searchBuilder.min.js"></script>
<!---footer-->
<footer>
	<?php
		include_once('footer.php');
	?>
</footer>
</body>
</html>

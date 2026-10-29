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
	<!-- Bootstrap 5 CSS -->
	<link href="vendor/twbs/bootstrap5/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="scripts/print.css" rel="stylesheet">
	<link href="scripts/myStyles.css" rel="stylesheet" media="screen" type="text/css">
	<!-- DataTables CSS (Bootstrap 5 styling) -->
	<link rel="stylesheet" type="text/css" href="vendor/datatables/datatables/media/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/datatables/datatables/media/css/dataTables.bootstrap5.min.css">
	<!-- DataTables FixedColumns CSS -->
	<link rel="stylesheet" type="text/css"
		href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
	<link rel="stylesheet" type="text/css"
		href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
	<link href="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/css/smart_wizard_all.min.css" rel="stylesheet"
		type="text/css" />
	<link href="scripts/fonts/inter.css" rel="stylesheet">
	<link href="scripts/fonts/material-icons.css" rel="stylesheet">
	<link href="scripts/minimal_premium.css" rel="stylesheet" media="screen" type="text/css">
	<link href="scripts/sidebar_layout.css?v=3" rel="stylesheet" media="screen" type="text/css">
	<script src="scripts/sidebar_layout.js?v=3" defer></script>
	<script>
		// Evitar FOUC cuando el menú está colapsado
		(function() {
			try {
				if (localStorage.getItem('ip_sidebar_collapsed') === '1' && window.innerWidth >= 992) {
					document.documentElement.classList.add('sidebar-collapsed');
				}
			} catch (e) {}
		})();
	</script>
	<!-- Chart.js local con fallback a CDN -->
	<script src="scripts/Chart.min.js"></script>
	<script>if (typeof Chart === 'undefined') { document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"><\/script>'); }</script>
	<!-- jQuery local para evitar timeout de CDN -->
	<script type="text/javascript" src="vendor/components/jquery/jquery.min.js"></script>
	<!-- DataTables JS local y adaptador Bootstrap 5 -->
	<script type="text/javascript" src="vendor/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="vendor/datatables/datatables/media/js/dataTables.bootstrap5.min.js"></script>
	<!-- DataTables FixedColumns JS -->
	<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"
		defer></script>
	<script src="scripts/JsBarcode.all.min.js"></script>

	<!-- Configuración dinámica de rutas base y resolvedor de endpoints -->
	<?php
	$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
	$scriptDir = str_replace('\\', '/', $scriptDir);
	$appBasePath = ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
	?>
	<script>
		window.APP_BASE_PATH = '<?php echo $appBasePath; ?>';
		window.getEndpointUrl = function(path) {
			if (!path) return '';
			if (/^https?:\/\//i.test(path)) return path;
			var clean = String(path).replace(/^(\.\.\/|\/)+/, '');
			var base = (typeof window.APP_BASE_PATH !== 'undefined' && window.APP_BASE_PATH !== null)
				? window.APP_BASE_PATH
				: (window.location.pathname.match(/^(.*\/info)(?:\/.*)?$/i) ? RegExp.$1 : '');
			base = base ? base.replace(/\/+$/, '') : '';
			return (base ? base + '/' : '') + clean;
		};
	</script>

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
	<?php setlocale(LC_TIME, "es_CO"); ?>
</head>

<body class="<?php echo !empty($_SESSION['usuario']) ? 'has-app-sidebar' : ''; ?>">
	<style>
		/* Keep modal header/actions visible below the navbar */
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
			.dataTables_wrapper .dataTables_length,
			.dataTables_wrapper .dataTables_filter {
				float: none !important;
				text-align: left !important;
				width: 100% !important;
				margin-bottom: 0.65rem !important;
			}
			.dataTables_wrapper .dataTables_filter input {
				width: 100% !important;
				margin-left: 0 !important;
				margin-top: 0.25rem !important;
				display: block !important;
			}
			.dataTables_wrapper .dataTables_info,
			.dataTables_wrapper .dataTables_paginate {
				float: none !important;
				text-align: center !important;
				width: 100% !important;
				margin-top: 0.65rem !important;
			}
			.dataTables_wrapper .dataTables_paginate {
				display: flex !important;
				flex-wrap: wrap !important;
				justify-content: center !important;
				gap: 4px !important;
			}
			.dt-buttons {
				display: flex !important;
				flex-wrap: wrap !important;
				gap: 6px !important;
				margin-bottom: 0.75rem !important;
			}
			.dt-buttons .dt-button {
				flex: 1 1 auto !important;
				text-align: center !important;
			}
		}

		@media screen {
			body:not(.has-app-sidebar) {
				padding-top: 4.5rem;
			}
			body.has-app-sidebar {
				padding-top: 0 !important;
			}
		}
	</style>

	<?php
	// Carga del sidebar navigation y definiciones
	require_once('header.php');
	?>

	<div id="appMainWrapper" class="app-main-wrapper d-flex flex-column min-vh-100">
		<?php if (!empty($_SESSION['usuario'])): ?>
			<?php renderAppTopBar(); ?>
		<?php else: ?>
			<header class="app-topbar justify-content-center border-bottom bg-white py-2">
				<img src="img/logo.png" height="34" alt="Inverpalmas">
			</header>
		<?php endif; ?>

		<main class="app-content-area flex-grow-1">
			<div class="container-fluid py-3 px-3 px-md-4">
				<?php
				// carga el archivo routing.php para direccionar a la página .php que se incrustará entre la header y el footer
				require_once('routing.php');
				?>
			</div>
		</main>

		<footer class="app-footer">
			<?php
			include_once('footer.php');
			?>
		</footer>
	</div>

	<!-- Cargar scripts de DataTables Buttons y Bootstrap 5 Bundle -->
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"
		defer></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"
		defer></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"
		defer></script>
	<!-- Bootstrap 5.3 Bundle (incluye Popper v2) -->
	<script src="vendor/twbs/bootstrap5/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/smartwizard@5/dist/js/jquery.smartWizard.min.js"
		type="text/javascript"></script>

	<!-- Puente de compatibilidad dual Bootstrap 4 -> Bootstrap 5 y Toast Global -->
	<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;" id="appToastContainer"></div>
	<script>
		// Puente bidireccional para atributos legacy BS4 (data-toggle, data-target, data-dismiss)
		$(document).on('click', '[data-toggle="modal"]', function(e) {
			var target = $(this).data('target') || $(this).attr('href');
			if (target && target.charAt(0) === '#') {
				var el = document.querySelector(target);
				if (el && window.bootstrap && window.bootstrap.Modal) {
					bootstrap.Modal.getOrCreateInstance(el).show();
				}
			}
		});

		$(document).on('click', '[data-dismiss="modal"]', function(e) {
			var modalEl = $(this).closest('.modal')[0];
			if (modalEl && window.bootstrap && window.bootstrap.Modal) {
				var inst = bootstrap.Modal.getInstance(modalEl);
				if (inst) inst.hide();
				else $(modalEl).modal('hide');
			}
		});

		$(document).on('click', '[data-toggle="collapse"]', function(e) {
			var target = $(this).data('target') || $(this).attr('href');
			if (target && target.charAt(0) === '#') {
				var el = document.querySelector(target);
				if (el && window.bootstrap && window.bootstrap.Collapse) {
					bootstrap.Collapse.getOrCreateInstance(el).toggle();
				}
			}
		});

		// Inicialización automática de Tooltips y Popovers nativos de Bootstrap 5
		document.addEventListener('DOMContentLoaded', function() {
			if (window.bootstrap && window.bootstrap.Tooltip) {
				var tooltipList = document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]');
				tooltipList.forEach(function(el) {
					new bootstrap.Tooltip(el);
				});
			}
			if (window.bootstrap && window.bootstrap.Popover) {
				var popoverList = document.querySelectorAll('[data-bs-toggle="popover"], [data-toggle="popover"]');
				popoverList.forEach(function(el) {
					new bootstrap.Popover(el);
				});
			}
		});

		// Función utilitaria global para Toasts dinámicos modernos
		window.showAppToast = function(message, type, title) {
			type = type || 'success';
			var bgClass = 'bg-success text-white';
			var icon = 'check_circle';
			if (type === 'danger' || type === 'error') {
				bgClass = 'bg-danger text-white';
				icon = 'error';
			} else if (type === 'warning') {
				bgClass = 'bg-warning text-dark';
				icon = 'warning';
			} else if (type === 'info') {
				bgClass = 'bg-info text-dark';
				icon = 'info';
			}

			var toastId = 'toast_' + Date.now();
			var html = '<div id="' + toastId + '" class="toast align-items-center ' + bgClass + ' border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius:10px;">' +
				'<div class="d-flex">' +
				'<div class="toast-body d-flex align-items-center py-2 px-3" style="font-size:0.9rem;">' +
				'<span class="material-icons me-2" style="font-size:1.25rem;">' + icon + '</span>' +
				'<div>' + (title ? '<strong>' + title + '</strong><br>' : '') + message + '</div>' +
				'</div>' +
				'<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
				'</div>' +
				'</div>';

			var $container = $('#appToastContainer');
			if (!$container.length) {
				$('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;" id="appToastContainer"></div>');
				$container = $('#appToastContainer');
			}
			$container.append(html);
			var toastEl = document.getElementById(toastId);
			if (window.bootstrap && window.bootstrap.Toast) {
				var t = new bootstrap.Toast(toastEl, { delay: 4000 });
				t.show();
				toastEl.addEventListener('hidden.bs.toast', function() { $(toastEl).remove(); });
			} else {
				setTimeout(function() { $(toastEl).fadeOut(300, function() { $(this).remove(); }); }, 4000);
			}
		};
	</script>
</body>

</html>
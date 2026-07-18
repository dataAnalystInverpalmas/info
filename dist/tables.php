<?php
//lamar conexion
include ('funciones/conexion.php');

//verificar los roles	
?>
<div id="mostrar_loading" style="display:none;"></div>
<div id="mostrar_listo" style="display:none;"></div>
<div id="mostrar_error" style="display:none;"></div>
<script src="scripts/loadFiles.js"></script>
<?php
// Mostrar enlace al report programs en el menú si el rol tiene permiso
$dir_menu = "views/programs.php";
$stmt_menu = $conexion->prepare("SELECT dir FROM roles WHERE users_role = ? AND dir = ? LIMIT 1");
$role_menu = intval($_SESSION['role']);
$stmt_menu->bind_param("is", $role_menu, $dir_menu);
$stmt_menu->execute();
$rmenu = $stmt_menu->get_result();
$stmt_menu->close();
?>
<script>
	function listar_tabla(link){
		$.ajax({
			url: link,
			beforeSend: function(){
				document.getElementById("mostrar_listo").style.display="none";
				document.getElementById("mostrar_error").style.display="none";
				document.getElementById("mostrar_loading").style.display="block";
				document.getElementById("mostrar_loading").innerHTML="<div class='row justify-content-sm-center'><div class='col-sm-12 col-md-10 col-lg-10 flex-column'><div class='text-center'><img style='width: 50%; height: 50%;' src='img/loader.gif' class='rounded mx-auto img-fluid d-block'></div></div></div>";
			},
			success: function(response){                                
				document.getElementById("mostrar_loading").style.display="none";
				document.getElementById("mostrar_error").style.display="none";
				document.getElementById("mostrar_listo").style.display="block";
				document.getElementById("mostrar_listo").innerHTML="<div class='row justify-content-sm-center'><div class='col-sm-12 col-md-10 col-lg-10 flex-column'><div class='text-center'><img style='width: 60%; height: 60%;' src='img/loaded.gif' class='rounded mx-auto img-fluid d-block'></div></div></div>";
			},
			error: function(response) {
				document.getElementById("mostrar_listo").style.display="none";
				document.getElementById("mostrar_loading").style.display="none";
				document.getElementById("mostrar_error").style.display="block";
				document.getElementById("mostrar_error").innerHTML="<div class='row justify-content-sm-center'><div class='col-sm-12 col-md-10 col-lg-10 flex-column'><div class='text-center'><img style='width: 30%; height: 30%;' src='img/error.gif' class='rounded mx-auto img-fluid d-block'></div></div></div>";
  				console.log("error" + response);
			}
		});
	}

</script>

<?php
$enruta = "../";

// ============================================================
// Función centralizada para verificar permisos por rol con sistema de caché de sesión
// Opitmiza drásticamente la velocidad de carga eliminando consultas repetitivas a la base de datos
// ============================================================
function tiene_permiso($conexion, $dir) {
	if (!isset($_SESSION['allowed_dirs']) || !is_array($_SESSION['allowed_dirs'])) {
		$_SESSION['allowed_dirs'] = array();
		$role = intval($_SESSION['role']);
		$stmt = $conexion->prepare("SELECT dir FROM roles WHERE users_role = ?");
		$stmt->bind_param("i", $role);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			$_SESSION['allowed_dirs'][] = strval($row['dir']);
		}
		$stmt->close();
	}
	return in_array($dir, $_SESSION['allowed_dirs'], true);
}

// ============================================================
// RUTAS DE TABLAS (table=X) — cargan vía AJAX con listar_tabla()
// ============================================================
$tablas = array(
	'1'                  => 'tables/plane.php',
	'2'                  => 'tables/varieties.php',
	'3'                  => 'tables/program.php',
	'4'                  => 'tables/seasons.php',
	'5'                  => 'tables/fusarium.php',
	'6'                  => 'tables/arrangements.php',
	'20'                 => 'tables/arrangements_crud.php',
	'21'                 => 'tables/arrangement_crud.php',
	'22'                 => 'tables/festivos.php',
	'7'                  => 'tables/companys.php',
	'8'                  => 'tables/farms.php',
	'9'                  => 'tables/products.php',
	'10'                 => 'tables/addxvariety.php',
	'11'                 => 'tables/areas.php',
	'12'                 => 'tables/programf.php',
	'13'                 => 'tables/hplane.php',
	'14'                 => 'tables/viewReportsP.php',
	'16'                 => 'tables/laborsSowing.php',
	'17'                 => 'tables/program_add.php',
	'18'                 => 'tables/greenhouses.php',
	'19'                 => 'tables/program_add_pto.php',
	'loadEvaluations'    => 'tables/evaluations.php',
	'loadComments'       => 'tables/comments.php',
	'loadCurves'         => 'tables/curves.php',
	'loadFeatures'       => 'tables/features.php',
	'loadEmployees'      => 'tables/employees.php',
	'loadSupervisors'    => 'tables/supervisors.php',
	'loadAssistances'    => 'tables/assistances.php',
	'withoutdatacovid'   => 'tables/withoutdatacovid.php',
	'generateViewBudget' => 'tables/viewBudget.php',
);

// ============================================================
// RUTAS DE REPORTES (report=X) — cargan vía include
// ============================================================
$reportes = array(
	'1'        => 'views/formBautizo.php',
	'2'        => 'views/applications.php',
	'4'        => 'views/program.php',
	'5'        => 'views/programView.php',
	'6'        => 'views/print.php',
	'7'        => 'views/growroot.php',
	'8'        => 'views/growplanting.php',
	'12'       => 'views/flowervase.php',
	'14'       => 'views/labors_form.php',
	'52'       => 'views/report_fv.php',
	'53'       => 'views/report_evaluations.php',
	'102'      => 'views/greenhouses.php',
	'103'      => 'views/report_pb_clavel.php',
	'104'      => 'views/report_pb_curvas.php',
	'105'      => 'views/report_trazabilidad.php',
	'108'      => 'views/report_pb_pto_vs_real.php',
	'106'      => 'views/report_pb_demandas.php',
	'107'      => 'views/report_pb_compara_prod.php',
	'109'      => 'views/report_pb_jl_proyecciones.php',
	'curvas'   => 'views/report_pb_jl_curvas_clavel.php',
	'200'      => 'views/proyectos.php',
	'201'      => 'views/tareas.php',
	'202'      => 'views/bitacora.php',
	'203'      => 'views/kanban.php',
	'204'      => 'views/resumen_ejecutivo_mensual.php',
	'205'      => 'views/gestion_dashboard.php',
	'1000'     => 'covid/covid.php',
	'1001'     => 'covid/settings.php',
	'1002'     => 'covid/report.php',
	'1004'     => 'covid/reportout.php',
	'programs'         => 'views/programs.php',
	'programsf'        => 'views/programsf.php',
	'orders'           => 'views/evaluaciones_crud.php',
	'arrangements_crud' => 'views/arrangements_crud.php',
	'arrangement_crud'  => 'views/arrangement_crud.php',
	'crud_breeders'     => 'views/breeders_crud.php',
	'crud_users'        => 'views/users_crud.php',
	'crud_roles'        => 'views/roles_crud.php',
	'crud_supplies'     => 'views/supplies_crud.php',
	'crud_varieties'    => 'views/varieties_crud.php',
	'crud_seasons'      => 'views/seasons_crud.php',
	'crud_emv'          => 'views/entrada_material_vegetal.php',
	'plano_consulta'    => 'views/plano_consulta.php',
	'plano_reemplazos'  => 'views/plano_reemplazos.php',
	'1005'              => 'views/loadfiles.php',
);

// ============================================================
// PROCESAR RUTA DE TABLA
// ============================================================
if (isset($_GET['table'])) {
	$key = strval($_GET['table']);
	if (isset($tablas[$key])) {
		$dir = $tablas[$key];
		$permitido = tiene_permiso($conexion, $dir);
		if (!$permitido && ($key === '20' || $key === '21')) {
			$permitido = tiene_permiso($conexion, 'tables/arrangements.php');
		}

		if ($permitido) {
			?>
			<script>
				var link = "<?php echo $enruta . $dir; ?>";
				listar_tabla(link);
			</script>
			<?php
		} else {
			?>
			<div class="row justify-content-center align-items-center" style="min-height: 50vh;">
				<div class="col-md-6 text-center">
					<div class="p-5 bg-white border border-danger-subtle rounded-lg shadow-sm" style="border-radius: 12px;">
						<span class="material-icons text-danger mb-3" style="font-size: 4rem;">block</span>
						<h3 class="font-weight-bold text-dark mb-2">Acceso Restringido</h3>
						<p class="text-secondary small mb-4">No tiene los permisos necesarios para visualizar esta tabla. Si considera que es un error, por favor contacte al administrador.</p>
						<a href="index.php" class="btn btn-outline-secondary btn-sm px-4">Volver al Inicio</a>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		include 'views/error_404.php';
	}
}

// ============================================================
// PROCESAR RUTA DE REPORTE
// ============================================================
if (isset($_GET['report'])) {
	$key = strval($_GET['report']);
	if (isset($reportes[$key])) {
		$dir = $reportes[$key];
		$permitido = tiene_permiso($conexion, $dir);
		// Fallback: si la ruta nueva de arrangements no está en roles, usar el permiso del original
		if (!$permitido && ($dir === 'views/arrangements_crud.php' || $dir === 'views/arrangement_crud.php')) {
			$permitido = tiene_permiso($conexion, 'tables/arrangements.php');
		}
		// Fallback: CRUD nuevos de catálogos visibles para admin aunque no exista rol cargado
		if (!$permitido && in_array($dir, [
			'views/breeders_crud.php',
			'views/users_crud.php',
			'views/roles_crud.php',
			'views/supplies_crud.php',
			'views/varieties_crud.php',
			'views/seasons_crud.php',
			'views/entrada_material_vegetal.php',
			'views/evaluaciones_crud.php',
		], true) && intval($_SESSION['role']) === 1) {
			$permitido = true;
		}
		// Fallback: módulo plano consulta accesible para cualquier usuario autenticado
		if (!$permitido && in_array($dir, ['views/plano_consulta.php', 'views/plano_reemplazos.php'], true) && intval($_SESSION['role']) > 0) {
			$permitido = true;
		}
		// Fallback: evaluaciones_crud puede estar registrada como views/orders.php (ruta histórica)
		if (!$permitido && $dir === 'views/evaluaciones_crud.php') {
			$permitido = tiene_permiso($conexion, 'views/orders.php');
		}
		// Fallback: carga de archivos accesible a todos los usuarios autenticados
		if (!$permitido && $dir === 'views/loadfiles.php' && intval($_SESSION['role']) > 0) {
			$permitido = true;
		}
		// Fallback: reporte Power BI del dashboard accesible a usuarios autenticados
		if (!$permitido && $dir === 'views/report_pb_pto_vs_real.php' && intval($_SESSION['role']) > 0) {
			$permitido = true;
		}
		// Fallback: resumen ejecutivo mensual accesible a usuarios autenticados
		if (!$permitido && $dir === 'views/resumen_ejecutivo_mensual.php' && intval($_SESSION['role']) > 0) {
			$permitido = true;
		}
		// Fallback: panel de gestion accesible para administradores aunque no exista rol cargado
		if (!$permitido && $dir === 'views/gestion_dashboard.php' && intval($_SESSION['role']) === 1) {
			$permitido = true;
		}
		if ($permitido) {
			if ($dir === 'views/formBautizo.php') {
				?>
				<div class="embed-responsive">
				<iframe src="views/formBautizo.php"
					style="position: fixed; top: 60px; left: 0; width: 100vw; height: calc(100vh - 70px); border: none;"
					frameborder="0"
					scrolling="auto">
				</iframe>
				</div>
				<?php
			} else {
				include "$dir";
			}
		} else {
			?>
			<div class="row justify-content-center align-items-center" style="min-height: 50vh;">
				<div class="col-md-6 text-center">
					<div class="p-5 bg-white border border-danger-subtle rounded-lg shadow-sm" style="border-radius: 12px;">
						<span class="material-icons text-danger mb-3" style="font-size: 4rem;">block</span>
						<h3 class="font-weight-bold text-dark mb-2">Acceso Restringido</h3>
						<p class="text-secondary small mb-4">No tiene los permisos necesarios para visualizar este reporte. Si considera que es un error, por favor contacte al administrador.</p>
						<a href="index.php" class="btn btn-outline-secondary btn-sm px-4">Volver al Inicio</a>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		include 'views/error_404.php';
	}
}
?>

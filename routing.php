<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
  }else{
	include ("../funciones/conexion.php");
}

	if (empty($_SESSION['usuario'])){
		?>
		<br>
			<div class="row justify-content-sm-center">
				<div class="col-sm-6 col-md-5 flex-column">
					<h1 class="text-center">Admin Panel</h1>
					<h2 class="text-center">Iniciar Sesión</h2>
					<?php if (!empty($_GET['error'])): ?>
					<div class="alert alert-danger text-center">Usuario o contraseña incorrectos.</div>
					<?php endif; ?>
					<div>
						<form action="login.php" method="POST" name="login">
						<input type="text" class="form-control" placeholder="Username" name="email" required autofocus><br>
						<input type="password" class="form-control" placeholder="Password" name="password" required><br>
						<button class="btn btn-lg btn-primary btn-block" type="submit" name="login">
							Ingresar</button>
						</form>
					</div>
					<br>
				</div>
				<div class="col-sm-6 col-md-5 flex-column">
					<div class="text-center">
						<img src="img/inverpalmas.png" class="rounded mx-auto img-fluid d-block" alt="...">
					</div>
				</div>
			</div>

		<?php
	} else {
		
		if (!isset($_GET['report']) && !isset($_GET['table'])) {
			include 'views/dashboard_proyecciones.php';
		}
		else {
			require_once('dist/tables.php');
		}
		
}
 ?>

<style>
  .embed-responsive {
    position: relative;
    width: 100%;
    padding-bottom: 75%; /* Ajustado para mejor visualización del informe */
    overflow: hidden;
  }

  .embed-responsive iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 75%; /* Ocupa todo el espacio del contenedor */
  }

  @media print {
    .button {
      display: none;
    }
  }

  .button {
    float: right;
  }
</style>
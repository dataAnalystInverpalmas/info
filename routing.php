<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
  }else{
	include ("../funciones/conexion.php");
}

	if (empty($_SESSION['usuario'])){
		?>
		<div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 180px); padding: 1.5rem 0;">
			<div class="col-12 col-md-10 col-lg-8">
				<div class="card border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
					<div class="row no-gutters">
						<!-- Columna Izquierda: Logo y Mensaje -->
						<div class="col-md-6 d-none d-md-flex flex-column justify-content-center align-items-center text-white text-center p-5" style="background: linear-gradient(135deg, #00796B 0%, #004D40 100%);">
							<div class="mb-4">
								<img src="img/inverpalmas.png" class="img-fluid" alt="Inverpalmas Logo" style="max-width: 80%; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.15));">
							</div>
							<h3 class="font-weight-bold mb-3" style="letter-spacing: 0.5px;">Panel Administrativo</h3>
							<p class="text-white-50 small mb-0" style="max-width: 250px;">Gestión de Cultivo, Siembras y Reportes de Producción de Planta</p>
						</div>
						<!-- Columna Derecha: Formulario de Login -->
						<div class="col-md-6 p-4 p-sm-5 bg-white">
							<div class="w-100">
								<!-- Logo móvil -->
								<div class="text-center d-md-none mb-4">
									<img src="img/inverpalmas.png" class="img-fluid mb-2" alt="Inverpalmas Logo" style="max-height: 50px;">
									<h4 class="font-weight-bold text-dark">Panel Administrativo</h4>
								</div>
								
								<h2 class="h3 font-weight-bold text-dark mb-4 d-none d-md-block">Iniciar Sesión</h2>
								<p class="text-muted small mb-4">Por favor, ingrese sus credenciales para acceder al sistema.</p>
								
								<?php if (!empty($_GET['error'])): ?>
								<div class="alert alert-danger text-center py-2 px-3 mb-4" style="border-radius: 0.5rem; font-size: 0.875rem;">
									<span class="material-icons align-middle mr-1" style="font-size: 1.1rem;">error_outline</span>
									Usuario o contraseña incorrectos.
								</div>
								<?php endif; ?>
								
								<form action="login.php" method="POST" name="login" class="needs-validation">
									<div class="form-group mb-4">
										<label class="text-dark small font-weight-bold mb-1" for="emailInput">Usuario</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text bg-light border-right-0" style="border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border: 1px solid #ced4da;">
													<span class="material-icons text-muted" style="font-size: 1.2rem;">person</span>
												</span>
											</div>
											<input type="text" id="emailInput" class="form-control bg-light border-left-0 py-2" placeholder="Nombre de usuario" name="email" required autofocus style="border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; font-size: 0.95rem;">
										</div>
									</div>
									
									<div class="form-group mb-4">
										<label class="text-dark small font-weight-bold mb-1" for="passwordInput">Contraseña</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text bg-light border-right-0" style="border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border: 1px solid #ced4da;">
													<span class="material-icons text-muted" style="font-size: 1.2rem;">lock</span>
												</span>
											</div>
											<input type="password" id="passwordInput" class="form-control bg-light border-left-0 py-2" placeholder="Contraseña" name="password" required style="border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; font-size: 0.95rem;">
										</div>
									</div>
									
									<button class="btn btn-block text-white py-2 font-weight-bold transition-all" type="submit" name="login" style="background-color: #00796B; border-radius: 0.5rem; font-size: 1rem; box-shadow: 0 4px 12px rgba(0, 121, 107, 0.2); border: none;">
										Ingresar
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<style>
			.transition-all {
				transition: all 0.3s ease;
			}
			.transition-all:hover {
				background-color: #004D40 !important;
				transform: translateY(-1px);
				box-shadow: 0 6px 15px rgba(0, 77, 64, 0.3) !important;
			}
			.form-control:focus {
				box-shadow: none;
				border-color: #00796B;
				background-color: #fff !important;
			}
		</style>
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
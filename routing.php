<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
  }else{
	include ("../funciones/conexion.php");
}

	if (!\App\Helpers\Auth::check()){
		?>
		<div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 190px); padding: 2rem 0;">
			<div class="col-12 col-md-10 col-lg-8 col-xl-7">
				<div class="card border shadow-lg" style="border-radius: var(--ip-radius-xl, 18px); overflow: hidden; border-color: var(--ip-border, #E2E8F0) !important;">
					<div class="row g-0">
						<!-- Columna Izquierda: Logo y Presentación -->
						<div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-white text-center p-4 p-lg-5" style="background: linear-gradient(150deg, #00796B 0%, #004D40 100%);">
							<div class="mb-4">
								<img src="img/inverpalmas.png" class="img-fluid" alt="Inverpalmas Logo" style="max-width: 82%; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.18));">
							</div>
							<h4 class="fw-semibold text-white mb-2" style="letter-spacing: -0.02em;">Panel Integral</h4>
							<p class="text-white-50 small mb-0" style="max-width: 210px; line-height: 1.45;">Gestión de Cultivo, Siembras y Proyecciones</p>
						</div>
						<!-- Columna Derecha: Formulario de Login -->
						<div class="col-md-7 p-4 p-sm-5 bg-white d-flex align-items-center">
							<div class="w-100">
								<!-- Logo móvil -->
								<div class="text-center d-md-none mb-4">
									<img src="img/inverpalmas.png" class="img-fluid mb-2" alt="Inverpalmas Logo" style="max-height: 48px;">
									<h5 class="fw-bold text-dark">Panel Administrativo</h5>
								</div>
								
								<div class="mb-4">
									<h3 class="fw-semibold text-dark mb-1 d-none d-md-block" style="letter-spacing: -0.02em;">Iniciar Sesión</h3>
									<p class="text-muted small mb-0">Ingrese sus credenciales de acceso institucional.</p>
								</div>
								
								<?php if (!empty($_GET['error'])): ?>
									<?php if ($_GET['error'] === 'inactive'): ?>
										<div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-4" role="alert">
											<span class="material-icons me-2" style="font-size: 1.2rem;">block</span>
											<span class="small">Su cuenta ha sido desactivada por el administrador.</span>
										</div>
									<?php elseif ($_GET['error'] === 'timeout'): ?>
										<div class="alert alert-info d-flex align-items-center py-2 px-3 mb-4" role="alert">
											<span class="material-icons me-2" style="font-size: 1.2rem;">schedule</span>
											<span class="small">Su sesión ha finalizado por inactividad.</span>
										</div>
									<?php else: ?>
										<div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-4" role="alert">
											<span class="material-icons me-2" style="font-size: 1.2rem;">error_outline</span>
											<span class="small">Usuario o contraseña incorrectos.</span>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								
								<form action="login.php" method="POST" name="login" class="needs-validation">
									<div class="mb-3">
										<label class="form-label text-muted small fw-semibold" for="emailInput">Usuario</label>
										<div class="input-group">
											<span class="input-group-text bg-light border-end-0 text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
												<span class="material-icons" style="font-size: 1.15rem;">person</span>
											</span>
											<input type="text" id="emailInput" class="form-control border-start-0 ps-1" placeholder="Nombre de usuario" name="email" required autofocus style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
										</div>
									</div>
									
									<div class="mb-4">
										<label class="form-label text-muted small fw-semibold" for="passwordInput">Contraseña</label>
										<div class="input-group">
											<span class="input-group-text bg-light border-end-0 text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
												<span class="material-icons" style="font-size: 1.15rem;">lock</span>
											</span>
											<input type="password" id="passwordInput" class="form-control border-start-0 ps-1" placeholder="Contraseña" name="password" required style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
										</div>
									</div>
									
									<button class="btn btn-primary w-100 py-2 fw-semibold" type="submit" name="login" style="border-radius: 8px; font-size: 0.95rem;">
										Ingresar al Sistema
									</button>
								</form>
							</div>
						</div>
					</div>
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
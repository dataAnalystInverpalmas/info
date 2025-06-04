<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
  }else{
	include ("../funciones/conexion.php");
}

	if (empty($_GET['menu']) & empty($_SESSION['usuario'])){
		?>
		<br>
			<div class="row justify-content-sm-center">
				<div class="col-sm-6 col-md-5 flex-column">
					<h1 class="text-center">Admin Panel</h1>
					<h2 class="text-center">Iniciar Sesión</h2>
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
		
		if (empty($_GET['menu'])) {
			?>
			<!-- <div class="jumbotron jumbotron-fluid"> -->
				<div class="container">
					<div class="row">
						<div class="col-sm-4"></div>
						<div class="col-sm-4"></div>
						<div class="col-sm-4 text-right">
							<?php echo "<h7>".$_SESSION['usuario']." </h7"; ?></h7>
							<!-- tutiempo.net - Ancho:300px - Alto:411px -->
							<!-- <iframe src="https://www.tutiempo.net/s-widget/app/?LocId=70463&sc=1" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:411px;" allowtransparency="true"></iframe> -->
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="embed-responsive">
								<iframe title="Pto Vs Real" class="powerbi-frame" src="https://app.powerbi.com/view?r=eyJrIjoiOGI4YWM0OWEtNzliYS00YmEyLWI5YzAtNjhmMmJlZTU4MTdmIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9" frameborder="0" allowFullScreen="true"></iframe>
							</div>
						</div>
					</div>	
				</div>
			<!-- </div> -->
			<?php
		}
		else {
			if(isset($_GET['report']) && $_GET['report'] == '1'){
				?>
				<div class="embed-responsive">
				<iframe src="views/formBautizo.php"
					style="position: fixed; top: 60px; left: 0; width: 100vw; height: calc(100vh - 70px); border: none;"
					frameborder="0"
					scrolling="auto">
				</iframe>
				</div>
				<?php
			}else{
				require_once('dist/tables.php');
			}
			
		}
		
}
 ?>

<style>
						.embed-responsive {
							position: relative;
							width: 100%;
							padding-bottom: 56.25%; /* Proporción de aspecto 16:9 (ajusta esto según tu necesidad) */
							overflow: hidden;
						}

						.embed-responsive iframe {
							position: absolute;
							top: 0;
							left: 0;
							width: 100%;
							height: 85%;
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
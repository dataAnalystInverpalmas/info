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
			<div class="jumbotron jumbotron-fluid">
				<div class="container">
					<div class="row">
						<div class="col-sm-4"></div>
						<div class="col-sm-4">
							<?php echo "<h1>".$_SESSION['usuario']." </h1"; ?></h7>
							<!-- tutiempo.net - Ancho:300px - Alto:411px -->
							<!-- <iframe src="https://www.tutiempo.net/s-widget/app/?LocId=70463&sc=1" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:411px;" allowtransparency="true"></iframe> -->
						</div>
						<div class="col-sm-4"></div>
					</div>	
				</div>
			</div>
			<?php
		}
		else {
			require_once('dist/tables.php');
		}
		
}
 ?>

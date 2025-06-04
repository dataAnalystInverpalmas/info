<?php
//lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
require "vendor/autoload.php";

//verificar los roles	
?>
<div id="mostrar_loading" style="display:none;"></div>
<div id="mostrar_listo" style="display:none;"></div>
<div id="mostrar_error" style="display:none;"></div>
<script src="scripts/loadFiles.js"></script>
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
//enrutador
$enruta="../";

if (isset($_GET['table'])){
if ($_GET['table']==1)
{
	$dir="tables/plane.php";//directorio a buscar
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." AND dir='".$dir."' ";//consulta
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)//si tiene permisos...
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==2)
{
	$dir="tables/varieties.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
		header("Location:index.php");
	}
}
else if ($_GET['table']==3)
{
	$dir="tables/program.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==4)
{
	$dir="tables/seasons.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==5)
{
	$dir="tables/fusarium.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==6)
{
	$dir="tables/arrangements.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==7)
{
	$dir="tables/companys.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==8)
{
	$dir="tables/farms.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==9)
{
	$dir="tables/products.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==10)
{
	$dir="tables/addxvariety.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==11)
{
	$dir="tables/areas.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==12)
{
	$dir="tables/programf.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==13)
{
	$dir="tables/hplane.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==14)
{
	$dir="tables/viewReportsP.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==15)
{
//disponible
}
else if ($_GET['table']==16)
{
	$dir="tables/laborsSowing.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==17)
{
	$dir="tables/program_add.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==18)
{
	$dir="tables/greenhouses.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']==19)
{
	$dir="tables/program_add_pto.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadQualities')
{
	$dir="tables/qualities.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadNalcauses')
{
	$dir="tables/nalCauses.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadEvaluations')
{
	$dir="tables/evaluations.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadComments')
{
	$dir="tables/comments.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadCurves')
{	
	$dir="tables/curves.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadFeatures')
{
	$dir="tables/features.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadEmployees')
{
	$dir="tables/employees.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadSupervisors')
{
	$dir="tables/supervisors.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='loadAssistances')
{
	$dir="tables/assistances.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']=='withoutdatacovid')
{
	$dir="tables/withoutdatacovid.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
else if ($_GET['table']== 'generateViewBudget')
{
	$dir="tables/viewBudget.php";	
	$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
			AND dir='".$dir."' ";
	$resQ=$conexion->query($query);
	if ($resQ->num_rows>0)
	{
		?>
			<script>
				var link = "<?php echo $enruta.$dir; ?>";
				listar_tabla(link);
			</script>
		<?php
	}else{
		echo "<h1>No tiene permisos</h1>";
	}
}
}else{
	echo "";
}

//reportes////////////////////////////////////////////////////////////////////////
if (isset($_GET['report']))
{
	if ($_GET['report']==1)
	{
		$dir="views/formBautizo.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{	
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==2)
	{
		$dir="views/applications.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==3)
	{
		$dir="views/addvarietys.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==4)
	{
		$dir="views/program.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==5)
	{
		$dir="views/programView.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
		
	else if ($_GET['report']==6)
	{
		$dir="views/print.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==7)
	{
		$dir="views/growroot.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==8)
	{
		$dir="views/growplanting.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==9)
	{
		$dir="views/xmodal.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==10)
	{
		$dir="views/ymodal.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==11)
	{
		$dir="views/datatable.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==12)
	{
		$dir="views/flowervase.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==13)
	{
		$dir="views/report_qualities.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==14)
	{
		$dir="views/labors_form.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']=='orders')
	{
		$dir="views/orders.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']=='months')
	{
		$dir="views/months.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==50)
	{
		$dir="views/management.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==51)
	{
		$dir="views/agronomist.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==52)
	{
		$dir="views/report_fv.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==53)
	{
		$dir="views/report_evaluations.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==100)
	{
		$dir="views/management.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==101)
	{
		$dir="views/modal.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==102)
	{
		$dir="views/greenhouses.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==103)
	{
		$dir="views/report_pb_clavel.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==104)
	{
		$dir="views/report_pb_curvas.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==105)
	{
		$dir="views/report_trazabilidad.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1000)
	{
		$dir="covid/covid.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1001)
	{
		$dir="covid/settings.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1002)
	{
		$dir="covid/report.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1003)
	{
		$dir="covid/export.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1004)
	{
		$dir="covid/reportout.php";	
		$query="SELECT dir FROM roles WHERE users_role=".$_SESSION['role']." 
				AND dir='".$dir."' ";
		$resQ=$conexion->query($query);
		if ($resQ->num_rows>0)
		{
			include "$dir";
		}else{
			echo "<h1>No tiene permisos</h1>";
		}
	}
	else if ($_GET['report']==1005)
	{
		$dir="views/loadfiles.php";	
		include "$dir";
	}
}
?>

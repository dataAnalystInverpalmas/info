<?php

//lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
//carbon
require "vendor/autoload.php";
use Carbon\Carbon;

	$di=Carbon::now();
	$de=Carbon::now();
	$dateEnd=new Carbon();
	$dateIni=new Carbon();
	
	$dateIni=$di->startOfWeek()->format('Y-m-d');
	$dateEnd=$de->endOfWeek()->format('Y-m-d');
	//consulta para combos
	$where=" WHERE p.producto in('CLAVEL','MINICLAVEL','CLAVEL STANDARD','CLAVEL MINIATURA') ";

	if(empty($_POST['xtipo']) and empty($_POST['xfinca']))
	{//las dos estan vacias
		$tipo="";
		$finca="";
	}else if(!empty($_POST['xtipo']) and empty($_POST['xfinca']))
	{//solo tipo de aplicacion	
		  $tipo=$_POST['xtipo'];
		  $finca="";
	}
	else if(empty($_POST['xtipo']) and !empty($_POST['xfinca']))
	{//solo finca
		$tipo="";
		$finca=$_POST['xfinca'];
	}
	else//en caso de que esten seteadas las dos, asignar valores
	{
		$tipo=$_POST['xtipo'];
		$finca=$_POST['xfinca'];
	}

	if(isset($_POST['buscar'])){
		$dateIni=$_POST['dateIni'];
		$dateEnd=$_POST['dateEnd'];
		if ($tipo=='')//condicion para filtrar tipo de aplicacion
		{
			$where.='';
		}
		else
		{
			$where.=" AND a.tipo='".$tipo."' ";
		}
		if ($finca=='')//condicion para filtrar finca
		{
			$where.='';	
		}
		else
		{
			$where.=" AND a.finca='".$finca."' ";
		}
		//resto de la condicion que siempre se cumple
			  $where.=" AND  
			  DATE_ADD(
		if(
		aa.calc_conciclo=0,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=2,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval + pr.ciclo week),
		if(aa.calc_conciclo=3,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=4,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=5,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=6,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		if(aa.calc_conciclo=7,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo  + pr.ciclo week )
		)))))))
		,interval a.valor * if(aa.calc_conciclo=6,-1,1) day)
		between '$dateIni' AND '$dateEnd' 		   		  
			  ";
	}else
	{//en caso de no haber presionado  buscar, hace...
		$where.=" AND a.finca='INVERPALMAS' AND a.tipo='GIBERELINA' AND
		DATE_ADD(
		if(
		aa.calc_conciclo=0,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=2,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval + pr.ciclo week),
		if(aa.calc_conciclo=3,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=4,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=5,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=6,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		if(aa.calc_conciclo=7,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo  + pr.ciclo week )
		)))))))
		,interval a.valor * if(aa.calc_conciclo=6,-1,1) day)
		between '$dateIni' AND '$dateEnd' 		  
		";
	}

	//consulta para tabla
	$sql="SELECT p.finca,
			p.bloque,
			p.variedad,
			p.temporada,
			a.tipo,
			a.aplicar,
		COUNT(p.bloque) as camas,round(sum(p.plantas)/960,1) as ncamas,
		DATE_ADD(
		if(
			aa.calc_conciclo=0,
			date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=2,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval + pr.ciclo week),
		if(aa.calc_conciclo=3,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
		if(aa.calc_conciclo=4,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=5,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
		,
		if(aa.calc_conciclo=6,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		if(aa.calc_conciclo=7,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
		,
		date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo  + pr.ciclo week )
		)))))))
		,interval a.valor * if(aa.calc_conciclo=6,-1,1) day) as fecha_aplica
		FROM plane AS p
		INNER JOIN arrangements as a
		ON a.variedad=p.variedad and a.finca=p.finca
		INNER JOIN varieties as v
		ON v.nombre=p.variedad
		INNER JOIN seasons as s
		ON s.nombre=p.temporada
		LEFT JOIN (SELECT variedad,programa,ciclo FROM program group by 1,2,3 
		) as pr
		ON pr.variedad=p.variedad and pr.programa=s.año
		left join arrangement as aa
		on a.tipo=aa.tipo and a.aplicar=aa.aplicar
		$where
		GROUP BY 1,2,3,4,5,6
		";

		//consulta para tabla
		$sql2="SELECT p.finca,
		p.bloque,
		a.tipo,
		a.aplicar,
	COUNT(p.bloque) as camas,round(sum(p.plantas)/960,1) as ncamas,
	DATE_ADD(
	if(
	aa.calc_conciclo=0,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
	if(aa.calc_conciclo=2,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval + pr.ciclo week),
	if(aa.calc_conciclo=3,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week),
	if(aa.calc_conciclo=4,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
	,
	if(aa.calc_conciclo=5,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo week )
	,
	if(aa.calc_conciclo=6,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
	,
	if(aa.calc_conciclo=7,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - 0 week )
	,
	date_add(if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),date_add(p.fecha_siembra, interval + pr.ciclo week),s.fecha_pico), interval - pr.ciclo  + pr.ciclo week )
	)))))))
	,interval a.valor * if(aa.calc_conciclo=6,-1,1) day) as fecha_aplica
	FROM plane AS p
	INNER JOIN arrangements as a
	ON a.variedad=p.variedad and a.finca=p.finca
	INNER JOIN varieties as v
	ON v.nombre=p.variedad
	INNER JOIN seasons as s
	ON s.nombre=p.temporada
	LEFT JOIN (SELECT variedad,programa,ciclo FROM program group by 1,2,3 
	) as pr
	ON pr.variedad=p.variedad and pr.programa=s.año
	left join arrangement as aa
	on a.tipo=aa.tipo and a.aplicar=aa.aplicar
	$where
	GROUP BY p.finca,p.bloque,a.tipo,a.aplicar
		";
			//consulta para tabla
	$sql3="SELECT IFNULL(a.aplicar,'Total') as aplicar,
		COUNT(p.bloque) as camas,round(sum(p.plantas)/960,1) as ncamas	
		FROM plane AS p
		INNER JOIN arrangements as a
		ON a.variedad=p.variedad and a.finca=p.finca
		$where
		GROUP BY a.aplicar
		WITH ROLLUP
		";	

	$sql4="SELECT a.aplicar as aplicacion,s.insumo,s.medida, round(sum(p.plantas)/960,1) * s.dosis as cantidad 
		FROM plane AS p 
		INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca 
		INNER JOIN arrangement as aa ON a.aplicar=aa.aplicar and a.tipo=aa.tipo 
		INNER JOIN supplies as s ON s.arrangement_id=aa.id 
		$where 
		GROUP BY a.aplicar,s.insumo
		";

	$res=$conexion->query($sql);
	$res2=$conexion->query($sql2);
	$res3=$conexion->query($sql3);
	$res4=$conexion->query($sql4);

	//consulta tipo
	$sqlTIPO="SELECT DISTINCT tipo FROM arrangements";
	$resT=$conexion->query($sqlTIPO);
	//consulta fincas
	$sqlFINCA="SELECT DISTINCT finca FROM plane";
	$resF=$conexion->query($sqlFINCA);

?>
	<!--formulario de fi-->
<style>
	@media screen, print
	{
		td,tr,th{
			border: 1px solid black;
			padding-bottom: 0em;
			height: 10px;
			margin-bottom: 0px;
 			margin-right: 0px;
 			margin-left: 0px;
		}
	}
</style>
<div class="card">
  <div class="card-header">
    <form class="form-inline" action="home.php?menu=tables&report=2" method="post" enctype="multipart/form-data">
	<div class="form-group mx-sm-3 mb-2">
        	<select name="xfinca" class="form-control" data-live-search="true">
				<option value="">Finca</option>
					<?php
					while($rf = $resF->fetch_object()){
						if($rf->finca==$finca){
						echo "<option value='".$rf->finca."' selected='selected'>" .$rf->finca. "</option>";
					}else{
						echo "<option value='".$rf->finca."'>" .$rf->finca. "</option>";
					}
					}
					?>
           </select>
        </div>
		<div class="form-group mx-sm-3 mb-2">
          <label class="sr-only" >Fecha Inicial</label>
          <input type="date" name="dateIni" value="<?php echo $dateIni; ?>" class="form-control" id="inlineFormInputName" >
        </div>
        <div class="form-group mx-sm-3 mb-2">
          <label class="sr-only" >Fecha Final</label>
          <div class="input-group">
            <input type="date" name="dateEnd" value="<?php echo $dateEnd; ?>" class="form-control" id="inlineFormInputGroupUsername" >
          </div>
        </div>
	  	<div class="form-group mx-sm-3 mb-2">
        	<select name="xtipo" class="form-control" data-live-search="true">
				<option value="">Tipo</option>
					<?php
					while($rp = $resT->fetch_object()){
						if($rp->tipo==$tipo){
						echo "<option value='".$rp->tipo."' selected='selected'>" .$rp->tipo. "</option>";
					}else{
						echo "<option value='".$rp->tipo."'>" .$rp->tipo. "</option>";
					}
					}
					?>
           </select>
        </div>
        <div class="form-group mx-sm-3 mb-2">
              <button name="buscar" type="submit" class="btn btn-primary mb-2">Buscar</button>
        </div>
		<div class="form-group mx-sm-3 mb-2">
              <button name="imprimir" type="submit" class="btn btn-primary mb-2" onclick="window.print();">Imprimir</button>
        </div>
   </form>
  </div>
</div>
<br>
<?php
//echo $sql;
	if ($res->num_rows>0)
	{
		$finicial=new Carbon($dateIni);
		$ffinal=new Carbon($dateEnd);
		echo "<h5>".$finca."</h2>";
		echo "<h5>Reporte Semanal de Aplicaciones</h2>";
		echo "<h5>".$tipo."</h2>";
		echo "<h5>Entre el: ".$finicial->format('d-m-y/W')." Y ".$ffinal->format('d-m-y/W')."</h3>";
		?>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm">
				<tr>
					<th>Bloque</th><th>Aplicar</th>
					<th>Variedad</th><th>Temporada</th>
					<th>#Cama Fisica</th><th>#Cama Real</th>
					<th>Realizado</th>
				</tr>
					<?php
					while($f = $res->fetch_object())
					{
						echo "<tr><td>" .$f->bloque. "</td><td>" .$f->aplicar. "</td><td>" .$f->variedad. "</td>
						<td>" .$f->temporada. "</td>
						<td>" .number_format($f->camas,0,'','.'). "</td><td>" .number_format($f->ncamas,0,'','.'). "</td><td></td></tr>";
					}
					?>
					<?php for ($i=0;$i<5;$i++){ ?>
						<tr style="height:30px">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
				<?php } ?>	 
				</table>
			</div>
		</div>
		<div class="saltoDePagina d-print-block" id="saltoDePagina "></div>
		<div class="row">
			<div class="col-6">
			<h7>Resumen por bloque</h7>
				<table class="table table-sm">
				<tr>
					<th>Bloque</th><th>Aplicar</th><th>#Cama Fisica</th><th>#Cama Real</th>
				</tr>
				<?php
				while($f = $res2->fetch_object())
				{
					echo "<tr>
					<td>" .$f->bloque. "</td><td>" .$f->aplicar. "</td><td>" .number_format($f->camas,0,'','.'). "</td><td>" .number_format($f->ncamas,0,'','.'). "</td>
					</tr>";
				}
				?>	
				</table>
			</div>
			<div class="col-6">
			<h7><stong>Resumen por tipo de aplicación</strong></h7>
				<table class="table table-sm">
				<tr>
					<th>Aplicar</th><th>#Cama Fisica</th><th>#Cama Real</th>
				</tr>
				<?php
				while($f = $res3->fetch_object())
				{
					echo "<tr>
					<td>" .$f->aplicar. "</td><td>" .number_format($f->camas,0,'','.'). "</td><td>" .number_format($f->ncamas,0,'','.'). "</td>
					</tr>";
				}
				?>	
				</table>

				<h7><stong>Insumos y cantidad de producto a usar</strong></h7>
				<table class="table table-sm">
				<tr>
					<th>Aplicación</th><th>Insumo</th><th>Medida</th><th>Cantidad</th>
				</tr>
				<?php
				while($f = $res4->fetch_object())
				{
					echo "<tr>
					<td>" .$f->aplicacion. "</td><td>" .$f->insumo. "</td><td>" .$f->medida. "</td><td>" .number_format($f->cantidad,1,',','.'). "</td>
					</tr>";
				}
				?>	
				</table>	
			</div>
		</div>
		<?php
		}
		else 
		{
			echo "0 results";
		}
			//cerrar conexion
			$conexion->close();
 	?>   


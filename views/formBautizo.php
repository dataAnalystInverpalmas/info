<?php

//lamar conexion
include ('funciones/conexion.php');

//carbon
require "vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

?>

<style>
  @media screen {
    div.sinBorde {
      border-left: 0.5px solid grey;
      border-top: 0.5px solid grey;  
      padding-bottom: 0em;
      margin-bottom: 0px;
      margin-right: 0px;
      margin-left: 0px;
      height: 25px;
    }
    div.sinBorde1 {
    border-left: 0.5px solid grey;

    padding-bottom: 0em;
    margin-bottom: 0px;
    height: 30px;
    }
    div.sinBorde2 {

    border-left: 0.5px solid grey;
    padding-bottom: 4em;
    margin-bottom: 0px;
    height: 60px;
    }
    div.sinBorde4 {
      border-left: 0.5px solid grey;

      padding-bottom: 0em;
      margin-bottom: 0px;
      height: 25px;
    }
    div.sinBorde5 {
      border-left: 0.5px solid grey;
      border-top: 0.5px solid grey;
      padding-bottom: 0em;
      margin-bottom: 0px;
      height: 25px;
    }
    div.forRow{
      border-top: 0.5px solid grey;
      border-bottom: 0.5px solid grey;

    }

  }
</style>
<?php

//inicializa variable vacia
$where=" WHERE p.fecha_siembra=(SELECT max(fecha_siembra) FROM plane)";

//consulta para combos FINCA
if(isset($_POST['xfinca']) AND
    empty($_POST['xfecha']) and
    empty($_POST['xbloque']) AND
    empty($_POST['xtemporada']) AND
    empty($_POST['xvariedad'])
){
    $finca=$_POST['xfinca'];
    $fecha="";
    $bloque="";
    $temporada="";
    $variedad="";
}else if(isset($_POST['xfecha']) and
    empty($_POST['xbloque']) AND
    empty($_POST['xtemporada']) AND
    empty($_POST['xvariedad']) and
    empty($_POST['xfinca'])) 
    {
    $finca="";
    $fecha=new carbon($_POST['xfecha']);
  	$fecha=$fecha->format('Y-m-d');
    $bloque="";
    $temporada="";
    $variedad="";
  }else if(empty($_POST['xfecha']) and
      isset($_POST['xbloque']) AND
      empty($_POST['xtemporada']) AND
      empty($_POST['xvariedad']) and
      empty($_POST['xfinca'])) 
      {
      $finca="";
      $fecha="";
      $bloque=$_POST['xbloque'];
      $temporada="";
      $variedad="";
    }else if(empty($_POST['xfecha']) and
        empty($_POST['xbloque']) AND
        isset($_POST['xtemporada']) AND
        empty($_POST['xvariedad']) and
        empty($_POST['xfinca'])) 
        {
        $finca="";
        $fecha="";
        $bloque="";
        $temporada=$_POST['xtemporada'];
        $variedad="";
      }else if(empty($_POST['xfecha']) and
          empty($_POST['xbloque']) AND
          empty($_POST['xtemporada']) AND
          isset($_POST['xvariedad']) and
          empty($_POST['xfinca'])) 
      {
          $finca="";
          $fecha="";
          $bloque="";
          $temporada="";
          $variedad=$_POST['xvariedad'];
      }
      else if(isset($_POST['xfecha']) and 
          isset($_POST['xfinca']) and 
          empty($_POST['xbloque']) and
          empty($_POST['xtemporada']) and
          empty($_POST['xvariedad']) 
          )
      {
          $finca=$_POST['xfinca'];
          $fecha=new carbon($_POST['xfecha']);
      	  $fecha=$fecha->format('Y-m-d');
          $bloque="";
          $temporada="";
          $variedad="";
      }
      else if(isset($_POST['xfinca']) and
           isset($_POST['xbloque']) and
           empty($_POST['xtemporada']) and
           empty($_POST['xvariedad']) and
           empty($_POST['xfecha'])
           ) 
      {
          $finca=$_POST['xfinca'];
      	  $fecha="";
          $bloque=$_POST['xbloque'];
          $temporada="";
          $variedad="";
      }
      else if
      (isset($_POST['xfecha']) and 
      isset($_POST['xfinca']) and 
      isset($_POST['xbloque']) and 
      empty($_POST['xtemporada']) and
      empty($_POST['xvariedad'])
      ) 
      {
          $finca=$_POST['xfinca'];
          $fecha=new carbon($_POST['xfecha']);
      	  $fecha=$fecha->format('Y-m-d');
          $bloque=$_POST['xbloque'];
          $temporada="";
          $variedad="";
      }
      else if
      (isset($_POST['xfecha']) and 
      isset($_POST['xfinca']) and 
      isset($_POST['xbloque']) and 
      isset($_POST['xtemporada']) and 
      empty($_POST['xvariedad'])
      ) 
      {
          $finca=$_POST['xfinca'];
          $fecha=new carbon($_POST['xfecha']);
      	  $fecha=$fecha->format('Y-m-d');
          $bloque=$_POST['xbloque'];
          $temporada=$_POST['xtemporada'];
          $variedad="";
      }
      else if
      (empty($_POST['xfecha']) and 
      isset($_POST['xfinca']) and 
      isset($_POST['xbloque']) and 
      isset($_POST['xtemporada']) and 
      empty($_POST['xvariedad'])) 
      {
          $finca=$_POST['xfinca'];
          $fecha='';
          $bloque=$_POST['xbloque'];
          $temporada=$_POST['xtemporada'];
          $variedad='';
      }
      else if
      (isset($_POST['xfinca']) and 
      isset($_POST['xbloque']) and 
      isset($_POST['xtemporada']) and 
      isset($_POST['xvariedad']) and
      empty($_POST['xfecha'])
      ) 
      {
        unset($fecha);
          $finca=$_POST['xfinca'];
          $bloque=$_POST['xbloque'];
          $temporada=$_POST['xtemporada'];
          $variedad=$_POST['xvariedad'];
      }else if
      (isset($_POST['xfinca']) and 
      isset($_POST['xbloque']) and 
      isset($_POST['xtemporada']) and 
      empty($_POST['xvariedad']) and
      empty($_POST['xfecha'])
      ) 
      {
        unset($fecha);
          $finca=$_POST['xfinca'];
          $bloque=$_POST['xbloque'];
          $temporada=$_POST['xtemporada'];
          $variedad="";
      }
      else {
        $finca="";
        $fecha="";
        $bloque="";
        $temporada="";
        $variedad="";
}
$error="";

/////////////////////////////////////Search button/////////////////
if(isset($_POST['buscar']))
{
  if(isset($_POST['xfinca']) AND
      empty($_POST['xfecha']) and
      empty($_POST['xbloque']) AND
      empty($_POST['xtemporada']) AND
      empty($_POST['xvariedad'])
  )
  {
    $where=" WHERE p.finca='$finca' ";
  }
  else if(isset($_POST['xfecha']) and
      empty($_POST['xbloque']) AND
      empty($_POST['xtemporada']) AND
      empty($_POST['xvariedad']) and
      empty($_POST['xfinca'])
  ) 
  {
    $where=" WHERE p.fecha_siembra='$fecha' ";
  }
  else if(empty($_POST['xfecha']) and
      isset($_POST['xbloque']) AND
      empty($_POST['xtemporada']) AND
      empty($_POST['xvariedad']) and
      empty($_POST['xfinca'])
    )
    {
    $where=" WHERE p.bloque='$bloque' ";
  }
  else if(empty($_POST['xfecha']) and
      empty($_POST['xbloque']) AND
      isset($_POST['xtemporada']) AND
      empty($_POST['xvariedad']) and
      empty($_POST['xfinca'])
    ) 
    {
    $where=" WHERE p.temporada='$temporada' ";
  }
    else if
    (
      empty($fecha) and 
      empty($finca) and 
      empty($bloque) and 
      empty($temporada) and 
     !empty($variedad)
    )
    {
    $where=" WHERE p.variedad='$variedad' ";
    }
    else if
    (
       empty($fecha) and 
      !empty($finca) and 
      !empty($bloque) and 
       empty($temporada) and 
       empty($variedad)
    )
    {
      $where=" WHERE p.finca='$finca' and p.bloque=$bloque ";
    }
    else if
    (
      !empty($fecha) and 
      !empty($finca) and 
       empty($bloque) and 
       empty($temporada) and 
       empty($variedad)
    )
    {
      $where=" WHERE p.fecha_siembra='$fecha' and p.finca='$finca' ";
    }
    else if
    ( 
      !empty($fecha) and 
      !empty($finca) and 
      !empty($bloque) and 
       empty($temporada) and 
       empty($variedad)
    )
    {
      $where=" WHERE p.fecha_siembra='$fecha' and p.finca='$finca' and p.bloque=$bloque ";
    }
    else if
    ( 
      !empty($fecha) and 
      !empty($finca) and 
      !empty($bloque) and 
      !empty($temporada) and 
       empty($variedad)
    ) 
    {
      $where=" WHERE p.fecha_siembra='$fecha' and p.finca='$finca' and p.bloque=$bloque and p.temporada='$temporada'";
    }
    else if
    (
       empty($fecha) and 
      !empty($finca) and 
      !empty($bloque) and 
      !empty($temporada) and 
       empty($variedad)
    ) 
    {
      $where=" WHERE p.finca='$finca' and p.bloque=$bloque and p.temporada='$temporada' ";
    } 
    else if
    (
       empty($fecha) and 
      !empty($finca) and 
      !empty($bloque) and 
      !empty($temporada) and 
      !empty($variedad)
    ) 
    {
      $where=" WHERE p.finca='$finca' and p.bloque=$bloque and p.temporada='$temporada' and p.variedad='$variedad' ";
    }
}

//CONSULTA PARA COMBO
$slqCOM="SELECT
	fecha_siembra as fecha
	FROM plane
	GROUP BY fecha_siembra
	ORDER BY fecha_siembra DESC
";

$COM=$conexion->query($slqCOM);
//CONSULTA PARA COMBO
$slqCOM1="SELECT
	finca
	FROM plane
	GROUP BY finca
	ORDER BY finca ASC
";
$COM1=$conexion->query($slqCOM1);
//consulta para bloque
$slqCOM2="SELECT
	bloque
	FROM plane
	GROUP BY bloque
	ORDER BY bloque ASC
";
$COM2=$conexion->query($slqCOM2);
//consulta para temporada
$slqCOM3="SELECT
	temporada
	FROM plane
  WHERE temporada<>''
  GROUP BY temporada
	ORDER BY temporada ASC
";
$COM3=$conexion->query($slqCOM3);
//consulta para variedad
$slqCOM4="SELECT
	variedad
	FROM plane
  WHERE variedad<>''
	GROUP BY variedad
	ORDER BY variedad ASC
";
$COM4=$conexion->query($slqCOM4);

?>
<div class="container-fluid portrait">
	<div class="card-header bg-muted d-print-none">
	<form class="form-inline" action="home.php?menu=tables&report=1" method="post" enctype="multipart/form-data">
		<div class="form-group mb-2">
		    <select name="xfecha" class="form-control" data-live-search="true"> <!--FINCA-->
		      <option value="">Fecha</option>
		      <?php
					while($f = $COM->fetch_object()){
						if($f->fecha==$fecha){
						  echo "<option value='".$f->fecha."' selected='selected'>" .$f->fecha. "</option>";
					  }else{
						  echo "<option value='".$f->fecha."'>" .$f->fecha. "</option>";
					  }
					}
		       ?>
		    </select>
        <select name="xfinca" class="form-control" data-live-search="true"> <!--FINCA-->
          <option value="">Finca</option>
          <?php
          while($f = $COM1->fetch_object()){
            if($f->finca==$finca){
             echo "<option value='".$f->finca."' selected='selected'>" .$f->finca. "</option>";
            }else{
             echo "<option value='".$f->finca."'>" .$f->finca. "</option>";
            }
          }
           ?>
        </select>
        <select name="xbloque" class="form-control" data-live-search="true"> <!--FINCA-->
          <option value="">Bloque</option>
          <?php
          while($f = $COM2->fetch_object()){
            if($f->bloque==$bloque){
              echo "<option value='".$f->bloque."' selected='selected'>" .$f->bloque. "</option>";
            }else{
              echo "<option value='".$f->bloque."'>" .$f->bloque. "</option>";
            }
          }
           ?>
        </select>
        <select name="xtemporada" class="form-control" data-live-search="true"> <!--FINCA-->
          <option value="">Siembra</option>
          <?php
          while($f = $COM3->fetch_object()){
            if($f->temporada==$temporada)
            {
              echo "<option value='".$f->temporada."' selected='selected'>" .$f->temporada. "</option>";
            }else
            {
              echo "<option value='".$f->temporada."'>" .$f->temporada. "</option>";
            }
          }
           ?>
        </select>
        <select name="xvariedad" class="form-control" data-live-search="true"> <!--FINCA-->
          <option  value="">Variedad</option>
          <?php
          while($f = $COM4->fetch_object()){
            if($f->variedad==$variedad){
            echo "<option value='".$f->variedad."' selected='selected'>" .$f->variedad. "</option>";
            }else{
            echo "<option value='".$f->variedad."'>" .$f->variedad. "</option>";
            }
          }
           ?>
        </select>
        <div class="form-group mx-sm-3 mb-2">
      		 <button name="buscar" type="submit" class="btn btn-primary mb-2">Buscar</button>
      	</div>
      	<div class="form-group mx-sm-3 mb-2">
      		 <button name="print" type="submit" class="btn btn-success mb-2" onclick="window.print();">Imprimir</button>
      	</div>
		</div>

</form>
</div>

<div class="container-fluid d-print-block" id="print">

<?php
$sql="SELECT p.id,p.finca,p.bloque,p.variedad,p.temporada, p.fecha_siembra,p.origen,p.producto, 
  p.tabla,count(p.bloque) as ndatos, sum(p.plantas) as plantas,53.3 as plantasM2,
  s.fecha_fiesta as fecha_temporada,s.fecha_pico, if(isnull(pr.ciclo),v.ciclo,pr.ciclo) as ciclo,tipo_siembra, b.nombre as casa,cosecha_reem,variedad_reem,p.pico,p.tipo_suelo,p.nmanguera
  FROM plane as p 
  LEFT JOIN seasons AS s ON s.nombre=p.temporada
  LEFT JOIN (SELECT variedad,programa,ciclo FROM program group by 1,2,3) as pr
  ON pr.variedad=p.variedad and pr.programa=s.año
  LEFT JOIN varieties as v ON p.variedad=v.nombre
  LEFT JOIN breeders as b ON v.casa_comercial=b.id
  $where
  GROUP BY p.finca,p.bloque,p.variedad,p.temporada,p.tabla,p.fecha_siembra,p.origen,p.producto,p.tipo_suelo,
    if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),p.pico,p.tipo_siembra),
	  if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),p.cosecha_reem,p.tipo_siembra),
    if(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'),p.variedad_reem,p.tipo_siembra)
  ORDER BY p.bloque ASC
";
echo $error;
		$query=$conexion->query($sql);
		//TRAER ARREGLOS
				if ($query->num_rows>0){

	////////////////////////////////////////////////////////////////////////////////
			while($r = $query->fetch_object()){

				 ?>
         Hoja de Bautizo I CYM PRO HBV 023
         <div class="row forRow">
          <div class="col-12">
          
              <div class="row justify-content-md-center">
                  <div class="col-12">
                    <div class="row">
  
                    <div class="col-9 text-justify sinBorde4">Variedad </div>
                    <div class="col-1 sinBorde4">Bloque </div>
                    <div class="col-1 sinBorde4"> #Camas </div>
                    <div class="col-1 sinBorde4"><p class="small">Dirección</div>
                    </div>
                  </div>
              </div>
              <div class="row justify-content-md-center">
                <div class="col-12">
                  <div class="row">

                    <div class="col-9 text-nowrap text-center sinBorde2"><h1 class="display-4 font-weight-bold"><?php  	echo $r->variedad; ?></h2></div>
                    <div class="col-1 sinBorde2"><h1 class="font-weight-bold"><?php	echo $r->bloque; ?></h1></div>
                    <div class="col-1 sinBorde2"><h1 class="font-weight-bold"><?php	echo $r->ndatos; ?></h1></div>
                    <div class="col-1 sinBorde2">
                      <?php
                      if ($r->tabla=='A' AND $r->ndatos>1){
                        echo "<h1 class='font-weight-bold'><strong>🢀</strong></h1>";
                      }else if($r->tabla=='B' AND $r->ndatos>1){
                        echo "<h1 class='font-weight-bold'><strong>🢂</strong></h1>";
                      }else{
                        echo "<h1 class='font-weight-bold'><strong>🢃</strong></h1>";
                      }
                    ?>
                  </div>
                </div>
                </div>
              </div>
            </div>
        </div>
			 <!--Resultados de la consulta-->
  <div class="row forRow"> 
    <div class="col-12">
       <div class="row justify-content-md-center">
          <div class="col-12">
            <div class="row">
              <div class="col-2 sinBorde4">Finca</div>
              <div class="col-6 text-justify sinBorde4">Siembra</div>
              <div class="col-2 sinBorde4"># Plantas</div>
              <div class="col-1 sinBorde4"><p class="small">PlantasM2</div>
              <div class="col-1 sinBorde4">Ciclo</div>
            </div>
          </div>
        </div>
				<div class="row justify-content-md-center">
          <div class="col-12 ">
            <div class="row">
    					<?php $picos=new carbon($r->fecha_pico);
    								$p=$picos->endOfWeek()->subDays(3)->format('W');
    								$p1=$picos->subWeeks(1)->endOfWeek()->subDays(3)->format('W');
    								$p2=$picos->subWeeks(1)->endOfWeek()->subDays(3)->format('W');
    								$p3=$picos->subWeeks(1)->endOfWeek()->subDays(3)->format('W');
                    $yearp=$picos->format('Y');
                    $fs=new Carbon($r->fecha_siembra);
                    $fst=new Carbon($r->fecha_pico);
                    $fst->subWeeks($r->ciclo); //se restan las semanas para obtener la fecha Teorica de seimbra
    					?>
    					<div class="col-2 sinBorde1"><h5 class="font-weight-bold"><?php echo $r->finca; ?></h1></div>
    					<div class="col-6 sinBorde1"><h4 class="font-weight-bold"><?php		echo $r->temporada; ?></h2>	</div>
    					<div class="col-2 sinBorde1"><h4 class="font-weight-bold"><?php		echo number_format($r->plantas,0,',','.'); ?></h4> </div>
    					<div class="col-1 sinBorde1"><h5><?php		echo number_format($r->plantasM2,2,'.',','); ?></h5> </div>
    					<div class="col-1 sinBorde1"><h4><?php		echo $r->ciclo; ?></h4> </div>
            </div>
          </div>
        </div>
    </div>    
  </div>

  <div class="row forRow">
    <div class="col-12">
          <div class="row justify-content-md-center">
            <div class="col-12">
              <div class="row">
              <?php if ($r->tipo_siembra=='REEMPLAZO'){ ?>
                <div class="col-2 sinBorde4"><strong>REEMPLAZO de <?php echo $r->pico; ?></strong></div>
              <?php }elseif ($r->tipo_siembra=='ADICIONAL'){ ?>
                <div class="col-2 sinBorde4"><strong>ADICIONAL</strong></div>
              <?php }else{ ?>  
                <div class="col-2 sinBorde4">Fecha Siembra T.</div>
              <?php } ?>
                <div class="col-2 sinBorde4">Fecha Siembra R.</div>
                <div class="col-2 sinBorde4">Semana Pico T.</div>
                <div class="col-2 sinBorde4">Semana Pico R.</div>
                <div class="col-2 sinBorde4">Origen</div>
                <div class="col-2 sinBorde4">Casa C.</div>
              </div>
            </div>
          </div>
          <div class="row justify-content-md-center">
            <div class="col-12">
              <div class="row">
                <div class="col-2 sinBorde1">
                  <?php		
                      if ($r->tipo_siembra=='REEMPLAZO'){
                        ?>
                          <p class="small">
                            <?php
                              echo $r->cosecha_reem.'-'.$r->variedad_reem;
                            ?>
                          </p>
                        <?php  
                      }elseif ($r->tipo_siembra=='ADICIONAL'){
                        ?>
                          <h6 class="font-weight-bold">
                            <?php
                              echo $fs->format('d-m-Y/W');
                            ?>
                          </h6>
                        <?php
                      }else {
                        ?>
                          <h6 class="font-weight-bold">
                            <?php
                              echo $fst->format('d-m-Y/W');
                            ?>
                          </h6>
                        <?php
                      }
                  ?> 
                </div>
      					<div class="col-2 sinBorde1"><h6 class="font-weight-bold"><?php		echo $fs->format('d-m-Y/W'); ?> </h4></div>
      					<div class="col-2 sinBorde1"><h6 class="font-weight-bold"><?php    echo $p3.'-'.$p2.'-'.$p1.'-'.$p ?></div>
                <div class="col-2 sinBorde1"><h6><?php		echo ""; ?> </div>
                <div class="col-2 sinBorde1"><p class="small"><?php		echo $r->origen; ?></p> </div>
                <div class="col-2 sinBorde1"><p class="small"><?php		echo $r->casa; ?></p> </div>
    				  </div>
            </div>
          </div>

          <div class="row forRow">
            <div class="col-12">
              <div class="row">
                <div class="col-4 sinBorde1">
                  <div class=""><p class="small"><?php		echo $r->tipo_suelo; ?></p> </div>
                </div>
                <div class="col-4 sinBorde1">
                  <div class=""><p class="small"><?php		echo $r->nmanguera; ?></p> </div>
                </div>
                <div class="col-4 sinBorde1">
                  <div class=""><p class="small"><?php		//echo $r->tipo_suelo; ?></p> </div>
                </div>
              </div>
            </div>
          </div>

    </div>
  </div>          
			 <div class="row justify-content-md-center">
				 <div class="col-6">
					 <div class="row">
						 <div class="col-3 sinBorde"><strong>Labores</strong></div>
						 <div class="col-2 sinBorde"><strong>Tipo</strong></div>
						 <div class="col-4 sinBorde"><strong>Fecha</strong></div>
						 <div class="col-3 sinBorde"><p class="small">Dato_Real</p></div>
					 </div>
				 </div>
				 <div class="col-6 ">
					 <div class="row">
						 <div class="col-4 sinBorde"><strong>Aplicaciones</strong></div>
						 <div class="col-4 sinBorde"><strong>Tipo</strong></div>
						 <div class="col-2 sinBorde"><strong>Fecha</strong></div>
						 <div class="col-2 sinBorde"><p class="small">Dato_Real</p></div>
					 </div>
				 </div>
			 </div>
   
			 <?php
       $sqlA="SELECT
        a.tipo,a.aplicar,a.valor,p.fecha_siembra,aa.seccion,if(isnull(pr.ciclo),v.ciclo,pr.ciclo) as ciclo,aa.calc_conciclo as cc,
        s.fecha_pico
        FROM plane as p
        LEFT JOIN seasons as s
        ON p.temporada=s.nombre
        left join varieties as v
        on p.variedad=v.nombre
        left join arrangements as a
        on p.finca=a.finca and p.variedad=a.variedad
        LEFT JOIN (SELECT variedad,programa,ciclo FROM program group by 1,2,3) as pr
        ON pr.variedad=p.variedad and pr.programa=s.año
        left join arrangement as aa
        on a.tipo=aa.tipo and a.aplicar=aa.aplicar
        WHERE p.variedad='$r->variedad' and p.temporada='$r->temporada'
        and p.bloque=$r->bloque and p.finca='$r->finca' and aa.seccion=1
        and p.fecha_siembra='$fs'
        GROUP BY a.tipo,a.aplicar,a.valor,p.fecha_siembra,aa.seccion,v.ciclo,aa.calc_conciclo,
        s.fecha_pico
        ORDER BY p.bloque asc,aa.orden
      ";
      $sqlB="SELECT
       a.tipo,a.aplicar,a.valor,p.fecha_siembra,aa.seccion,if(isnull(pr.ciclo),v.ciclo,pr.ciclo) as ciclo,aa.calc_conciclo as cc,
       s.fecha_pico
       FROM plane as p
       LEFT JOIN seasons as s
       ON p.temporada=s.nombre
       left join varieties as v
        on p.variedad=v.nombre
       left join arrangements as a
       on p.finca=a.finca and p.variedad=a.variedad
       LEFT JOIN (SELECT variedad,programa,ciclo FROM program group by 1,2,3) as pr
       ON pr.variedad=p.variedad and pr.programa=s.año
       left join arrangement as aa
       on a.tipo=aa.tipo and a.aplicar=aa.aplicar
       WHERE p.variedad='$r->variedad' and p.temporada='$r->temporada'
       and p.bloque=$r->bloque and p.finca='$r->finca' and aa.seccion=2
       and p.fecha_siembra='$fs'
       GROUP BY a.tipo,a.aplicar,a.valor,p.fecha_siembra,aa.seccion,v.ciclo,aa.calc_conciclo,
       s.fecha_pico
       ORDER BY aa.orden,a.valor ASC
     ";
			$queryA=$conexion->query($sqlA);
			$queryB=$conexion->query($sqlB);
      $sqlNum=$queryB->num_rows;
      $sqlNumA=$queryA->num_rows - 1;
			?>
	<div class="row">
		<div class="col-6 forRow">
			<?php
      $kind="";
      while($rA = $queryA->fetch_object()){
 
        
        //$fechas=new Carbon($rA->fecha_siembra);//fecha de siembra  
        $fecha_pico = new Carbon($rA->fecha_pico);//fecha de siembra
        if ($r->tipo_siembra == 'REEMPLAZO' || $r->tipo_siembra == 'ADICIONAL' ){
          $fechas=new Carbon($rA->fecha_siembra);//fecha de siembra  
        }else{
          $fechas = $fecha_pico->subWeeks($rA->ciclo);
        }
        

        //cc es la variable que me indica que hago calculos con ciclo y es = a 1 sino es 0
        if($rA->cc==1){
          echo $fechas;
          $faplicar=$fechas->addDays($rA->valor)->addWeeks($rA->ciclo)->endOfWeek()->subDays(3)->format('yW'); 
        }elseif ($rA->cc==2) {//si es dos es caso especial y multiplicamos el ciclo por 2 mas 2 semanas
          if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL' ){
            $fechap=new Carbon($rA->fecha_pico);//fecha de pico
          }else {
            $fechap=new Carbon($fechas->addWeeks($rA->ciclo));
          }
          ///////////////////////////////////////////////////////////////////////////
          $faplicar=$fechap->addWeeks($rA->ciclo)->addDays($rA->valor)->format('yW'); 
        }
        elseif ($rA->cc==3) {
            if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL' ){
              $fechap=new Carbon($rA->fecha_pico);//fecha de pico
            }else {
              $fechap=new Carbon($fechas->addWeeks($rA->ciclo));
            }
          //////////////////////////////////////////////////////////////////////////  
          $faplicar=$fechap->subWeeks($rA->ciclo)->addDays($rA->valor)->format('yW'); 
        }
        elseif ($rA->cc==4){
            if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL'){
              $fechap=new Carbon($rA->fecha_pico);//fecha de pico
            }else {
              $fechap=new Carbon($fechas->addWeeks($rA->ciclo));
            }
            ///////////////////////////////////////////////////////////////////////////////  
          $faplicar=$fechap->subWeeks($rA->ciclo)->addDays($rA->valor)->format('d-m-Y/W');  
        }
        elseif ($rA->cc==5){
          $faplicar=$fechas->addDays($rA->valor)->endOfWeek()->subDays(3)->format('yW');
        }
        elseif ($rA->cc==6){
          if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL'){
            $fechap=new Carbon($rA->fecha_pico);//fecha de pico
          }else {
            $fechap=new Carbon($fechas->addWeeks($rA->ciclo));
          }
          /////////////////////////////////////////////////////////////////////////////
          $faplicar=$fechap->subDays($rA->valor)->endOfWeek()->subDays(3)->format('yW');
          
        }
        elseif ($rA->cc==7) {//si es dos es caso especial y multiplicamos el ciclo por 2 mas 2 semanas
          if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL'){
            $fechap=new Carbon($rA->fecha_pico);//fecha de pico
          }else {
            $fechap=new Carbon($fechas->addWeeks($rA->ciclo));
          }
          ///////////////////////////////////////////////////////////////////////////
          $faplicar=$fechap->addDays($rA->valor)->format('yW'); 
        }

				 ?>

				 <div class="row justify-content-md-center">
           <?php if ($rA->seccion==1){
            if ($kind==$rA->tipo){ ?>
              <div class="col-3 sinBorde4"><p>
              <?php
            }else { ?>
              <div class="col-3 sinBorde5"><p>
              <?php
              echo $rA->tipo;
            }
           } ?>	</p></div>
					 <div class="col-2 sinBorde"><p> <?php if ($rA->seccion==1){echo $rA->aplicar;} ?>	</p></div>
					 <div class="col-4 sinBorde"><h6 class="font-weight-bold"> <?php if ($rA->seccion==1){echo $faplicar;}	?> </h></div>
					 <div class="col-3 sinBorde"></div>
					</div>
        <?php 
        $kind=$rA->tipo;
      } ?>
      </div>
      <div class="col-6 forRow">
        <?php
        $kind="";
        $x=0;
        while($rB = $queryB->fetch_object()){

          $fechas=new Carbon($rB->fecha_siembra);

          if ($rB->cc==1){
            if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL'){
              $fechap=new Carbon($rB->fecha_pico);//fecha de pico
            }else {
              $fechap=new Carbon($fechas->addWeeks($rB->ciclo));
            }      

            $faplicar=$fechap->addDays($rB->valor)->year;
          }
          elseif ($rB->cc==7) {//si es dos es caso especial y multiplicamos el ciclo por 2 mas 2 semanas
            if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL' ){
              $fechap=new Carbon($rB->fecha_pico);//fecha de pico
            }else {
              $fechap=new Carbon($fechas->addWeeks($rB->ciclo));
            }
            ///////////////////////////////////////////////////////////////////////////
            $faplicar=$fechap->addDays($rB->valor)->format('yW'); 
          }
          else{
            if ($r->tipo_siembra != 'REEMPLAZO' && $r->tipo_siembra != 'ADICIONAL'){
              $fechap=new Carbon($rB->fecha_pico);//fecha de pico
            }else {
              $fechap=new Carbon($fechas->addWeeks($rB->ciclo));
            }

            $faplicar= new carbon($fechap->subWeeks($rB->ciclo)->addDays($rB->valor)->endOfWeek()->subDays(3));
            $faplicar=$faplicar->format('yW');
          }
                ?>
                <div class="row justify-content-md-center">

                  <?php if ($rB->seccion==2){
                    if ($kind==$rB->tipo){ ?>
                      <div class="col-4 sinBorde4"><p>
                      <?php
                    }else { ?>
                    <div class="col-4 sinBorde5"><p>
                    <?php
                      echo $rB->tipo;
                    }
                  } ?>	</p></div>
                  <div class="col-4 sinBorde"><p> <?php if ($rB->seccion==2){echo $rB->aplicar;} ?>	</p></div>
                  <div class="col-2 sinBorde"><h6 class="font-weight-bold"> <?php if ($rB->seccion==2){echo $faplicar;}	?> </h></div>
                  <div class="col-2 sinBorde"></div>

                </div>
            <?php

              $kind=$rB->tipo;
              $x++;
              if (($x==$sqlNum)){
                for ($i = $sqlNum; $i <= $sqlNumA; $i++)
                 {
            ?>
                <div class="row">
                <div class="col-4 sinBorde4">
                
                </div>
                  <div class="col-4 sinBorde"></div>
                  <div class="col-2 sinBorde"></div>
                  <div class="col-2 sinBorde"></div>
                </div>
                <?php

              }
            }
              } ?>
	 </div>
   <span> Nota: Formato Fecha Completa, dia-mes-año/semana; Formato Fecha Corta, año-semana </span></div>
   <div class="saltoDePagina d-print-block" id="saltoDePagina "></div>
		<?php } ?>
		<?php
		////////////////////////////////////////////////////////////////////////////
				}
			?>
		</div>
		</div><!--Cierro container general-->

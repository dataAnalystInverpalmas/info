<?php
    //lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
//carbon
require "vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;
Carbon::setLocale('es'); 

////////////producto='".$producto."' ";
//inicializa variable vacia
$where=" WHERE p.plantas>0 ";

$producto="";
$temporada="";
$fecha_ensarte="";
$finca="";
$encabezado=0;

$prg="SELECT max(programa) as y FROM programf ";
$result=$conexion->query($prg);

$row = $result -> fetch_assoc();
$y=$row['y'];

$programa=$y;

if(isset($_POST['xprograma'])){
  $programa=$_POST['xprograma'];
}
if(isset($_POST['xproducto'])){
  $producto=$_POST['xproducto'];
}
if(isset($_POST['xtemporada'])){
  $temporada=$_POST['xtemporada'];
}
if(isset($_POST['xfinca'])){
  $finca=$_POST['xfinca'];
}

if(isset($_POST['buscar'])){
  if ($programa!="" and $producto=="" and $temporada=="" and $finca=="") {
    $where=" WHERE p.plantas>0 and p.programa='".$programa."' " ;
  } elseif ($programa=="" and $producto!="" and $temporada=="" and $finca=="") {
    $where=" WHERE p.plantas>0 and p.producto='".$producto."' " ;
  }elseif ($programa!="" and $producto!="" and $temporada=="" and $finca=="") {
    $where=" WHERE p.plantas>0 and p.producto='".$producto."' and p.programa='".$programa."' " ;
  }
  elseif ($programa!="" and $producto!="" and $temporada!="" and $finca!="") {
    $where=" WHERE p.plantas>0 and p.producto='".$producto."'
    and p.programa='".$programa."' and p.temporada_obj='".$temporada."' and p.finca='".$finca."' " ;
    $encabezado=1;
  }
  elseif ($programa=="" and $producto=="" and $temporada!="" and $finca=="") {
    $where=" WHERE p.plantas>0 and p.temporada_obj='".$temporada."' " ;
  }elseif ($programa=="" and $producto!="" and $temporada!="" and $finca!="") {
    $where=" WHERE p.plantas>0 and p.producto='".$producto."' and p.temporada_obj='".$temporada."'
    and p.finca='".$finca."' " ;
    $encabezado=1;
  }elseif ($programa!="" and $producto!="" and $temporada!="" and $finca=="") {
    $where=" WHERE p.plantas>0 and p.producto='".$producto."' and p.temporada_obj='".$temporada."'
    and p.finca<>'' " ;
    $encabezado=1;
  }
  
  else {
    $where=" WHERE p.plantas>0 ";
  }
}

    //CONSULTA
    $sql="SELECT p.variedad,p.temporada_obj,p.producto,p.ciclo,
     p.fecha_siembra,p.fecha_pico,p.abreviatura,p.bloque, sum(p.plantas) as plantas,
     ROUND(sum(p.plantas)/960,0) as ncamas,p.casa FROM print_budget as p
     $where
    GROUP BY p.variedad,p.temporada_obj,p.producto,p.fecha_siembra,p.fecha_pico,p.finca,p.bloque
    ORDER BY p.fecha_pico,p.fecha_siembra,p.producto,p.variedad ASC
    ";
    $result=$conexion->query($sql);

    //CONSULTA PARA COMBO
    $slqCOMBO="SELECT programa FROM programf GROUP BY programa";
    $COM=$conexion->query($slqCOMBO);
    //CONSULTA PARA COMBO2
    $slqCOMBO2="SELECT producto FROM programf GROUP BY 1";
    $COM2=$conexion->query($slqCOMBO2);
    //CONSULTA PARA COMBO3
    $slqCOMBO3="SELECT temporada_obj FROM programf WHERE Programa=$programa GROUP BY 1 ORDER BY fecha_pico";
    $COM3=$conexion->query($slqCOMBO3);
    //CONSULTA PARA COMBO4
    $slqCOMBO4="SELECT finca FROM programf GROUP BY 1";
    $COM4=$conexion->query($slqCOMBO4);
    //CONSULTAS PARA ENCABEZADO INDEPENDIENTES
    $sqlSUM="SELECT programa,producto,tipo,temporada_obj,fecha_pico,sum(plantas) as plantas,sum(plantas)/960 as ncamas
    FROM programf as p
    $where
    GROUP BY 1,2,3,4,5";

    $bancos=$conexion->query($sqlSUM);
?>

<div class="card-header d-print-none">
  <form class="form-inline" action="home.php?menu=tables&report=6" method="post" enctype="multipart/form-data">
  <div class="form-group mb-2">
      <select name="xprograma" class="form-control" data-live-search="true"> <!--FINCA-->
        <option value="">Año Programa</option>
        <?php
        while($f = $COM->fetch_object()){
          if($f->programa==$programa){
          echo "<option value='".$f->programa."' selected='selected'>" .$f->programa. "</option>";
        }else{
          echo "<option value='".$f->programa."'>" .$f->programa. "</option>";
        }

        }
        ?>
      </select>
      <select name="xproducto" class="form-control" data-live-search="true"> <!--FINCA-->
          <option value="">Producto</option>
          <?php
          while($f = $COM2->fetch_object()){

            if($f->producto==$producto){
            echo "<option value='".$f->producto."' selected='selected'>" .$f->producto. "</option>";
          }else{
            echo "<option value='".$f->producto."'>" .$f->producto. "</option>";
          }
          }
            ?>
      </select>
      <select name="xtemporada" class="form-control" data-live-search="true"> <!--FINCA-->
              <option value="">Temporada</option>
              <?php
              while($f = $COM3->fetch_object()){

                if($f->temporada_obj==$temporada){
                echo "<option value='".$f->temporada_obj."' selected='selected'>" .$f->temporada_obj. "</option>";
              }else{
                echo "<option value='".$f->temporada_obj."'>" .$f->temporada_obj. "</option>";
              }
              }
              ?>
      </select>
      <select name="xfinca" class="form-control" data-live-search="true"> <!--FINCA-->
                <option value="">Finca</option>
                <?php
                while($f = $COM4->fetch_object()){

                  if($f->finca==$finca){
                  echo "<option value='".$f->finca."' selected='selected'>" .$f->finca. "</option>";
                }else{
                  echo "<option value='".$f->finca."'>" .$f->finca. "</option>";
                }
                }
                  ?>
      </select>
        </div>
      <div class="form-group mx-sm-3 mb-2">
            <button name="buscar" type="submit" class="btn btn-primary mb-2">Buscar</button>
      </div>
      <div class="form-group mx-sm-3 mb-2">
            <button name="Print" type="submit" class="btn btn-success mb-2" onclick="imprime();">Imprimir</button>
      </div>
  </form>
</div>

<?php

if ($result->num_rows>0){ ?>
<div class="landscape2">
<?php  if ($encabezado==1){
    ?>
  <div class="row">
    <?php while ($b=$bancos->fetch_object()) {
      
      $fecha=new carbon($b->fecha_pico);

      ?>
      <div class="col-1">
        <h4>Siembras <br> <?php echo $b->programa; ?></h5>
      </div>
      <div class="col-2">
        <h4>Finca: <br>
          <?php if ($finca==""){ echo "INVERPALMAS S.A.S"; }else{ ?>
          <?php echo $b->finca; } ?> 
        </h6>
      </div>
      <div class="col-1">
        <h4>Producto: <br><?php echo $b->producto; ?></h5>
      </div>
      <div class="col-3">
        <h4>Temporada: <br><?php echo $b->temporada_obj; ?></h5>
      </div>
      <div class="col-2">
        <h4>Fecha pico: <br><?php echo $fecha->format('D d-m-Y / W'); ?></h5>
      </div>
      <div class="col-1">
        <h4>#Camas: <br><?php echo number_format($b->ncamas,0,',','.'); ?></h5>
      </div>
      <div class="col-1">
        <h4>#Plantas: <br><?php echo number_format($b->plantas,0,',','.'); ?></h5>
      </div>
      <div class="col-1">
        <h4>Tipo: <br><?php echo $b->tipo; ?></h5>
      </div>
    <?php }} ?>
  </div>
	<div class="row">
    <div class="col-12">
        <table class="table table-sm center">
          <thead>
            <tr>
              <th>Nombre_de_la_Variedad</th>
              <th>Variedad_de_Reemplazo</th>
              <th>Ciclo</th>
              <th>#Camas</th>
              <th>No Platas Teorico</th>
              <th>No Plantas Real</th>
              <th>Fecha Siembra_Teorica</th>      
              <th>Fecha Siembra_Real</th>
              <th>Finca Teorica</th>
              <th>Finca Real</th>
              <th>Bloque Teorico</th>
              <th>Bloque Real</th>
              <th>Tipo Cascarilla</th>
              <th>Semana Teo</th>
              <th>Semana Real</th>
              <th>Maquilador</th>
              <th>Observaciones_de_la_Siembra</th>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($f=$result->fetch_object()){
            $fecha=new carbon($f->fecha_siembra);
              if (isset($_POST['buscar'])){
              ?>  
              <tr style="height:30px">
                <td><h5 class="font-weight-bold"><?php echo $f->variedad; ?></h></td>
                <td></td>
                <td><?php echo $f->ciclo; ?></td>
                <td><h5 class="font-weight-bold"><?php echo $f->ncamas; ?></h></td>
                <td><h5 class="font-weight-bold"><?php echo number_format($f->plantas,0,',','.'); ?></h></td>
                <td></td>
                <td><h5 class="font-weight-bold"><?php echo $fecha->endOfWeek()->subDays(4)->format('d-m-Y'); ?></h></td> 
                <td></td>
                <td><?php echo $f->abreviatura; ?></td>
                <td></td>
                <td><?php echo $f->bloque; ?></td>
                <td></td>
                <td></td>
                <td><h5 class="font-weight-bold"><?php echo $fecha->endOfWeek()->subDays(4)->format('W'); ?></h></td> 
                <td></td>
                <td><?php echo $f->casa; ?></td>
                <td></td>
              </tr>
              <?php  
              }/* else{
              ?>  
              <tr style="height:30px">
                <td><?php echo $f->variedad; ?></td>
                <td></td>
                <td><?php echo $f->ciclo; ?></td>
                <td><?php echo $f->ncamas; ?></td>
                <td><?php echo number_format($f->plantas,0,',','.'); ?></td>
                <td></td>
                <td><?php echo $fecha->endOfWeek()->subDays(4)->format('d-m-Y'); ?></td> 
                <td></td>
                <td><?php echo $f->abreviatura; ?></td>
                <td></td>
                <td><?php echo $f->bloque; ?></td>
                <td></td>
                <td></td>
                <td><?php echo $fecha->endOfWeek()->subDays(4)->format('W'); ?></td> 
                <td></td>
                <td></td>
              </tr>
            <?php
            } */
          }
          ?>
        </tbody>

        <?php for ($i=0;$i<5;$i++){ ?>
        <tr style="height:30px">
          <?php for ($j=1;$j<=17;$j++){ ?> 
            <td></td>
          <?php } ?> 
        </tr>
        <?php } ?>

       </table>
    </div>
  </div>
</div>
<?php
}
?>

<?php
//$conexion->close();
?>

<script>
function imprime(){
  var css = '@page { size: landscape; }',
    head = document.head || document.getElementsByTagName('head')[0],
    style = document.createElement('style');
  
    style.type = 'text/css';
    style.media = 'print';
  
  if (style.styleSheet){
    style.styleSheet.cssText = css;
  } else {
    style.appendChild(document.createTextNode(css));
  }
  
    head.appendChild(style);
    window.print();
}
</script>
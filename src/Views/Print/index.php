<?php
global $conexion;
use Carbon\Carbon;

Carbon::setLocale('es');

$where = " WHERE p.plantas > 0 AND p.estado = 1";
$producto = "";
$temporada = "";
$fecha_ensarte = "";
$finca = "";
$encabezado = 0;

$prg = "SELECT max(programa) as y FROM programf where estado=1";
$result = $conexion->query($prg);
$row = $result->fetch_assoc();
$y = $row['y'];
$programa = $y;

if (isset($_POST['xprograma'])) {
    $programa = $_POST['xprograma'];
}
if (isset($_POST['xproducto'])) {
    $producto = $_POST['xproducto'];
}
if (isset($_POST['xtemporada'])) {
    $temporada = $_POST['xtemporada'];
}
if (isset($_POST['xfinca'])) {
    $finca = $_POST['xfinca'];
}

if (isset($_POST['buscar'])) {
  $programaEsc = $conexion->real_escape_string($programa);
  $productoEsc = $conexion->real_escape_string($producto);
  $temporadaEsc = $conexion->real_escape_string($temporada);
  $fincaEsc = $conexion->real_escape_string($finca);

  if ($programa != "" && $producto == "" && $temporada == "" && $finca == "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.programa = '$programaEsc'";
  } elseif ($programa != "" && $producto != "" && $temporada == "" && $finca == "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.producto = '$productoEsc' AND p.programa = '$programaEsc'";
    $encabezado = 2;
  } elseif ($programa != "" && $producto != "" && $temporada == "" && $finca != "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.producto = '$productoEsc' AND p.programa = '$programaEsc' AND p.finca = '$fincaEsc'";
    $encabezado = 1;
  } elseif ($programa != "" && $producto == "" && $temporada == "" && $finca != "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.programa = '$programaEsc' AND p.finca = '$fincaEsc'";
  } elseif ($programa != "" && $producto != "" && $temporada != "" && $finca != "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.producto = '$productoEsc' AND p.programa = '$programaEsc' AND p.temporada_obj = '$temporadaEsc' AND p.finca = '$fincaEsc'";
    $encabezado = 1;
  } elseif ($programa == "" && $producto == "" && $temporada != "" && $finca == "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.temporada_obj = '$temporadaEsc'";
  } elseif ($programa == "" && $producto != "" && $temporada != "" && $finca != "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.producto = '$productoEsc' AND p.temporada_obj = '$temporadaEsc' AND p.finca = '$fincaEsc'";
    $encabezado = 1;
  } elseif ($programa != "" && $producto != "" && $temporada != "" && $finca == "") {
    $where = " WHERE p.plantas > 0 AND p.estado = 1 AND p.producto = '$productoEsc' AND p.programa = '$programaEsc' AND p.temporada_obj = '$temporadaEsc' AND p.finca <> ''";
    $encabezado = 1;
  } else {
    $where = " WHERE p.plantas > 0 AND p.estado = 1";
  }
}

$sql = "SELECT p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.fecha_pico, p.abreviatura, p.bloque,
        SUM(p.plantas) AS plantas, ROUND(SUM(p.plantas) / 960, 0) AS ncamas, p.casa 
        FROM print_budget AS p $where
        GROUP BY p.variedad, p.temporada_obj, p.producto, p.fecha_siembra, p.fecha_pico, p.finca, p.bloque,p.ciclo
        ORDER BY p.fecha_siembra, p.producto, p.finca, p.bloque, p.variedad ASC";

$result = $conexion->query($sql);

$slqCOMBO = "SELECT programa FROM programf WHERE estado = 1 GROUP BY programa";
$COM = $conexion->query($slqCOMBO);

$slqCOMBO2 = "SELECT producto FROM programf WHERE estado = 1 GROUP BY 1";
$COM2 = $conexion->query($slqCOMBO2);

$programaCombo = $conexion->real_escape_string($programa);
$slqCOMBO3 = "SELECT temporada_obj FROM programf WHERE estado = 1 AND Programa = '$programaCombo' GROUP BY 1 ORDER BY fecha_pico";
$COM3 = $conexion->query($slqCOMBO3);

$slqCOMBO4 = "SELECT finca FROM programf WHERE estado = 1 GROUP BY 1";
$COM4 = $conexion->query($slqCOMBO4);

$sqlSUM = "SELECT programa, producto, finca, SUM(plantas) AS plantas, SUM(plantas) / 960 AS ncamas
           FROM programf AS p $where GROUP BY 1, 2, 3";

$sqlSUM_t = "SELECT programa, producto, SUM(plantas) AS plantas, SUM(plantas) / 960 AS ncamas
             FROM programf AS p $where GROUP BY 1, 2";

$bancos = $conexion->query($sqlSUM);
$bancos_t = $conexion->query($sqlSUM_t);
?>

<div class="card-header d-print-none">
  <form class="form-inline" action="home.php?menu=tables&report=6" method="post" enctype="multipart/form-data">
    <div class="form-group mb-2">
      <select name="xprograma" class="form-control" data-live-search="true">
        <option value="">Año Programa</option>
        <?php
        while ($f = $COM->fetch_object()) {
          if ($f->programa == $programa) {
            echo "<option value='{$f->programa}' selected='selected'>{$f->programa}</option>";
          } else {
            echo "<option value='{$f->programa}'>{$f->programa}</option>";
          }
        }
        ?>
      </select>
      <select name="xproducto" class="form-control" data-live-search="true">
        <option value="">Producto</option>
        <?php
        while ($f = $COM2->fetch_object()) {
          if ($f->producto == $producto) {
            echo "<option value='{$f->producto}' selected='selected'>{$f->producto}</option>";
          } else {
            echo "<option value='{$f->producto}'>{$f->producto}</option>";
          }
        }
        ?>
      </select>
      <select name="xtemporada" class="form-control" data-live-search="true">
        <option value="">Temporada</option>
        <?php
        while ($f = $COM3->fetch_object()) {
          if ($f->temporada_obj == $temporada) {
            echo "<option value='{$f->temporada_obj}' selected='selected'>{$f->temporada_obj}</option>";
          } else {
            echo "<option value='{$f->temporada_obj}'>{$f->temporada_obj}</option>";
          }
        }
        ?>
      </select>
      <select name="xfinca" class="form-control" data-live-search="true">
        <option value="">Finca</option>
        <?php
        while ($f = $COM4->fetch_object()) {
          if ($f->finca == $finca) {
            echo "<option value='{$f->finca}' selected='selected'>{$f->finca}</option>";
          } else {
            echo "<option value='{$f->finca}'>{$f->finca}</option>";
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

<?php if ($result->num_rows > 0) { ?>
  <div class="landscape2">
    <?php if ($encabezado == 1) { ?>
      <div class="row">
        <?php while ($b = $bancos->fetch_object()) { ?>
          <div class="col-1">
            <h4>Siembras <br> <?php echo $b->programa; ?></h4>
          </div>
          <div class="col-2">
            <h4>Finca: <br>
              <?php echo ($finca == "") ? "Global Empresa" : $b->finca; ?>
            </h4>
          </div>
          <div class="col-1">
            <h4>Producto: <br><?php echo $b->producto; ?></h4>
          </div>
          <div class="col-1">
            <h4>#Camas: <br><?php echo number_format($b->ncamas, 0, ',', '.'); ?></h4>
          </div>
          <div class="col-1">
            <h4>#Plantas: <br><?php echo number_format($b->plantas, 0, ',', '.'); ?></h4>
          </div>
        <?php } ?>
      </div>
    <?php } else if ($encabezado == 2) { ?>
      <div class="row">
        <?php while ($b = $bancos_t->fetch_object()) { ?>
          <div class="col-1">
            <h4>Siembras <br> <?php echo $b->programa; ?></h4>
          </div>
          <div class="col-2">
            <h4>Finca: <br>
              <?php echo ($finca == "") ? "Global Empresa" : $b->finca; ?>
            </h4>
          </div>
          <div class="col-1">
            <h4>Producto: <br><?php echo $b->producto; ?></h4>
          </div>
          <div class="col-1">
            <h4>#Camas: <br><?php echo number_format($b->ncamas, 0, ',', '.'); ?></h4>
          </div>
          <div class="col-1">
            <h4>#Plantas: <br><?php echo number_format($b->plantas, 0, ',', '.'); ?></h4>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
      <div class="row">
        <div class="col-12">
          <table class="table table-sm center">
            <thead>
              <tr>
                <th>Nombre_de_la_Variedad</th>
                <th>Variedad_Reemplazo</th>
                <th>Cosecha</th>
                <th>Pico</th>
                <th>#Camas</th>
                <th>No Platas Teorico</th>
                <th>Año_Mes Siembra</th>
                <th>Finca Teorica</th>
                <th>Finca Real</th>
                <th>Bloque Teorico</th>
                <th>Bloque Real</th>
                <th>Semana Teo</th>
                <th>Observaciones_de_la_Siembra</th>
              </tr>
            </thead>
            <tbody>
              <?php
              while ($f = $result->fetch_object()) {
                $fecha = new Carbon($f->fecha_siembra);
                if (isset($_POST['buscar'])) { ?>
                  <tr style="height:30px">
                    <td><h5 class="font-weight-bold"><?php echo str_replace(' ', '_', $f->variedad); ?></h5></td>
                    <td></td>
                    <td><?php echo $f->temporada_obj; ?></td>
                    <td><?php echo $f->ciclo; ?></td>
                    <td><h5 class="font-weight-bold"><?php echo $f->ncamas; ?></h5></td>
                    <td><h5><?php echo number_format($f->plantas, 0, ',', '.'); ?></h5></td>
                    <td><h5><?php echo $fecha->endOfWeek()->subDays(4)->format('Y-m'); ?></h5></td>
                    <td class="font-weight-bold"><?php echo $f->abreviatura; ?></td>
                    <td></td>
                    <td><?php echo $f->bloque; ?></td>
                    <td></td>
                    <td><h5 class="font-weight-bold"><?php echo $fecha->endOfWeek()->subDays(4)->format('W'); ?></h5></td>
                    <td></td>
                  </tr>
              <?php }
              } ?>
            </tbody>
            <?php for ($i = 0; $i < 5; $i++) { ?>
              <tr style="height:30px">
                <?php for ($j = 1; $j <= 13; $j++) { ?>
                  <td></td>
                <?php } ?>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
  </div>
<?php } ?>

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

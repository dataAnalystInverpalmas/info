<?php
global $conexion;
use Carbon\Carbon;

$where = " WHERE p.plantas>0 ";
$producto = "";
$temporada = "";
$finca = "";
$header = false;

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
    if ($producto != "" && $temporada != "" && $finca != "") {
        $where .= " AND p.producto='" . $producto . "' AND p.temporada='" . $temporada . "' AND p.finca='" . $finca . "' ";
        $header = true;
    } else {
        $where = " WHERE p.plantas>0 ";
    }
}

$sql = "SELECT IFNULL(p.bloque, 'Total') AS bloque, IFNULL(p.variedad, 'Total') AS variedad, IFNULL(p.tipo_siembra, 'Total') AS tipo_siembra, sum(p.plantas) as plantas
    FROM plane as p 
    LEFT JOIN seasons AS s ON s.nombre=p.temporada
    LEFT JOIN varieties as v ON p.variedad=v.nombre
    LEFT JOIN farms AS f ON f.nombre=p.finca
    $where
    GROUP BY 1,2,3
    WITH ROLLUP";

$result = $conexion->query($sql);

$slqCOMBO2 = "SELECT producto FROM plane WHERE plantas>0 GROUP BY 1";
$COM2 = $conexion->query($slqCOMBO2);

$slqCOMBO3 = "SELECT temporada FROM plane WHERE plantas>0 GROUP BY 1";
$COM3 = $conexion->query($slqCOMBO3);

$slqCOMBO4 = "SELECT finca FROM plane WHERE plantas>0 GROUP BY 1";
$COM4 = $conexion->query($slqCOMBO4);
?>

<div class="card-header d-print-none">
<form class="form-inline" action="home.php?menu=tables&report=8" method="post" enctype="multipart/form-data">
  <div class="form-group mb-2">
    <select id="producto" name="xproducto" class="form-control" data-live-search="true">
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

    <select id="temporada" name="xtemporada" class="form-control" data-live-search="true">
      <option value="">Temporada</option>
      <?php
      while ($f = $COM3->fetch_object()) {
        if ($f->temporada == $temporada) {
          echo "<option value='{$f->temporada}' selected='selected'>{$f->temporada}</option>";
        } else {
          echo "<option value='{$f->temporada}'>{$f->temporada}</option>";
        }
      }
      ?>
    </select>

    <select id="finca" name="xfinca" class="form-control" data-live-search="true">
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
    <button name="print" type="submit" class="btn btn-success mb-2" onclick="imprime();">Imprimir</button>
  </div>
</form>
</div>

<?php
if ($result->num_rows > 0 && $header) {
    $temporadaEsc = $conexion->real_escape_string($_POST['xtemporada']);
    $sqlFP = "SELECT fecha_pico FROM seasons WHERE nombre='$temporadaEsc'";
    $resultFP = $conexion->query($sqlFP);
    $row = $resultFP->fetch_assoc();
    $sempico = new Carbon($row['fecha_pico']);
?>
   <div id="hello"></div>
   <script type="text/javascript">
       title();
       function title(){
           var finca = document.getElementById("finca").value;
           var producto = document.getElementById("producto").value;
           var temporada = document.getElementById("temporada").value;
           document.getElementById('hello').innerHTML = "Finca: "+finca+"<br> Producto: "+producto+"<br> Evaluación de Cosecha: "+temporada;
       };
   </script>

<div class="row portrait2">
  <div class="col">
    <table id="cosechas" class="table display nowrap">
      <thead>
        <tr>
          <th style="width: 50px">Bloque</th>
          <th style="width: 400px">Nombre_Variedad</th>
          <th style="width: 50px">Tipo siembra</th>
          <th style="width: 50px">No plantas</th>
          <th><?php echo $sempico->subDays(70)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
          <th><?php echo $sempico->addDays(14)->format('W'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($f = $result->fetch_object()) {
            if (isset($_POST['buscar'])) {
                if (($f->variedad != null && $f->tipo_siembra != null) || ($f->variedad == null && $f->tipo_siembra == null)) { ?>
                  <tr style="height:30px">
                    <td class="bolded"><?php echo $f->bloque; ?></td>
                    <td class="bolded"><?php echo ($f->variedad == null) ? "Total" : $f->variedad; ?></td>
                    <td><?php echo $f->tipo_siembra; ?></td>
                    <td class="bolded"><?php echo number_format($f->plantas, 0, ",", "."); ?></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                  </tr>
        <?php }}} ?>
      </tbody>
      <tfoot>
        <tr><td></td></tr>
      </tfoot>
    </table>
  </div>
</div>
<?php } else { ?>
  <h1>No hay resultados</h1>
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

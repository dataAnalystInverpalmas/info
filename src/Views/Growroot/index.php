<?php
global $conexion;
use Carbon\Carbon;

$where = " WHERE p.plantas>0 AND p.raiz=0 AND p.estado=1 ";
$programa = "";
$fecha_ensarte = "";
$tbanco = 18000;

if (isset($_POST['tipo'])) {
    $tabla = $_POST['tipo'];
} else {
    $tabla = "program";
}

if (isset($_POST['xprograma'])) {
    $programa = $_POST['xprograma'];
} else {
    $prg = "SELECT max(programa) as y FROM program";
    $result = $conexion->query($prg);
    $row = $result->fetch_assoc();
    $programa = $row['y'];
}

if (isset($_POST['xfecha'])) {
    $fecha_ensarte = new Carbon($_POST['xfecha']);
}

if (isset($_POST['buscar'])) {
    if ($programa != "" && $fecha_ensarte == "") {
        $where .= " AND p.programa='" . $programa . "' ";
    } elseif ($programa == "" && $fecha_ensarte != "") {
        $where .= " AND p.fecha_ensarte between '" . $fecha_ensarte->startOfWeek() . "' and '" . $fecha_ensarte->endOfWeek() . "' ";
    } elseif ($programa != "" && $fecha_ensarte != "") {
        $where .= " AND p.fecha_ensarte between '" . $fecha_ensarte->startOfWeek() . "' and '" . $fecha_ensarte->endOfWeek() . "' and p.programa='" . $programa . "' ";
    }
}

$sql = "SELECT p.variedad,p.temporada_obj,p.producto,p.ciclo,
    p.fecha_siembra,p.fecha_pico,sum(p.plantas) as plantas,ROUND(sum(p.plantas)/960,0) as ncamas,
    p.fecha_temporada,p.fecha_ensarte,p.fecha_cosecha,s.cod_temporada,b.nombre as casa
    FROM $tabla as p
    LEFT JOIN seasons as s ON s.nombre=p.temporada_obj
    LEFT JOIN breeders as b ON b.id=p.casa_id
    $where
    GROUP BY p.variedad,p.temporada_obj,p.producto,b.nombre
    ORDER BY p.fecha_temporada,p.fecha_siembra,p.producto,p.variedad ASC";

$result = $conexion->query($sql);

$slqCOMBO = "SELECT programa FROM program GROUP BY programa";
$COM = $conexion->query($slqCOMBO);

$slqCOMBO2 = "SELECT fecha_ensarte FROM $tabla where programa=$programa GROUP BY YEARWEEK(fecha_ensarte)";
$COM2 = $conexion->query($slqCOMBO2);
?>

<div class="card-header d-print-none">
<form class="form-inline" action="home.php?menu=tables&report=7" method="post" enctype="multipart/form-data">
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
    <select id="tipo" name="tipo" class="form-control" data-live-search="true">
      <?php if ($tabla == 'program') { ?>
        <option value="program" selected='selected'>Programa</option>
        <option value="program_add_pto">Reemplazo</option>
      <?php } else { ?>
        <option value="program">Programa</option>
        <option value="program_add_pto" selected='selected'>Reemplazo</option>
      <?php } ?>
    </select>
  </div>
  <div class="form-group mb-2">
    <select name="xfecha" class="form-control" data-live-search="true">
      <option value="">Fecha Ensarte</option>
      <?php
      while ($f = $COM2->fetch_object()) {
        $fe = new Carbon($f->fecha_ensarte);
        if ($f->fecha_ensarte == $fecha_ensarte) {
          echo "<option value='{$f->fecha_ensarte}' selected='selected'>" . $fe->format('Y-W') . "</option>";
        } else {
          echo "<option value='{$f->fecha_ensarte}'>" . $fe->format('Y-W') . "</option>";
        }
      }
      ?>
    </select>
  </div>

  <button name="buscar" type="submit" class="btn btn-primary mb-2">Buscar</button>
  <button name="print" type="submit" class="btn btn-success mb-2" onclick="imprime();">Imprimir</button>
</form>
</div>

<div class="row landscape">
  <div class="col">
    <?php if ($result->num_rows > 0) { ?>
    <div class="row">
      <div class="col-5">
        <h5>INVERPALMAS S.A.S</h5>
      </div>
      <div class="col-5">
        <h5>SIEMBRAS <?php echo $programa; ?></h5>
      </div>
      <div class="col-2">
        <h7>Formato de fecha yyyy-semana</h7>
      </div>
    </div>
    <div class="row">
      <div class="col-6">
        <h1>Programa <?php if ($tabla == 'program_add_pto') { echo "Reemplazo"; } ?> de Ensartes y Cosechas</h1>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <table class="table table-sm">
          <thead>
            <tr>
              <td>Sem_Maq</td>
              <th>Semana</th>
              <th>Producto</th>
              <th style="width:40px">No Banco</th>
              <th style="width:250px">.::Nombre_de_la_Variedad::.</th>
              <th style="width:70px">Variedad Remplazo</th>
              <th style="width:70px">#Plantas Programa</th>
              <th style="width:70px">#Plantas Ensarte</th>
              <th>Fecha Ensarte_Teorica</th>
              <th>Fecha Ensarte_Real</th>
              <th style="width:70px">Temporada</th>
              <th>Fecha Cosecha_Teorica</th>
              <th style="width:70px">Fecha Cosecha_Real</th>
              <th style="width:70px">#Plantas Real_Cosecha</th>
              <th style="width:70px">#Plantas Perdidas</th>
              <th>Casa</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <?php
          $tplantas = 0;
          while ($f = $result->fetch_object()) {
            $fecha  = new Carbon($f->fecha_siembra);
            $fechae = new Carbon($f->fecha_ensarte);
            $fechac = new Carbon($f->fecha_cosecha);
            if (isset($_POST['buscar'])) { ?>
              <tr style="height:30px">
                <td></td>
                <td><h5><?php echo $fechae->startOfWeek()->addDays(3)->format('W'); ?></h5></td>
                <td><h5><?php echo $f->producto; ?></h5></td>
                <td></td>
                <td><h5><?php echo $f->variedad; ?></h5></td>
                <td></td>
                <td><h5><?php echo number_format($f->plantas, 0, ',', '.'); ?></h5></td>
                <td></td>
                <td><h5><?php echo $fechae->startOfWeek()->addDays(0)->format('d-m-Y'); ?></h5></td>
                <td></td>
                <td><h5><?php echo $f->cod_temporada; ?></h5></td>
                <td><h5><?php echo $fechac->startOfWeek()->addDays(2)->format('d-m-Y'); ?></h5></td>
                <td></td>
                <td></td>
                <td></td>
                <td><h5><?php echo $f->casa; ?></h5></td>
                <td></td>
              </tr>
            <?php
            $tplantas += $f->plantas;
            }
          }
          ?>
          <tr style="height:30px">
            <td></td><td></td><td></td><td></td><td></td>
            <td>Total:</td>
            <td><h5><?php echo number_format($tplantas, 0, ',', '.'); ?></h5></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
          </tr>
          <tr style="height:30px">
            <td></td><td></td><td></td><td></td><td></td>
            <td>#Bancos:</td>
            <td><h5><?php echo number_format($tplantas / $tbanco, 2, ',', '.'); ?></h5></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
          </tr>
          <?php for ($i = 0; $i < 5; $i++) { ?>
          <tr style="height:30px">
            <?php for ($j = 1; $j <= 17; $j++) { ?><td></td><?php } ?>
          </tr>
          <?php } ?>
        </table>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

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

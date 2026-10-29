<?php
global $conexion;
use Carbon\Carbon;

Carbon::setLocale('es');

// Obtener el año máximo de programa disponible
$prg = "SELECT MAX(programa) AS y FROM programf WHERE estado = 1";
$resultPrg = $conexion->query($prg);
$rowPrg = $resultPrg ? $resultPrg->fetch_assoc() : null;
$y = $rowPrg['y'] ?? date('Y');

// Filtros iniciales
$programa = $y;
$producto = "";
$temporada = "";
$finca = "";

if (isset($_REQUEST['xprograma'])) {
    $programa = trim($_REQUEST['xprograma']);
}
if (isset($_REQUEST['xproducto'])) {
    $producto = trim($_REQUEST['xproducto']);
}
if (isset($_REQUEST['xtemporada'])) {
    $temporada = trim($_REQUEST['xtemporada']);
}
if (isset($_REQUEST['xfinca'])) {
    $finca = trim($_REQUEST['xfinca']);
}

// Construcción dinámica y segura de cláusula WHERE
$conditions = ["p.plantas > 0", "p.estado = 1"];
if ($programa !== "") {
    $conditions[] = "p.programa = '" . $conexion->real_escape_string($programa) . "'";
}
if ($producto !== "") {
    $conditions[] = "p.producto = '" . $conexion->real_escape_string($producto) . "'";
}
if ($temporada !== "") {
    $conditions[] = "p.temporada_obj = '" . $conexion->real_escape_string($temporada) . "'";
}
if ($finca !== "") {
    $conditions[] = "p.finca = '" . $conexion->real_escape_string($finca) . "'";
}
$where = " WHERE " . implode(" AND ", $conditions);

// Determinación de tipo de encabezado
$encabezado = 0;
if ($producto !== "") {
    $encabezado = ($finca !== "" || $temporada !== "") ? 1 : 2;
}

// Consulta principal para la tabla (100% compatible con ONLY_FULL_GROUP_BY)
$sql = "SELECT p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.fecha_pico, p.abreviatura, p.bloque,
        SUM(p.plantas) AS plantas, ROUND(SUM(p.plantas) / 960, 0) AS ncamas, p.casa 
        FROM print_budget AS p $where
        GROUP BY p.variedad, p.temporada_obj, p.producto, p.fecha_siembra, p.fecha_pico, p.finca, p.bloque, p.ciclo, p.abreviatura, p.casa
        ORDER BY p.fecha_siembra, p.producto, p.finca, p.bloque, p.variedad ASC";
$result = $conexion->query($sql);

// Opciones de Combos
// 1. Programa
$slqCOMBO = "SELECT programa FROM programf WHERE estado = 1 AND programa IS NOT NULL AND programa != '' GROUP BY programa ORDER BY programa DESC";
$COM = $conexion->query($slqCOMBO);

// 2. Producto
$slqCOMBO2 = "SELECT producto FROM programf WHERE estado = 1 AND producto IS NOT NULL AND producto != '' GROUP BY producto ORDER BY producto ASC";
$COM2 = $conexion->query($slqCOMBO2);

// 3. Temporada (Compatible con ONLY_FULL_GROUP_BY usando MIN(fecha_pico))
$programaCombo = $conexion->real_escape_string((string)$programa);
if ($programa !== "") {
    $slqCOMBO3 = "SELECT temporada_obj FROM programf WHERE estado = 1 AND programa = '$programaCombo' AND temporada_obj IS NOT NULL AND temporada_obj != '' GROUP BY temporada_obj ORDER BY MIN(fecha_pico) ASC";
} else {
    $slqCOMBO3 = "SELECT temporada_obj FROM programf WHERE estado = 1 AND temporada_obj IS NOT NULL AND temporada_obj != '' GROUP BY temporada_obj ORDER BY MIN(fecha_pico) ASC";
}
$COM3 = $conexion->query($slqCOMBO3);

// 4. Finca
$slqCOMBO4 = "SELECT finca FROM programf WHERE estado = 1 AND finca IS NOT NULL AND finca != '' GROUP BY finca ORDER BY finca ASC";
$COM4 = $conexion->query($slqCOMBO4);

// Resumen / Bancos
$sqlSUM = "SELECT programa, producto, finca, SUM(plantas) AS plantas, ROUND(SUM(plantas) / 960, 0) AS ncamas
           FROM programf AS p $where GROUP BY programa, producto, finca";

$sqlSUM_t = "SELECT programa, producto, SUM(plantas) AS plantas, ROUND(SUM(plantas) / 960, 0) AS ncamas
             FROM programf AS p $where GROUP BY programa, producto";

$bancos = $conexion->query($sqlSUM);
$bancos_t = $conexion->query($sqlSUM_t);
$totalFilas = $result ? $result->num_rows : 0;
?>

<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<style>
@media print {
  @page {
    size: landscape;
    margin: 8mm;
  }
  body {
    padding-top: 0 !important;
    background: #fff !important;
    font-size: 10pt;
  }
  .d-print-none {
    display: none !important;
  }
  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
  }
  .print-table th, .print-table td {
    border: 1px solid #333 !important;
    padding: 3px 5px !important;
    font-size: 8.5pt !important;
  }
  .print-header-card {
    border: 1px solid #666 !important;
    margin-bottom: 8px !important;
    padding: 6px !important;
  }
}

.table-print-siembras {
  font-size: 0.85rem;
}
.table-print-siembras thead th {
  background-color: #00796B;
  color: #ffffff;
  font-weight: 600;
  text-align: center;
  vertical-align: middle;
  padding: 8px 6px;
  font-size: 0.78rem;
  letter-spacing: 0.02em;
  border: 1px solid #00695c;
}
.table-print-siembras tbody td {
  vertical-align: middle;
  padding: 6px 8px;
  border: 1px solid #dee2e6;
}
.table-print-siembras tbody tr:hover {
  background-color: #f1f5f9;
}
.stat-box-summary {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 12px;
}
</style>

<div class="container-fluid py-2">
  <!-- Page Header Banner (Oculto en Impresión) -->
  <div class="crud-page-header d-print-none mb-3">
    <div class="crud-header-left">
      <div class="crud-header-icon">
        <span class="material-icons">print</span>
      </div>
      <div>
        <h4 class="crud-page-title">Programa de Siembras - Imprimir</h4>
        <div class="crud-page-subtitle">
          <span class="crud-page-badge badge rounded-pill bg-success-subtle text-success border border-success-subtle">Siembras</span>
          <span class="text-muted d-none d-sm-inline">• Generación y consulta de formato horizontal para siembras</span>
        </div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold">
        <span class="material-icons align-middle me-1" style="font-size: 1.05rem;">format_list_numbered</span>
        <?php echo number_format($totalFilas, 0, ',', '.'); ?> Registros
      </span>
    </div>
  </div>

  <!-- Barra de Filtros (Oculto en Impresión) -->
  <div class="card shadow-sm border-0 mb-3 d-print-none" style="border-radius: 12px;">
    <div class="card-body p-3">
      <form action="index.php?report=imprimir_siembras" method="post" id="formFiltrosPrint">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Año Programa</label>
            <select name="xprograma" class="form-select form-select-sm" onchange="document.getElementById('formFiltrosPrint').submit();">
              <option value="">Todos</option>
              <?php
              if ($COM) {
                while ($f = $COM->fetch_object()) {
                  $selected = ((string)$f->programa === (string)$programa) ? "selected" : "";
                  echo "<option value='{$f->programa}' {$selected}>{$f->programa}</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Producto</label>
            <select name="xproducto" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php
              if ($COM2) {
                while ($f = $COM2->fetch_object()) {
                  $selected = ($f->producto === $producto) ? "selected" : "";
                  echo "<option value='{$f->producto}' {$selected}>{$f->producto}</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Temporada</label>
            <select name="xtemporada" class="form-select form-select-sm">
              <option value="">Todas</option>
              <?php
              if ($COM3) {
                while ($f = $COM3->fetch_object()) {
                  $selected = ($f->temporada_obj === $temporada) ? "selected" : "";
                  echo "<option value='{$f->temporada_obj}' {$selected}>{$f->temporada_obj}</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Finca</label>
            <select name="xfinca" class="form-select form-select-sm">
              <option value="">Todas</option>
              <?php
              if ($COM4) {
                while ($f = $COM4->fetch_object()) {
                  $selected = ($f->finca === $finca) ? "selected" : "";
                  echo "<option value='{$f->finca}' {$selected}>{$f->finca}</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-md-3 d-flex gap-2">
            <button name="buscar" value="1" type="submit" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center" style="height: 33px;">
              <span class="material-icons me-1" style="font-size: 1.1rem;">search</span> Buscar
            </button>
            <a href="index.php?report=imprimir_siembras" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center px-2" title="Limpiar filtros" style="height: 33px;">
              <span class="material-icons" style="font-size: 1.1rem;">refresh</span>
            </a>
            <button type="button" class="btn btn-outline-success btn-sm flex-fill d-flex align-items-center justify-content-center" onclick="imprime();" style="height: 33px;">
              <span class="material-icons me-1" style="font-size: 1.1rem;">print</span> Imprimir
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Resumen de Totales por Finca / Producto -->
  <?php if ($encabezado == 1 && $bancos && $bancos->num_rows > 0) { ?>
    <div class="card shadow-sm border-0 mb-3 print-header-card" style="border-radius: 10px;">
      <div class="card-body p-2">
        <div class="row g-2">
          <?php while ($b = $bancos->fetch_object()) { ?>
            <div class="col-12 col-md-4 col-lg-3">
              <div class="stat-box-summary d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-bold text-dark" style="font-size: 0.9rem;">Siembras <?php echo htmlspecialchars($b->programa); ?></span>
                  <span class="badge bg-success-subtle text-success"><?php echo htmlspecialchars($b->producto); ?></span>
                </div>
                <div class="small text-muted mb-1">
                  <strong>Finca:</strong> <?php echo empty($finca) ? "Global Empresa" : htmlspecialchars($b->finca); ?>
                </div>
                <div class="d-flex justify-content-between text-end pt-1 border-top">
                  <span class="small text-muted"><strong>#Camas:</strong> <?php echo number_format($b->ncamas, 0, ',', '.'); ?></span>
                  <span class="small text-dark fw-bold"><strong>#Plantas:</strong> <?php echo number_format($b->plantas, 0, ',', '.'); ?></span>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } else if ($encabezado == 2 && $bancos_t && $bancos_t->num_rows > 0) { ?>
    <div class="card shadow-sm border-0 mb-3 print-header-card" style="border-radius: 10px;">
      <div class="card-body p-2">
        <div class="row g-2">
          <?php while ($b = $bancos_t->fetch_object()) { ?>
            <div class="col-12 col-md-4 col-lg-3">
              <div class="stat-box-summary d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-bold text-dark" style="font-size: 0.9rem;">Siembras <?php echo htmlspecialchars($b->programa); ?></span>
                  <span class="badge bg-success-subtle text-success"><?php echo htmlspecialchars($b->producto); ?></span>
                </div>
                <div class="small text-muted mb-1">
                  <strong>Finca:</strong> <?php echo empty($finca) ? "Global Empresa" : htmlspecialchars($finca); ?>
                </div>
                <div class="d-flex justify-content-between text-end pt-1 border-top">
                  <span class="small text-muted"><strong>#Camas:</strong> <?php echo number_format($b->ncamas, 0, ',', '.'); ?></span>
                  <span class="small text-dark fw-bold"><strong>#Plantas:</strong> <?php echo number_format($b->plantas, 0, ',', '.'); ?></span>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- Contenedor de la Tabla -->
  <?php if ($totalFilas > 0) { ?>
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-print-siembras print-table text-center align-middle mb-0">
          <thead>
            <tr>
              <th>Nombre de la Variedad</th>
              <th>Variedad Reemplazo</th>
              <th>Cosecha</th>
              <th>Pico</th>
              <th>#Camas</th>
              <th>No Plantas Teórico</th>
              <th>Año-Mes Siembra</th>
              <th>Finca Teórica</th>
              <th>Finca Real</th>
              <th>Bloque Teórico</th>
              <th>Bloque Real</th>
              <th>Semana Teo</th>
              <th>Observaciones de la Siembra</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($f = $result->fetch_object()) {
              $fechaSiembraFormatted = '-';
              $semanaTeo = '-';
              if (!empty($f->fecha_siembra) && $f->fecha_siembra !== '0000-00-00') {
                try {
                  $fecha = new Carbon($f->fecha_siembra);
                  $fechaSiembraFormatted = $fecha->endOfWeek()->subDays(4)->format('Y-m');
                  $semanaTeo = $fecha->endOfWeek()->subDays(4)->format('W');
                } catch (\Throwable $e) {
                  $fechaSiembraFormatted = htmlspecialchars($f->fecha_siembra);
                }
              }
            ?>
              <tr>
                <td class="fw-bold text-start ps-2"><?php echo htmlspecialchars(str_replace(' ', '_', $f->variedad)); ?></td>
                <td></td>
                <td><?php echo htmlspecialchars($f->temporada_obj ?? ''); ?></td>
                <td><?php echo htmlspecialchars($f->ciclo ?? ''); ?></td>
                <td class="fw-bold"><?php echo htmlspecialchars($f->ncamas ?? '0'); ?></td>
                <td class="fw-semibold text-end pe-2"><?php echo number_format($f->plantas, 0, ',', '.'); ?></td>
                <td><?php echo $fechaSiembraFormatted; ?></td>
                <td class="fw-bold"><?php echo htmlspecialchars($f->abreviatura ?? ''); ?></td>
                <td></td>
                <td><?php echo htmlspecialchars($f->bloque ?? ''); ?></td>
                <td></td>
                <td class="fw-bold"><?php echo $semanaTeo; ?></td>
                <td></td>
              </tr>
            <?php } ?>

            <!-- Filas en blanco para anotaciones físicas de impresión -->
            <?php for ($i = 0; $i < 5; $i++) { ?>
              <tr style="height: 28px;">
                <?php for ($j = 1; $j <= 13; $j++) { ?>
                  <td></td>
                <?php } ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php } else { ?>
    <div class="card shadow-sm border-0 text-center p-5" style="border-radius: 12px;">
      <div class="card-body">
        <span class="material-icons text-muted mb-2" style="font-size: 3rem;">search_off</span>
        <h5 class="fw-bold text-dark">No se encontraron registros</h5>
        <p class="text-muted small mb-3">No hay datos de siembras que coincidan con los filtros seleccionados.</p>
        <a href="index.php?report=imprimir_siembras" class="btn btn-outline-brand-green btn-sm px-3">
          <span class="material-icons align-middle me-1" style="font-size: 1rem;">refresh</span> Restablecer filtros
        </a>
      </div>
    </div>
  <?php } ?>
</div>

<script>
function imprime() {
  window.print();
}
</script>

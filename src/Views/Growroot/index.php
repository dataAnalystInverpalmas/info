<?php
global $conexion;
use Carbon\Carbon;

Carbon::setLocale('es');

$tbanco = 18000;

// Tipo de planificación (tabla)
$tabla = $_REQUEST['tipo'] ?? 'program';
if ($tabla !== 'program' && $tabla !== 'program_add_pto') {
    $tabla = 'program';
}

// Año máximo de programa
$prg = "SELECT MAX(programa) AS y FROM " . $tabla;
$resultPrg = $conexion->query($prg);
$rowPrg = $resultPrg ? $resultPrg->fetch_assoc() : null;
$y = $rowPrg['y'] ?? date('Y');

// Filtros
$programa = isset($_REQUEST['xprograma']) ? trim($_REQUEST['xprograma']) : (string)$y;
$fecha_raw = isset($_REQUEST['xfecha']) ? trim($_REQUEST['xfecha']) : '';

// Construcción dinámica de la condición WHERE
$conditions = ["p.plantas > 0", "p.raiz = 0", "p.estado = 1"];
if ($programa !== '') {
    $conditions[] = "p.programa = '" . $conexion->real_escape_string($programa) . "'";
}
if ($fecha_raw !== '') {
    try {
        $fecha_obj = new Carbon($fecha_raw);
        $start = $fecha_obj->copy()->startOfWeek()->format('Y-m-d');
        $end = $fecha_obj->copy()->endOfWeek()->format('Y-m-d');
        $conditions[] = "p.fecha_ensarte BETWEEN '$start' AND '$end'";
    } catch (\Throwable $e) {
        // En caso de formato inválido no se añade el filtro de fecha
    }
}
$where = " WHERE " . implode(" AND ", $conditions);

// Consulta principal para la tabla (100% compatible con ONLY_FULL_GROUP_BY)
$sql = "SELECT p.variedad, p.temporada_obj, p.producto, p.ciclo,
        p.fecha_siembra, p.fecha_pico, SUM(p.plantas) AS plantas, ROUND(SUM(p.plantas)/960, 0) AS ncamas,
        p.fecha_temporada, p.fecha_ensarte, p.fecha_cosecha, s.cod_temporada, b.nombre AS casa
        FROM $tabla AS p
        LEFT JOIN seasons AS s ON s.nombre = p.temporada_obj
        LEFT JOIN breeders AS b ON b.id = p.casa_id
        $where
        GROUP BY p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.fecha_pico, 
                 p.fecha_temporada, p.fecha_ensarte, p.fecha_cosecha, s.cod_temporada, b.nombre
        ORDER BY p.fecha_temporada, p.fecha_siembra, p.producto, p.variedad ASC";

$result = $conexion->query($sql);
$totalFilas = $result ? $result->num_rows : 0;

// Opciones de Combos
// 1. Programa
$slqCOMBO = "SELECT programa FROM $tabla GROUP BY programa ORDER BY programa DESC";
$COM = $conexion->query($slqCOMBO);

// 2. Fecha Ensarte (Agrupada por semana, compatible con ONLY_FULL_GROUP_BY)
$programaCombo = $conexion->real_escape_string((string)$programa);
$whereProg = ($programa !== '') 
    ? "WHERE programa = '$programaCombo' AND fecha_ensarte IS NOT NULL AND fecha_ensarte > '1970-01-01'" 
    : "WHERE fecha_ensarte IS NOT NULL AND fecha_ensarte > '1970-01-01'";

$slqCOMBO2 = "SELECT MIN(fecha_ensarte) AS fecha_ensarte 
              FROM $tabla 
              $whereProg 
              GROUP BY YEARWEEK(fecha_ensarte, 1) 
              ORDER BY MIN(fecha_ensarte) ASC";
$COM2 = $conexion->query($slqCOMBO2);
?>

<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<style>
@media print {
  @page {
    size: landscape;
    margin-top: 30mm; /* 3 cm para archivar en carpeta/gancho */
    margin-bottom: 6mm;
    margin-left: 4mm;
    margin-right: 4mm;
  }
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    padding-top: 0 !important;
    background: #fff !important;
    font-size: 7pt;
    width: 100% !important;
    color: #000 !important;
  }
  header, footer, nav, aside, .d-print-none, .crud-page-header, #appToastContainer {
    display: none !important;
  }
  section {
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
  }
  .container-fluid {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 0 !important;
  }
  .table-responsive {
    overflow: visible !important;
    display: block !important;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    box-shadow: none !important;
  }
  .table-print-ensartes {
    width: 100% !important;
    max-width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
    font-size: 6.8pt !important;
    line-height: 1.15 !important;
    margin: 0 !important;
  }
  .table-print-ensartes,
  .table-print-ensartes tr,
  .table-print-ensartes th,
  .table-print-ensartes td {
    border: 0.5pt solid #333 !important;
    box-sizing: border-box !important;
    min-width: 0 !important;
  }
  .table-print-ensartes thead {
    display: table-header-group !important;
  }
  .table-print-ensartes thead th {
    background-color: #f1f5f9 !important;
    color: #000 !important;
    font-weight: 700 !important;
    font-size: 6.3pt !important;
    text-align: center !important;
    vertical-align: middle !important;
    padding: 3px 1px !important;
    word-break: break-word !important;
    white-space: normal !important;
    line-height: 1.1 !important;
  }
  .table-print-ensartes tbody tr {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
  .table-print-ensartes tbody td {
    padding: 2px 2px !important;
    font-size: 6.8pt !important;
    vertical-align: middle !important;
    word-break: break-word !important;
    white-space: normal !important;
    color: #000 !important;
    height: 19px !important;
  }
  .table-print-ensartes tfoot {
    display: table-footer-group !important;
  }
  .table-print-ensartes tfoot td {
    padding: 3px 2px !important;
    font-size: 7pt !important;
    font-weight: bold !important;
    background-color: #f8fafc !important;
    color: #000 !important;
  }
  .print-header-doc {
    margin-bottom: 6px !important;
  }

  /* Anchos proporcionales al 100% para formato horizontal completo */
  .table-print-ensartes th.col-ens-sem-maq { width: 3% !important; }
  .table-print-ensartes th.col-ens-semana { width: 3.5% !important; }
  .table-print-ensartes th.col-ens-producto { width: 5.5% !important; }
  .table-print-ensartes th.col-ens-banco { width: 3.5% !important; }
  .table-print-ensartes th.col-ens-variedad { width: 14% !important; }
  .table-print-ensartes th.col-ens-reemplazo { width: 5.5% !important; }
  .table-print-ensartes th.col-ens-plantas-prg { width: 6% !important; }
  .table-print-ensartes th.col-ens-plantas-ens { width: 5.5% !important; }
  .table-print-ensartes th.col-ens-fecha-ens-t { width: 6.8% !important; }
  .table-print-ensartes th.col-ens-fecha-ens-r { width: 5.8% !important; }
  .table-print-ensartes th.col-ens-temporada { width: 4.8% !important; }
  .table-print-ensartes th.col-ens-fecha-cos-t { width: 6.8% !important; }
  .table-print-ensartes th.col-ens-fecha-cos-r { width: 5.8% !important; }
  .table-print-ensartes th.col-ens-plantas-cos { width: 5.8% !important; }
  .table-print-ensartes th.col-ens-plantas-per { width: 5.2% !important; }
  .table-print-ensartes th.col-ens-casa { width: 6.5% !important; }
  .table-print-ensartes th.col-ens-obs { width: 9% !important; }
}

@media screen {
  .table-print-ensartes {
    min-width: 1250px;
    font-size: 0.82rem;
  }
  .table-print-ensartes thead th {
    background-color: #00796B;
    color: #ffffff;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 6px 4px;
    font-size: 0.75rem;
    letter-spacing: 0.01em;
    border: 1px solid #00695c;
  }
  .table-print-ensartes tbody td {
    vertical-align: middle;
    padding: 5px 6px;
    border: 1px solid #dee2e6;
  }
  .table-print-ensartes tbody tr:hover {
    background-color: #f1f5f9;
  }
  .table-print-ensartes tfoot td {
    background-color: #f8fafc;
    font-weight: 600;
    border: 1px solid #cbd5e1;
  }
}
</style>

<div class="container-fluid py-2">
  <!-- Page Header Banner (Oculto en Impresión) -->
  <div class="crud-page-header d-print-none mb-3">
    <div class="crud-header-left">
      <div class="crud-header-icon">
        <span class="material-icons">content_cut</span>
      </div>
      <div>
        <h4 class="crud-page-title">Programa de Ensartes - Imprimir</h4>
        <div class="crud-page-subtitle">
          <span class="crud-page-badge badge rounded-pill bg-success-subtle text-success border border-success-subtle">Siembras</span>
          <span class="text-muted d-none d-sm-inline">• Control de ensartes y cosechas de esquejes para formato horizontal</span>
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
      <form action="index.php?report=imprimir_ensartes" method="post" id="formFiltrosEnsartes">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Año Programa</label>
            <select name="xprograma" class="form-select form-select-sm" onchange="document.getElementById('formFiltrosEnsartes').submit();">
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

          <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Tipo de Planificación</label>
            <select id="tipo" name="tipo" class="form-select form-select-sm" onchange="document.getElementById('formFiltrosEnsartes').submit();">
              <option value="program" <?php echo ($tabla === 'program') ? 'selected' : ''; ?>>Programa Principal</option>
              <option value="program_add_pto" <?php echo ($tabla === 'program_add_pto') ? 'selected' : ''; ?>>Reemplazo</option>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Fecha Ensarte (Semana)</label>
            <select name="xfecha" class="form-select form-select-sm">
              <option value="">Todas las semanas</option>
              <?php
              if ($COM2) {
                while ($f = $COM2->fetch_object()) {
                  try {
                    $fe = new Carbon($f->fecha_ensarte);
                    $val = $fe->format('Y-m-d');
                    $label = $fe->format('Y') . " - Sem " . $fe->format('W') . " (" . $fe->format('d/m/Y') . ")";
                    $selected = ($val === $fecha_raw || $f->fecha_ensarte === $fecha_raw) ? "selected" : "";
                    echo "<option value='{$val}' {$selected}>{$label}</option>";
                  } catch (\Throwable $e) {
                    $selected = ($f->fecha_ensarte === $fecha_raw) ? "selected" : "";
                    echo "<option value='{$f->fecha_ensarte}' {$selected}>{$f->fecha_ensarte}</option>";
                  }
                }
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-md-3 d-flex gap-2">
            <button name="buscar" value="1" type="submit" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center" style="height: 33px;">
              <span class="material-icons me-1" style="font-size: 1.1rem;">search</span> Buscar
            </button>
            <a href="index.php?report=imprimir_ensartes" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center px-2" title="Limpiar filtros" style="height: 33px;">
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

  <!-- Encabezado de Documento Impreso -->
  <div class="d-none d-print-block mb-2">
    <div class="row align-items-center">
      <div class="col-4">
        <h5 class="fw-bold mb-0">INVERPALMAS S.A.S</h5>
      </div>
      <div class="col-5 text-center">
        <h5 class="fw-bold mb-0">SIEMBRAS <?php echo htmlspecialchars($programa); ?> - <?php echo ($tabla === 'program_add_pto') ? 'REEMPLAZOS' : 'PROGRAMA'; ?></h5>
      </div>
      <div class="col-3 text-end">
        <small class="text-muted">Formato YYYY-Semana</small>
      </div>
    </div>
  </div>

  <!-- Contenedor de la Tabla -->
  <?php if ($totalFilas > 0) { ?>
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-print-ensartes text-center align-middle mb-0">
          <thead>
            <tr>
              <th style="width: 45px;">Sem Maq</th>
              <th style="width: 50px;">Semana</th>
              <th>Producto</th>
              <th style="width: 55px;">No Banco</th>
              <th style="min-width: 170px;">Nombre de la Variedad</th>
              <th style="width: 80px;">Variedad Reemplazo</th>
              <th style="width: 80px;">#Plantas Programa</th>
              <th style="width: 80px;">#Plantas Ensarte</th>
              <th>Fecha Ensarte Teórica</th>
              <th>Fecha Ensarte Real</th>
              <th style="width: 70px;">Temporada</th>
              <th>Fecha Cosecha Teórica</th>
              <th style="width: 75px;">Fecha Cosecha Real</th>
              <th style="width: 75px;">#Plantas Real Cosecha</th>
              <th style="width: 75px;">#Plantas Perdidas</th>
              <th>Casa</th>
              <th style="min-width: 110px;">Observaciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $tplantas = 0;
            while ($f = $result->fetch_object()) {
              $semanaEnsarte = '-';
              $fechaEnsarteFmt = '-';
              $fechaCosechaFmt = '-';

              if (!empty($f->fecha_ensarte) && $f->fecha_ensarte !== '0000-00-00') {
                try {
                  $fechae = new Carbon($f->fecha_ensarte);
                  $semanaEnsarte = $fechae->startOfWeek()->addDays(3)->format('W');
                  $fechaEnsarteFmt = $fechae->startOfWeek()->addDays(0)->format('d-m-Y');
                } catch (\Throwable $e) {
                  $fechaEnsarteFmt = htmlspecialchars($f->fecha_ensarte);
                }
              }

              if (!empty($f->fecha_cosecha) && $f->fecha_cosecha !== '0000-00-00') {
                try {
                  $fechac = new Carbon($f->fecha_cosecha);
                  $fechaCosechaFmt = $fechac->startOfWeek()->addDays(2)->format('d-m-Y');
                } catch (\Throwable $e) {
                  $fechaCosechaFmt = htmlspecialchars($f->fecha_cosecha);
                }
              }

              $tplantas += (int)$f->plantas;
            ?>
              <tr>
                <td></td>
                <td class="fw-bold"><?php echo $semanaEnsarte; ?></td>
                <td><?php echo htmlspecialchars($f->producto ?? ''); ?></td>
                <td></td>
                <td class="fw-bold text-start ps-2"><?php echo htmlspecialchars($f->variedad ?? ''); ?></td>
                <td></td>
                <td class="fw-semibold text-end pe-2"><?php echo number_format($f->plantas, 0, ',', '.'); ?></td>
                <td></td>
                <td><?php echo $fechaEnsarteFmt; ?></td>
                <td></td>
                <td><?php echo htmlspecialchars($f->cod_temporada ?? ''); ?></td>
                <td><?php echo $fechaCosechaFmt; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo htmlspecialchars($f->casa ?? ''); ?></td>
                <td></td>
              </tr>
            <?php } ?>

            <!-- Filas en blanco para anotaciones físicas de campo -->
            <?php for ($i = 0; $i < 5; $i++) { ?>
              <tr style="height: 26px;">
                <?php for ($j = 1; $j <= 17; $j++) { ?>
                  <td></td>
                <?php } ?>
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" class="text-end pe-3 fw-bold">Total Plantas:</td>
              <td class="text-end pe-2 fw-bold text-dark"><?php echo number_format($tplantas, 0, ',', '.'); ?></td>
              <td colspan="10"></td>
            </tr>
            <tr>
              <td colspan="6" class="text-end pe-3 fw-bold"># Bancos Requeridos:</td>
              <td class="text-end pe-2 fw-bold text-primary"><?php echo number_format($tplantas / $tbanco, 2, ',', '.'); ?></td>
              <td colspan="10"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  <?php } else { ?>
    <div class="card shadow-sm border-0 text-center p-5" style="border-radius: 12px;">
      <div class="card-body">
        <span class="material-icons text-muted mb-2" style="font-size: 3rem;">search_off</span>
        <h5 class="fw-bold text-dark">No se encontraron registros de ensartes</h5>
        <p class="text-muted small mb-3">No hay datos que coincidan con los filtros seleccionados.</p>
        <a href="index.php?report=imprimir_ensartes" class="btn btn-outline-brand-green btn-sm px-3">
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

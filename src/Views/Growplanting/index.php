<?php
global $conexion;
use Carbon\Carbon;

$where = " WHERE p.plantas>0 ";
$producto = "";
$temporada = "";
$finca = "";
$header = false;

$conditions = ["p.plantas > 0"];
$producto = isset($_REQUEST['xproducto']) ? trim($_REQUEST['xproducto']) : '';
$temporada = isset($_REQUEST['xtemporada']) ? trim($_REQUEST['xtemporada']) : '';
$finca = isset($_REQUEST['xfinca']) ? trim($_REQUEST['xfinca']) : '';

if ($producto !== '') {
    $conditions[] = "p.producto = '" . $conexion->real_escape_string($producto) . "'";
}
if ($temporada !== '') {
    $conditions[] = "p.temporada = '" . $conexion->real_escape_string($temporada) . "'";
}
if ($finca !== '') {
    $conditions[] = "p.finca = '" . $conexion->real_escape_string($finca) . "'";
}
$where = " WHERE " . implode(" AND ", $conditions);
$header = ($producto !== '' && $temporada !== '' && $finca !== '');

$sql = "SELECT IFNULL(p.bloque, 'Total') AS bloque, IFNULL(p.variedad, 'Total') AS variedad, IFNULL(p.tipo_siembra, 'Total') AS tipo_siembra, SUM(p.plantas) AS plantas
    FROM plane AS p 
    LEFT JOIN seasons AS s ON s.nombre = p.temporada
    LEFT JOIN varieties AS v ON p.variedad = v.nombre
    LEFT JOIN farms AS f ON f.nombre = p.finca
    $where
    GROUP BY p.bloque, p.variedad, p.tipo_siembra
    WITH ROLLUP";

$result = $conexion->query($sql);

$slqCOMBO2 = "SELECT DISTINCT producto FROM plane WHERE plantas > 0 AND producto IS NOT NULL AND producto != '' ORDER BY producto ASC";
$COM2 = $conexion->query($slqCOMBO2);

$slqCOMBO3 = "SELECT DISTINCT temporada FROM plane WHERE plantas > 0 AND temporada IS NOT NULL AND temporada != '' ORDER BY temporada ASC";
$COM3 = $conexion->query($slqCOMBO3);

$slqCOMBO4 = "SELECT DISTINCT finca FROM plane WHERE plantas > 0 AND finca IS NOT NULL AND finca != '' ORDER BY finca ASC";
$COM4 = $conexion->query($slqCOMBO4);
?>

<style>
    .gp-page-wrapper {
        padding: 0.5rem 0 2rem;
        color: #2D3748;
    }
    .gp-hero-header {
        background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
        border-radius: 14px;
        padding: 1.25rem 1.6rem;
        color: #FFFFFF;
        box-shadow: 0 8px 24px -4px rgba(0, 121, 107, 0.25);
        margin-bottom: 1.25rem;
    }
    .gp-filter-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        margin-bottom: 1.25rem;
    }
    .gp-filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
        margin-bottom: 0.35rem;
        display: block;
    }
    .gp-table-wrapper {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .gp-table thead th {
        background-color: #F8FAFC !important;
        color: #475569 !important;
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        padding: 10px 12px !important;
        border-bottom: 2px solid #E2E8F0 !important;
        white-space: nowrap;
    }
    .gp-table tbody td {
        padding: 8px 12px !important;
        font-size: 0.84rem !important;
        color: #334155 !important;
        border-bottom: 1px solid #F1F5F9 !important;
        vertical-align: middle !important;
    }
    .gp-table tbody tr.row-total td {
        background-color: #F8FAFC !important;
        font-weight: 800 !important;
        color: #00796B !important;
    }
    .gp-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #E6F4F1;
        color: #00796B;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        border: 1px solid #BCE2DB;
    }
    @media print {
        header, footer, .d-print-none, .gp-filter-card {
            display: none !important;
        }
        .gp-hero-header {
            background: #00796B !important;
            color: #FFFFFF !important;
            box-shadow: none !important;
        }
        @page {
            size: landscape;
            margin: 10mm;
        }
    }
</style>

<div class="gp-page-wrapper container-fluid">
    <!-- Header Banner -->
    <div class="gp-hero-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 1rem;">
            <div>
                <div class="d-flex align-items-center mb-1" style="gap: 0.6rem;">
                    <span class="badge badge-light px-2 py-1 font-weight-bold" style="color: #00796B; font-size: 0.75rem;">CULTIVO &bull; COSECHAS</span>
                    <span class="text-white-50 small">&bull; Seguimiento Fenológico</span>
                </div>
                <h2 class="font-weight-bold mb-1 d-flex align-items-center" style="font-size: 1.45rem; letter-spacing: -0.01em;">
                    <span class="material-icons mr-2" style="font-size: 1.6rem;">analytics</span>
                    Evaluación de Cosechas
                </h2>
                <p class="text-white-50 small mb-0">
                    Control de plantas sembradas y cronograma de picos de cosecha por bloque y variedad.
                </p>
            </div>
            <div class="d-print-none">
                <button type="button" class="btn btn-light font-weight-bold d-inline-flex align-items-center px-3" onclick="window.print();" style="border-radius: 8px; color: #004D40;">
                    <span class="material-icons mr-1" style="font-size: 1.1rem;">print</span> Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="gp-filter-card d-print-none">
        <form action="" method="post">
            <div class="row align-items-end">
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <label class="gp-filter-label" for="producto">Flor / Producto</label>
                    <select id="producto" name="xproducto" class="form-control" required>
                        <option value="">Seleccione flor...</option>
                        <?php
                        if ($COM2) {
                            $COM2->data_seek(0);
                            while ($f = $COM2->fetch_object()) {
                                $sel = ($f->producto == $producto) ? 'selected' : '';
                                echo "<option value='{$f->producto}' {$sel}>{$f->producto}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <label class="gp-filter-label" for="temporada">Temporada</label>
                    <select id="temporada" name="xtemporada" class="form-control" required>
                        <option value="">Seleccione temporada...</option>
                        <?php
                        if ($COM3) {
                            $COM3->data_seek(0);
                            while ($f = $COM3->fetch_object()) {
                                $sel = ($f->temporada == $temporada) ? 'selected' : '';
                                echo "<option value='{$f->temporada}' {$sel}>{$f->temporada}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <label class="gp-filter-label" for="finca">Finca</label>
                    <select id="finca" name="xfinca" class="form-control" required>
                        <option value="">Seleccione finca...</option>
                        <?php
                        if ($COM4) {
                            $COM4->data_seek(0);
                            while ($f = $COM4->fetch_object()) {
                                $sel = ($f->finca == $finca) ? 'selected' : '';
                                echo "<option value='{$f->finca}' {$sel}>{$f->finca}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 d-flex" style="gap: 0.5rem;">
                    <button name="buscar" type="submit" class="btn btn-success flex-grow-1 font-weight-bold d-inline-flex align-items-center justify-content-center" style="background-color: #00796B; border: none; border-radius: 8px;">
                        <span class="material-icons mr-1" style="font-size: 1.1rem;">search</span> Consultar
                    </button>
                    <?php if ($header): ?>
                        <a href="index.php?report=evaluacion_cosechas" class="btn btn-outline-secondary font-weight-bold d-inline-flex align-items-center" style="border-radius: 8px;" title="Limpiar filtros">
                            <span class="material-icons" style="font-size: 1.1rem;">restart_alt</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Section -->
    <?php if ($result && $result->num_rows > 0 && $header): ?>
        <?php
        $temporadaEsc = $conexion->real_escape_string($temporada);
        $sqlFP = "SELECT fecha_pico FROM seasons WHERE nombre='$temporadaEsc'";
        $resultFP = $conexion->query($sqlFP);
        $rowFP = $resultFP ? $resultFP->fetch_assoc() : null;
        $sempico = ($rowFP && !empty($rowFP['fecha_pico'])) ? new Carbon($rowFP['fecha_pico']) : Carbon::now();
        ?>
        <div class="gp-table-wrapper">
            <!-- Active Selection Meta Strip -->
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3 pb-2 border-bottom" style="gap: 0.5rem;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                    <span class="gp-meta-badge"><span class="material-icons" style="font-size: 0.95rem;">domain</span> Finca: <?php echo htmlspecialchars($finca); ?></span>
                    <span class="gp-meta-badge"><span class="material-icons" style="font-size: 0.95rem;">spa</span> Flor: <?php echo htmlspecialchars($producto); ?></span>
                    <span class="gp-meta-badge"><span class="material-icons" style="font-size: 0.95rem;">date_range</span> Temporada: <?php echo htmlspecialchars($temporada); ?></span>
                </div>
                <span class="text-muted small">Cálculo de semanas relativo a fecha de pico</span>
            </div>

            <div class="table-responsive">
                <table id="cosechas" class="table table-hover table-striped gp-table mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 70px;">Bloque</th>
                            <th style="min-width: 220px;">Variedad</th>
                            <th style="min-width: 100px;">Tipo Siembra</th>
                            <th class="text-right" style="min-width: 110px;">Plantas</th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->subDays(70)->format('W'); ?></th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->subDays(56)->format('W'); ?></th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->subDays(42)->format('W'); ?></th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->subDays(28)->format('W'); ?></th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->subDays(14)->format('W'); ?></th>
                            <th class="text-center font-weight-bold" style="color: #00796B;">Pico Sem <?php echo $sempico->format('W'); ?></th>
                            <th class="text-center">Sem <?php echo (clone $sempico)->addDays(14)->format('W'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($f = $result->fetch_object()) {
                            if (($f->variedad != null && $f->tipo_siembra != null) || ($f->variedad == null && $f->tipo_siembra == null)) {
                                $isTotal = ($f->variedad == null);
                                $rowClass = $isTotal ? 'row-total' : '';
                        ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td class="font-weight-bold"><?php echo htmlspecialchars($f->bloque); ?></td>
                                <td><?php echo $isTotal ? '<strong>TOTAL ACUMULADO</strong>' : htmlspecialchars($f->variedad); ?></td>
                                <td><?php echo $isTotal ? '-' : htmlspecialchars($f->tipo_siembra); ?></td>
                                <td class="text-right font-weight-bold" style="<?php echo $isTotal ? 'color:#00796B; font-size:0.95rem;' : ''; ?>">
                                    <?php echo number_format($f->plantas, 0, ",", "."); ?>
                                </td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($_POST['buscar'])): ?>
        <div class="gp-table-wrapper text-center py-5">
            <span class="material-icons text-muted mb-2" style="font-size: 3rem;">search_off</span>
            <h5 class="font-weight-bold text-dark mb-1">No se encontraron registros</h5>
            <p class="text-muted small mb-0">No hay datos de siembra para la combinación seleccionada de flor, temporada y finca.</p>
        </div>
    <?php else: ?>
        <div class="gp-table-wrapper text-center py-5">
            <span class="material-icons text-muted mb-2" style="font-size: 3rem;">filter_list</span>
            <h5 class="font-weight-bold text-dark mb-1">Seleccione los criterios de búsqueda</h5>
            <p class="text-muted small mb-0">Elija el producto, temporada y finca en el panel superior para generar la evaluación de cosechas.</p>
        </div>
    <?php endif; ?>
</div>

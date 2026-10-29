<?php use Carbon\Carbon; ?>
<style>
    /* Estilos base de la vista */
    .reportes-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        margin: -0.5rem -15px 1.25rem;
        padding: 0.85rem 20px;
    }

    .reportes-bar h1 {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0;
        color: #17312d;
    }

    .reportes-bar p {
        margin: 0.15rem 0 0;
        color: #5f6f6b;
        font-size: 0.82rem;
    }

    .filtro-sidebar {
        position: sticky;
        top: 1rem;
    }

    @media (max-width: 991.98px) {
        .filtro-sidebar {
            position: static !important;
            margin-bottom: 1.25rem;
        }
    }

    .filtro-sidebar .card {
        border: 1px solid #dbe7e4;
        border-radius: 14px;
        overflow: hidden;
    }

    .filtro-sidebar .card-header {
        background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
        color: #fff;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filtro-sidebar label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .table-aplicaciones-reporte {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
    }

    .table-aplicaciones-reporte th,
    .table-aplicaciones-reporte td {
        border: 1px solid #000000;
        padding: 4px 6px;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .table-aplicaciones-reporte thead th {
        background-color: #f1f5f3;
        font-weight: 700;
        text-align: center;
    }

    .table-aplicaciones-reporte .subtotal-row {
        background-color: #f8fbf9;
        font-weight: 700;
    }

    .table-aplicaciones-reporte .total-row {
        background-color: #e8f5e9;
        font-weight: 800;
        font-size: 0.9rem;
    }

    /* ESTILOS DEL FORMATO SOLICITUD SALIDA DE MATERIALES */
    .formato-materiales-wrapper {
        background: #ffffff;
        padding: 10px 15px;
        color: #000000;
        font-family: Arial, Calibri, sans-serif;
    }

    .formato-header {
        text-align: center;
        margin-bottom: 6px;
    }

    .formato-header .empresa-nombre {
        font-size: 13pt;
        font-weight: 800;
        color: #000000;
        letter-spacing: 0.05em;
        line-height: 1.2;
    }

    .formato-header .formato-titulo {
        font-size: 11pt;
        font-weight: 800;
        color: #000000;
        margin: 2px 0;
        line-height: 1.2;
    }

    .formato-header .formato-codigo {
        font-size: 9pt;
        font-weight: 800;
        color: #000000;
        letter-spacing: 0.04em;
        line-height: 1.2;
    }

    .formato-meta-row {
        display: flex;
        justify-content: flex-start;
        gap: 35px;
        font-size: 9pt;
        font-weight: 800;
        margin-bottom: 6px;
        color: #000000;
    }

    .formato-meta-row .meta-item {
        display: inline-block;
    }

    .formato-meta-row .meta-label {
        font-weight: 800;
        margin-right: 4px;
    }

    .formato-meta-row .meta-val {
        font-weight: 700;
    }

    .table-formato-mat {
        width: 100%;
        border-collapse: collapse !important;
        font-size: 8.5pt;
        color: #000000;
        table-layout: fixed;
    }

    .table-formato-mat th,
    .table-formato-mat td {
        border: 1px solid #000000 !important;
        padding: 2.5px 3px !important;
        vertical-align: middle !important;
        line-height: 1.2 !important;
        height: 22px !important;
    }

    .table-formato-mat thead th {
        background-color: #ffffff !important;
        color: #000000 !important;
        font-weight: 800 !important;
        text-align: center !important;
        font-size: 8pt !important;
        height: 16px !important;
        line-height: 1.1 !important;
    }

    .table-formato-mat tbody td {
        height: 22px !important;
        font-size: 8.5pt !important;
        font-variant-numeric: tabular-nums;
    }

    .table-formato-mat tbody tr.empty-row td {
        height: 22px !important;
    }

    @media print {
        body {
            padding-top: 0 !important;
            background: #ffffff !important;
        }

        .imprimir-ocultar,
        .reportes-bar,
        .navbar,
        #aplicacionesSidebarCol,
        #modalSalidaMateriales,
        .modal-backdrop {
            display: none !important;
        }

        #aplicacionesMainCol {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .table-aplicaciones-reporte th,
        .table-aplicaciones-reporte td {
            border: 1px solid #000 !important;
            padding: 2px 4px !important;
            font-size: 8pt !important;
        }

        h5 {
            margin: 0 0 4px 0;
            font-size: 1.05rem;
        }

        @page {
            margin-top: 10mm;
            margin-bottom: 15mm;

            @bottom-left {
                content: "Impreso el: <?php echo date('d/m/Y H:i'); ?>";
                font-size: 9px;
                font-family: Arial, sans-serif;
            }

            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 9px;
                font-family: Arial, sans-serif;
            }
        }
    }
</style>

<!-- Barra superior del reporte -->
<div class="reportes-bar d-flex flex-wrap justify-content-between align-items-center">
    <div class="mb-2 mb-md-0">
        <h1 class="d-flex align-items-center">
            <span class="material-icons mr-2 me-2" style="color: #00796B; font-size: 1.4rem;">science</span>
            Reporte Semanal de Aplicaciones
        </h1>
        <p>Programación semanal y control de labores y mezclas de insumos por finca</p>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-2 imprimir-ocultar">
        <button type="button" id="btnMostrarFiltros" class="btn btn-brand-green btn-sm d-none align-items-center me-2 mr-2" onclick="toggleSidebar();" style="border-radius: 8px;">
            <span class="material-icons mr-1 me-1" style="font-size: 1.1rem;">tune</span>
            Mostrar Filtros
        </button>
        <button type="button" class="btn btn-outline-brand-green btn-sm d-flex align-items-center me-2 mr-2" onclick="window.print();" style="border-radius: 8px;">
            <span class="material-icons mr-1 me-1" style="font-size: 1.1rem;">print</span>
            Imprimir
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btnFormatoSalidaMateriales"
            data-toggle="modal" data-target="#modalSalidaMateriales" data-bs-toggle="modal" data-bs-target="#modalSalidaMateriales"
            title="Formato Solicitud Salida de Materiales" style="border-radius: 8px;">
            <span class="material-icons mr-1 me-1" style="font-size: 1.1rem;">receipt_long</span>
            Formato Salida Materiales
        </button>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 pb-4">
    <div class="row" id="aplicacionesRow">
        <!-- Panel Izquierdo de Filtros (Sidebar) -->
        <div class="col-md-4 col-lg-3 mb-3 imprimir-ocultar" id="aplicacionesSidebarCol">
            <div class="filtro-sidebar">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <span class="d-flex align-items-center">
                            <span class="material-icons mr-2 me-2" style="font-size: 1.1rem;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <button type="button" class="btn btn-link text-white p-0 d-flex align-items-center text-decoration-none" onclick="toggleSidebar();" title="Ocultar filtros">
                            <span class="material-icons" style="font-size: 1.25rem;">chevron_left</span>
                        </button>
                    </div>
                    <div class="card-body p-3 bg-white">
                        <form id="formFiltrosAplicaciones" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'index.php?report=aplicaciones'); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="xfinca">Finca</label>
                                <select name="xfinca" id="xfinca" class="form-control form-select">
                                    <option value="">-- Todas las fincas --</option>
                                    <?php foreach ($fincas as $f): ?>
                                        <option value="<?php echo htmlspecialchars($f); ?>" <?php echo ($f === $finca) ? " selected='selected'" : ''; ?>>
                                            <?php echo htmlspecialchars($f); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="xtipo">Tipo de Aplicación</label>
                                <select name="xtipo" id="xtipo" class="form-control form-select">
                                    <option value="">-- Todos los tipos --</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($t === $tipo) ? " selected='selected'" : ''; ?>>
                                            <?php echo htmlspecialchars($t); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="dateIni">Fecha Inicial</label>
                                <input type="date" name="dateIni" id="dateIni" value="<?php echo htmlspecialchars($dateIni); ?>" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="dateEnd">Fecha Final</label>
                                <input type="date" name="dateEnd" id="dateEnd" value="<?php echo htmlspecialchars($dateEnd); ?>" class="form-control">
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button name="buscar" type="submit" class="btn btn-brand-green btn-block w-100 d-flex align-items-center justify-content-center py-2 shadow-sm mb-2" style="border-radius: 8px; font-weight: 600;">
                                    <span class="material-icons mr-1 me-1" style="font-size: 1.15rem;">search</span>
                                    Buscar
                                </button>
                                <a href="index.php?report=aplicaciones" class="btn btn-outline-secondary btn-block w-100 btn-sm d-flex align-items-center justify-content-center py-1" style="border-radius: 8px;">
                                    <span class="material-icons mr-1 me-1" style="font-size: 1rem;">restart_alt</span>
                                    Restablecer
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Contenido Principal -->
        <div class="col-md-8 col-lg-9" id="aplicacionesMainCol">
            <?php if (!empty($rows)): ?>
                <?php
                try {
                    $finicial = new Carbon($dateIni);
                    $ffinal = new Carbon($dateEnd);
                    $txtIni = $finicial->format('d-m-y/W');
                    $txtEnd = $ffinal->format('d-m-y/W');
                } catch (\Throwable $e) {
                    $txtIni = date('d-m-y', strtotime($dateIni)) . '/' . date('W', strtotime($dateIni));
                    $txtEnd = date('d-m-y', strtotime($dateEnd)) . '/' . date('W', strtotime($dateEnd));
                }
                ?>
                <!-- Tarjeta de Información de Filtros Actuales -->
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 p-3 bg-white border rounded shadow-sm">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-2 py-1 me-2 mr-2" style="font-size: 0.85rem;">
                            <strong>Finca:</strong> <?php echo htmlspecialchars($finca ?: 'Todas'); ?>
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 me-2 mr-2" style="font-size: 0.85rem;">
                            <strong>Tipo:</strong> <?php echo htmlspecialchars($tipo ?: 'Todos'); ?>
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.85rem;">
                            <strong>Semana/Rango:</strong> <?php echo htmlspecialchars($txtIni . ' a ' . $txtEnd); ?>
                        </span>
                    </div>
                    <div class="small text-muted mt-2 mt-sm-0">
                        Total registros: <strong><?php echo count($rows); ?></strong>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                            <span class="material-icons mr-1 me-1 text-teal" style="font-size: 1.15rem; color: #00796B;">table_chart</span>
                            Programación de Aplicaciones por Bloque y Cama
                        </h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table-aplicaciones-reporte table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Bloque</th>
                                        <th>Aplicar</th>
                                        <th>Variedad</th>
                                        <th>Temporada</th>
                                        <th>#Cama Física</th>
                                        <th>#Cama Real</th>
                                        <th>Realizado</th>
                                    </tr>
                                </thead>
                                <tbody>
                <?php
                $prevBloque = null;
                $prevAplicar = null;

                $subAplicarCamas = 0;
                $subAplicarNcamas = 0.0;

                $subBloqueCamas = 0;
                $subBloqueNcamas = 0;

                $totalCamas = 0;
                $totalNcamas = 0;

                foreach ($rows as $f):
                    if ($prevBloque !== null && $f->bloque !== $prevBloque):
                        $camasAppRedondeadas = (int) round($subAplicarNcamas, 0);
                        $subBloqueCamas += $subAplicarCamas;
                        $subBloqueNcamas += $camasAppRedondeadas;
                        ?>
                        <tr>
                            <td colspan="4"><strong>Subtotal <?php echo htmlspecialchars($prevBloque); ?> -
                                    <?php echo htmlspecialchars($prevAplicar); ?></strong></td>
                            <td><strong><?php echo number_format($subAplicarCamas, 0, '', '.'); ?></strong></td>
                            <td><strong><?php echo number_format($camasAppRedondeadas, 0, '', '.'); ?></strong></td>
                            <td></td>
                        </tr>
                        <?php
                        $subAplicarCamas = 0;
                        $subAplicarNcamas = 0.0;
                        ?>
                        <tr>
                            <td colspan="4"><strong>Subtotal Bloque <?php echo htmlspecialchars($prevBloque); ?></strong></td>
                            <td><strong><?php echo number_format($subBloqueCamas, 0, '', '.'); ?></strong></td>
                            <td><strong><?php echo number_format($subBloqueNcamas, 0, '', '.'); ?></strong></td>
                            <td></td>
                        </tr>
                        <?php
                        $totalCamas += $subBloqueCamas;
                        $totalNcamas += $subBloqueNcamas;
                        $subBloqueCamas = 0;
                        $subBloqueNcamas = 0;
                    elseif ($prevAplicar !== null && $f->aplicar !== $prevAplicar):
                        $camasAppRedondeadas = (int) round($subAplicarNcamas, 0);
                        $subBloqueCamas += $subAplicarCamas;
                        $subBloqueNcamas += $camasAppRedondeadas;
                        ?>
                        <tr>
                            <td colspan="4"><strong>Subtotal <?php echo htmlspecialchars($prevBloque); ?> -
                                    <?php echo htmlspecialchars($prevAplicar); ?></strong></td>
                            <td><strong><?php echo number_format($subAplicarCamas, 0, '', '.'); ?></strong></td>
                            <td><strong><?php echo number_format($camasAppRedondeadas, 0, '', '.'); ?></strong></td>
                            <td></td>
                        </tr>
                        <?php
                        $subAplicarCamas = 0;
                        $subAplicarNcamas = 0.0;
                    endif;

                    $prevBloque = $f->bloque;
                    $prevAplicar = $f->aplicar;

                    $subAplicarCamas += (int) $f->camas;
                    $subAplicarNcamas += (float) $f->ncamas;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($f->bloque); ?></td>
                        <td><?php echo htmlspecialchars($f->aplicar); ?></td>
                        <td><?php echo htmlspecialchars($f->variedad); ?></td>
                        <td><?php echo htmlspecialchars($f->temporada); ?></td>
                        <td><?php echo number_format($f->camas, 0, '', '.'); ?></td>
                        <td><?php echo number_format($f->ncamas, 0, '', '.'); ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($prevBloque !== null): ?>
                    <?php
                    $camasAppRedondeadas = (int) round($subAplicarNcamas, 0);
                    $subBloqueCamas += $subAplicarCamas;
                    $subBloqueNcamas += $camasAppRedondeadas;
                    $totalCamas += $subBloqueCamas;
                    $totalNcamas += $subBloqueNcamas;
                    ?>
                    <tr>
                        <td colspan="4"><strong>Subtotal <?php echo htmlspecialchars($prevBloque); ?> -
                                <?php echo htmlspecialchars($prevAplicar); ?></strong></td>
                        <td><strong><?php echo number_format($subAplicarCamas, 0, '', '.'); ?></strong></td>
                        <td><strong><?php echo number_format($camasAppRedondeadas, 0, '', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>Subtotal Bloque <?php echo htmlspecialchars($prevBloque); ?></strong></td>
                        <td><strong><?php echo number_format($subBloqueCamas, 0, '', '.'); ?></strong></td>
                        <td><strong><?php echo number_format($subBloqueNcamas, 0, '', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>TOTAL GENERAL</strong></td>
                        <td><strong><?php echo number_format($totalCamas, 0, '', '.'); ?></strong></td>
                        <td><strong><?php echo number_format($totalNcamas, 0, '', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                <tr style="height:30px">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>

    <?php if (!empty($supplies)): ?>
        <?php
        $totalesPorInsumo = [];
        foreach ($supplies as $sp) {
            $cant = (float) $sp->cantidad;
            $key = $sp->insumo . '|' . $sp->medida;
            if (!isset($totalesPorInsumo[$key]))
                $totalesPorInsumo[$key] = 0.0;
            $totalesPorInsumo[$key] += $cant;
        }
        ?>
        <div class="saltoDePagina d-print-block"></div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                            <span class="material-icons mr-1 me-1 text-teal" style="font-size: 1.15rem; color: #00796B;">inventory_2</span>
                            Resumen de Insumos por Semana
                        </h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table-aplicaciones-reporte table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Aplicar</th>
                                        <th>Insumo</th>
                                        <th>Medida</th>
                                        <th>Dosis</th>
                                        <th>#Cama Real</th>
                                        <th>Cantidad a Usar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supplies as $sp): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($sp->aplicacion); ?></td>
                                            <td class="font-weight-bold"><?php echo htmlspecialchars($sp->insumo); ?></td>
                                            <td><?php echo htmlspecialchars($sp->medida); ?></td>
                                            <td class="text-right"><?php echo number_format($sp->dosis, 2, ',', '.'); ?></td>
                                            <td class="text-right"><?php echo number_format($sp->ncamas, 0, '', '.'); ?></td>
                                            <td class="text-right font-weight-bold"><?php echo number_format($sp->cantidad, 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-2 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                            <span class="material-icons mr-1 me-1 text-teal" style="font-size: 1.15rem; color: #00796B;">calculate</span>
                            Total por Insumo
                        </h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="table-responsive">
                            <table class="table-aplicaciones-reporte table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Insumo</th>
                                        <th>Medida</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($totalesPorInsumo as $key => $tot): ?>
                                        <?php list($insumo, $medida) = explode('|', $key); ?>
                                        <tr>
                                            <td class="font-weight-bold"><?php echo htmlspecialchars($insumo); ?></td>
                                            <td><?php echo htmlspecialchars($medida); ?></td>
                                            <td class="text-right font-weight-bold"><?php echo number_format($tot, 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="card shadow-sm border-0 p-5 text-center bg-white" style="border-radius: 14px;">
        <div class="py-4">
            <span class="material-icons text-muted mb-2" style="font-size: 3rem; color: #a0aec0;">search_off</span>
            <h5 class="text-dark font-weight-bold">No se encontraron aplicaciones</h5>
            <p class="text-muted small mb-0">No hay registros de aplicaciones programadas para la finca y rango de fechas seleccionado.</p>
        </div>
    </div>
<?php endif; ?>

        </div> <!-- Fin #aplicacionesMainCol -->
    </div> <!-- Fin #aplicacionesRow -->
</div> <!-- Fin container-fluid -->

<!-- ========================================================
     MODAL: FORMATO SOLICITUD SALIDA DE MATERIALES (FOR 012.2 ALM 406)
     ======================================================== -->
<div class="modal fade" id="modalSalidaMateriales" tabindex="-1" role="dialog"
    aria-labelledby="modalSalidaMaterialesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width: 95vw; width: 1100px;">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center bg-light py-2">
                <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalSalidaMaterialesLabel"
                    style="font-size: 0.95rem;">
                    <span class="material-icons align-middle mr-1 text-teal"
                        style="font-size: 1.15rem; color: #00796B;">description</span>
                    Formato Solicitud Salida de Materiales (FOR 012.2 ALM 406)
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3 bg-white" style="overflow-x: auto;">
                <!-- CONTENEDOR DEL FORMATO PARA IMPRESIÓN / VISTA PREVIA -->
                <div id="formatoSalidaMaterialesPrint" class="formato-materiales-wrapper">
                    <!-- Encabezado del Formato Fiel a la Imagen -->
                    <div class="formato-header">
                        <div class="empresa-nombre"><?php
                        $fincaLimpia = preg_replace('/\s+(S\.A\.S\.?|SAS)$/i', '', strtoupper(trim($finca ?: 'INVERPALMAS')));
                        echo htmlspecialchars($fincaLimpia . '');
                        ?></div>
                        <div class="formato-titulo">FORMATO SOLICITUD SALIDA DE MATERIALES</div>
                        <div class="formato-codigo">CÓDIGO: FOR 012.2 ALM 406</div>
                    </div>

                    <!-- Metadatos de Área y Fecha -->
                    <div class="formato-meta-row">
                        <div class="meta-item"><span class="meta-label">AREA:</span> <span class="meta-val">RIEGO</span>
                        </div>
                        <div class="meta-item"><span class="meta-label">FECHA:</span> <span class="meta-val"><?php
                        echo !empty($dateIni) ? date('j/n/Y', strtotime($dateIni)) : date('j/n/Y');
                        ?></span></div>
                    </div>

                    <!-- Tabla Cuadrícula Fiel a la Imagen (11 columnas) -->
                    <table class="table-formato-mat">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 7%;">CÓDIGO</th>
                                <th style="width: 23%;">DESCRIPCIÓN</th>
                                <th style="width: 5%;">PRE</th>
                                <th rowspan="2" style="width: 17%;">JUSTIFICACIÓN</th>
                                <th rowspan="2" style="width: 7%;">DESTINO</th>
                                <th rowspan="2" style="width: 7%;">UBICACIÓN</th>
                                <th rowspan="2" style="width: 6%;">DOSIS</th>
                                <th style="width: 5%;">UNI</th>
                                <th style="width: 8%;">CAN</th>
                                <th rowspan="2" style="width: 7%;">TANQUES</th>
                                <th style="width: 8%;">CAN</th>
                            </tr>
                            <tr>
                                <th>ARTICULO SOLICITADO</th>
                                <th>PRO</th>
                                <th>MED</th>
                                <th>SOLI</th>
                                <th>ENTRE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($materialsByBlock)): ?>
                                <?php foreach ($materialsByBlock as $mat): ?>
                                    <?php
                                    // Formateo del nombre del insumo
                                    $insumoNombre = ucwords(strtolower(trim($mat->insumo)));
                                    if (stripos($insumoNombre, 'Trichoderma') !== false) {
                                        $insumoNombre = 'Trichoderma';
                                    } elseif (stripos($insumoNombre, 'Biomix') !== false) {
                                        $insumoNombre = 'Biomix';
                                    }

                                    // Justificación (tipo de aplicación)
                                    $justif = ucfirst(strtolower(trim($mat->justificacion ?: $tipo)));

                                    // Unidad de medida y dosis configuradas en el catálogo
                                    $uniMed = strtoupper(trim($mat->medida ?: ''));
                                    $dosisStr = is_numeric($mat->dosis)
                                        ? str_replace('.', ',', (string)(float)$mat->dosis)
                                        : str_replace('.', ',', (string)$mat->dosis);

                                    // Cantidad total por bloque: calculada por aplicación (camas reales * dosis cama, a 2 decimales) y agrupada por bloque
                                    $canSoli = isset($mat->cantidad)
                                        ? (float)$mat->cantidad
                                        : round(((float)($mat->ncamas ?? 0)) * (float)$mat->dosis, 2);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo htmlspecialchars((string)($mat->codigo ?? '')); ?></td>
                                        <td class="text-left px-2 font-weight-bold">
                                            <?php echo htmlspecialchars($insumoNombre); ?></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"><?php echo htmlspecialchars($justif); ?></td>
                                        <td class="text-center"></td>
                                        <td class="text-center font-weight-bold"><?php echo htmlspecialchars($mat->bloque); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($dosisStr); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($uniMed); ?></td>
                                        <td class="text-right pr-2 font-weight-bold">
                                            <?php echo number_format($canSoli, 2, ',', '.'); ?></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php
                                $filasVacias = max(0, 16 - count($materialsByBlock));
                                for ($i = 0; $i < $filasVacias; $i++):
                                    ?>
                                    <tr class="empty-row">
                                        <td>&nbsp;</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php else: ?>
                                <?php for ($i = 0; $i < 18; $i++): ?>
                                    <tr class="empty-row">
                                        <td>&nbsp;</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer py-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.8rem;">
                    * Datos generados a partir de los filtros seleccionados (Finca:
                    <?php echo htmlspecialchars($finca); ?>, Tipo: <?php echo htmlspecialchars($tipo); ?>)
                </span>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <span class="material-icons align-middle mr-1" style="font-size: 0.95rem;">close</span>
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-brand-green btn-sm" onclick="imprimirFormatoSalidaMateriales();">
                        <span class="material-icons align-middle mr-1" style="font-size: 0.95rem;">print</span>
                        Imprimir Formato
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function imprimirFormatoSalidaMateriales() {
        var content = document.getElementById('formatoSalidaMaterialesPrint');
        if (!content) {
            alert('No se encontró el contenido del formato para imprimir.');
            return;
        }

        var printIframe = document.getElementById('iframe_print_salida_materiales');
        if (!printIframe) {
            printIframe = document.createElement('iframe');
            printIframe.id = 'iframe_print_salida_materiales';
            printIframe.name = 'iframe_print_salida_materiales';
            printIframe.style.position = 'fixed';
            printIframe.style.top = '-9999px';
            printIframe.style.left = '-9999px';
            printIframe.style.width = '1000px';
            printIframe.style.height = '1000px';
            printIframe.style.border = 'none';
            document.body.appendChild(printIframe);
        }

        var iframeDoc = printIframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write('<!DOCTYPE html><html><head><title>Formato Solicitud Salida de Materiales</title>');
        iframeDoc.write('<style>' +
            '@page { size: portrait; margin: 8mm 10mm; }' +
            '* { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }' +
            'body { margin: 0; padding: 0; background: #ffffff; color: #000000; font-family: Arial, Calibri, sans-serif; font-size: 8.5pt; }' +
            '.formato-materiales-wrapper { width: 100%; background: #ffffff; color: #000000; padding: 0; }' +
            '.formato-header { text-align: center; margin-bottom: 8px; }' +
            '.formato-header .empresa-nombre { font-size: 13pt; font-weight: 800; color: #000000; letter-spacing: 0.05em; line-height: 1.2; text-transform: uppercase; }' +
            '.formato-header .formato-titulo { font-size: 11pt; font-weight: 800; color: #000000; margin: 2px 0; line-height: 1.2; }' +
            '.formato-header .formato-codigo { font-size: 9pt; font-weight: 800; color: #000000; letter-spacing: 0.04em; line-height: 1.2; }' +
            '.formato-meta-row { display: flex; justify-content: flex-start; gap: 35px; font-size: 9pt; font-weight: 800; margin-bottom: 8px; color: #000000; }' +
            '.formato-meta-row .meta-item { display: inline-block; }' +
            '.formato-meta-row .meta-label { font-weight: 800; margin-right: 4px; }' +
            '.formato-meta-row .meta-val { font-weight: 700; }' +
            '.table-formato-mat { width: 100%; border-collapse: collapse !important; font-size: 8pt; color: #000000; table-layout: fixed; }' +
            '.table-formato-mat th, .table-formato-mat td { border: 1px solid #000000 !important; padding: 3px 2px !important; vertical-align: middle !important; line-height: 1.15 !important; height: 22px !important; }' +
            '.table-formato-mat thead th { background-color: #ffffff !important; color: #000000 !important; font-weight: 800 !important; text-align: center !important; font-size: 7.5pt !important; height: 16px !important; }' +
            '.table-formato-mat tbody td { height: 22px !important; font-size: 8pt !important; font-variant-numeric: tabular-nums; }' +
            '.table-formato-mat tbody tr.empty-row td { height: 22px !important; }' +
            '.text-center { text-align: center !important; }' +
            '.text-left { text-align: left !important; }' +
            '.text-right { text-align: right !important; }' +
            '.font-weight-bold { font-weight: bold !important; }' +
            '.px-2 { padding-left: 4px !important; padding-right: 4px !important; }' +
            '.pr-2 { padding-right: 4px !important; }' +
            '</style>');
        iframeDoc.write('</head><body>');
        iframeDoc.write(content.outerHTML);
        iframeDoc.write('</body></html>');
        iframeDoc.close();

        setTimeout(function() {
            printIframe.contentWindow.focus();
            printIframe.contentWindow.print();
        }, 250);
    }

    function toggleSidebar() {
        var sidebar = document.getElementById('aplicacionesSidebarCol');
        var main = document.getElementById('aplicacionesMainCol');
        var btnMostrar = document.getElementById('btnMostrarFiltros');
        if (!sidebar || !main) return;

        var isHidden = sidebar.classList.toggle('d-none');
        if (isHidden) {
            main.classList.remove('col-md-8', 'col-lg-9');
            main.classList.add('col-12');
            if (btnMostrar) {
                btnMostrar.classList.remove('d-none');
                btnMostrar.classList.add('d-inline-flex');
            }
        } else {
            main.classList.remove('col-12');
            main.classList.add('col-md-8', 'col-lg-9');
            if (btnMostrar) {
                btnMostrar.classList.add('d-none');
                btnMostrar.classList.remove('d-inline-flex');
            }
        }
    }
</script>
<?php use Carbon\Carbon; ?>
<style>
    @media screen, print {
        td, tr, th {
            border: 1px solid black;
            padding-bottom: 0em;
            height: 10px;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
        }
    }

    @media print {
        /* elimina el espacio superior del navbar fijo y del formulario de filtros */
        body {
            padding-top: 0 !important;
        }
        .imprimir-form,
        .imprimir-gap {
            display: none !important;
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

<div class="card imprimir-form">
    <div class="card-header">
        <form class="form-inline" action="home.php?menu=tables&report=2" method="post" enctype="multipart/form-data">
            <div class="form-group mx-sm-3 mb-2">
                <select name="xfinca" class="form-control" data-live-search="true">
                    <option value="">Finca</option>
                    <?php foreach ($fincas as $f): ?>
                        <option value="<?php echo htmlspecialchars($f); ?>"<?php echo ($f === $finca) ? " selected='selected'" : ''; ?>>
                            <?php echo htmlspecialchars($f); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <label class="sr-only">Fecha Inicial</label>
                <input type="date" name="dateIni" value="<?php echo htmlspecialchars($dateIni); ?>" class="form-control">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <label class="sr-only">Fecha Final</label>
                <input type="date" name="dateEnd" value="<?php echo htmlspecialchars($dateEnd); ?>" class="form-control">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <select name="xtipo" class="form-control" data-live-search="true">
                    <option value="">Tipo</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>"<?php echo ($t === $tipo) ? " selected='selected'" : ''; ?>>
                            <?php echo htmlspecialchars($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <button name="buscar" type="submit" class="btn btn-brand-green mb-2">Buscar</button>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <button name="imprimir" type="submit" class="btn btn-outline-brand-green mb-2" onclick="window.print();">Imprimir</button>
            </div>
        </form>
    </div>
</div>
<br class="imprimir-gap">

<?php if (!empty($rows)): ?>
    <?php
        $finicial = new Carbon($dateIni);
        $ffinal   = new Carbon($dateEnd);
    ?>
    <h5><?php echo htmlspecialchars($finca); ?></h5>
    <h5>Reporte Semanal de Aplicaciones</h5>
    <h5><?php echo htmlspecialchars($tipo); ?></h5>
    <h5>Entre el: <?php echo $finicial->format('d-m-y/W'); ?> Y <?php echo $ffinal->format('d-m-y/W'); ?></h5>

    <div class="row">
        <div class="col-12">
            <table class="table table-sm">
                <tr>
                    <th>Bloque</th><th>Aplicar</th>
                    <th>Variedad</th><th>Temporada</th>
                    <th>#Cama Fisica</th><th>#Cama Real</th>
                    <th>Realizado</th>
                </tr>
                <?php foreach ($rows as $f): ?>
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
                <tr style="height:30px">
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="saltoDePagina d-print-block"></div>

    <div class="row">
        <div class="col-6">
            <h7>Resumen por bloque</h7>
            <table class="table table-sm">
                <tr>
                    <th>Bloque</th><th>Aplicar</th><th>#Cama Fisica</th><th>#Cama Real</th>
                </tr>
                <?php foreach ($rowsByBlock as $f): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($f->bloque); ?></td>
                        <td><?php echo htmlspecialchars($f->aplicar); ?></td>
                        <td><?php echo number_format($f->camas, 0, '', '.'); ?></td>
                        <td><?php echo number_format($f->ncamas, 0, '', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="col-6"></div>
    </div>

    <?php if (!empty($supplies)): ?>
        <?php
            $totalGeneral = 0.0;
            $totalesPorInsumo = [];
            foreach ($supplies as $sp) {
                $cant = (float)$sp->cantidad;
                $totalGeneral += $cant;
                $key = $sp->insumo . '|' . $sp->medida;
                if (!isset($totalesPorInsumo[$key])) $totalesPorInsumo[$key] = 0.0;
                $totalesPorInsumo[$key] += $cant;
            }
        ?>
        <div class="saltoDePagina d-print-block"></div>

        <div class="row">
            <div class="col-8">
                <h5>Resumen de Insumos por Semana</h5>
                <table class="table table-sm">
                    <tr>
                        <th>Finca</th><th>Aplicar</th><th>Insumo</th><th>Medida</th><th>Dosis</th><th>#Cama Real</th><th>Cantidad a Usar</th>
                    </tr>
                    <?php
                        $subtotal = 0.0;
                        $prevFinca = null;
                        foreach ($supplies as $sp):
                            if ($prevFinca !== null && $sp->finca !== $prevFinca):
                    ?>
                        <tr>
                            <td colspan="6" class="text-right"><strong>Subtotal <?php echo htmlspecialchars($prevFinca); ?></strong></td>
                            <td><strong><?php echo number_format($subtotal, 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php
                            endif;
                            $prevFinca = $sp->finca;
                            $subtotal += (float)$sp->cantidad;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sp->finca); ?></td>
                        <td><?php echo htmlspecialchars($sp->aplicacion); ?></td>
                        <td><?php echo htmlspecialchars($sp->insumo); ?></td>
                        <td><?php echo htmlspecialchars($sp->medida); ?></td>
                        <td><?php echo number_format($sp->dosis, 2, ',', '.'); ?></td>
                        <td><?php echo number_format($sp->ncamas, 0, '', '.'); ?></td>
                        <td><?php echo number_format($sp->cantidad, 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if ($prevFinca !== null): ?>
                    <tr>
                        <td colspan="6" class="text-right"><strong>Subtotal <?php echo htmlspecialchars($prevFinca); ?></strong></td>
                        <td><strong><?php echo number_format($subtotal, 2, ',', '.'); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="6" class="text-right"><strong>TOTAL</strong></td>
                        <td><strong><?php echo number_format($totalGeneral, 2, ',', '.'); ?></strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-4">
                <h5>Total por Insumo</h5>
                <table class="table table-sm">
                    <tr>
                        <th>Insumo</th><th>Medida</th><th>Total</th>
                    </tr>
                    <?php foreach ($totalesPorInsumo as $key => $tot): ?>
                        <?php list($insumo, $medida) = explode('|', $key); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($insumo); ?></td>
                            <td><?php echo htmlspecialchars($medida); ?></td>
                            <td><?php echo number_format($tot, 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <p>0 results</p>
<?php endif; ?>

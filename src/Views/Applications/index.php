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
</style>

<div class="card">
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
                <button name="buscar" type="submit" class="btn btn-primary mb-2">Buscar</button>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <button name="imprimir" type="submit" class="btn btn-primary mb-2" onclick="window.print();">Imprimir</button>
            </div>
        </form>
    </div>
</div>
<br>

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
                    <th>Barcode</th>
                </tr>
                <?php foreach ($rows as $f): ?>
                    <?php
                        $codigo     = '*' . ($f->finca === 'INVERPALMAS' ? '10' : '20') . $f->bloque . $f->codigo . $f->temporada . '*';
                        $barcode    = new Barcode39($codigo);
                        ob_start();
                        $barcode->draw();
                        $barcodeImg = ob_get_clean();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($f->bloque); ?></td>
                        <td><?php echo htmlspecialchars($f->aplicar); ?></td>
                        <td><?php echo htmlspecialchars($f->variedad); ?></td>
                        <td><?php echo htmlspecialchars($f->temporada); ?></td>
                        <td><?php echo number_format($f->camas, 0, '', '.'); ?></td>
                        <td><?php echo number_format($f->ncamas, 0, '', '.'); ?></td>
                        <td></td>
                        <td><img src="data:image/png;base64,<?php echo base64_encode($barcodeImg); ?>" alt="Código de Barras"></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="height:30px">
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
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

<?php else: ?>
    <p>0 results</p>
<?php endif; ?>

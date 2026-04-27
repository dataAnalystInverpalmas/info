<!-- Vista index de Greenhouses (Sólo marcado e interpolación de variables) -->
<div class="container-fluid">
    <div class="row">
        <div class="table-responsive">
            <table id="panelBQ" class="table displayBQ compact" style="width:100%">
                <thead>
                    <tr>
                        <td>Finca</td>
                        <td>Bloque</td>
                        <td>Tabla</td>
                        <td>Nave</td>
                        <td>#Cama</td>
                        <td>Camas_Reales</td>
                        <td>#_Plantas</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($greenhouses)): ?>
                        <?php foreach ($greenhouses as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row->finca); ?></td>
                                <td><?php echo htmlspecialchars($row->bloque); ?></td>
                                <td><?php echo htmlspecialchars($row->tabla); ?></td>
                                <td><?php echo htmlspecialchars($row->nave); ?></td>
                                <td><?php echo htmlspecialchars($row->cama); ?></td>
                                <td><?php echo number_format($row->camas_real, 2); ?></td>
                                <td><?php echo number_format($row->nplantas, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/mainSP.js"></script>

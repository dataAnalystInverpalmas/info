<?php
    //lamar conexion
    include ('funciones/conexion.php');
    //funciones personalizadas
    require "vendor/autoload.php";
    //

    $query = "SELECT finca_id as finca,bloque,tabla,nave,cama,sum(longitud*ancho)/18 as camas_real, sum(longitud*ancho)*53.3333 as nplantas 
                FROM greenhouses WHERE longitud>0 GROUP BY finca_ID,bloque,tabla,nave,cama ";
           
    $result = $conexion->query($query);
?>
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
                        <?php while ( $row = $result->fetch_object() ){ ?>
                            <tr>
                                <td><?php echo $row->finca; ?></td>
                                <td><?php echo $row->bloque; ?></td>
                                <td><?php echo $row->tabla; ?></td>
                                <td><?php echo $row->nave; ?></td>
                                <td><?php echo $row->cama; ?></td>
                                <td><?php echo number_format($row->camas_real,2); ?></td>
                                <td><?php echo number_format($row->nplantas,0,',','.'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
        </div>
</div>

<script src="scripts/mainSP.js"></script>

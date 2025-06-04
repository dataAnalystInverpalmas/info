<?php
//lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
//carbon
require "vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

$sqlP="select distinct(producto) as nombre from varieties WHERE estado='1' ";
$resultP=$conexion->query($sqlP);

$sqlC="select distinct(color) as nombre from varieties WHERE estado='1' ";
$resultC=$conexion->query($sqlC);

$sqlV="select distinct(nombre) as nombre from varieties WHERE estado='1' ";
$resultV=$conexion->query($sqlV);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 d-print-none">
            <div class="list-group">
                <h3>Producto</h3>
                <?php
                while($row=$resultP->fetch_object())
                { ?>
                    <div class="list-group-item checkbox">
                        <label for=""><input type="checkbox" class="common-selector producto" value="<?php echo $row->nombre; ?>" />
                        <?php echo $row->nombre; ?></label>
                    </div>
                <?php 
                }
                ?>
                
            </div>
            <div class="list-group">
                <h3>Color</h3>
                <?php
                while($row=$resultC->fetch_object())
                { ?>
                    <div class="list-group-item checkbox">
                        <label for=""><input type="checkbox" class="common-selector color" value="<?php echo $row->nombre; ?>" />
                        <?php echo $row->nombre; ?></label>
                    </div>
                <?php 
                }
                ?>
                
            </div>    
            <div class="list-group">
                <h3>Variedad</h3>
                <?php
                while($row=$resultV->fetch_object())
                { ?>
                    <div class="list-group-item checkbox">
                        <label for=""><input type="checkbox" class="common-selector nombre" value="<?php echo $row->nombre; ?>" />
                        <?php echo $row->nombre; ?></label>
                    </div>
                <?php 
                }
                ?>
                
            </div>                     
        </div>
        <div class="col-sm-10">
            <br>
            <div class="row filter_data">

            </div>
        </div>
    </div>
</div>
<script>
   
    $(document).ready(function(){
        filter_data();

        function filter_data(){
            $('.filter_data').html(
                '<div id="loading"></div>'
            );
            var action = 'fetch_data';
            var producto = get_filter('producto');
            var color = get_filter('color');
            var nombre= get_filter('nombre');
            $.ajax({
                url:"dist/fetch_data.php",
                method:"POST",
                data:{
                    action:action, producto:producto, color:color, nombre:nombre
                },
                success:function(data){
                    $('.filter_data').html(data);
                }
            });
        }
        function get_filter(class_name){
            var filter = [];
            $('.'+class_name+':checked').each(function(){
                filter.push($(this).val());
            });
            return filter;
            
        }
        $('.common-selector').click(function(){
            filter_data();
        });
    });

</script>

    
<?php
//lamar conexion
include ('funciones/conexion.php');
require "vendor/autoload.php";
require "dist/data.php";

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

$i=0; //INCIALIZAR VARIBLE PARA CONTROL DE ID DE GRAFICOS

$resultado=$conexion->query($sqlVariedadesV);
$where="";
?>

<?php
while ($fila=$resultado->fetch_object()) {
  ?>
  <div class="container-fluid">
  <div class="row">
    <div class="col-12 d-flex justify-content-center">
      <h1><?php echo '<strong>'.$fila->nombre.'</strong>'; ?> </h1>
    </div>
  </div>
  <?php
  $where1=" WHERE nombre='".$fila->nombre."' ";  
  $where=" WHERE variedad='".$fila->nombre."' ";
  $where2=" WHERE tabla_id='".$fila->id."' ";
  $i++;
  $x=1000+$i;
  $y=10000+$i;
  $z=20000+$i;
  $h=30000+$i;
?>  
<span class="rounded">
      <div class="row">
        <div class="col-6 col-sm-6 col-md-6 col-xs-6">
          <div class="chart-container">
          <canvas id="<?php echo $i; ?>"> </canvas>
        </div>
          <script>
          var i = <?php echo $i; ?>;
          //grafico de tallos vendidos--------------------------------------------
          var ctx = document.getElementById(i).getContext('2d');
          var chart = new Chart(ctx, {
              // The type of chart we want to create
              type: 'bar',
              // The data for our dataset
              data: {
                  labels: [
                    <?php
                    $sqlCalidades="
                    select a.grado,sum(a.valor)/ (select sum(b.valor) from table_qualities as b
                    $where) as valor
                    from table_qualities as a 
                    INNER JOIN grades as c
                    ON c.abv=a.grado
                    $where
                    group by a.grado
                    order by c.id
                    ";
                    
                    $result=$conexion->query($sqlCalidades);
                    while($f = $result->fetch_object()){
                      ?>
                        '<?php echo $f->grado; ?>',
                      <?php
                      }
                     
                     ?>
                 ],
                  datasets: [{
                      label: ['% por grados'],
                      backgroundColor: [
                      'rgba(0, 255, 0, 0.5)',
                      'rgba(0, 100, 255, 0.5)',
                      'rgba(255, 0, 0, 0.5)',
                      'rgba(100, 0, 50, 0.5)',
                      'rgba(100, 60, 200, 0.5)'],
                      borderColor: 'rgba(0, 99, 132, 1)',
                      data: [
                        <?php
                        //labels para el grafico
                        $result=$conexion->query($sqlCalidades);
                        while($f = $result->fetch_object()){
                          ?>
                            '<?php echo $f->valor; ?>',
                          <?php
                          }
                        
                         ?>
                      ]
                  }]
              },
          
              // Configuration options go here
              options: {
                responsive: true,
                title: {
                      display: true,
                      text: 'Calidades'
                    },
                    scales: {
                  yAxes: [{
                      ticks: {
                          beginAtZero: true
                      }
                  }]
              }
              }
          });
        </script>
        </div>

      <div class="col-6 col-sm-6 col-md-6 col-xs-6">
        <div class="chart-container">
        <canvas id="<?php echo $x; ?>"> </canvas>
        </div>
        <script>
        var x = <?php echo $x; ?>;
        //grafico de tallos vendidos--------------------------------------------
        var ctx = document.getElementById(x).getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'bar',
            // The data for our dataset
            data: {
                labels: [
                  <?php
                  $sqlCausas="select t2.variedad,t2.causa,sum(t2.valor) as valor
                              from 
                                	(select t3.variedad,t3.causa,sum(t3.valor)/sum(t3.muestra)/
                                	(select count(distinct(fecha)) from table_nalcauses $where)
                                	as valor
                                	from table_nalcauses as t3
                                	$where
                                	group by t3.causa,t3.fecha,t3.variedad) AS t2 
                                  $where
                                  group by t2.causa,t2.variedad ";
                  $result=$conexion->query($sqlCausas);
                  while($f = $result->fetch_object()){
                    ?>
                      '<?php echo $f->causa; ?>',
                    <?php
                    }
                    
                   ?>
               ],
                datasets: [{
                    label: ['% de afectación por causa'],
                    backgroundColor: [
                    'rgba(0, 255, 0, 0.5)',
                    'rgba(0, 100, 255, 0.5)',
                    'rgba(255, 0, 0, 0.5)',
                    'rgba(50, 50, 0, 0.5)',
                    'rgba(0, 50, 50, 0.5)',
                    'rgba(100, 100, 0, 0.5)',
                    'rgba(100, 60, 200, 0.5)'],
                    borderColor: 'rgba(0, 99, 132, 1)',
                    data: [
                      <?php
                      $result=$conexion->query($sqlCausas);
                      while($f = $result->fetch_object()){
                        ?>
                          '<?php echo $f->valor; ?>',
                        <?php
                        }
                        
                       ?>
                    ]
                }]
            },
        
            // Configuration options go here
            options: {
              responsive: true,
              title: {
                    display: true,
                    text: 'Causas de Nacional'
                  },
                  scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
            }
        });
      </script>
      </div>
    </div><!--fIN PRIMERA FILA DE GRAFICOS-->
    <div class="row">
      <div class="col-6">
        <canvas id="<?php echo $y; ?>"> </canvas>
        <script>
        var y = <?php echo $y; ?>;
        //grafico de tallos vendidos--------------------------------------------
        var ctx = document.getElementById(y).getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'bar',
            // The data for our dataset
            data: {
                labels: [
                  <?php
                  $sqlEval="select item,avg(valor) as valor from table_evaluations 
                              $where group by item order by avg(valor) desc";
                  $result=$conexion->query($sqlEval);
                  while($f = $result->fetch_object()){
                    ?>
                      '<?php echo $f->item; ?>',
                    <?php
                    }
                    
                   ?>
               ],
                datasets: [{
                    label: ['Calificación: 1=malo, 2=normal, 3=bueno'],
                    backgroundColor: [
                    'rgba(0, 255, 0, 0.5)',
                    'rgba(0, 100, 255, 0.5)',
                    'rgba(255, 0, 0, 0.5)',
                    'rgba(50, 50, 0, 0.5)',
                    'rgba(0, 50, 50, 0.5)',
                    'rgba(100, 100, 0, 0.5)',
                    'rgba(200, 100, 0, 0.5)',
                    'rgba(100, 100, 200, 0.5)',
                    'rgba(100, 200, 0, 0.5)',
                    'rgba(150, 100, 0, 0.5)',
                    'rgba(100, 60, 200, 0.5)',
                    'rgba(80, 150, 150, 0.5)'],
                    borderColor: 'rgba(0, 99, 132, 1)',
                    data: [
                      <?php
                      $result=$conexion->query($sqlEval);
                      while($f = $result->fetch_object()){
                        ?>
                          '<?php echo $f->valor; ?>',
                        <?php
                        }
                        
                       ?>
                    ]
                }]
            },
        
            // Configuration options go here
            options: {
              title: {
                    display: true,
                    text: 'Visita a Vitrinas - Evaluación'
                  },
                  scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
            }
        });
      </script>
      </div>
      <div class="col-6">
        <canvas id="<?php echo $z; ?>"> </canvas>
        <script>
        var z = <?php echo $z; ?>;
        //grafico de tallos vendidos--------------------------------------------
        var ctx = document.getElementById(z).getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',
            // The data for our dataset
            data: {
                labels: [
                  <?php
                  $sqlCurves="select edad,sum(fmata) as valor from table_curves
                              $where group by edad order by edad asc";
                  $result=$conexion->query($sqlCurves);
                  while($f = $result->fetch_object()){
                    ?>
                      '<?php echo $f->edad; ?>',
                    <?php
                    }
                    
                   ?>
               ],
                datasets: [{
                    label: ['Flor Mata'],
                    backgroundColor: [
                    'rgba(0, 255, 0, 0.5)',
                    'rgba(0, 100, 255, 0.5)',
                    'rgba(255, 0, 0, 0.5)',
                    'rgba(100, 60, 200, 0.5)'],
                    borderColor: 'rgba(0, 99, 132, 1)',
                    data: [
                      <?php
                      $result=$conexion->query($sqlCurves);
                      while($f = $result->fetch_object()){
                        ?>
                          '<?php echo $f->valor; ?>',
                        <?php
                        }
                        
                       ?>
                    ]
                }]
            },
        
            // Configuration options go here
            options: {
              title: {
                    display: true,
                    text: 'Curva de producción'
                  },
                  scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
            }
        });
      </script>
      </div>
    </div>
  </span><br>
    <div class="row">
      
      <div class="col-3">
        <span><h4>Caracteristicas</h4></span>
          <?php 
            //$sqlFEATURES="select item,medida,AVG(valor) AS valor from table_features $where GROUP BY item,medida";
            $sqlFEATURES="
              SELECT 'Producto' as item, producto as valor FROM varieties $where1
              union
              SELECT 'Color' as item,color FROM varieties $where1
              union
              select item,avg(valor) from table_features $where group by item
              UNION
              SELECT 'Ciclo' as item,ciclo FROM varieties $where1
              UNION
              SELECT 'Calificación' as item,avg(valor)/3*100 from table_evaluations $where group by variedad
              union
              select '% De Fusarium' as item, avg(dato/plantas) as valor from fusarium $where group by variedad
              ";
  
            $result=$conexion->query($sqlFEATURES);
            ?>
            <table class="table table-bordered">
              <thead><tr><th>Item</th><th>Valor</th></tr></thead>
              <tbody>
            <?php 
              while ($fila=$result->fetch_object()) {
                //utf8_decode(strtolower()) para convertir cadena a minusculas
                  ?>
                      <tr><td>
                        <?php echo $fila->item; ?>
                      </td><td>
                        <?php 
                          if ($fila->item=='Calificación' or $fila->item=='% De Fusarium')
                          {
                            echo number_format($fila->valor,2,'.','').'%';
                          }else 
                          {
                            echo "$fila->valor";
                          }
                          ?>
                      </td></tr>
    
              <?php
              }
          ?>
            </tbody>
         </table>
      </div>
      <div class="col-9">
        <span class="center"><h4>Comentarios</h1></span>
        <?php 
          $sqlCOMMENTS="select usuario,post,comentario from table_comments $where2 and comentario<>'' and usuario!='Floreros'";
          $result=$conexion->query($sqlCOMMENTS);
          
          ?>
          <table class="table table-bordered">
            <thead><tr><th>Usuario</th><th>Acerca de</th><th>Comentario</th></tr></thead>
            <tbody>
          <?php 
          
            while ($fila=$result->fetch_object()) {
              //utf8_decode(strtolower()) para convertir cadena a minusculas
                ?>
                    <tr><td>
                      <?php echo $fila->usuario; ?>
                    </td><td>
                      <?php echo $fila->post; ?>
                    </td><td>
                      <?php echo $fila->comentario; ?>
                    </td></tr>
  
            <?php
            }
        ?>
          </tbody>
       </table> 
             
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <span class="center"><h4>Comentarios Floreros</h1></span>
        <?php 
          $sqlCOMMENTSF="select usuario,post,comentario from table_comments $where2 and comentario<>'' and usuario='Floreros'";
          $result=$conexion->query($sqlCOMMENTSF);
          ?>
          <table class="table table-bordered">
            <thead><tr><th>Usuario</th><th>Acerca de</th><th>Comentario</th></tr></thead>
            <tbody>
          <?php 
            while ($fila=$result->fetch_object()) {
              //utf8_decode(strtolower()) para convertir cadena a minusculas
                ?>
                    <tr><td>
                      <?php echo $fila->usuario; ?>
                    </td><td>
                      <?php echo $fila->post; ?>
                    </td><td>
                      <?php echo $fila->comentario; ?>
                    </td></tr>
  
            <?php
            }
        ?>
          </tbody>
       </table> 
      </div>
    </div>
  </div><!--cerrar el container general por variedad-->
    <div class="saltoDePagina d-print-block" id="saltoDePagina "></div>
<?php
 } 
?>
    
         
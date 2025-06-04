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

//inicializa variable vacia
$where="";
$fi=Carbon::now();
$ff=Carbon::now();
$fini=$fi->subWeek(1)->startOfWeek()->format('Y-m-d');
$ffin=$ff->subWeek(1)->endOfWeek()->format('Y-m-d');

if(isset($_POST['consultar'])){
  if (!Empty($_POST['xfinca'])){
    $fini=$_POST['fini'];
    $ffin=$_POST['ffin'];
  }
  else{
    $fini=$_POST['fini'];
    $ffin=$_POST['ffin'];
    $where="";
  }
}

?>
<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Graficos</a>
    <a class="nav-item nav-link" id="nav-graph-tab" data-toggle="tab" href="#nav-graph" role="tab" aria-controls="nav-graph" aria-selected="true">Graficos Teo Vs Real</a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Tabla</a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Comentarios por Variedad</a>
    <a class="nav-item nav-link" id="nav-comments-tab" data-toggle="tab" href="#nav-comments" role="tab" aria-controls="nav-comments" aria-selected="false">Comentarios</a>
    <form class="form-inline" action="home.php?menu=tables&report=51" method="post" enctype="multipart/form-data">
      <div class="form-row align-items-center">
        <div class="col-sm my-1">
          <label class="sr-only" >Fecha Inicial</label>
          <input type="date" name="fini" value="<?php echo $fini; ?>" class="form-control" id="inlineFormInputName" >
        </div>
        <div class="col-sm my-1">
          <label class="sr-only" >Fecha Final</label>
          <div class="input-group">
            <input type="date" name="ffin" value="<?php echo $ffin; ?>" class="form-control" id="inlineFormInputGroupUsername" >
          </div>
        </div>
        <div class="col-auto my-1">
          <button type="submit" name="consultar" class="btn btn-primary">Consultar</button>
        </div>
      </div>
    </form>
  </div>
</nav>

<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
    <div class="row">
      <div class="col-12" style="box-shadow: 0px 0px 1.5px #666; height:300px; padding:4px;">
        <?php
        $sql="
        SELECT '2.RECIBIDO' AS tipo,sum(valor) as cant 
          from labors_sowing WHERE tipo IN('RECIBIDO') AND fecha between '".$fini."' and '".$ffin."'  group by 1 
         UNION
         SELECT '3.ENSARTE' AS tipo,sum(valor) as cant 
          from labors_sowing WHERE tipo IN('ENSARTE') AND fecha between '".$fini."' and '".$ffin."'  group by 1 
         UNION
         SELECT '4.COSECHA' AS tipo,sum(valor) as cant 
          from labors_sowing WHERE tipo IN('COSECHA') AND fecha between '".$fini."' and '".$ffin."'  group by 1 
         UNION
         SELECT '5.SIEMBRA' as tipo, sum(plantas) FROM hplane WHERE fecha_siembra between '".$fini."'  and '".$ffin."' 
          UNION
           SELECT '1.PRESUPUESTO' as tipo, sum(plantas) FROM program where fecha_siembra between '".$fini."'  and '".$ffin."' 
        ORDER BY tipo ASC
           ";

        $result=$conexion->query($sql);
?> 
      <div><h1>Informes de Propagación</h1></div>
      <div><h2>Entre <?php echo $fini; ?> y <?php echo $ffin; ?></h></div>
      <div class = "card"> 
          <div class="card-title"><h3>Gráficos</h3></div>
            <div class = "card-body" style="width:60%;">
              <canvas id="myChart" width="500" height="350"> </canvas>
            </div>
       </div> 
        <script>
        //grafico de tallos vendidos--------------------------------------------
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'bar',
            // The data for our dataset
            data: {
                labels: [
                  <?php
                  while($f = $result->fetch_object()){
                    ?>
                      '<?php echo $f->tipo; ?>',
                    <?php
                    }
                   ?>
               ],
                datasets: [{
                    label: ['Esquejes'],
                    backgroundColor: [
                    'rgba(0, 255, 0, 0.2)',
                    'rgba(0, 100, 255, 0.2)',
                    'rgba(255, 0, 0, 0.2)',
                    'rgba(100, 60, 200, 0.2)'],
                    borderColor: 'rgba(0, 99, 132, 1)',
                    data: [
                      <?php
                      //labels para el grafico
                      $result=$conexion->query($sql);
                      while($f = $result->fetch_object()){
                        ?>
                          '<?php echo $f->cant; ?>',
                        <?php
                        }
                       ?>
                    ]
                }]
            },

            // Configuration options go here
            options: 
            {
              legend: {
                display: false
              },
              title: {
                    display: true,
                    text: 'Comparativo Pto vs Ensarte vs Cosecha vs Siembra'
                  },
                  scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        display: true
                    }
                }]
              }
            }
        });
        </script>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
    <h1>Resumen</h1>
    <?php   
      $queryResumen="
      SELECT year(fecha) as año,week(fecha) as sem,
      sum(case tipo when 'ENSARTE' then valor else 0 end) as valEnsarte,
      sum(case tipo when 'TEO_ENSARTE' then valor else 0 end) as teoEnsarte,
      sum(case tipo when 'COSECHA' then valor else 0 end) as valCosecha,
      sum(case tipo when 'TEO_COSECHA' then valor else 0 end) as teoCosecha,                
      sum(case tipo when 'SIEMBRA' then valor else 0 end) as valSiembra,
      sum(case tipo when 'TEO_SIEMBRA' then valor else 0 end) as teoSiembra 
      FROM viewSowing 
      WHERE fecha between '".$fini."'  and '".$ffin."'
      ";
      $resultResumen=$conexion->query($queryResumen);

      if($resultResumen->num_rows>0){
        ?>
        <div class="row">
        <div class="col">
        <table class="table table-responsive table-sm">
          <thead>
            <tr>
              <th>teoEnsarte</th><th>realEnsarte</th>
              <th>teoCosecha</th><th>realCosecha</th>               
              <th>teoSiembra</th><th>realSiembra</th>
            </tr>
          </thead>
          <tbody>
        <?php
        while ($row=$resultResumen->fetch_object()) {
          ?>
            <tr>
              <td><?php echo number_format($row->teoEnsarte,0,',','.'); ?></td>
              <td><?php echo number_format($row->valEnsarte,0,',','.'); ?></td>
              <td><?php echo number_format($row->teoCosecha,0,',','.'); ?></td>
              <td><?php echo number_format($row->valCosecha,0,',','.'); ?></td>
              <td><?php echo number_format($row->teoSiembra,0,',','.'); ?></td>
              <td><?php echo number_format($row->valSiembra,0,',','.'); ?></td>
            </tr>
          <?php
        }
        
        ?>
        </tbody></table>
        </div>
        </div>
        <?php
      }else{
        echo "<h1>No hay resultados para mostrar</h1>";
      }
           /////////////////////////////////////////////////////////////////////
     ?>
         <h1>Resumen por Temporadas</h1>
      <?php 
        
        $query="
        SELECT year(fecha) as año,week(fecha) as sem,temporada,
        sum(case tipo when 'ENSARTE' then valor else 0 end) as valEnsarte,
        sum(case tipo when 'TEO_ENSARTE' then valor else 0 end) as teoEnsarte,
        sum(case tipo when 'COSECHA' then valor else 0 end) as valCosecha,
        sum(case tipo when 'TEO_COSECHA' then valor else 0 end) as teoCosecha,                
        sum(case tipo when 'SIEMBRA' then valor else 0 end) as valSiembra,
        sum(case tipo when 'TEO_SIEMBRA' then valor else 0 end) as teoSiembra 
        FROM viewSowing 
        WHERE fecha between '".$fini."'  and '".$ffin."'
        group by temporada
        order by fecha_pico asc
        ";
        $result=$conexion->query($query);

        if($result->num_rows>0){
          ?>
          <div class="row">
          <div class="col">
          <table class="table table-responsive table-sm">
            <thead>
              <tr>
                <th>Temporada</th>
                <th>teoEnsarte</th><th>realEnsarte</th>
                <th>teoCosecha</th><th>realCosecha</th>               
                <th>teoSiembra</th><th>realSiembra</th>
              </tr>
            </thead>
            <tbody>
          <?php
          while ($row=$result->fetch_object()) {
            ?>
              <tr>
                <td><?php echo $row->temporada; ?></td>
                <td><?php echo number_format($row->teoEnsarte,0,',','.'); ?></td>
                <td><?php echo number_format($row->valEnsarte,0,',','.'); ?></td>
                <td><?php echo number_format($row->teoCosecha,0,',','.'); ?></td>
                <td><?php echo number_format($row->valCosecha,0,',','.'); ?></td>
                <td><?php echo number_format($row->teoSiembra,0,',','.'); ?></td>
                <td><?php echo number_format($row->valSiembra,0,',','.'); ?></td>
              </tr>
            <?php
          }
          
          ?>
          </tbody></table>
          </div></div>
          <?php
        }else{
          echo "<h1>No hay resultados para mostrar</h1>";
        }
        
       ?>
  </div>
<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
   <div class="row">
      <div class="col-12">
      <h1>Comentarios por variedad</h1>
          <?php
          $sql="select variedad,group_concat(distinct(comentario)) as comentarios,count(comentario) as cuenta
          from labors_sowing where comentario<>'' and fecha between '".$fini."' and '".$ffin."' group by 1 order by (count(comentario)) desc";
          $result=$conexion->query($sql);
          
          while ($fila=$result->fetch_object()) {
            ?>
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">
                <?php
                echo $fila->variedad;
                ?>
                </h5><h6 class="card-subtitle mb-2 text-muted">
                <?php
                echo "Tiene ".$fila->cuenta." comentarios, resumidos en:";
                ?>
                </h6><p class="card-text">
                <?php
                echo $fila->comentarios;
                ?>
              </p></div></div>
            <?php
          }
            ?>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="nav-comments" role="tabpanel" aria-labelledby="nav-comments-tab">
   <div class="row">
      <div class="col-12">
        <h1>Comentarios</h1>
          <?php
          $sql="select comentario,group_concat(distinct(variedad)) as variedades,count(variedad) as cuenta
          from labors_sowing where comentario<>'' and fecha between '".$fini."' and '".$ffin."' group by 1 order by (count(comentario)) desc";
          $result=$conexion->query($sql);
          while ($fila=$result->fetch_object()) {
            ?>
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">
                <?php
                echo $fila->comentario;
                ?>
                </h5><h6 class="card-subtitle mb-2 text-muted">
                <?php
                echo "Tiene ".$fila->cuenta." variedades, resumidas en:";
                ?>
                </h6><p class="card-text">
                <?php
                echo $fila->variedades;
                ?>
              </p></div></div>
            <?php
          }
            ?>
      </div>
    </div>
  </div>

  <div class="tab-pane fade" id="nav-graph" role="tabpanel" aria-labelledby="nav-graph-tab">
   <div class="row">
      <div class="col-12">
        <h1>Teorico vs Real Siembras por finca</h1>
          <?php
          $sqlReport="SELECT producto,'TEO_SIEMBRA' AS tipo,sum(plantas) as plantas FROM programf 
          WHERE plantas>0 AND fecha_siembra between '".$fini."' and '".$ffin."'
          AND finca='INVERPALMAS'
          GROUP by producto
          UNION
          SELECT v.producto,'SIEMBRA' as tipo,sum(h.plantas) as plantas FROM hplane as h
          LEFT JOIN varieties as v
          ON v.nombre=h.variedad
          WHERE v.producto IN('CLAVEL','MINICLAVEL') AND h.fecha_siembra between '".$fini."' and '".$ffin."'
          AND h.finca='INVERPALMAS'
          GROUP BY v.producto
          ";

          $sqlReport2="SELECT producto,'TEO_SIEMBRA' AS tipo,sum(plantas) as plantas FROM programf 
          WHERE plantas>0 AND fecha_siembra between '".$fini."' and '".$ffin."'
          AND finca='PALERMO'
          GROUP by producto
          UNION
          SELECT v.producto,'SIEMBRA' as tipo,sum(h.plantas) as plantas FROM hplane as h
          LEFT JOIN varieties as v
          ON v.nombre=h.variedad
          WHERE v.producto IN('CLAVEL','MINICLAVEL') AND h.fecha_siembra between '".$fini."' and '".$ffin."'
          AND h.finca='PALERMO'
          GROUP BY v.producto
          ";

          ?>
      <div class = "card"> 
          <div class="card-title">INVERPALMAS</div>
            <div class = "card-body" style="width:60%;">
              <canvas id="graphReport" width="500" height="350"> </canvas>
              
          </div>
       </div> 
       <div class = "card"> 
          <div class="card-title">PALERMO</div>
            <div class = "card-body" style="width:60%;">
              
              <canvas id="graphReport2" width="500" height="350"> </canvas>
          </div>
       </div> 
          <script>
                //grafico finca palmas --------------------------------------------
                var ctx = document.getElementById('graphReport').getContext('2d');
                var chart = new Chart(ctx, 
                {
                    // The type of chart we want to create
                    type: 'bar',
                    // The data for our dataset
                    data: 
                    {
                      labels: 
                      [
                        <?php
                        $resultReport=$conexion->query($sqlReport);

                        while ($fReport=$resultReport->fetch_object()) 
                          {
                            ?>
                              '<?php echo $fReport->producto; ?>',
                            <?php
                          
                          }
                        ?>

                      ],
                        datasets: 
                        [
                          {
                            label: ['Presup'],
                            backgroundColor: [
                            'rgba(69, 243, 16, 0.5)',
                            'rgba(69, 243, 16, 0.5)'],
                            borderColor: 'rgba(0, 99, 132, 1)',
                            data: 
                            [
                              <?php
                              //labels para el grafico
                              $resultReport=$conexion->query($sqlReport);
                              while ($fReport=$resultReport->fetch_object())
                              {
                              if($fReport->tipo=='TEO_SIEMBRA')
                              {
                              ?>
                                  '<?php echo $fReport->plantas; ?>',
                              <?php
                              }
                              }
                              ?>
                            ]
                          },
                          {
                            label: ['Real'],
                            backgroundColor: [
                              'rgba(16, 69, 243, 0.5)',
                            'rgba(16, 69, 243, 0.5)'],
                            borderColor: 'rgba(0, 99, 132, 1)',
                            data: 
                            [
                              <?php
                              //labels para el grafico
                              $resultReport=$conexion->query($sqlReport);
                              while ($fReport=$resultReport->fetch_object())
                              {
                              if($fReport->tipo=='SIEMBRA')
                              {
                              ?>
                                  '<?php echo $fReport->plantas; ?>',
                              <?php
                              }
                              }
                              ?>
                            ]
                          }
                        ]
                    },
                    // Configuration options go here
                    options: 
                    {
                      legend: 
                      {
                        display: true
                      },
                      title: 
                      {
                        display: true,
                        text: 'Teorico Vs Real por flor'
                      },
                      scales: 
                      {
                        yAxes: 
                        [{
                            ticks: 
                            {
                                beginAtZero: true,
                                display: false
                            }
                        }]
                      }
                    }//cierra options
                  });
                  //finca palermo
                var ctx = document.getElementById('graphReport2').getContext('2d');
                var chart = new Chart(ctx, 
                {
                    // The type of chart we want to create
                    type: 'bar',
                    // The data for our dataset
                    data: 
                    {
                      labels: 
                      [
                        <?php
                        $resultReport2=$conexion->query($sqlReport2);

                        while ($fReport2=$resultReport2->fetch_object()) 
                          {
                            ?>
                              '<?php echo $fReport2->producto; ?>',
                            <?php
                          }
                        ?>

                      ],
                        datasets: 
                        [
                          {
                            label: ['Presup'],
                            backgroundColor: [
                            'rgba(69, 243, 16, 0.5)',
                            'rgba(69, 243, 16, 0.5)'],
                            borderColor: 'rgba(0, 99, 132, 1)',
                            data: 
                            [
                              <?php
                              //labels para el grafico
                              $resultReport2=$conexion->query($sqlReport2);
                              while ($fReport2=$resultReport2->fetch_object())
                              {
                              if($fReport2->tipo=='TEO_SIEMBRA')
                              {
                              ?>
                                  '<?php echo $fReport2->plantas; ?>',
                              <?php
                              }
                              }
                              ?>
                            ]
                          },
                          {
                            label: ['Real'],
                            backgroundColor: [
                              'rgba(16, 69, 243, 0.5)',
                            'rgba(16, 69, 243, 0.5)'],
                            borderColor: 'rgba(0, 99, 132, 1)',
                            data: 
                            [
                              <?php
                              //labels para el grafico
                              $resultReport2=$conexion->query($sqlReport2);
                              while ($fReport2=$resultReport2->fetch_object())
                              {
                              if($fReport2->tipo=='SIEMBRA')
                              {
                              ?>
                                  '<?php echo $fReport2->plantas; ?>',
                              <?php
                              }
                              }
                              ?>
                            ]
                          }
                        ]
                    },
                    // Configuration options go here
                    options: 
                    {
                      legend: 
                      {
                        display: true
                      },
                      title: 
                      {
                        display: true,
                        text: 'Teorico Vs Real por flor'
                      },
                      scales: 
                      {
                        yAxes: 
                        [{
                            ticks: 
                            {
                                beginAtZero: true,
                                display: false
                            }
                        }]
                      }
                    }//cierra options
                  });  
              </script>
      </div>
    </div>
  </div>

</div>
<?php
include ('funciones/conexion.php');
//variables filtrar datos
if(empty($_POST['xproducto'])){
  $producto="";
}else{
  $producto=$_POST['xproducto'];
}
//ahora $where
$where="";
$where1="";
//ano
$ano=date('Y');
//boton buscar
if(isset($_POST['search'])){
  if (!Empty($_POST['xproducto'])){
    $where1=" WHERE producto='".$producto."' ";
    $where=" WHERE producto='".$producto."' AND grado<>'i.GRA' AND grado<>'j.NAL' ";
  }
  else {
    $where="";
    $where1="";
  }
}
?>
  <div class="card">
    <div class="card-header">
      <div class="dropdown dropleft float-right">
        <button type="button" class="btn btn-primary dropdown-toggle " data-toggle="dropdown">
          Reports
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="index.php?menu=ex&inf=1">Exportable vs Exportado</a>
          <a class="dropdown-item" href="index.php?menu=ex&inf=2">Tallos Vendidos</a>
          <a class="dropdown-item" href="index.php?menu=ex&inf=3">Produccion vs Exportable vs Exportado</a>
        </div>
    </div>
<!--Filtros de acuerdo a la pagina--------------------------------------------------->
<?php if (empty($_GET['inf'])){ ?>
</div>
</div>
<?php }else if (($_GET['inf'])==1){ //si tiene el valor 1 traera el primer informe ?>
</div>
</div>
<?php }else if (($_GET['inf'])==2){ //si tiene el valor 1 traera el primer informe ?>
  <div class="dropdown dropleft float-left">
    <form class="form-inline" action="home.php?menu=ex&inf=2" method="post" enctype="multipart/form-data">
      <div class="form-group mx-sm-3 mb-2">
        <select name="xproducto" class="form-control" data-live-search="true">
          <option value="">Producto</option>
          <?php
          //consulta producto
          $p="SELECT producto FROM documentos GROUP BY producto";
          $resp=$conexion->query($p);
          while($rp = $resp->fetch_object()){
            echo "<option value='".$rp->producto."'>" .$rp->producto. "</option>";
          }
           ?>
           </select>
        </div>
        <button name="search" type="submit" class="btn btn-primary mb-2">Search</button>
    </form>
  </div>
</div>
</div>
<?php
}else if (($_GET['inf'])==3){ //si tiene el valor 1 traera el primer informe ?>
  <div class="dropdown dropleft float-left">
    <form class="form-inline" action="home.php?menu=ex&inf=3" method="post" enctype="multipart/form-data">
      <div class="form-group mx-sm-3 mb-2">
        <select name="xproducto" class="form-control" data-live-search="true">
          <option value="">Producto</option>
          <?php
          //consulta producto
          $p="SELECT DISTINCT producto FROM exportados";
          $resp=$conexion->query($p);
          while($rp = $resp->fetch_object()){
            echo "<option value='".$rp->producto."'>" .$rp->producto. "</option>";
          }
           ?>
           </select>
        </div>
        <button name="search" type="submit" class="btn btn-primary mb-2">Search</button>
    </form>
  </div>
</div>
</div>
<?php
}
//consulta principal
$sql="SELECT mes,producto,'a.produccion' as tipo,SUM(CASE WHEN ano=($ano-1) THEN produccion ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano)  THEN produccion ELSE 0 END) as year2019
FROM exportados $where1 GROUP BY 1,2,3
UNION SELECT mes,producto,'b.exportable' as tipo,SUM(CASE WHEN ano=($ano-1)  THEN exptble ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano) THEN exptble ELSE 0 END) AS year2019
FROM exportados $where GROUP BY 1,2,3
UNION SELECT mes,producto,'c.exportado' as tipo,SUM(CASE WHEN ano=($ano-1)  THEN exptdo ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano) THEN exptdo ELSE 0 END) AS year2019
FROM exportados $where GROUP BY 1,2,3 ORDER BY 1,2,3 ASC";

//consulta para grafico
$sqlgi3="SELECT producto,'a.produccion' as tipo,SUM(CASE WHEN ano=($ano-1)  THEN produccion ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano)  THEN produccion ELSE 0 END) as year2019
FROM exportados $where1 AND mes<month(now())  GROUP BY 1,2
UNION SELECT producto,'b.exportable' as tipo,SUM(CASE WHEN ano=($ano-1)   THEN exptble ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano) THEN exptble ELSE 0 END) AS year2019
FROM exportados $where and mes<month(now()) GROUP BY 1,2
UNION SELECT producto,'c.exportado' as tipo,SUM(CASE WHEN ano=($ano-1)   THEN exptdo ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano) THEN exptdo ELSE 0 END) AS year2019
FROM exportados $where and mes<month(now()) GROUP BY 1,2 ORDER BY 1,2 ASC";

//consulta inf=1
$sqlinf1="SELECT producto,grado,ano,'Exportable' AS tipo, SUM(CASE WHEN mes=1 THEN exptble ELSE 0 END) AS 'ene',SUM(CASE WHEN mes=2 then exptble ELSE 0 END) AS 'feb',
SUM(CASE WHEN mes=3 then exptble ELSE 0 END) AS 'mar',SUM(CASE WHEN mes=4 then exptble ELSE 0 END) AS 'abr',SUM(CASE WHEN mes=5 then exptble ELSE 0 END) AS 'may',
SUM(CASE WHEN mes=6 then exptble ELSE 0 END) AS 'jun',SUM(CASE WHEN mes=7 then exptble ELSE 0 END) AS 'jul',SUM(CASE WHEN mes=8 then exptble ELSE 0 END) AS 'ago',
SUM(CASE WHEN mes=9 then exptble ELSE 0 END) AS 'sep',SUM(CASE WHEN mes=10 then exptble ELSE 0 END) AS 'oct',SUM(CASE WHEN mes=11 then exptble ELSE 0 END) AS 'nov',
SUM(CASE WHEN mes=12 then exptble ELSE 0 END) AS 'dic',sum(exptble) as total FROM exportados GROUP BY 1,3,4 UNION
SELECT producto,grado,ano,'Exportado' AS tipo, SUM(CASE WHEN mes=1 THEN exptdo ELSE 0 END) AS 'ene',SUM(CASE WHEN mes=2 then exptdo ELSE 0 END) AS 'feb',
SUM(CASE WHEN mes=3 then exptdo ELSE 0 END) AS 'mar',SUM(CASE WHEN mes=4 then exptdo ELSE 0 END) AS 'abr',SUM(CASE WHEN mes=5 then exptdo ELSE 0 END) AS 'may',
SUM(CASE WHEN mes=6 then exptdo ELSE 0 END) AS 'jun',SUM(CASE WHEN mes=7 then exptdo ELSE 0 END) AS 'jul',SUM(CASE WHEN mes=8 then exptdo ELSE 0 END) AS 'ago',
SUM(CASE WHEN mes=9 then exptdo ELSE 0 END) AS 'sep',SUM(CASE WHEN mes=10 then exptdo ELSE 0 END) AS 'oct',SUM(CASE WHEN mes=11 then exptdo ELSE 0 END) AS 'nov',
SUM(CASE WHEN mes=12 then exptdo ELSE 0 END) AS 'dic',sum(exptdo) as total FROM exportados WHERE grado<>'j.NAL' GROUP BY 1,3,4 ORDER BY 1,3,4";

//consulta inf=2 y para graficos
$sqlinf2="SELECT mes,producto,grado,SUM(CASE WHEN ano=($ano-1) THEN exptdo ELSE 0 END) AS year2018,SUM(CASE WHEN ano=($ano) THEN exptdo ELSE 0 END) AS year2019 FROM exportados $where GROUP BY 1,2 ORDER BY 1,2 ASC";
//consulta para garfico 2
$sqlinfg="SELECT producto,grado,SUM(CASE WHEN ano=($ano-1) THEN exptdo/(SELECT SUM(exptdo) FROM exportados WHERE ano=($ano-1)) ELSE 0 END) AS year2018,
SUM(CASE WHEN ano=$ano THEN exptdo/(SELECT SUM(exptdo) FROM exportados WHERE ano=$ano) ELSE 0 END) AS year2019
FROM exportados $where GROUP BY 1,2";

//consulta para informe mensual
$sqlim="SELECT ano,mes,producto,case when grado is null then 'Total Mes' else grado end as grado,sum(produccion) as produccion,sum(zusa) as zusa,sum(zeuro) as zeuro,
sum(case when grado='NAL' then 0 else zotras end) as zotras,sum(zusa+zeuro+zotras) as total,
sum(bajas_grado) as bajas_grado,sum(bajas_nacional) as bajas_nacional,sum(desechos) as desechos,sum(obsequios) as obsequios,
sum(compras) as compras,(zusa+zeuro+zotras+bajas_grado+bajas_nacional+desechos+obsequios-compras) as total2,
sum(inicial) as inventariofinal
from exportados
group by 1,2,3,4
with rollup";

if (empty($_GET['inf'])){
//si esta vacia la variable mostrara la tabla principal
?>
<div class="table-bordered"><table class="table table-striped table-sm"> <!--div de tablas y tabla-->
<?php
 //hacer Query
 $res = $conexion->query($sqlim);
 //realizar tablas
 if ($res->num_rows>0){
   ?>
 <!--Encabezado tabla-->
      <tr><th>ANO</th><th>MES</th><th>PRODUCTO</th>
        <th>GRADO</th><th>PRODUCCION</th><th>ZONA USA</th><th>ZONA EURO</th><th>OTRAS ZONAS</th><th>TOTAL</th>
        <th>BAJAS DE GRADO</th><th>BAJAS A NACIONAL</th><th>DESECHOS</th><th>OBSEQUIOS</th><th>COMPRAS</th><th>TOTAL</th><th>INVENTARIO FINAL</th></tr>

 <?php
    while($f = $res->fetch_object()){
      echo "<tr>";
      echo "<td>" .$f->ano. "</td><td>" .$f->mes. "</td><td>" .$f->producto. "</td>";
      echo "<td>" .$f->grado. "</td><td>" .number_format($f->produccion,0,'','.'). "</td><td>" .number_format($f->zusa,0,'','.'). "</td>";
      echo "<td>" .number_format($f->zeuro,0,'','.'). "</td><td>" .number_format($f->zotras,0,'','.'). "</td><td>" .number_format($f->total,0,'','.'). "</td>";
      echo "<td>" .number_format($f->bajas_grado,0,'','.'). "</td><td>" .number_format($f->bajas_nacional,0,'','.'). "</td><td>" .number_format($f->desechos,0,'','.'). "</td>";
      echo "<td>" .number_format($f->obsequios,0,'','.'). "</td><td>" .number_format($f->compras,0,'','.'). "</td><td>" .number_format($f->total2,0,'','.'). "</td><td>" .number_format($f->inventariofinal,0,'','.'). "</td>";
      echo "</tr>";
    }
 }
 else {
    echo "0 results";
 }
echo "</table></div>";
////////////////////////////////////////////////////////////////////////////
}else if (($_GET['inf'])==1){ //si tiene el valor 1 traera el primer informe
?>
 <div class="table-bordered"><table class="table table-striped table-sm"> <!--div de tablas y tabla-->
<?php
  //hacer Query
  $res = $conexion->query($sqlinf1);
  //realizar tablas
  if ($res->num_rows>0){
    ?>
  <!--Encabezado tabla-->
       <tr><th>producto</th><th>ano</th><th>tipo</th>
         <th>ene</th><th>feb</th><th>mar</th><th>abr</th><th>may</th><th>jun</th>
         <th>jul</th><th>ago</th><th>sep</th><th>oct</th><th>nov</th><th>dic</th><th>total</th></tr>

  <?php
     while($f = $res->fetch_object()){
       echo "<tr>";
       echo "<td>" .$f->producto. "</td><td>" .$f->ano. "</td><td>" .$f->tipo. "</td>";
       echo "<td>" .number_format($f->ene,0,'','.'). "</td><td>" .number_format($f->feb,0,'','.'). "</td><td>" .number_format($f->mar,0,'','.'). "</td>";
       echo "<td>" .number_format($f->abr,0,'','.'). "</td><td>" .number_format($f->may,0,'','.'). "</td><td>" .number_format($f->jun,0,'','.'). "</td>";
       echo "<td>" .number_format($f->jul,0,'','.'). "</td><td>" .number_format($f->ago,0,'','.'). "</td><td>" .number_format($f->sep,0,'','.'). "</td>";
       echo "<td>" .number_format($f->oct,0,'','.'). "</td><td>" .number_format($f->nov,0,'','.'). "</td><td>" .number_format($f->dic,0,'','.'). "</td><td>" .number_format($f->total,0,'','.'). "</td>";
       echo "</tr>";
     }
  }
  else {
     echo "0 results";
  }
echo "</table></div>";
 $conexion->close();
}else if (($_GET['inf'])==2){ //si tiene el valor 2 traera el primer informe
?>
<!--Grilla para tabla y grafico-->
<div class="row">
  <div class="col-sm-6">
     <div class="table-bordered"><table class="table"> <!--div de tablas y tabla-->
<?php
       //hacer Query
       $res = $conexion->query($sqlinf2);
       //realizar tablas
       if ($res->num_rows>0){
         ?>
       <!--Encabezado tabla-->
            <tr><th>producto</th><th>mes</th><th><?php echo $ano-1; ?></th><th><?php echo $ano; ?></th></tr>

       <?php
          while($f = $res->fetch_object()){
            echo "<tr>";
            echo "<td>" .$f->producto. "</td><td>" .$f->mes. "</td>";
            echo "<td>" .number_format($f->year2018,0,'','.'). "</td><td>" .number_format($f->year2019,0,'','.'). "</td>";
            echo "</tr>";
          }
       }
       else {
          echo "0 results";
       }
?>
  </table></div>
  </div>
  <div class="col-sm-6">
  <div class="card card-body">
    <canvas id="myChart">
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
                //labels para el grafico
                $res = $conexion->query($sqlinf2);
                while($f = $res->fetch_object()){
                  ?>
                    '<?php echo $f->mes; ?>',
                  <?php
                  }
                 ?>
             ],
              datasets: [{
                  label: '<?php echo $ano-1; ?>',//Ano anterior
                  backgroundColor: 'rgba(0, 255, 0, 0.4)',
                  borderColor: 'rgba(0, 99, 132, 1)',
                  data: [
                    <?php
                    //labels para el grafico
                    $res = $conexion->query($sqlinf2);
                    while($f = $res->fetch_object()){
                      ?>
                        '<?php echo $f->year2018; ?>',
                      <?php
                      }
                     ?>
                  ]
              },
              {
                  label: '<?php echo $ano; ?>',//ano actual
                  backgroundColor: 'rgba(0, 255, 255, 0.4)',
                  borderColor: 'rgba(0, 99, 132, 1)',
                  data: [
                    <?php
                    //labels para el grafico
                    $res = $conexion->query($sqlinf2);
                    while($f = $res->fetch_object()){
                      ?>
                        '<?php echo $f->year2019; ?>',
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
                  text: 'Tallos Vendidos <?php echo $ano-1; ?> vs <?php echo $ano; ?>'
                }
          }
      });
      </script>
    </canvas>
  </div>
  <br>
  <!---Div segundo grafico----------------------------------------------------->
  <div class="card card-body">
    <canvas id="myChart2">
      <script>
      //grafico de tallos vendidos
      var ctx = document.getElementById('myChart2').getContext('2d');
      var chart = new Chart(ctx, {
          // The type of chart we want to create
          type: 'bar',

          // The data for our dataset
          data: {
              labels: [
                <?php
                //labels para el grafico
                $res = $conexion->query($sqlinfg);
                while($f = $res->fetch_object()){
                  ?>
                    '<?php echo $f->grado; ?>',
                  <?php
                  }
                 ?>
             ],
              datasets: [{
                  label: '<?php echo $ano-1; ?>',
                  backgroundColor: 'rgba(0, 255, 0, 0.4)',
                  borderColor: 'rgba(0, 99, 132, 1)',
                  data: [
                    <?php
                    //labels para el grafico
                    $res = $conexion->query($sqlinfg);
                    while($f = $res->fetch_object()){
                      ?>
                        '<?php echo $f->year2018; ?>',
                      <?php
                      }
                     ?>
                  ]
              },
              {
                  label: '<?php echo $ano; ?>',
                  backgroundColor: 'rgba(0, 255, 255, 0.4)',
                  borderColor: 'rgba(0, 99, 132, 1)',
                  data: [
                    <?php
                    //labels para el grafico
                    $res = $conexion->query($sqlinfg);
                    while($f = $res->fetch_object()){
                      ?>
                        '<?php echo $f->year2019; ?>',
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
                  text: '<?php echo "Tallos Vendidos Por Calidades " . ($ano-1) . " vs " .$ano; ?>'
                }
          }
      });
      </script>
    </canvas>
  </div>
  </div>
</div>
<?php
 $conexion->close();
}else if (($_GET['inf'])==3){ //si tiene el valor 2 traera el primer informe?>
  <div class="row">
    <div class="col-sm-5">
<div class="table-bordered"><table class="table">
<?php
//hacer Query
$res = $conexion->query($sql);
//realizar tablas
if ($res->num_rows>0){
  ?>
<!--Encabezado tabla-->
     <tr><th>mes</th><th>producto</th><th>tipo</th><th><?php echo $ano-1; ?></th><th><?php echo $ano; ?></th></tr>

<?php
   while($f = $res->fetch_object()){
     echo "<tr><td>" .$f->mes. "</td><td>" .$f->producto. "</td><td>" .$f->tipo. "</td><td>" .number_format($f->year2018,0,'','.'). "</td><td>" .number_format($f->year2019,0,'','.'). "</td></tr>";
   }
}
else {
   echo "0 results";
}
echo "</table></div>";
 ?>
</div>
<div class="col-sm-7">
Espacio para grafico
</div>
</div>
<?php
   $conexion->close();
}
else {
  echo "no";
}
?>

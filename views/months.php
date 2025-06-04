<?php

//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
    require "vendor/autoload.php";
  }else{
    include ("../funciones/conexion.php");
    require "../vendor/autoload.php";
}

?>

<h4>PDF</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_ini">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_fin">
              <br>
              <input id="listar" type="submit" onclick="listar_reports()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <div class="row" id="nuevo">
                <div class="col-sm-4">            
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal"><i class="material-icons">library_add</i></button>    
                </div>    
            </div> 
            <div class="row">
                                <!-- PDF Viewer -->
                <iframe src="PDF" frameborder="0" style="width:100%; height:500px;"></iframe>

                <!-- Comment Form -->
                <form>
                <textarea id="comment"></textarea>
                <button id="submit-comment">Submit</button>
                </form>

                <!-- Comment List -->
                <ul id="comment-list">
                <!-- Comment items will be added here -->
                </ul>
            </div>
        </div>  
        </div>
    </div>
</div>

<script>
    // Submit comment
$('#submit-comment').click(function() {
    var comment = $('#comment').val();
    var file_id = <?php echo $file_id; ?>;
    var user_id = <?php echo $user_id; ?>;
  
    $.ajax({
      url: 'add-comment.php',
      type: 'post',
      data: {
        comment: comment,
        file_id: file_id,
        user_id: user_id
      },
      success: function(response) {
        $('#comment').val('');
        getComments();
      }
    });
  });
  
  // Get comments
  function getComments() {
    var file_id = <?php echo $file_id; ?>;
  
    $.ajax({
      url: 'get-comments.php',
      type: 'get',
      data: { file_id: file_id },
      success: function(response) {
        $('#comment-list').html(response);
      }
    });
  }
  
  // Initialize on page load
  $(document).ready(function() {
    getComments();
  });

</script>
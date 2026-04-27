$(document).ready(function(){
 ///////////////////CARGA COMBO FLOR////
 $.ajax({
    type: "POST",
    url: "ajax/fv_fetchProducts.php",
    success: function(response)
    {
        $('#flor').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

  $.ajax({
    type: "POST",
    url: "ajax/fetchFarms.php",
    success: function(response)
    {
        $('#finca').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

});

/////////////////CARGAR AL COMBO VARIEDADES/
function fetch_varieties(val){
  $.ajax({
      type: 'post',
      url: 'ajax/fetchVarieties.php',
      data: { get_option:val },
    success: function (response) {
      document.getElementById("variedad").innerHTML=response; 
    }
  });
}
///////////////////////////////////////////////
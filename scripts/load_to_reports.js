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

  $.ajax({
    type: "POST",
    url: "ajax/fetchAreas.php",
    success: function(response)
    {
        $('#area').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

  ////////////////////////////////////////////

  $.ajax({
    type: "POST",
    url: "ajax/fetchDataTypes.php",
    success: function(response)
    {
        $('#tipod').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

});
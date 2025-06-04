$(document).ready(function(){
 ///////////////////CARGA COMBO FLOR////
 $.ajax({
    type: "POST",
    url: "CRUD/fv_fetchProducts.php",
    success: function(response)
    {
        $('#flor').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

  $.ajax({
    type: "POST",
    url: "CRUD/fetchFarms.php",
    success: function(response)
    {
        $('#finca').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

  $.ajax({
    type: "POST",
    url: "CRUD/fetchAreas.php",
    success: function(response)
    {
        $('#area').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

  ////////////////////////////////////////////

  $.ajax({
    type: "POST",
    url: "CRUD/fetchDataTypes.php",
    success: function(response)
    {
        $('#tipod').html(response).fadeIn();
    }
  });
  ////////////////////////////////////////////

});
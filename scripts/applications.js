



function fetch_apply(val){
    $.ajax({
        type: 'post',
        url: 'CRUD/fetchApply.php',
        data: { get_option:val },
      success: function (response) {
        document.getElementById("aplicar").innerHTML=response; 
      }
    });
};


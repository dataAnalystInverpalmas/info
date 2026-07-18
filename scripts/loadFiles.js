$(document).ready(function(){
    $("#boton").on("click", function(){
        var miArchivo = $("#archivoId").prop('files')[0];
        if (!miArchivo) {
            alert("Seleccione un archivo antes de subir.");
            return;
        }
        var datosForm = new FormData;
        datosForm.append("archivoId", miArchivo);
        var destino = "dist/server.php";
        console.log(miArchivo);
        $.ajax({
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            data: datosForm,
            url: destino
        }).done(function(data){
            if(alert(data)){
            }else{
                if (miArchivo.name == "Plantas Sembradas ld5000.xlsx"){
                    window.location.href = "home.php?menu=tables&table=1";
                }else if(miArchivo.name == "tabla_variedades.xlsx"){
                    window.location.href = "home.php?menu=tables&table=2";
                }else if(miArchivo.name == "tabla_hplano.xlsx"){
                    window.location.href = "home.php?menu=tables&table=13";
                }else if(miArchivo.name == "tabla_arreglos.xlsx"){
                    window.location.href = "home.php?menu=tables&table=6";
                }else if(miArchivo.name == "tabla_presupuesto.xlsx"){
                    window.location.href = "home.php?menu=tables&table=3";
                }else if(miArchivo.name == "tabla_festivos.xlsx"){
                    window.location.href = "home.php?menu=tables&table=22";
                }
                else{
                    alert("Archivo subido con exito!")
                }
            }      //window.location.reload();
        }).fail(function(data){
            alert("Error")
        });
    })
})
// agregar registros
function addRecord() {

    // get values
    var fecha = $("#fecha").val();
    var finca = $("#finca").val();
    var producto = $("#producto").val();
    var tipo = $("#tipo").val();
    var indicador = $("#indicador").val();
    var revision = $("#revision").val();
    var valor = $("#valor").val();

    // agregar registros
    $.post("CRUD/addRecord.php", {
        fecha: fecha,
        finca: finca,
        producto: producto,
        tipo: tipo,
        indicador: indicador,
        revision: revision,
		valor: valor
    }, function (data, status) {
        // close the popup
        //$("#add_new_record_modal").modal("hide");

        // leer registros
        readRecords();

        // borrar campos
        $("#fecha").val("");
        $("#finca").val("");
        $("#producto").val("");
        $("#tipo").val("");
        $("#indicador").val("");
        $("#revision").val("");
        $("#valor").val("");
    });

}

function DeleteUser(id) {
    var conf = confirm("¿Está seguro, realmente desea eliminar el registro?");
    if (conf == true) {
        $.post("CRUD/deleteDetails.php", {
                id: id
            },
            function (data, status) {
                // reload Users by using readRecords();
                readRecords();
            }
        );
    }
}

function GetUserDetails(id) {
    // Add User ID to the hidden field for furture usage
    $("#hidden_user_id").val(id);
    $.post("CRUD/readDetails.php", {
            id: id
        },
        function (data, status) {
            // PARSE json data
            var registro = JSON.parse(data);
            // Assing existing values to the modal popup fields
            $("#update_fecha").val(registro.fecha);
            $("#update_finca").val(registro.finca);
            $("#update_producto").val(registro.producto);
            $("#update_tipo").val(registro.tipo);
            $("#update_indicador").val(registro.indicador);
            $("#update_revision").val(registro.revision);
            $("#update_valor").val(registro.valor);    
        }
        
    );
    
    
    // Open modal popup
    $("#update_user_modal").modal("show");
}

function UpdateUserDetails() {
    // get values
    var update_fecha = $("#update_fecha").val();
    var update_finca = $("#update_finca").val();
    var update_producto = $("#update_producto").val();
    var update_tipo = $("#update_tipo").val();
    var update_indicador = $("#update_indicador").val();
    var update_revision = $("#update_revision").val();
    var update_valor = $("#update_valor").val();

    // get hidden field value
    var id = $("#hidden_user_id").val();

    // Update the details by requesting to the server using ajax
    $.post("CRUD/updateRecord.php", {
            id: id,
            update_fecha: update_fecha,
            update_finca: update_finca,
            update_producto: update_producto,
            update_tipo: update_tipo,
            update_indicador: update_indicador,
            update_revision: update_revision,
            update_valor: update_valor
        },
        function (data, status) {
            // hide modal popup
            $("#update_user_modal").modal("hide");
            // reload Users by using readRecords();
            readRecords();
        }
    );

}

// Leer record
function readRecords() {
    $.get("CRUD/readRecord.php", {}, function (data, status) {
        $("#records_content").html(data);
    });
}

    $(document).ready(function(){
        readRecords(); // calling function
    });


    
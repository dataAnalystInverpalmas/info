function addRecord() {
    // get values
    var codigo = $("#codigo").val();
    var turno= $("#turno").val();
    var pregunta = $("#pregunta").val();
    var valor = $("#valor").val();

    // agregar registros
    $.post("covid/insert.php", {
        codigo: codigo,
        turno: turno,
        pregunta: pregunta,
		valor: valor
    }, function (data, status) {
        // close the popup
       //$("#add_new_record_modal").modal("hide");

        // leer registros
        //readRecords();

        // borrar campos
        $("#codigo").val("");
        $("#turno").val("");
        $("#pregunta").val("");
        $("#valor").val("");
    });
}
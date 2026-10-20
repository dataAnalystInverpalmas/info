/**
 * Modern LoadFiles Upload & Synchronization Controller
 * Inverpalmas
 */
$(document).ready(function () {
    var knownMatrices = {
        "Plantas Sembradas ld5000.xlsx": {
            tag: "Matriz de Siembras (Plane)",
            link: "../tables/plane.php",
            tableParam: "1"
        },
        "tabla_variedades.xlsx": {
            tag: "Matriz de Variedades",
            link: "../tables/varieties.php",
            tableParam: "2"
        },
        "tabla_hplano.xlsx": {
            tag: "Histórico de Planos",
            link: "../tables/hplane.php",
            tableParam: "13"
        },
        "tabla_arreglos.xlsx": {
            tag: "Matriz de Arreglos",
            link: "../tables/arreglos.php",
            tableParam: "6"
        },
        "tabla_presupuesto.xlsx": {
            tag: "Presupuesto de Siembras",
            link: "../tables/program.php",
            tableParam: "3"
        },
        "tabla_festivos.xlsx": {
            tag: "Matriz de Festivos",
            link: "../tables/festivos.php",
            tableParam: "22"
        }
    };

    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        var k = 1024;
        var sizes = ['B', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Handle file input change: Show preview
    $("#archivoId").on("change", function () {
        var file = this.files[0];
        var previewBox = document.getElementById("archivoPreviewBox");
        var previewName = document.getElementById("previewFileName");
        var previewSize = document.getElementById("previewFileSize");
        var previewTag = document.getElementById("previewFileTag");
        var alertArea = document.getElementById("uploadAlertArea");

        if (alertArea) alertArea.innerHTML = '';

        if (!file) {
            if (previewBox) previewBox.classList.add("d-none");
            return;
        }

        if (previewName) previewName.textContent = file.name;
        if (previewSize) previewSize.textContent = formatFileSize(file.size);

        if (previewTag) {
            var matrixInfo = knownMatrices[file.name];
            if (matrixInfo) {
                previewTag.textContent = "✓ " + matrixInfo.tag;
                previewTag.style.color = "#00796B";
                previewTag.style.borderColor = "#80CBC4";
                previewTag.style.backgroundColor = "#E6F4F1";
            } else {
                previewTag.textContent = "Archivo Excel";
                previewTag.style.color = "#475569";
                previewTag.style.borderColor = "#CBD5E1";
                previewTag.style.backgroundColor = "#F1F5F9";
            }
        }

        if (previewBox) previewBox.classList.remove("d-none");
    });

    // Handle Upload Button Click
    $("#boton").on("click", function () {
        var btn = this;
        var miArchivo = $("#archivoId").prop('files')[0];
        var alertArea = document.getElementById("uploadAlertArea");

        if (alertArea) alertArea.innerHTML = '';

        if (!miArchivo) {
            if (alertArea) {
                alertArea.innerHTML = '<div class="alert alert-warning py-2 px-3 small d-flex align-items-center mb-0" style="border-radius:8px;">' +
                    '<span class="material-icons mr-2" style="font-size:1.1rem;">warning</span>' +
                    '<span>Por favor seleccione un archivo Excel antes de continuar.</span>' +
                    '</div>';
            } else {
                alert("Seleccione un archivo antes de subir.");
            }
            return;
        }

        var datosForm = new FormData();
        datosForm.append("archivoId", miArchivo);
        var destino = "dist/server.php";

        // Button uploading state
        var originalBtnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Subiendo archivo...';

        $.ajax({
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            data: datosForm,
            url: destino
        }).done(function (data) {
            var fileName = miArchivo.name;
            var matrixInfo = knownMatrices[fileName];

            if (matrixInfo) {
                // If listar_tabla exists on this page, smoothly run import without full page reload
                if (typeof listar_tabla === 'function') {
                    var mainView = document.getElementById("loadFilesMainView");
                    if (mainView) mainView.style.display = "none";
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    listar_tabla(matrixInfo.link);
                } else {
                    // Otherwise redirect to index.php?table=X
                    window.location.href = "index.php?table=" + matrixInfo.tableParam;
                }
            } else {
                // Generic file uploaded
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                if (alertArea) {
                    alertArea.innerHTML = '<div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-0" style="border-radius:8px;">' +
                        '<span class="material-icons mr-2" style="font-size:1.1rem;">check_circle</span>' +
                        '<span>¡Archivo <strong>' + fileName + '</strong> subido con éxito al servidor!</span>' +
                        '</div>';
                }
            }
        }).fail(function (xhr, status, error) {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
            if (alertArea) {
                alertArea.innerHTML = '<div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-0" style="border-radius:8px;">' +
                    '<span class="material-icons mr-2" style="font-size:1.1rem;">error_outline</span>' +
                    '<span>Error al subir el archivo al servidor. Intente nuevamente.</span>' +
                    '</div>';
            } else {
                alert("Error al subir el archivo.");
            }
        });
    });
});
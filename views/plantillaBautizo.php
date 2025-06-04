<?php
include(__DIR__ . "/../funciones/conexion.php");
require(__DIR__ . "/../vendor/autoload.php");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../scripts/stylesPlantillaBautizo.css?v=<?php echo time(); ?>">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../scripts/filtros_bautizos.js"></script>
<script src="../scripts/ubicacionesBautizos.js"></script>

<div id="pantallaExpandida">
    <!-- Panel Izquierdo -->
    <div class="panel-izquierdo">
        <button onclick="cerrarExpandido()" class="btn btn-outline-danger mb-3 text-start">
            <i class="bi bi-arrow-left"></i> Volver
        </button>

        <div><strong>Bautizos Encontrados:</strong> <span id="totalBautizos">0</span></div>

        <div id="listaFormatos" class="flex-grow-1 overflow-auto my-3">
            <!-- aqui van las tarjetas de los bautizos disponibles -->
        </div>

        <button class="btn btn-success" id="btnObtenerSeleccionados">
            Seleccionados (<span id="contadorSeleccionados">0</span>)
        </button>
    </div>

    <!-- Panel Derecho -->
    <div class="panel-derecho" id="contenidoDetalle">
        <!-- Encabezado visual -->
        <div class="encabezado-info" id="infoResumen">
            <!-- aquí se carga el texto "Bautizos encontrados..." -->
        </div>

        <!-- Hoja del bautizo -->
        <div id="hojaBautizoCompleta" class="flex-grow-1 overflow-auto p-3">
            <!-- aquí se carga dinámicamente la hoja de bautizo -->
        </div>

        <!-- Nota al final -->
        <div class="nota-informativa">
            Nota: Para visualizar un bautizo pulse sobre la tarjeta del bautizo que desea detallar, si va a imprimir el bautizo pulse sobre el icono de la impresora para agregarlo a la cola de impresión, una vez seleccionado todos los bautizos a imprimir pulse seleccionados para guardar la selección.
        </div>
    </div>

</div>


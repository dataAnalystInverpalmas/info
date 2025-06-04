<?php
include(__DIR__ . "/../funciones/conexion.php");
require(__DIR__ . "/../vendor/autoload.php");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../scripts/styles_formBautizo.css">
<link rel="stylesheet" href="../scripts/stylesPlantillaBautizo.css" media="print">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../scripts/filtros_bautizos.js"></script>
<script src="../scripts/ubicacionesBautizos.js"></script>

<div class="layout" style="height: 100vh; overflow: hidden;">
  <!-- panel lateral izquierdo -->
  <div class="sidebar d-flex flex-column" style="height: 100vh;">
    <div class="p-3 border-bottom">
      <h6 class="mb-2">Bautizos Seleccionados</h6>
      <div class="text-muted">Seleccion: <strong id="totalSeleccionados">0</strong></div>
    </div>
    <div class="flex-grow-1 overflow-auto px-2 py-2" id="contenedorSeleccionados" style="gap: 0.75rem;">
      <!-- aqui se inyectan los bautizos seleccionados -->
    </div>
    <div class="p-3 border-top">
      <button class="btn btn-outline-success btn-sm w-100 btn-select" onclick="imprimirSeleccionados()">
        <i class="bi bi-printer-fill"></i> Imprimir
      </button>
    </div>
  </div>

  <!-- contenido principal -->
  <div class="main d-flex flex-column" style="height: 100vh;">
    <div class="main-header d-flex justify-content-between align-items-start mb-2">
      <div>
        <h3 class="text-uppercase mb-1" style="color:var(--verde-inverpalmas)">HOJA BAUTIZOS</h3>
        <div id="resumenFiltroContainer" class="d-flex flex-column">
          <small class="text-muted" id="resumenFiltroContainer">Datos filtrados por: <span id="resumenFiltros" style="font-weight: bold;"></span></small>
          <small class="text-muted" id="n_datosEncontrados">Datos Encontrados: <span id="datosEncontrados" style="font-weight: bold;"></span></small>
        </div>
      </div>

      <div class="d-flex aling-items-start flex-wrap gap-2">
        <button id="btnOrdenar" class="btn btn-outline-dark btn-sm" onclick="ordenarTarjetas()">
          <i class="bi bi-sort-up-alt"></i> Ordenar Nuevos
        </button>
        <button class="btn btn-outline-danger btn-sm" onclick="limpiarTarjetas()">
          <i class="bi bi-trash3"></i> Limpiar
        </button>
        <button class="btn btn-outline-dark btn-sm me-3" data-bs-toggle="offcanvas" data-bs-target="#filtroLateral">
          <i class="bi bi-search"></i>
          Buscar
        </button>
      </div>
    </div>
    <div class="cards-scrollable">
      <div class="cards-container d-grid gap-3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));" id="contenedorTarjetas">
        <!-- aqui se inyectan las tarjetas de los bautizos disponibles -->
      </div>
    </div>
  </div>
</div>

<!-- vista elevada de filtros -->
<div id="overlayModal" class="modal-overlay d-none">
  <div class="modal-contenido shadow-lg rounded bg-white">
  </div>
</div>

<!-- SideSheetDialog -->
<?php include(__DIR__ . '/../views/filtros_sideSheet.php'); ?>

<script>

  //aqui se construyen las tarjetas de los bautizos seleccionados que se van a imprimir
  function cargarSeleccionadosDesdeStorage() {
    const contenedor = document.getElementById("contenedorSeleccionados");
    const total = document.getElementById("totalSeleccionados");
    contenedor.innerHTML = '';

    let seleccionados = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];
    total.textContent = seleccionados.length;

    seleccionados.forEach((b, index) => {
      const div = document.createElement("div");
      div.className = "selected-card";

      div.innerHTML = `
        <strong>${b.finca}</strong><br>
        <span class="text-muted">BQ: ${b.bloque}</span><br>
        ${b.variedad}<br>
        ${b.temporada}<br>
        ${b.fecha_siembra_r}<br>
        Camas: ${b.camas}<br>
        Plantas: ${b.plantas}<br>
        <span class="text-muted">Desde: ${b.desde}</span><br>
        <span class="text-muted">Hasta: ${b.hasta}</span><br>
        <button class="btn btn-outline-danger btn-sm mt-1" title="Eliminar" onclick="eliminarSeleccionado(${index})">X</button>
      `;

      contenedor.appendChild(div);
    });
  }

  //aqui se puede eliminar de la lista de bautizos seleccionados el que uno quiera
  function eliminarSeleccionado(index) {
    let seleccionados = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];
    const eliminado = seleccionados[index];

    if (!eliminado) return;

    seleccionados.splice(index, 1);
    localStorage.setItem('bautizosSeleccionados', JSON.stringify(seleccionados));

    cargarSeleccionadosDesdeStorage();
    const tarjetas = document.querySelectorAll('#contenedorTarjetas .card-custom');

    tarjetas.forEach(card => {
      const finca = card.dataset.finca;
      const bloque = card.dataset.bloque;
      const variedad = card.dataset.variedad;
      const temporada = card.dataset.temporada;
      const fecha_siembra = card.dataset.fecha_siembra;

      if (
        finca === eliminado.finca &&
        bloque === eliminado.bloque &&
        variedad === eliminado.variedad &&
        temporada === eliminado.temporada &&
        fecha_siembra === eliminado.fecha_siembra
      ) {
        card.classList.remove('selected');
        const btnSelect = card.querySelector('.btn-select');
        const btnExpand = card.querySelector('.btn-expand');

        if (btnSelect) {
          btnSelect.textContent = 'Seleccionar';
          btnSelect.classList.remove('seleccionado');
        }
        if (btnExpand) {
          btnExpand.style.backgroundColor = '';
          btnExpand.style.color = '';
          btnExpand.style.borderColor = '';
        }
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function() {
    localStorage.removeItem('bautizosSeleccionados');
    cargarSeleccionadosDesdeStorage();
  });
</script>

<!-- contenedor para cargar los datos de impresion -->
<div id="contenedorImpresion" class="d-none" style="position: absolute; top: 0; left: 0; z-index: 9999;"></div>
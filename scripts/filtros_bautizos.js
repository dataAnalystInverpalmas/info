let ordenInvertido = false;
let tarjetasOriginales = [];

//actualiza filtros dependiendo los valores a buscar
function actualizarFiltros() {
  $.ajax({
    url: '../ajax/filtrar_datos.php',
    type: 'POST',
    data: {
      finca: $('#finca').val(),
      bloque: $('#bloque').val(),
      variedad: $('#variedad').val(),
      siembra: $('#siembra').val()
    },
    dataType: 'json',
    success: function (data) {
      const campos = [
        { id: 'finca', valores: data.finca },
        { id: 'bloque', valores: data.bloque },
        { id: 'variedad', valores: data.variedad },
        { id: 'siembra', valores: data.siembra }
      ];

      campos.forEach(({ id, valores }) => {
        const $campo = $('#' + id);
        const seleccionados = obtenerValoresLimpios($campo);
        $campo.empty();

        let opcionesFinales = new Set([...valores]);
        if (seleccionados.length > 0) {
          seleccionados.forEach(val => opcionesFinales.add(val));
        }

        opcionesFinales.forEach(op => {
          const selected = seleccionados.includes(op);
          $campo.append(new Option(op, op, selected, selected));
        });
      });

      $('#finca, #bloque, #variedad, #siembra').trigger('change.select2');
      verificaBusquedaActiva();
    }
  });
}

//verifica si ha seleccionado algun dato para realizar la busqueda, si no tiene nada seleccionado  no permite la bsuqueda
function verificaBusquedaActiva() {
  const algoSeleccionado =
    $('#finca').val().length > 0 ||
    $('#bloque').val().length > 0 ||
    $('#variedad').val().length > 0 ||
    $('#siembra').val().length > 0;

  $('button[name="buscar"]').prop('disabled', !algoSeleccionado);
}

//actualiza los resultados actuales 
function actualizaResumenFiltros() {
  const finca = $('#finca').val() || [];
  const bloque = $('#bloque').val() || [];
  const variedad = $('#variedad').val() || [];
  const siembra = $('#siembra').val() || [];

  const resumenContainer = document.getElementById("resumenFiltroContainer");
  const texto = document.getElementById("resumenFiltros");

  let resumen = [];

  if (finca.length > 0) resumen.push('Finca: ' + finca.join(', '));
  if (bloque.length > 0) resumen.push('Bloque: ' + bloque.join(', '));
  if (variedad.length > 0) resumen.push('Variedad: ' + variedad.join(', '));
  if (siembra.length > 0) resumen.push('Siembra: ' + siembra.join(', '));

  if (resumen.length > 0) {
    texto.textContent = resumen.join(' | ');
    if (resumenContainer) resumenContainer.style.display = 'inline';
  } else {
    resumen.textContent = '';
    if (resumenContainer) resumenContainer.style.display = 'none';
  }
}

//guarda la primera carga de los datos, esto se utiliza para la organizacion de las listas
function guardarOriginales() {
  const tarjetas = document.querySelectorAll('#contenedorTarjetas .card-custom');

  tarjetasOriginales = Array.from(tarjetas).map(card => {
    return {
      finca: card.dataset.finca,
      bloque: card.dataset.bloque,
      variedad: card.dataset.variedad,
      temporada: card.dataset.temporada,
      fecha_siembra: card.dataset.fecha_siembra,
      fecha_siembra_r: card.dataset.fecha_siembra_r, 
      camas: card.dataset.camas,
      plantas: card.dataset.plantas,
      tipo_siembra: card.dataset.tipo_siembra,
      estado: card.querySelector('.badge-nuevo') ? 'NUEVO' : ''
    };
  });
}

//borra toda la seleccion actual
function limpiarTarjetas() {
  location.reload();
}

//esta funcion se utiliza para cuando se quiere buscar otro valor dentro del mismo filtro
function obtenerValoresLimpios(selector) {
  const val = $(selector).val();
  if (!val || (Array.isArray(val) && val.length === 0)) {
    return [];
  }
  return val;
}

//funcion para ordenar las tarjetas de  nuevo a lista original
function ordenarTarjetas() {
  const contenedor = document.getElementById("contenedorTarjetas");
  const btnOrdenar = document.getElementById("btnOrdenar");

  if (!ordenInvertido) {
    guardarOriginales();
    const tarjetas = Array.from(contenedor.children);

    tarjetas.sort((a, b) => {
      const esNuevoA = a.querySelector(".badge-nuevo") ? 1 : 0;
      const esNuevoB = b.querySelector(".badge-nuevo") ? 1 : 0;
      return esNuevoB - esNuevoA;
    });

    contenedor.innerHTML = '';
    tarjetas.forEach(t => contenedor.appendChild(t));
    btnOrdenar.innerHTML = '<i class="bi bi-arrow-down-up"></i> Orden Original';
    ordenInvertido = true;
  } else {
    //aqui se contruye la tarjeta
    contenedor.innerHTML = '';
    tarjetasOriginales.forEach(item => {
      //si dentro de la consulta el item que se esta mostrando tiene una fecha de siembra <= 7 dias a la fecha actual va a mostrar que es nuevo en caso contrario no
      const badgeNuevo = item.estado === 'NUEVO'
        ? '<span class="badge-nuevo bg-success ms-1"><i class="bi bi-star-fill"></i> NUEVO</span>'
        : '';
    
      const tarjeta = `
        <div class="card-custom" 
          data-finca="${item.finca}" 
          data-bloque="${item.bloque}" 
          data-variedad="${item.variedad}" 
          data-temporada="${item.temporada}" 
          data-fecha_siembra="${item.fecha_siembra}"
          data-fecha_siembra_r="${item.fecha_siembra_r}">
          <div class="d-flex justify-content-between">
            <div class="info">
              <strong>${item.finca}</strong>${badgeNuevo}<br>
              ${item.variedad}<br>
              ${item.temporada}<br>
              ${item.fecha_siembra}<br>
              CAMAS: ${item.camas} | PLANTAS: ${item.plantas}<br>
              <strong>${item.tipo_siembra}</strong>
            </div>
            <div class="card-big-number">${item.bloque}</div>
          </div>
          <div class="card-buttons">
            <button class="btn btn-outline-success btn-sm btn-select" onclick='seleccionarDesdeTarjeta(this, ${JSON.stringify(item)})'>
              Seleccionar
            </button>
            <button class="btn btn-outline-secondary btn-sm btn-expand" title="Expandir"
              onclick='expandirVista(${JSON.stringify(item)})'>
              <i class="bi bi-arrows-angle-expand"></i>
            </button>
          </div>
        </div>
      `;
      contenedor.insertAdjacentHTML('beforeend', tarjeta);
    });

    btnOrdenar.innerHTML = '<i class="bi bi-sort-up-alt"></i> Ordenar Nuevos';
    ordenInvertido = false;

    setTimeout(() => {
      marcarTarjetaSeleccionada();
    }, 10);
  }
}

//esta funcion expande la ventana para mostrar el detalle de la tarjeta seleccionada, adentro de esta funcion tambien se encuentra la funcionalidad de que se pueda selccionar individualmente para imprimir
function expandirVista(item) {
  const overlay = document.getElementById('overlayModal');
  const modalContenido = overlay.querySelector('.modal-contenido');

  fetch('plantillaBautizo.php')
    .then(res => res.text())
    .then(html => {
      modalContenido.innerHTML = html;
      overlay.classList.remove('d-none');
      const btn = document.getElementById('btnObtenerSeleccionados');
      if (btn) {
        btn.addEventListener('click', function () {
          const seleccionados = document.querySelectorAll('.formato-item.seleccionado');
          const lista = [];

          seleccionados.forEach(item => {
            lista.push({
              finca: item.dataset.finca,
              bloque: item.dataset.bloque,
              variedad: item.dataset.variedad,
              temporada: item.dataset.temporada,
              tipo_siembra: item.dataset.tipo_siembra,
              fecha_siembra: item.dataset.fecha_siembra,
              fecha_siembra_r: item.dataset.fecha_siembra_r,
              camas: item.dataset.total_camas,
              plantas: item.dataset.total_plantas,
              desde: item.dataset.desde,
              hasta: item.dataset.hasta
            });
          });

          console.log("Resultados Seleccionados", lista);

          if (lista.length === 0) {
            alert("No has seleccionado ningún bautizo.");
            return;
          }
          
         //aqui guarda y valida los datos seleccionados cuando se seleccionan desde adentro de la ventana de expandir
          let seleccionadosActuales = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];

          lista.forEach(nuevo => {
            const yaExiste = seleccionadosActuales.some(sel =>
              sel.finca === nuevo.finca &&
              sel.bloque === nuevo.bloque &&
              sel.variedad === nuevo.variedad &&
              sel.temporada === nuevo.temporada &&
              sel.tipo_siembra === nuevo.tipo_siembra &&
              sel.desde === nuevo.desde &&
              sel.hasta === nuevo.hasta
            );

            if (!yaExiste) {
              seleccionadosActuales.push(nuevo);
            }
          });

          localStorage.setItem('bautizosSeleccionados', JSON.stringify(seleccionadosActuales));
          cargarSeleccionadosDesdeStorage(); //llama a los bautizos  seleccionados y los ubica en el panel izquierdo de la ventana principal
          cerrarExpandido(); //cierra la ventana de expandido

          if (typeof marcarTarjetaSeleccionada === 'function') {
            marcarTarjetaSeleccionada();
          }

          if (typeof actualizarContadorSeleccionados === 'function') {
            actualizarContadorSeleccionados();
          }
        });
      }

      verAgrupamientosEnConsola(
        item.finca,
        item.bloque,
        item.variedad,
        item.temporada,
        item.tipo_siembra
      );
    });
}


//esta funcion es para obtener todos los bautizos asociados a la seleccion del usuario y mostrar esa seleccion en el panel izquierdo de la ventana principal
//trae todos los bautizos de la seleccion
function seleccionarDesdeTarjeta(btn, item) {
  $.ajax({
    url: '../ajax/ubicacionBautizos.php',
    type: 'POST',
    dataType: 'json',
    data: { finca: item.finca, bloque: item.bloque },
    success: function (ubicaciones) {
      $.ajax({
        url: '../ajax/dataHeaderBautizos.php',
        type: 'POST',
        dataType: 'json',
        data: {
          finca: item.finca,
          bloque: item.bloque,
          variedad: item.variedad,
          temporada: item.temporada,
          tipo_siembra: item.tipo_siembra
        },
        success: function (resultados) {
          const agrupados = agruparPorUbicacion(resultados, ubicaciones);
          if (agrupados.length === 0) {
            console.warn("No se encontraron bautizos agrupados.");
            return;
          }

          let seleccionadosActuales = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];

          agrupados.forEach(g => {
            const index = seleccionadosActuales.findIndex(sel =>
              sel.finca === g.finca &&
              sel.bloque === g.bloque &&
              sel.variedad === g.variedad &&
              sel.temporada === g.temporada &&
              sel.tipo_siembra === g.tipo_siembra &&
              sel.desde === g.desde &&
              sel.hasta === g.hasta &&
              sel.fecha_siembra === g.fecha_siembra
            );

            if (index !== -1) {
              seleccionadosActuales.splice(index, 1); // valida si ya esta seleccionado
            } else {
              seleccionadosActuales.push({
                finca: g.finca,
                bloque: g.bloque,
                variedad: g.variedad,
                temporada: g.temporada,
                tipo_siembra: g.tipo_siembra,
                fecha_siembra: g.fecha_siembra,
                fecha_siembra_r: g.fecha_siembra_r,
                camas: g.total_camas,
                plantas: g.total_plantas,
                desde: g.desde,
                hasta: g.hasta
              });
            }
          });

          localStorage.setItem('bautizosSeleccionados', JSON.stringify(seleccionadosActuales));
          cargarSeleccionadosDesdeStorage();
          const card = btn.closest('.card-custom');
          if (card.classList.contains('selected')) {
            card.classList.remove('selected');
            btn.textContent = 'Seleccionar';
            btn.classList.remove('seleccionado');
          } else {
            card.classList.add('selected');
            btn.textContent = 'Seleccionado';
            btn.classList.add('seleccionado');
          }

          const botonExpandir = card.querySelector('.btn-expand');
          if (botonExpandir) {
            if (card.classList.contains('selected')) {
              botonExpandir.style.backgroundColor = '#ffffff';
              botonExpandir.style.color = '#19692c';
              botonExpandir.style.borderColor = '#ffffff';
            } else {
              botonExpandir.style.backgroundColor = '';
              botonExpandir.style.color = '';
              botonExpandir.style.borderColor = '';
            }
          }
        }
      });
    }
  });
}

function cerrarExpandido() {
  const overlay = document.getElementById('overlayModal');
  overlay.classList.add('d-none');
  overlay.querySelector('.modal-contenido').innerHTML = '';
}


//esta funcion sirve para construir dinamica mente el listado de tarjets que va dentro de la ventana de expandir
//en caso de no mostrar datos se debe a aque alguno de los parametros que solicita se encuentra como undefined
//llama a la consulta de ubicaciones, dataheader y databody para construir todo los datos del bautizo
function verAgrupamientosEnConsola(finca, bloque, variedad, temporada, tipo_siembra) {
  if (!finca || !bloque || !variedad || !temporada || !tipo_siembra) {
    console.warn("Debe enviar finca, bloque, variedad y temporada para ver agrupamientos.");
    return;
  }

  $.ajax({
    url: '../ajax/ubicacionBautizos.php',
    type: 'POST',
    dataType: 'json',
    data: { finca, bloque },
    success: function (ubicaciones) {
      $.ajax({
        url: '../ajax/dataHeaderBautizos.php',
        type: 'POST',
        dataType: 'json',
        data: { finca, bloque, variedad, temporada, tipo_siembra },
        success: function (resultadosHeader) {
          const fecha_siembra = resultadosHeader[0]?.fecha_siembra_r?.split('/')[0] || '';
          $.ajax({
            url: '../ajax/dataBodyBautizos.php',
            type: 'POST',
            dataType: 'json',
            data: {
              finca,
              bloque,
              variedad,
              temporada,
              fecha_siembra
            },
            success: function (dataBody) {
             // console.log("headerdata", resultadosHeader);
             // console.log("databody", dataBody);
              const agrupados = agruparPorUbicacion(resultadosHeader, ubicaciones);
              if (agrupados.length === 0) return;
              //encabezado de la seleccion actual
              const textoEncabezado = `BAUTIZOS ENCONTRADOS PARA LA FINCA: ${finca} || BLOQUE: ${bloque} || VARIEDAD: ${variedad} || TEMPORADA: ${temporada} || TIPO SIEMBRA: ${tipo_siembra}`;
              document.getElementById("infoResumen").textContent = textoEncabezado;

              const lista = document.getElementById("listaFormatos");
              lista.innerHTML = '';

              agrupados.forEach((g, i) => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'formato-item';

                tarjeta.dataset.finca = g.finca;
                tarjeta.dataset.bloque = g.bloque;
                tarjeta.dataset.variedad = g.variedad;
                tarjeta.dataset.temporada = g.temporada;
                tarjeta.dataset.tipo_siembra = g.tipo_siembra;
                tarjeta.dataset.fecha_siembra = g.fecha_siembra;
                tarjeta.dataset.fecha_siembra_r = g.fecha_siembra_r;
                tarjeta.dataset.total_camas = g.total_camas;
                tarjeta.dataset.total_plantas = g.total_plantas;
                tarjeta.dataset.desde = g.desde;
                tarjeta.dataset.hasta = g.hasta;
              
                //aqui se cargan las tarjetas del panel izquierdo
                tarjeta.innerHTML = `
                  <div class="formato-contenido">
                    <div><strong>Camas:</strong> ${g.total_camas}</div>
                    <div><strong>Plantas:</strong> ${g.total_plantas}</div>
                    <div class="formato-fila-horizontal">
                      <div><strong>Desde</strong><br>${g.desde}</div>
                      <div><strong>Hasta</strong><br>${g.hasta}</div>
                    </div>
                  </div>
                  <div class="formato-accion">
                    <i class="bi bi-printer" onclick="toggleSeleccion(this)"></i>
                  </div>
                `;
                // cuando se hace click sobre la tarjeta del panel izquierdo carga  la vista de la hoja de bautizo
                tarjeta.addEventListener('click', function () {
                  const panel = document.getElementById("hojaBautizoCompleta");
                  construirHojaDeBautizo(g, dataBody, panel);
                });

                lista.appendChild(tarjeta);
              });
              //cuantos bautizos se encontraron
              document.getElementById("totalBautizos").textContent = agrupados.length;
            },
            error: function (xhr) {
              console.error("error al obtener cuerpo:", xhr.responseText);
            }
          });
        },
        error: function (xhr) {
          console.error("error al obtener encabezados:", xhr.responseText);
        }
      });
    },
    error: function (xhr) {
      console.error("error al obtener ubicaciones:", xhr.responseText);
    }
  });
}

//funcion para construir la hoja de bautizo dinamicamente
function construirHojaDeBautizo(headerData, bodyData, contenedor) {
  // Separar labores y aplicaciones
  const labores = bodyData.filter(r => r.seccion === 'LABORES');
  const aplicaciones = bodyData.filter(r => r.seccion === 'APLICACIONES');

  const agruparPor = (data, campo) => {
    const grupos = {};
    for (const r of data) {
      const clave = r[campo];
      if (!grupos[clave]) grupos[clave] = [];
      grupos[clave].push(r);
    }
    return grupos;
  };

  const construirFilasAgrupadas = (grupos, campoLabel) => {
    let html = '';
    for (const [nombre, items] of Object.entries(grupos)) {
      items.forEach((item, i) => {
        html += `<tr>`;
        if (i === 0) html += `<td rowspan="${items.length}">${nombre}</td>`;
        html += `<td>${item.aplicar}</td>`;
        html += `<td>${item.fecha_formato}</td>`;
        html += `<td></td>`;
        html += `</tr>`;
      });
    }
    return html;
  };

  const filasLabores = construirFilasAgrupadas(agruparPor(labores, 'tipo'), 'LABORES');
  const filasAplicaciones = construirFilasAgrupadas(agruparPor(aplicaciones, 'tipo'), 'APLICACIONES');

  // Dirección basada en 'desde' y 'hasta'
  let direccion = '';
  const desde = headerData.desde || '';
  const hasta = headerData.hasta || '';

  const letraDesde = desde.charAt(0);
  const letraHasta = hasta.charAt(0);

  if (desde === hasta) {
    direccion = '🢃';
  } else if (letraDesde === 'A') {
    direccion = '🢀';
  } else if (letraDesde === 'B') {
    direccion = '🢂';
  } else {
    direccion = '';
  }

  // Renderizar hoja completa
  contenedor.innerHTML = `
    <table class="encabezado-bautizo">
      <tr>
        <td colspan="5"><div class="label">Variedad</div><div class="fila1">${headerData.variedad}</div></td>
        <td><div class="label">Bloque</div><div class="fila1">${headerData.bloque}</div></td>
        <td><div class="label"># Camas</div><div class="fila1">${headerData.total_camas}</div></td>
        <td><div class="label">Direccion</div><div class="flecha">${direccion}</div></td>
      </tr>
      <tr>
        <td colspan="2"><div class="label">Finca</div><div class="fila2">${headerData.finca}</div></td>
        <td colspan="2"><div class="label">Temporada</div><div class="fila2">${headerData.temporada}</div></td>
        <td><div class="label"># Plantas</div><div class="fila2">${headerData.total_plantas}</div></td>
        <td><div class="label">Plantas M2</div><div class="fila2">${headerData.plantasm2}</div></td>
        <td><div class="label">Pico</div><div class="fila2">${headerData.pico}</div></td>
        <td><div class="label">Ciclo</div><div class="fila2">${headerData.ciclo}</div></td>
      </tr>
      <tr>
        <td>
          <div class="label">
            ${headerData.tipo_siembra === 'REEMPLAZO'
              ? 'REEMPLAZO de:'
              : 'Fecha Siembra T'}
          </div>
          <div class="fila3">
            ${headerData.tipo_siembra === 'REEMPLAZO'
              ? `${headerData.cosecha_reem} - ${headerData.variedad_reem}`
              : `${headerData.fecha_siembra_t || ''}`}
          </div>
        </td>
        <td><div class="label">Fecha Siembra R</div><div class="fila3">${headerData.fecha_siembra_r}</div></td>
        <td><div class="label">Semana Pico T</div><div class="fila3">${headerData.semana_pico_t}</div></td>
        <td><div class="label">Semana Pico R</div><div class="fila3">${headerData.semana_pico_r}</div></td>
        <td><div class="label">Origen</div><div class="fila3">${headerData.origen}</div></td>
        <td><div class="label">Casa C</div><div class="fila3">${headerData.casa_comercial}</div></td>
        <td><div class="label">Desde</div><div class="fila3">${headerData.desde}</div></td>
        <td><div class="label">Hasta</div><div class="fila3">${headerData.hasta}</div></td>
      </tr>
      <tr>
        <td colspan="2"><div class="fila4">${headerData.tipo_suelo}</div></td>
        <td colspan="2"><div class="fila4">${headerData.nmanguera}</div></td>
        <td colspan="4"><div class="fila4">FECHA ERRADICACION: <span class="fila14">${headerData.ferradica}</span></div></td>
      </tr>
    </table>

    <!-- LABORES -->
    <table class="tabla-inferior tabla-labores">
      <thead>
        <tr>
          <th class="titulo-columna">Labores</th>
          <th class="titulo-columna">Tipo</th>
          <th class="titulo-columna">Fecha</th>
          <th class="titulo-columna">Dato Real</th>
        </tr>
      </thead>
      <tbody>${filasLabores}</tbody>
    </table>

    <!-- APLICACIONES -->
    <table class="tabla-inferior tabla-aplicaciones">
      <thead>
        <tr>
          <th class="titulo-columna">Aplicaciones</th>
          <th class="titulo-columna">Tipo</th>
          <th class="titulo-columna">Fecha</th>
          <th class="titulo-columna">Dato Real</th>
        </tr>
      </thead>
      <tbody>${filasAplicaciones}</tbody>
    </table>
  `;
}


//funcion para cambiar el color de la tarjeta cuando es seleccionada
function marcarTarjetaSeleccionada() {

  const tarjetas = document.querySelectorAll('#contenedorTarjetas .card-custom');
  let seleccionados = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];

  tarjetas.forEach(card => {
    const finca = card.dataset.finca;
    const bloque = card.dataset.bloque;
    const variedad = card.dataset.variedad;
    const temporada = card.dataset.temporada;
    const fecha_siembra = card.dataset.fecha_siembra;

    const botonSeleccion = card.querySelector('.btn-select');
    const botonExpandir = card.querySelector('.btn-expand');

    const estaSeleccionado = seleccionados.some(sel =>
      sel.finca === finca &&
      sel.bloque === bloque &&
      sel.variedad === variedad &&
      sel.temporada === temporada &&
      sel.fecha_siembra === fecha_siembra
    );

    if (estaSeleccionado) {
      card.classList.add('selected');
      if (botonSeleccion) {
        botonSeleccion.textContent = 'Seleccionado';
        botonSeleccion.classList.add('seleccionado');
      }
      if (botonExpandir) {
        botonExpandir.style.backgroundColor = '#ffffff';
        botonExpandir.style.color = '#19692c';
        botonExpandir.style.borderColor = '#ffffff';
      }
    } else {
      card.classList.remove('selected');
      if (botonSeleccion) {
        botonSeleccion.textContent = 'Seleccionar';
        botonSeleccion.classList.remove('seleccionado');
      }
      if (botonExpandir) {
        botonExpandir.style.backgroundColor = '';
        botonExpandir.style.color = '';
        botonExpandir.style.borderColor = '';
      }
    }
  });
}

//esta funcion busca todos los bautizos seleccionados y constuye las vistas para  que se puedan imprimir en media carta
function imprimirSeleccionados() {
  const seleccionados = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];
  if (seleccionados.length === 0) {
    alert('No hay bautizos seleccionados para imprimir.');
    return;
  }

  const contenedor = document.getElementById('contenedorImpresion');
  contenedor.innerHTML = '';
  contenedor.classList.remove('d-none');
  contenedor.style.display = 'block';
  contenedor.style.visibility = 'none';

  let pendientes = seleccionados.length;

  seleccionados.forEach((sel, index) => {
    $.ajax({
      url: '../ajax/dataHeaderBautizos.php',
      type: 'POST',
      dataType: 'json',
      data: {
        finca: sel.finca,
        bloque: sel.bloque,
        variedad: sel.variedad,
        temporada: sel.temporada,
        tipo_siembra: sel.tipo_siembra
      },
      success: function (headerData) {
        const fecha_siembra = headerData[0]?.fecha_siembra_r?.split('/')[0] || '';

        $.ajax({
          url: '../ajax/dataBodyBautizos.php',
          type: 'POST',
          dataType: 'json',
          data: {
            finca: sel.finca,
            bloque: sel.bloque,
            variedad: sel.variedad,
            temporada: sel.temporada,
            fecha_siembra
          },
          success: function (bodyData) {
            const hoja = document.createElement('div');
            hoja.className = 'media-carta-hoja';

            const headerFinal = Object.assign({}, headerData[0], {
              desde: sel.desde,
              hasta: sel.hasta,
              total_camas: sel.camas,
              total_plantas: sel.plantas,
              tabla: headerData[0].tabla
            });

            construirHojaDeBautizo(headerFinal, bodyData, hoja);
            contenedor.appendChild(hoja);

            pendientes--;
            // cuando todas las hojas esten listas, se imprime
            if (pendientes === 0) {
              // fuerza la vista para asegurar render completo
              void contenedor.offsetHeight;
              // espera un tiempo para asegurar que  todo se haya cargado
              setTimeout(() => {
                window.print();
                // limpia la vista despues de imprimir
                contenedor.innerHTML = '';
                contenedor.classList.add('d-none');
                contenedor.style.display = 'none';
                contenedor.style.visibility = 'hidden';
              }, 500);
            }
          },
          error: function (xhr) {
            console.error('error al obtener BodyData:', xhr.responseText);
            pendientes--;
          }
        });
      },
      error: function (xhr) {
        console.error('error al obtener HeaderData:', xhr.responseText);
        pendientes--;
      }
    });
  });
}


//aqui se cargan la mayoria de los metodos para mostrar las tarjetas en la vista principal
$(document).ready(function () {
  $('#filtroLateral .select2').select2({
    width: '100%',
    dropdownParent: $('#filtroLateral')
  });

  actualizarFiltros();
  actualizaResumenFiltros();

  $('#finca, #bloque, #variedad, #siembra').on('change', function () {
    actualizarFiltros();
    verificaBusquedaActiva();
    actualizaResumenFiltros();
  });

  $('#formFiltros').on('submit', function (e) {
    e.preventDefault();
    actualizaResumenFiltros();

    const datosFiltros = {
      finca: $('#finca').val(),
      bloque: $('#bloque').val(),
      variedad: $('#variedad').val(),
      siembra: $('#siembra').val()
    };

    $.ajax({
      url: '../ajax/consulta_bautizos.php',
      method: 'POST',
      data: datosFiltros,
      dataType: 'json',
      success: function (respuesta) {
        const contenedor = $('#contenedorTarjetas');
        contenedor.empty();
        if (respuesta.length === 0) {
          document.getElementById("datosEncontrados").textContent = ' 0';
          contenedor.html('<div class="text-muted">No se encontraron resultados.</div>');
          return;
        }

        document.getElementById("datosEncontrados").textContent = ' ' + respuesta.length;
        //carga las tarjetas en la vista principal

        respuesta.forEach(item => {
          const badgeNuevo = item.estado === 'NUEVO'
            ? '<span class="badge-nuevo bg-success ms-1"><i class="bi bi-star-fill"></i> NUEVO</span>'
            : '';

          const tarjeta = `
            <div class="card-custom" 
              data-finca="${item.finca}" 
              data-bloque="${item.bloque}" 
              data-variedad="${item.variedad}" 
              data-temporada="${item.temporada}" 
              data-fecha_siembra="${item.fecha_siembra}">
              <div class="d-flex justify-content-between">
                <div class="info">
                  <strong>${item.finca}</strong>${badgeNuevo}<br>
                  ${item.variedad}<br>
                  ${item.temporada}<br>
                  ${item.fecha_siembra}<br>
                  CAMAS: ${item.camas} | PLANTAS: ${item.plantas}<br>
                  <strong>${item.tipo_siembra}</strong>
                </div>
                <div class="card-big-number">${item.bloque}</div>
              </div>
              <div class="card-buttons">
                <button class="btn btn-outline-success btn-sm btn-select" onclick='seleccionarDesdeTarjeta(this, ${JSON.stringify(item)})'>
                  Seleccionar
                </button>
                <button class="btn btn-outline-secondary btn-sm btn-expand" title="Expandir"
                  onclick='expandirVista(${JSON.stringify(item)})'>
                  <i class="bi bi-arrows-angle-expand"></i>
                </button>
              </div>
            </div>
          `;
          contenedor.append(tarjeta);
        });
        setTimeout(() => {
          marcarTarjetaSeleccionada();
        }, 10);
      }
    });

    const filtroEl = document.getElementById('filtroLateral');
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(filtroEl);
    offcanvas.hide();
  });
});
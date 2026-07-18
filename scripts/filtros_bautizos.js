let ordenInvertido = false;
let tarjetasOriginales = [];
let modoBarcodeActivo = false;
let barcodeRows = [];
let barcodeSeleccionados = new Set();

function normalizarTextoCode39(valor) {
  return String(valor ?? '')
    .toUpperCase()
    .replace(/\s+/g, '')
    .replace(/[^A-Z0-9\-\.\$\/\+%]/g, '');
}

function construirClaveBarcode(row) {
  const fincaCode = String(row.finca || '').toUpperCase() === 'INVERPALMAS' ? '10' : '20';
  const bloque2 = String(row.bloque ?? '').trim().padStart(2, '0');
  const codigoVariedad4 = String(row.codigo_variedad ?? '').trim().padStart(4, '0');
  const temporada = normalizarTextoCode39(row.temporada || '');
  const yyww = String(row.siembra_yyww || '').trim().slice(-4);

  return `${fincaCode}${bloque2}${codigoVariedad4}${temporada}${yyww}`;
}

function actualizarContadorBarcodeSeleccionados() {
  const el = document.getElementById('barcodeSeleccionadosCount');
  if (el) el.textContent = barcodeSeleccionados.size;
}

function actualizarEstadoChecksMasivosBarcode() {
  const checkHeader = document.getElementById('checkAllBarcodeHeader');
  const checkVisible = document.getElementById('checkAllBarcodeVisible');
  const visibles = barcodeRows.map(r => r._rowId);
  const totalVisibles = visibles.length;
  const totalSeleccionadosVisibles = visibles.filter(id => barcodeSeleccionados.has(id)).length;
  const checked = totalVisibles > 0 && totalVisibles === totalSeleccionadosVisibles;

  if (checkHeader) checkHeader.checked = checked;
  if (checkVisible) checkVisible.checked = checked;
}

function toggleSeleccionMasivaBarcode(marcar) {
  barcodeRows.forEach(r => {
    if (marcar) {
      barcodeSeleccionados.add(r._rowId);
    } else {
      barcodeSeleccionados.delete(r._rowId);
    }
  });

  document.querySelectorAll('.barcode-row-check').forEach(chk => {
    chk.checked = marcar;
  });

  actualizarContadorBarcodeSeleccionados();
  actualizarEstadoChecksMasivosBarcode();
}

function renderTablaBarcode() {
  const tbody = document.getElementById('tbodyBarcodeBautizos');
  if (!tbody) return;

  if (!Array.isArray(barcodeRows) || barcodeRows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">No hay datos para modo barcode.</td></tr>';
    actualizarContadorBarcodeSeleccionados();
    actualizarEstadoChecksMasivosBarcode();
    return;
  }

  tbody.innerHTML = barcodeRows.map((r) => {
    const checked = barcodeSeleccionados.has(r._rowId) ? 'checked' : '';
    const clave = construirClaveBarcode(r);
    const invalida = !r.codigo_variedad ? 'barcode-clave-invalida' : '';

    return `
      <tr>
        <td><input type="checkbox" class="form-check-input barcode-row-check" data-rowid="${r._rowId}" ${checked}></td>
        <td>${r.finca}</td>
        <td>${String(r.bloque ?? '').padStart(2, '0')}</td>
        <td>${r.variedad}</td>
        <td>${r.temporada}</td>
        <td>${r.siembra_yyww}</td>
        <td>${Number(r.matas || 0).toLocaleString('es-CO')}</td>
        <td>${r.codigo_variedad || '<span class="text-danger">N/D</span>'}</td>
        <td class="${invalida}">${clave || '-'}</td>
      </tr>
    `;
  }).join('');

  document.querySelectorAll('.barcode-row-check').forEach(chk => {
    chk.addEventListener('change', function () {
      const rowId = this.dataset.rowid;
      if (this.checked) {
        barcodeSeleccionados.add(rowId);
      } else {
        barcodeSeleccionados.delete(rowId);
      }
      actualizarContadorBarcodeSeleccionados();
      actualizarEstadoChecksMasivosBarcode();
    });
  });

  actualizarContadorBarcodeSeleccionados();
  actualizarEstadoChecksMasivosBarcode();
}

function cargarTablaBarcode(datosFiltros) {
  $.ajax({
    url: '../ajax/consulta_bautizos.php',
    method: 'POST',
    data: Object.assign({}, datosFiltros, { modo: 'barcode' }),
    dataType: 'json',
    success: function (respuesta) {
      if (!Array.isArray(respuesta)) {
        barcodeRows = [];
        barcodeSeleccionados = new Set();
        renderTablaBarcode();
        return;
      }

      barcodeRows = respuesta.map((r, idx) => {
        const rowId = `${r.finca}|${r.bloque}|${r.variedad}|${r.temporada}|${r.siembra_yyww}|${idx}`;
        return Object.assign({}, r, { _rowId: rowId });
      });

      const idsValidos = new Set(barcodeRows.map(r => r._rowId));
      barcodeSeleccionados.forEach(id => {
        if (!idsValidos.has(id)) barcodeSeleccionados.delete(id);
      });

      document.getElementById('datosEncontrados').textContent = ` ${barcodeRows.length}`;
      renderTablaBarcode();
    },
    error: function (xhr) {
      console.error('Error cargando tabla barcode:', xhr.responseText);
      barcodeRows = [];
      barcodeSeleccionados = new Set();
      renderTablaBarcode();
    }
  });
}

function activarModoBarcode(activar) {
  modoBarcodeActivo = !!activar;

  const contTarjetas = document.getElementById('contenedorTarjetas');
  const contTabla = document.getElementById('contenedorBarcodeTabla');
  const btnOrdenar = document.getElementById('btnOrdenar');
  const btnImprimirBarcode = document.getElementById('btnImprimirBarcode');

  if (contTarjetas) contTarjetas.classList.toggle('d-none', modoBarcodeActivo);
  if (contTabla) contTabla.classList.toggle('d-none', !modoBarcodeActivo);
  if (btnOrdenar) btnOrdenar.classList.toggle('d-none', modoBarcodeActivo);
  if (btnImprimirBarcode) btnImprimirBarcode.classList.toggle('d-none', !modoBarcodeActivo);

  if (modoBarcodeActivo) {
    const datosFiltros = {
      finca: $('#finca').val(),
      bloque: $('#bloque').val(),
      variedad: $('#variedad').val(),
      siembra: $('#siembra').val()
    };
    cargarTablaBarcode(datosFiltros);
  }
}

function imprimirCodigosBarras() {
  if (typeof JsBarcode === 'undefined') {
    alert('No se pudo cargar JsBarcode. Revisa la inclusión de ../scripts/JsBarcode.all.min.js');
    return;
  }

  const seleccion = barcodeRows.filter(r => barcodeSeleccionados.has(r._rowId));
  if (seleccion.length === 0) {
    alert('No hay registros seleccionados para imprimir codigos.');
    return;
  }

  const invalidas = seleccion.filter(r => !r.codigo_variedad);
  if (invalidas.length > 0) {
    alert('Hay filas sin codigo de variedad (ld_variedades). Ajusta la seleccion para continuar.');
    return;
  }

  const contenedor = document.getElementById('contenedorImpresion');
  contenedor.innerHTML = `<div class="barcode-print-grid">${seleccion.map((row, index) => {
    const clave = construirClaveBarcode(row);
    return `
      <div class="barcode-label-card">
        <div class="barcode-label-title">${row.finca} REGISTRO DE CORTES</div>
        <div class="barcode-label-meta">
          <div><span>FLOR</span> <strong>CLAVEL</strong></div>
          <div><span>BLOQUE</span> <strong>${String(row.bloque ?? '').padStart(2, '0')}</strong></div>
          <div><span>VARIEDAD</span> <strong>${row.variedad}</strong></div>
          <div><span>MATAS</span> <strong>${Number(row.matas || 0).toLocaleString('es-CO')}</strong></div>
          <div><span>COSECHA</span> <strong>${row.temporada}</strong></div>
          <div><span>SIEMBRA</span> <strong>${row.siembra_yyww}</strong></div>
        </div>
        <svg id="barcode-svg-${index}" class="barcode-svg" data-code39="${clave}" data-text="*${clave}*"></svg>
      </div>
    `;
  }).join('')}</div>`;

  contenedor.classList.remove('d-none');
  contenedor.style.display = 'block';

  document.querySelectorAll('#contenedorImpresion .barcode-svg').forEach(svg => {
    const code = svg.getAttribute('data-code39');
    const text = svg.getAttribute('data-text') || `*${code}*`;
    JsBarcode(svg, code, {
      format: 'CODE39',
      text,
      displayValue: true,
      fontSize: 16,
      height: 70,
      margin: 0,
      width: 1.7
    });
  });

  setTimeout(() => {
    window.print();
    contenedor.innerHTML = '';
    contenedor.classList.add('d-none');
    contenedor.style.display = 'none';
  }, 300);
}

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
        origen: card.dataset.origen || '',
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

//funcion auxiliar: busca dentro de un arreglo de headers la fila que corresponde
//exactamente al bautizo seleccionado, comparando por fecha_siembra_r.
function buscarHeaderCoincidente(headerData, sel) {
  if (!Array.isArray(headerData) || headerData.length === 0) return null;

  const normalizar = (valor) => String(valor ?? '').trim();
  const isoDesdeFecha = (fechaTexto) => {
    const valor = normalizar(fechaTexto);
    if (!/^\d{4}-\d{2}-\d{2}$/.test(valor)) return '';
    const fecha = new Date(`${valor}T00:00:00Z`);
    if (Number.isNaN(fecha.getTime())) return '';

    const dia = fecha.getUTCDay() || 7;
    fecha.setUTCDate(fecha.getUTCDate() + 4 - dia);
    const anioIso = fecha.getUTCFullYear();
    const inicio = new Date(Date.UTC(anioIso, 0, 1));
    const semana = Math.ceil((((fecha - inicio) / 86400000) + 1) / 7);
    return `${anioIso}${String(semana).padStart(2, '0')}`;
  };
  const obtenerYYWW = (registro) => {
    const directo = normalizar(registro?.fecha_siembra_yyww);
    if (directo) return directo;

    const fechaR = normalizar(registro?.fecha_siembra_r);
    if (fechaR.includes('/')) {
      const [fecha, semana] = fechaR.split('/');
      const anioSemana = isoDesdeFecha(fecha);
      if (anioSemana) return anioSemana;
      if (semana && fecha) return `${fecha.slice(0, 4)}${String(semana).padStart(2, '0')}`;
    }

    return isoDesdeFecha(registro?.fecha_siembra);
  };

  const origenSel = normalizar(sel.origen);
  const yywwSel = obtenerYYWW(sel);

  if (origenSel && yywwSel) {
    const coincidenteOrigenSemana = headerData.find(h =>
      normalizar(h.origen) === origenSel &&
      obtenerYYWW(h) === yywwSel
    );
    if (coincidenteOrigenSemana) return coincidenteOrigenSemana;
  }

  if (origenSel) {
    const coincidentePorOrigen = headerData.find(h => normalizar(h.origen) === origenSel);
    if (coincidentePorOrigen) return coincidentePorOrigen;
  }

  if (yywwSel) {
    const coincidentePorSemana = headerData.find(h => obtenerYYWW(h) === yywwSel);
    if (coincidentePorSemana) return coincidentePorSemana;
  }

  // 1. Coincidencia exacta por fecha_siembra_r completo (fecha/semana)
  let coincidente = headerData.find(h => h.fecha_siembra_r === sel.fecha_siembra_r);
  if (coincidente) return coincidente;

  // 2. Coincidencia parcial: solo la fecha (parte antes del '/')
  if (sel.fecha_siembra_r) {
    const fechaSel = sel.fecha_siembra_r.split('/')[0];
    coincidente = headerData.find(h => h.fecha_siembra_r?.split('/')[0] === fechaSel);
    if (coincidente) return coincidente;
  }

  // 3. Coincidencia por fecha_siembra simple (cuando se guardo desde la tarjeta principal)
  if (sel.fecha_siembra) {
    const fechaSel = sel.fecha_siembra.split('/')[0];
    coincidente = headerData.find(h => h.fecha_siembra_r?.split('/')[0] === fechaSel);
    if (coincidente) return coincidente;
  }

  console.warn('No se encontro header coincidente, se usa el primero', sel);
  return headerData[0];
}

function inferirOrigenDesdeRango(headerData, sel) {
  if (!Array.isArray(headerData) || headerData.length === 0) return '';
  const desde = String(sel?.desde || '').trim();
  if (!desde) return '';

  const partes = desde.split('-');
  if (partes.length < 3) return '';

  const tabla = String(partes[0] || '').trim().toUpperCase();
  const nave = Number(partes[1]);
  const cama = Number(partes[2]);
  if (!tabla || Number.isNaN(nave) || Number.isNaN(cama)) return '';

  const fila = headerData.find(h =>
    String(h.tabla || '').trim().toUpperCase() === tabla &&
    Number(h.nave) === nave &&
    Number(h.cama) === cama
  );

  return String(fila?.origen || '').trim();
}

function resolverOrigenSeleccion(sel) {
  const normalizar = (valor) => String(valor ?? '').trim();
  const yywwSel = normalizar(sel.fecha_siembra_yyww || '').trim();

  const tarjetas = [
    ...document.querySelectorAll('#listaFormatos .formato-item'),
    ...document.querySelectorAll('#contenedorTarjetas .card-custom')
  ];

  const coincidencia = tarjetas.find(card => {
    const yywwCard = normalizar(card.dataset.fecha_siembra_yyww || card.dataset.fecha_siembra_r || card.dataset.fecha_siembra || '').split('/').pop();
    if (!yywwSel || yywwCard !== yywwSel) return false;

    return (
      (!sel.finca || normalizar(sel.finca) === normalizar(card.dataset.finca)) &&
      (!sel.bloque || normalizar(sel.bloque) === normalizar(card.dataset.bloque)) &&
      (!sel.variedad || normalizar(sel.variedad) === normalizar(card.dataset.variedad)) &&
      (!sel.temporada || normalizar(sel.temporada) === normalizar(card.dataset.temporada)) &&
      (!sel.tipo_siembra || normalizar(sel.tipo_siembra) === normalizar(card.dataset.tipo_siembra))
    );
  });

  return coincidencia?.dataset?.origen || '';
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
          data-fecha_siembra_r="${item.fecha_siembra_r}"
          data-origen="${item.origen || ''}">
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
              origen: item.dataset.origen,
              fecha_siembra: item.dataset.fecha_siembra,
              fecha_siembra_r: item.dataset.fecha_siembra_r,
              fecha_siembra_yyww: item.dataset.fecha_siembra_yyww,
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
              sel.origen === nuevo.origen &&
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
              sel.origen === g.origen &&
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
                origen: g.origen,
                fecha_siembra: g.fecha_siembra,
                fecha_siembra_r: g.fecha_siembra_r,
                fecha_siembra_yyww: g.fecha_siembra_yyww,
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
          //agrupamos los headers por ubicacion para construir las tarjetas del panel izquierdo
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
            tarjeta.dataset.origen = g.origen;
            tarjeta.dataset.fecha_siembra = g.fecha_siembra;
            tarjeta.dataset.fecha_siembra_r = g.fecha_siembra_r;
            tarjeta.dataset.fecha_siembra_yyww = g.fecha_siembra_yyww || '';
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

            // cuando se hace click sobre la tarjeta del panel izquierdo carga la vista de la hoja de bautizo
           tarjeta.addEventListener('click', function () {
              //buscamos el header que coincide con la fecha del grupo seleccionado
              const headerCoincidente = buscarHeaderCoincidente(resultadosHeader, g);
              const fecha_siembra = headerCoincidente?.fecha_siembra_r?.split('/')[0] || '';

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
                  const panel = document.getElementById("hojaBautizoCompleta");
                  //combinamos el header coincidente con los datos del grupo (desde, hasta, totales)
                  const headerFinal = Object.assign({}, headerCoincidente, {
                    desde: g.desde,
                    hasta: g.hasta,
                    total_camas: g.total_camas,
                    total_plantas: g.total_plantas,
                    fecha_siembra_r: g.fecha_siembra_r || headerCoincidente?.fecha_siembra_r
                  });
                  construirHojaDeBautizo(headerFinal, dataBody, panel);
                },
                error: function (xhr) {
                  console.error("error al obtener cuerpo:", xhr.responseText);
                }
              });
            });

            lista.appendChild(tarjeta);
          });
          //cuantos bautizos se encontraron
          document.getElementById("totalBautizos").textContent = agrupados.length;
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
    const origen = card.dataset.origen;

    const botonSeleccion = card.querySelector('.btn-select');
    const botonExpandir = card.querySelector('.btn-expand');

    const estaSeleccionado = seleccionados.some(sel =>
      sel.finca === finca &&
      sel.bloque === bloque &&
      sel.variedad === variedad &&
      sel.temporada === temporada &&
      sel.origen === origen &&
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
    const selConOrigen = { ...sel };

    $.ajax({
      url: '../ajax/dataHeaderBautizos.php',
      type: 'POST',
      dataType: 'json',
      data: {
        finca: selConOrigen.finca,
        bloque: selConOrigen.bloque,
        variedad: selConOrigen.variedad,
        temporada: selConOrigen.temporada,
        tipo_siembra: selConOrigen.tipo_siembra
      },
      success: function (headerData) {
        if (!selConOrigen.origen) {
          selConOrigen.origen = inferirOrigenDesdeRango(headerData, selConOrigen) || resolverOrigenSeleccion(selConOrigen);
        }

        //buscamos el header que corresponde EXACTAMENTE al bautizo seleccionado
        //esto evita que al haber varias filas (por distintas fechas de siembra)
        //se tome siempre la primera y la fecha_siembra_r cambie al imprimir
        const headerCoincidente = buscarHeaderCoincidente(headerData, selConOrigen);
        const fecha_siembra = headerCoincidente?.fecha_siembra_r?.split('/')[0] || '';

        $.ajax({
          url: '../ajax/dataBodyBautizos.php',
          type: 'POST',
          dataType: 'json',
          data: {
            finca: selConOrigen.finca,
            bloque: selConOrigen.bloque,
            variedad: selConOrigen.variedad,
            temporada: selConOrigen.temporada,
            fecha_siembra
          },
          success: function (bodyData) {
            const hoja = document.createElement('div');
            hoja.className = 'media-carta-hoja';

            //usamos headerCoincidente en lugar de headerData[0] para garantizar
            //que la fecha_siembra_r sea la misma que el usuario selecciono
            const headerFinal = Object.assign({}, headerCoincidente, {
              desde: selConOrigen.desde,
              hasta: selConOrigen.hasta,
              total_camas: selConOrigen.camas,
              total_plantas: selConOrigen.plantas,
              tabla: headerCoincidente.tabla,
              //preservamos la fecha_siembra_r del seleccionado como fuente de verdad
              fecha_siembra_r: selConOrigen.fecha_siembra_r || headerCoincidente.fecha_siembra_r
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
    if (modoBarcodeActivo) {
      cargarTablaBarcode({
        finca: $('#finca').val(),
        bloque: $('#bloque').val(),
        variedad: $('#variedad').val(),
        siembra: $('#siembra').val()
      });
    }
  });

  const chkModo = document.getElementById('chkModoBarcode');
  if (chkModo) {
    chkModo.addEventListener('change', function () {
      activarModoBarcode(this.checked);
    });
  }

  const chkAllHeader = document.getElementById('checkAllBarcodeHeader');
  if (chkAllHeader) {
    chkAllHeader.addEventListener('change', function () {
      toggleSeleccionMasivaBarcode(this.checked);
    });
  }

  const chkAllVisible = document.getElementById('checkAllBarcodeVisible');
  if (chkAllVisible) {
    chkAllVisible.addEventListener('change', function () {
      toggleSeleccionMasivaBarcode(this.checked);
    });
  }

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
        if (modoBarcodeActivo) {
          cargarTablaBarcode(datosFiltros);
          return;
        }

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
              data-fecha_siembra="${item.fecha_siembra}"
              data-origen="${item.origen || ''}">
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

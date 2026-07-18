function agruparPorUbicacion(resultados, ubicaciones) {
  // Construye la clave de ubicacion con el mismo formato del backend: id_finca+bloque+tabla+nave+cama.
  const construirCodigoUbicacion = (r) => {
    const idFinca = String(r.finca || '').toUpperCase() === 'INVERPALMAS' ? '1' : '2';
    const bloque = String(r.bloque ?? '').trim();
    const tabla = String(r.tabla ?? '').trim();
    const nave = String(r.nave ?? '').trim().padStart(2, '0');
    const cama = String(r.cama ?? '').trim().padStart(2, '0');
    return `${idFinca}${bloque}${tabla}${nave}${cama}`;
  };

  // que constui aqui att: camilo :D
  // creo un indice que mapea cada ubicacion a su posicion (indice) en el array 'ubicaciones'
  const ubicIndex = {};
  ubicaciones.forEach((u, idx) => {
    const key = String(u.ubicacion || construirCodigoUbicacion(u));
    ubicIndex[key] = idx; // ej: "PALERMO|1|A|8|4" => 0
  });

  // asigno el indice de orden (segun la lista 'ubicaciones') a cada resultado
  resultados.forEach(r => {
    const key = construirCodigoUbicacion(r);
    r.orden = ubicIndex[key] ?? null; // si no esta en el indice, se marca como null
  });

  // ordeno los resultados segun el orden definido en 'ubicaciones'
  resultados.sort((a, b) => a.orden - b.orden);

  const grupos = [];         // aqui guardo los grupos generados
  let grupoActual = [];      // grupo actual que se esta construyendo
  let lastOrden = null;      // ultimo indice usado para controlar continuidad

  const obtenerYYWW = (registro) => {
    const valor = String(registro?.fecha_siembra_yyww || registro?.fecha_siembra_r || '').trim();
    if (!valor) return '';
    if (/^\d{6}$/.test(valor)) return valor;
    if (valor.includes('/')) {
      const [fecha, semana] = valor.split('/');
      if (fecha && semana) return `${fecha.slice(0, 4)}${String(semana).padStart(2, '0')}`;
    }
    return valor;
  };

  // funcion que detecta si algun campo clave cambio entre dos registros ayuda a discriminar mas los bautizos no solo por ubicacion
  const cambiaCampoClave = (a, b) => {
    return (
      obtenerYYWW(a) !== obtenerYYWW(b) ||
      a.plantasm2 !== b.plantasm2 ||
      a.ciclo !== b.ciclo ||
      a.pico !== b.pico ||
      a.casa_comercial !== b.casa_comercial ||
      a.origen !== b.origen ||
      a.tipo_suelo !== b.tipo_suelo ||
      a.ferradica !== b.ferradica
    );
  };

  // recorremos todos los resultados ya ordenados
  for (const r of resultados) {
    if (r.orden === null) continue; // ignora registros que no tengan orden asignado

    // si es el primer registro o si es consecutivo al anterior y no cambia ningun campo clave
    if (
      lastOrden === null ||
      (r.orden === lastOrden + 1 &&
        grupoActual.length > 0 &&
        !cambiaCampoClave(r, grupoActual[grupoActual.length - 1]))
    ) {
      grupoActual.push(r); //se anade al grupo actual
    } else {
      // si ya no es consecutivo o cambio algun campo, se cierra el grupo actual
      if (grupoActual.length > 0) {
        const desde = grupoActual[0];                      // primer elemento del grupo
        const hasta = grupoActual[grupoActual.length - 1]; // ultimo elemento del grupo

        grupos.push({
          ...desde,
          desde: `${desde.tabla}-${desde.nave}-${desde.cama}`,
          hasta: `${hasta.tabla}-${hasta.nave}-${hasta.cama}`,
          total_camas: grupoActual.length,
          total_plantas: grupoActual.reduce((sum, item) => sum + (parseFloat(item.plantas) || 0), 0)
        });
      }
      grupoActual = [r]; // comienzo un nuevo grupo
    }

    lastOrden = r.orden; // actualizo el ultimo indice de orden usado
  }

  // al finalizar el recorrido, agrego el ultimo grupo si tiene datos
  if (grupoActual.length > 0) {
    const desde = grupoActual[0];
    const hasta = grupoActual[grupoActual.length - 1];
    grupos.push({
      ...desde,
      desde: `${desde.tabla}-${desde.nave}-${desde.cama}`,
      hasta: `${hasta.tabla}-${hasta.nave}-${hasta.cama}`,
      total_camas: grupoActual.length,
      total_plantas: grupoActual.reduce((sum, item) => sum + (parseFloat(item.plantas) || 0), 0)
    });
  }

  return grupos; // devuelvo todos los grupos generados
}

// Esta función es para cambiar la tarjeta al hacer clic en el icono de impresora
function toggleSeleccion(icono) {
  const item = icono.closest('.formato-item');
  const datos = item.dataset;

  console.log("Datos Seleccionados", datos);
  if (!item) return;

  item.classList.toggle('seleccionado');

  if (item.classList.contains('seleccionado')) {
    icono.classList.replace('bi-printer', 'bi-check2');
  } else {
    icono.classList.replace('bi-check2', 'bi-printer');
  }

  actualizarContadorSeleccionados();
}

function actualizarContadorSeleccionados(){
  const totalSeleccionados = document.querySelectorAll('.formato-item.seleccionado').length;

  const contadorModal = document.getElementById('contadorSeleccionados');
  if (contadorModal) {
    contadorModal.textContent = totalSeleccionados;
  }

  const contadorLateral = document.getElementById('totalSeleccionados');
  if (contadorLateral) {
    const desdeStorage = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];
    contadorLateral.textContent = desdeStorage.length;
  }
}

function marcarTarjetaSeleccionada() {
  const tarjetas = document.querySelectorAll('#contenedorTarjetas .card-custom');
  const seleccionados = JSON.parse(localStorage.getItem('bautizosSeleccionados')) || [];

  tarjetas.forEach(card => {
    const finca = card.dataset.finca;
    const bloque = card.dataset.bloque;
    const variedad = card.dataset.variedad;
    const temporada = card.dataset.temporada;
    const fecha_siembra = card.dataset.fecha_siembra;
    const origen = card.dataset.origen || '';

    const botonSeleccion = card.querySelector('.btn-select');
    const botonExpandir = card.querySelector('.btn-expand');

    const estaSeleccionado = seleccionados.some(sel =>
      sel.finca === finca &&
      sel.bloque === bloque &&
      sel.variedad === variedad &&
      sel.temporada === temporada &&
      (sel.origen || '') === origen &&
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

window.agruparPorUbicacion = agruparPorUbicacion;
window.toggleSeleccion = toggleSeleccion;
window.marcarTarjetaSeleccionada = marcarTarjetaSeleccionada;
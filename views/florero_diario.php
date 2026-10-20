<?php
// Seguimiento diario de floreros (optimizado para celular).
include_once('funciones/conexion.php');
?>
<style>
  .fvd { max-width: 640px; margin: 0 auto; padding: 8px 4px 90px; }
  .fvd-head { display: flex; gap: 8px; align-items: center; margin-bottom: 12px; }
  .fvd-head h4 { margin: 0; font-size: 1.1rem; color: #00796B; font-weight: 700; flex: 1; }
  .fvd-head input { max-width: 150px; }
  .fvd-card { background: #fff; border-radius: 14px; padding: 14px; margin-bottom: 10px;
              box-shadow: 0 2px 10px rgba(0,0,0,.07); border-left: 5px solid #00796B; cursor: pointer; }
  .fvd-card.done { border-left-color: #2e7d32; background: #f1f8f2; }
  .fvd-card .t { font-weight: 700; }
  .fvd-card .s { color: #6c757d; font-size: .85rem; }
  .fvd-badge { font-size: .75rem; padding: 2px 8px; border-radius: 10px; color: #fff; background: #00796B; margin-right: 4px; }
  .fvd-badge.sim { background: #ef6c00; }
  .fvd-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(64px, 1fr)); gap: 8px; margin: 12px 0; }
  .fvd-stem { border: 0; border-radius: 12px; height: 60px; font-weight: 700; font-size: 1.1rem; color: #fff;
              background: #b0bec5; position: relative; }
  .fvd-stem small { display: block; font-size: .6rem; font-weight: 500; }
  .fvd-stem.sel { outline: 4px solid #00796B; outline-offset: -4px; }
  .fvd-stem.fin { opacity: .85; }
  .fvd-stem.pend { background: #b0bec5; }
  .fvd-eval { margin-bottom: 14px; }
  .fvd-eval label { font-weight: 600; margin-bottom: 4px; }
  .fvd-eval select { height: 48px; font-size: 1rem; }
  .fvd-bar { position: fixed; left: 0; right: 0; bottom: 0; background: #fff; padding: 10px;
             box-shadow: 0 -2px 10px rgba(0,0,0,.1); z-index: 1030; display: flex; gap: 8px; }
  .fvd-bar .btn { flex: 1; padding: 12px; font-weight: 600; }
  .fvd-estado { display: block; width: 100%; margin-bottom: 8px; padding: 14px; border: 0; border-radius: 12px;
                color: #fff; font-weight: 700; font-size: 1rem; text-align: left; }
  .fvd-prog { height: 8px; border-radius: 4px; background: #e0e0e0; overflow: hidden; margin-top: 8px; }
  .fvd-prog > div { height: 100%; background: #2e7d32; transition: width .3s; }
  .fvd-steps { display: flex; gap: 6px; margin-bottom: 14px; }
  .fvd-steps span { flex: 1; height: 6px; border-radius: 3px; background: #e0e0e0; transition: background .2s; }
  .fvd-steps span.on { background: #00796B; }
  .fvd-paso { display: none; }
  .fvd-paso.on { display: block; }
  #fvdNuevoModal .form-control { height: 48px; font-size: 1rem; }
  #fvdNuevoModal textarea.form-control { height: auto; }
  #fvdNuevoModal .custom-switch { padding: 6px 0 6px 2.6rem; font-size: 1rem; }
  #fvdNuevoModal .modal-footer .btn { flex: 1; padding: 12px; font-weight: 600; }
</style>

<div class="fvd" id="fvd">
  <!-- Lista -->
  <div id="fvdLista">
    <div class="fvd-head">
      <h4>Revisión diaria de floreros</h4>
      <input type="date" id="fvdFecha" class="form-control form-control-sm">
    </div>
    <button class="btn btn-brand-green btn-block mb-3" id="fvdNuevo" style="padding:12px;font-weight:600">+ Nuevo florero</button>
    <div id="fvdItems"><p class="text-muted text-center">Cargando…</p></div>
  </div>

  <!-- Detalle -->
  <div id="fvdDetalle" style="display:none">
    <button class="btn btn-sm btn-outline-secondary mb-2" id="fvdVolver">&larr; Floreros</button>
    <div id="fvdInfo"></div>
    <div class="fvd-grid" id="fvdGrid"></div>
    <div class="fvd-bar" style="flex-wrap:wrap">
      <button class="btn btn-outline-secondary" id="fvdMulti">Selección múltiple</button>
      <button class="btn btn-outline-secondary" id="fvdPend" style="display:none">Pendientes</button>
      <button class="btn btn-success" id="fvdAsignar" style="display:none">Asignar causa</button>
      <button class="btn btn-brand-green" id="fvdEval" style="display:none">Hacer evaluación</button>
      <button class="btn btn-outline-secondary" id="fvdListo">Listo</button>
    </div>
  </div>
</div>

<!-- Selector de causa -->
<div class="modal fade" id="fvdModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px">
      <div class="modal-header">
        <h5 class="modal-title" id="fvdModalTitulo">Tallo</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="fvdModalBody"></div>
    </div>
  </div>
</div>

<!-- Evaluación final (cuando todos los tallos tienen causa) -->
<div class="modal fade" id="fvdEvalModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px">
      <div class="modal-header">
        <h5 class="modal-title">Evaluación del florero</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="fvdEvalBody"></div>
        <div class="text-danger small" id="fvdEvalError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-brand-green btn-block" id="fvdEvalGuardar" style="padding:12px;font-weight:600">Guardar y finalizar</button>
      </div>
    </div>
  </div>
</div>

<!-- Nuevo florero (por pasos) -->
<div class="modal fade" id="fvdNuevoModal" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius:16px">
      <div class="modal-header">
        <h5 class="modal-title" id="fvdNTitulo">Nuevo florero</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="fvd-steps"><span></span><span></span><span></span></div>

        <div class="fvd-paso" data-p="1">
          <div class="form-group"><label>Fecha de corte</label><input type="date" id="nFechaCorte" class="form-control"></div>
          <div class="form-group"><label>Fecha de florero</label><input type="date" id="nFechaFlorero" class="form-control"></div>
          <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="nSimulacion"><label class="custom-control-label" for="nSimulacion">Simulación de viaje</label></div>
          <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="nAgrupar"><label class="custom-control-label" for="nAgrupar">Agrupar con el anterior</label></div>
        </div>

        <div class="fvd-paso" data-p="2">
          <div class="form-group"><label>Flor</label><select id="nProducto" class="form-control"></select></div>
          <div class="form-group"><label>Variedad</label><select id="nVariedad" class="form-control"></select></div>
          <div class="form-group"><label>Tipo</label><select id="nTipo" class="form-control"></select></div>
          <div class="form-group"><label>Origen</label><select id="nOrigen" class="form-control"></select></div>
        </div>

        <div class="fvd-paso" data-p="3">
          <div class="row">
            <div class="form-group col-6"><label>Total tallos</label><input type="number" inputmode="numeric" min="1" id="nTallos" class="form-control"></div>
            <div class="form-group col-6"><label>Días de guarde</label><input type="number" inputmode="numeric" min="0" id="nGuarde" class="form-control"></div>
            <div class="form-group col-6"><label>Punto máx. apertura</label><input type="number" inputmode="numeric" id="nPmax" class="form-control"></div>
            <div class="form-group col-6"><label>Punto corte</label><input type="number" inputmode="numeric" id="nPcorte" class="form-control"></div>
            <div class="form-group col-6"><label>Punto empaque</label><input type="number" inputmode="numeric" id="nPempaque" class="form-control"></div>
            <div class="form-group col-6"><label>Punto florero</label><input type="number" inputmode="numeric" id="nPflorero" class="form-control"></div>
          </div>
          <div class="form-group"><label>Comentarios</label><textarea id="nComentario" rows="2" class="form-control"></textarea></div>
        </div>
        <div class="text-danger small" id="nError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" id="nAtras">Atrás</button>
        <button type="button" class="btn btn-brand-green" id="nSiguiente">Siguiente</button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var PALETA = ['#c62828', '#6a1b9a', '#ef6c00', '#8d6e63', '#ec407a', '#00796b', '#455a64', '#f9a825', '#1565c0', '#558b2f'];
  function colorCausa(id) {
    var i = cur.causas.findIndex(function (c) { return +c.id === +id; });
    return PALETA[(i < 0 ? 0 : i) % PALETA.length];
  }
  var API = 'ajax/fv_daily.php';
  var cur = null, tallos = {}, multi = false, sel = {};
  var $ = function (id) { return document.getElementById(id); };

  var hoy = new Date(); hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());
  $('fvdFecha').value = hoy.toISOString().slice(0, 10);

  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

  function post(data) {
    var body = new URLSearchParams(data);
    return fetch(API, { method: 'POST', body: body, credentials: 'same-origin' }).then(function (r) { return r.json(); });
  }

  function cargarLista() {
    fetch(API + '?accion=list&fecha=' + $('fvdFecha').value, { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d.ok) { $('fvdItems').innerHTML = '<p class="text-danger">' + esc(d.error) + '</p>'; return; }
        if (!d.floreros.length) { $('fvdItems').innerHTML = '<p class="text-muted text-center">No hay floreros activos en esta fecha.</p>'; return; }
        $('fvdItems').innerHTML = d.floreros.map(function (f) {
          var fin = !!f.fecha_final, n = +f.tallos, k = +f.con_causa;
          var pct = n > 0 ? Math.min(100, Math.round(k * 100 / n)) : 0;
          var estado = fin ? '<span class="fvd-badge" style="background:#2e7d32">Finalizado</span>'
            : (k >= n && n > 0 ? '<span class="fvd-badge sim">Falta evaluación</span>'
              : '<span class="fvd-badge" style="background:#607d8b">Faltan ' + (n - k) + ' tallos</span>');
          return '<div class="fvd-card ' + (fin ? 'done' : '') + '" data-id="' + f.id + '">' +
            '<div class="t">' + esc(f.variedad) + '</div>' +
            '<div class="s"><span class="fvd-badge">Día ' + f.dia + '</span>' + estado +
            (+f.simulacion ? '<span class="fvd-badge sim">Con viaje</span>' : '<span class="fvd-badge">Sin viaje</span>') +
            esc(f.tipo) + ' · ' + n + ' tallos · ' + esc(f.fecha_florero) + '</div>' +
            '<div class="fvd-prog"><div style="width:' + pct + '%"></div></div></div>';
        }).join('');
      });
  }

  function conCausa() { return Object.keys(tallos).length; }
  function pendientes() {
    var out = [], n = +cur.florero.tallos;
    for (var i = 1; i <= n; i++) { if (!tallos[i]) out.push(i); }
    return out;
  }

  function pintarGrid() {
    var n = +cur.florero.tallos, h = '', fin = !!cur.finalizado;
    for (var i = 1; i <= n; i++) {
      var c = tallos[i];
      h += '<button class="fvd-stem' + (c ? '' : ' pend') + (sel[i] ? ' sel' : '') + (fin ? ' fin' : '') + '" ' +
           (c ? 'style="background:' + colorCausa(c.causes_item_id) + '" ' : '') + 'data-t="' + i + '">' + i +
           '<small>' + (c ? esc(c.causa) + ' · d' + c.dias : 'pendiente') + '</small></button>';
    }
    $('fvdGrid').innerHTML = h;
    pintarInfo();
    pintarBarra();
  }

  function pintarInfo() {
    var f = cur.florero, n = +f.tallos, k = conCausa(), falta = n - k;
    var msg = cur.finalizado ? '<div class="s" style="color:#2e7d32;font-weight:600">Florero finalizado el ' + esc(cur.finalizado) + '</div>'
      : (falta > 0 ? '<div class="s">Causas registradas: <b>' + k + '/' + n + '</b> · faltan <b>' + falta + '</b></div>'
        : '<div class="s" style="color:#ef6c00;font-weight:600">Todos los tallos tienen causa. Falta la evaluación.</div>');
    $('fvdInfo').innerHTML = '<div class="fvd-card" style="cursor:default"><div class="t">' + esc(f.variedad) + '</div>' +
      '<div class="s"><span class="fvd-badge">Día ' + f.dia + '</span>' +
      (+f.simulacion ? '<span class="fvd-badge sim">Con viaje</span>' : '<span class="fvd-badge">Sin viaje</span>') +
      esc(f.tipo) + ' · ' + n + ' tallos</div>' + msg +
      '<div class="fvd-prog"><div style="width:' + (n ? Math.round(k * 100 / n) : 0) + '%"></div></div></div>';
  }

  function nSel() { return Object.keys(sel).length; }

  function pintarBarra() {
    var fin = !!cur.finalizado, completo = pendientes().length === 0;
    $('fvdMulti').style.display = fin ? 'none' : '';
    $('fvdMulti').textContent = multi ? 'Cancelar selección' : 'Selección múltiple';
    $('fvdPend').style.display = multi ? '' : 'none';
    $('fvdAsignar').style.display = multi ? '' : 'none';
    $('fvdAsignar').textContent = 'Asignar causa (' + nSel() + ')';
    $('fvdEval').style.display = (!fin && completo && !multi) ? '' : 'none';
  }

  function abrir(id, mantener) {
    fetch(API + '?accion=get&fv_id=' + id + '&fecha=' + $('fvdFecha').value, { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d.ok) { alert(d.error); return; }
        cur = d; tallos = d.tallos || {}; multi = false; sel = {};
        pintarGrid();
        $('fvdLista').style.display = 'none';
        $('fvdDetalle').style.display = 'block';
        if (!mantener) window.scrollTo(0, 0);
      });
  }

  function volver() {
    $('fvdDetalle').style.display = 'none';
    $('fvdLista').style.display = 'block';
    cargarLista();
  }

  function elegirCausa(lista) {
    $('fvdModalTitulo').textContent = lista.length === 1 ? 'Tallo ' + lista[0] : lista.length + ' tallos';
    var h = '<p class="text-muted small">Causa de fin de florero · día ' + cur.florero.dia + ' (' + esc($('fvdFecha').value) + ')</p>';
    h += cur.causas.map(function (c) {
      return '<button class="fvd-estado" style="background:' + colorCausa(c.id) + '" data-c="' + c.id + '">' + esc(c.nombre) + '</button>';
    }).join('');
    if (!cur.causas.length) h += '<p class="text-danger">No hay causas configuradas (Tablas → CRUD Causas).</p>';
    if (lista.some(function (t) { return tallos[t]; })) {
      h += '<button class="fvd-estado" style="background:#90a4ae" data-c="0">Quitar causa (volver a pendiente)</button>';
    }
    $('fvdModalBody').innerHTML = h;
    $('fvdModalBody').dataset.t = lista.join(',');
    jQuery('#fvdModal').modal('show');
  }

  $('fvdItems').addEventListener('click', function (e) {
    var c = e.target.closest('.fvd-card'); if (c) abrir(c.dataset.id);
  });
  $('fvdGrid').addEventListener('click', function (e) {
    var b = e.target.closest('.fvd-stem'); if (!b || cur.finalizado) return;
    var t = b.dataset.t;
    if (multi) {
      if (sel[t]) delete sel[t]; else sel[t] = true;
      pintarGrid();
    } else {
      elegirCausa([+t]);
    }
  });
  $('fvdMulti').addEventListener('click', function () { multi = !multi; sel = {}; pintarGrid(); });
  $('fvdPend').addEventListener('click', function () {
    sel = {}; pendientes().forEach(function (t) { sel[t] = true; }); pintarGrid();
  });
  $('fvdAsignar').addEventListener('click', function () {
    var l = Object.keys(sel).map(Number);
    if (!l.length) { alert('Seleccione al menos un tallo'); return; }
    elegirCausa(l);
  });
  $('fvdModalBody').addEventListener('click', function (e) {
    var b = e.target.closest('.fvd-estado'); if (!b) return;
    post({ accion: 'set_cause', fv_id: cur.florero.id, fecha: $('fvdFecha').value, tallos: $('fvdModalBody').dataset.t, causa_id: b.dataset.c })
      .then(function (d) {
        if (!d.ok) { alert(d.error || 'No se pudo guardar'); return; }
        jQuery('#fvdModal').modal('hide');
        abrir(cur.florero.id, true);
      });
  });

  // Evaluación: solo disponible cuando todos los tallos tienen causa.
  $('fvdEval').addEventListener('click', function () {
    $('fvdEvalError').textContent = '';
    $('fvdEvalBody').innerHTML = (cur.items || []).map(function (it, i) {
      return '<div class="fvd-eval"><label>' + esc(it.nombre) + '</label><select class="form-control" data-nombre="' + esc(it.nombre) + '">' +
        '<option value="">Seleccione…</option>' +
        it.opciones.map(function (o) { return '<option value="' + o.id + '">' + esc(o.puntaje) + '</option>'; }).join('') +
        '</select></div>';
    }).join('') || '<p class="text-muted">No hay ítems de evaluación configurados.</p>';
    jQuery('#fvdEvalModal').modal('show');
  });
  $('fvdEvalGuardar').addEventListener('click', function () {
    var items = {}, falta = false;
    document.querySelectorAll('#fvdEvalBody select').forEach(function (s) {
      if (!s.value) falta = true; else items[s.dataset.nombre] = +s.value;
    });
    if (falta) { $('fvdEvalError').textContent = 'Evalúe todos los ítems'; return; }
    var b = $('fvdEvalGuardar'); b.disabled = true;
    post({ accion: 'finalize', fv_id: cur.florero.id, fecha: $('fvdFecha').value, items: JSON.stringify(items) })
      .then(function (d) {
        b.disabled = false;
        if (!d.ok) { $('fvdEvalError').textContent = d.error || 'No se pudo finalizar'; return; }
        jQuery('#fvdEvalModal').modal('hide');
        volver();
      }).catch(function () { b.disabled = false; $('fvdEvalError').textContent = 'Error de conexión'; });
  });

  $('fvdListo').addEventListener('click', volver);
  $('fvdVolver').addEventListener('click', volver);
  $('fvdFecha').addEventListener('change', cargarLista);

  cargarLista();

  // ---------- Nuevo florero (modal por pasos) ----------
  var paso = 1, TOTAL = 3, catalogosListos = false;

  function opciones(sel, items, placeholder, valFn, txtFn) {
    $(sel).innerHTML = '<option value="">' + placeholder + '</option>' +
      items.map(function (i) { return '<option value="' + esc(valFn(i)) + '">' + esc(txtFn(i)) + '</option>'; }).join('');
  }

  function cargarCatalogos() {
    if (catalogosListos) return Promise.resolve();
    return fetch(API + '?accion=catalogs', { credentials: 'same-origin' }).then(function (r) { return r.json(); })
      .then(function (d) {
        opciones('nProducto', d.productos, 'Seleccione flor', function (x) { return x; }, function (x) { return x; });
        opciones('nTipo', d.tipos, 'Seleccione tipo', function (x) { return x.id; }, function (x) { return x.nombre; });
        opciones('nOrigen', d.origenes, 'Seleccione origen', function (x) { return x.id; }, function (x) { return x.nombre; });
        opciones('nVariedad', [], 'Primero elija la flor', function () {}, function () {});
        catalogosListos = true;
      });
  }

  $('nProducto').addEventListener('change', function () {
    if (!this.value) { opciones('nVariedad', [], 'Primero elija la flor', function () {}, function () {}); return; }
    fetch(API + '?accion=variedades&producto=' + encodeURIComponent(this.value), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) { opciones('nVariedad', d.variedades, 'Seleccione variedad', function (x) { return x.id; }, function (x) { return x.nombre; }); });
  });

  function mostrarPaso() {
    document.querySelectorAll('#fvdNuevoModal .fvd-paso').forEach(function (p) { p.classList.toggle('on', +p.dataset.p === paso); });
    document.querySelectorAll('#fvdNuevoModal .fvd-steps span').forEach(function (s, i) { s.classList.toggle('on', i < paso); });
    $('fvdNTitulo').textContent = 'Nuevo florero (' + paso + '/' + TOTAL + ')';
    $('nAtras').style.visibility = paso === 1 ? 'hidden' : 'visible';
    $('nSiguiente').textContent = paso === TOTAL ? 'Guardar' : 'Siguiente';
    $('nError').textContent = '';
  }

  function validarPaso() {
    if (paso === 1 && (!$('nFechaCorte').value || !$('nFechaFlorero').value)) return 'Indique ambas fechas';
    if (paso === 2 && (!$('nVariedad').value || !$('nTipo').value || !$('nOrigen').value)) return 'Seleccione flor, variedad, tipo y origen';
    if (paso === 3 && !(+$('nTallos').value > 0)) return 'Indique el total de tallos';
    return '';
  }

  function guardarNuevo() {
    var b = $('nSiguiente'); b.disabled = true;
    post({
      accion: 'create',
      fecha_corte: $('nFechaCorte').value, fecha_florero: $('nFechaFlorero').value,
      simulacion: $('nSimulacion').checked ? 1 : '', agrupar: $('nAgrupar').checked ? 1 : '',
      variedad_id: $('nVariedad').value, tipo_id: $('nTipo').value, origen_id: $('nOrigen').value,
      tallos: $('nTallos').value, guarde: $('nGuarde').value, pmax: $('nPmax').value,
      pcorte: $('nPcorte').value, pempaque: $('nPempaque').value, pflorero: $('nPflorero').value,
      comentario: $('nComentario').value
    }).then(function (d) {
      b.disabled = false;
      if (!d.ok) { $('nError').textContent = d.error || 'No se pudo guardar'; return; }
      jQuery('#fvdNuevoModal').modal('hide');
      // Si el florero es posterior a la fecha mostrada, mover la fecha para que aparezca en la lista.
      if ($('nFechaFlorero').value > $('fvdFecha').value) $('fvdFecha').value = $('nFechaFlorero').value;
      cargarLista();
    }).catch(function () { b.disabled = false; $('nError').textContent = 'Error de conexión'; });
  }

  $('fvdNuevo').addEventListener('click', function () {
    cargarCatalogos().then(function () {
      ['nTallos', 'nGuarde', 'nPmax', 'nPcorte', 'nPempaque', 'nPflorero', 'nComentario'].forEach(function (id) { $(id).value = ''; });
      $('nSimulacion').checked = false; $('nAgrupar').checked = false;
      $('nFechaCorte').value = $('nFechaFlorero').value = $('fvdFecha').value;
      $('nProducto').value = ''; opciones('nVariedad', [], 'Primero elija la flor', function () {}, function () {});
      $('nTipo').value = ''; $('nOrigen').value = '';
      paso = 1; mostrarPaso();
      jQuery('#fvdNuevoModal').modal('show');
    });
  });
  $('nAtras').addEventListener('click', function () { if (paso > 1) { paso--; mostrarPaso(); } });
  $('nSiguiente').addEventListener('click', function () {
    var err = validarPaso();
    if (err) { $('nError').textContent = err; return; }
    if (paso < TOTAL) { paso++; mostrarPaso(); } else { guardarNuevo(); }
  });
})();
</script>

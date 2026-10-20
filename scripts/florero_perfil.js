/* Reporte interactivo "Perfil de Variedades" (floreros).
 * Datos crudos desde ajax/fv_report2.php; toda la agregación se calcula aquí para filtrar al instante.
 * Convenciones (las mismas del reporte anterior):
 *  - causa id 1 = "final óptimo"; cualquier otra causa es inconformidad.
 *  - calificación = promedio de puntajes de evaluación / puntaje máximo * 100.
 *  - vida = promedio de días ponderado por la cantidad de tallos de cada causa.
 */
(function () {
  'use strict';

  var API = 'ajax/fv_report2.php';
  var C_CON = '#ef6c00', C_SIN = '#00796B', C_ALL = '#546e7a';
  var PALETA = ['#00796B', '#ef6c00', '#6a1b9a', '#1565c0', '#c62828', '#558b2f', '#ad1457', '#455a64'];
  var MAX_CMP = 6;

  var $ = function (id) { return document.getElementById(id); };
  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
  function f1(x) { return (x == null || isNaN(x)) ? '—' : (Math.round(x * 10) / 10).toFixed(1); }
  function f0(x) { return (x == null || isNaN(x)) ? '—' : String(Math.round(x)); }
  function pct(x) { return (x == null || isNaN(x)) ? '—' : f0(x) + '%'; }

  var ALL = [], FIL = [], MAXD = 21, MAXP = 3;
  var st = { tab: 'ranking', sort: { k: 'vida', dir: -1 }, sel: '', cmp: [], cmpViaje: '' };
  var charts = {};

  if (typeof Chart === 'undefined') {
    $('fpMain').innerHTML = '<div class="fp-empty">No se pudo cargar la librería de gráficos.</div>';
    return;
  }

  // ---------- Gráficos ----------
  function killChart(id) { if (charts[id]) { charts[id].destroy(); delete charts[id]; } }
  function mk(id, cfg) {
    killChart(id);
    var el = $(id); if (!el) return;
    cfg.options = cfg.options || {};
    cfg.options.responsive = true;
    cfg.options.maintainAspectRatio = false;
    charts[id] = new Chart(el.getContext('2d'), cfg);
  }
  function killAll() { Object.keys(charts).forEach(killChart); }

  // ---------- Datos ----------
  function groupBy(arr, fn) {
    var o = {};
    arr.forEach(function (x) { var k = fn(x); (o[k] = o[k] || []).push(x); });
    return o;
  }

  function procesar(raw) {
    var map = {};
    ALL = raw.floreros.map(function (f) {
      f.id = +f.id; f.tallos = +f.tallos;
      f.viaje = +f.simulacion ? 1 : 0; f.granel = +f.guarde_granel ? 1 : 0;
      f.variedad = f.variedad || '(sin variedad)';
      f.causas = []; f.evals = [];
      map[f.id] = f;
      return f;
    });
    MAXP = 3; MAXD = 7;
    raw.causas.forEach(function (c) {
      var f = map[c.fv_id]; if (!f) return;
      var o = { id: +c.causes_item_id, nombre: c.causa || ('Causa ' + c.causes_item_id), dias: +c.dias, cant: +c.cantidad };
      f.causas.push(o);
      if (o.cant > 0 && o.dias > MAXD) MAXD = Math.min(o.dias, 30);
    });
    raw.evals.forEach(function (e) {
      var f = map[e.fv_id]; if (!f) return;
      var p = +e.puntaje;
      f.evals.push({ item: e.item, p: p });
      if (p > MAXP) MAXP = p;
    });
    ALL.forEach(function (f) { f.m = agg([f]); });
  }

  function agg(list) {
    var dist = {}, cau = {}, tot = 0, sumd = 0, ev = {}, scores = [], vidas = [];
    list.forEach(function (f) {
      var ft = 0, fd = 0;
      f.causas.forEach(function (c) {
        if (!(c.cant > 0)) return;
        var d = Math.min(c.dias, 30);
        dist[d] = (dist[d] || 0) + c.cant;
        var o = cau[c.id] = cau[c.id] || { id: c.id, nombre: c.nombre, cant: 0 };
        o.cant += c.cant;
        tot += c.cant; sumd += d * c.cant; ft += c.cant; fd += d * c.cant;
      });
      if (ft > 0) vidas.push(fd / ft);
      if (f.evals.length) {
        var s = 0;
        f.evals.forEach(function (e) {
          s += e.p;
          var o = ev[e.item] = ev[e.item] || { s: 0, n: 0 };
          o.s += e.p; o.n++;
        });
        scores.push(s / f.evals.length / MAXP * 100);
      }
    });
    var mean = vidas.length ? vidas.reduce(function (a, b) { return a + b; }, 0) / vidas.length : null;
    var sd = null;
    if (vidas.length > 1) {
      sd = Math.sqrt(vidas.reduce(function (a, b) { return a + (b - mean) * (b - mean); }, 0) / (vidas.length - 1));
    }
    return {
      nf: list.length, tot: tot, vida: tot ? sumd / tot : null, dist: dist, cau: cau, ev: ev, sd: sd, vmean: mean,
      score: scores.length ? scores.reduce(function (a, b) { return a + b; }, 0) / scores.length : null
    };
  }

  function optimo(a) { return a.tot ? ((a.cau[1] ? a.cau[1].cant : 0) / a.tot * 100) : null; }

  function reach(a, d) {
    if (!a.tot) return null;
    var s = 0;
    Object.keys(a.dist).forEach(function (k) { if (+k >= d) s += a.dist[k]; });
    return s / a.tot * 100;
  }
  function survival(a) {
    var out = [];
    for (var d = 1; d <= MAXD; d++) out.push(a.tot ? reach(a, d) : null);
    return out;
  }
  // Día (entero) en que se acumula al menos p de los tallos terminados.
  function quant(a, p) {
    if (!a.tot) return null;
    var ds = Object.keys(a.dist).map(Number).sort(function (x, y) { return x - y; }), c = 0;
    for (var i = 0; i < ds.length; i++) { c += a.dist[ds[i]]; if (c >= p * a.tot - 1e-9) return ds[i]; }
    return ds[ds.length - 1];
  }
  function deltaCls(d) { return d == null ? '' : (d >= 0 ? 'fp-pos' : 'fp-neg'); }
  function sgn(d) { return d == null ? '—' : (d >= 0 ? '+' : '') + f1(d); }

  // ---------- Filtros ----------
  function llenarSelect(id, vals, etiqueta) {
    var el = $(id), prev = el.value;
    el.innerHTML = '<option value="">' + etiqueta + '</option>' + vals.map(function (v) {
      return '<option>' + esc(v) + '</option>';
    }).join('');
    if (vals.indexOf(prev) >= 0) el.value = prev;
  }
  function uniq(arr) {
    var o = {}; arr.forEach(function (x) { if (x) o[x] = 1; });
    return Object.keys(o).sort();
  }

  function aplicar() {
    var b = $('fpBuscar').value.trim().toLowerCase(), fl = $('fpFlor').value, or = $('fpOrigen').value,
      ti = $('fpTipo').value, vi = $('fpViaje').value, gr = $('fpGranel').value, de = $('fpDesc').value.trim().toLowerCase();
    FIL = ALL.filter(function (f) {
      return (!b || f.variedad.toLowerCase().indexOf(b) >= 0) && (!fl || f.producto === fl) && (!or || f.origen === or) &&
        (!ti || f.tipo === ti) && (vi === '' || f.viaje === +vi) && (gr === '' || f.granel === +gr) &&
        (!de || (f.grupo_descripcion || '').toLowerCase().indexOf(de) >= 0);
    });
    var vars = uniq(FIL.map(function (f) { return f.variedad; }));
    if (vars.indexOf(st.sel) < 0) st.sel = vars[0] || '';
    st.cmp = st.cmp.filter(function (v) { return vars.indexOf(v) >= 0; });
    renderKpis();
    renderTab();
  }

  // ---------- KPIs ----------
  function renderKpis() {
    var a = agg(FIL), c = agg(FIL.filter(function (f) { return f.viaje; })), s = agg(FIL.filter(function (f) { return !f.viaje; }));
    var nv = uniq(FIL.map(function (f) { return f.variedad; })).length;
    function k(cls, t, v, sub) { return '<div class="fp-kpi ' + cls + '"><small>' + t + '</small><b>' + v + '</b><span>' + sub + '</span></div>'; }
    $('fpKpis').innerHTML =
      k('', 'Floreros', FIL.length, nv + ' variedades') +
      k('neu', 'Tallos evaluados', a.tot.toLocaleString(), 'con causa registrada') +
      k('', 'Vida media', f1(a.vida) + ' d', 'mitad de tallos &lt; día ' + (quant(a, .5) == null ? '—' : quant(a, .5))) +
      k('alt', 'Con viaje / Sin viaje', f1(c.vida) + ' / ' + f1(s.vida) + ' d', 'Δ ' + (c.vida != null && s.vida != null ? sgn(c.vida - s.vida) + ' d' : '—')) +
      k('', 'Final óptimo', pct(optimo(a)), 'tallos que terminan bien') +
      k('neu', 'Calificación', pct(a.score), 'evaluación promedio');
  }

  // ---------- Pestañas ----------
  function renderTab() {
    killAll();
    document.querySelectorAll('.fp-tab').forEach(function (b) { b.classList.toggle('on', b.dataset.tab === st.tab); });
    if (!FIL.length) { $('fpMain').innerHTML = '<div class="fp-card fp-empty">No hay floreros con esos filtros.</div>'; return; }
    ({ ranking: tabRanking, perfil: tabPerfil, comparar: tabComparar, viaje: tabViaje, matriz: tabMatriz })[st.tab]();
    var n = st.cmp.length; $('fpCmpN').style.display = n ? '' : 'none'; $('fpCmpN').textContent = n;
  }
  function go(tab) { st.tab = tab; renderTab(); window.scrollTo(0, 0); }
  function goPerfil(v) { st.sel = v; go('perfil'); }

  // ---------- Ranking ----------
  function rankRows() {
    var by = groupBy(FIL, function (f) { return f.variedad; });
    return Object.keys(by).map(function (v) {
      var l = by[v], a = agg(l), c = agg(l.filter(function (f) { return f.viaje; })), s = agg(l.filter(function (f) { return !f.viaje; }));
      return {
        v: v, nf: l.length, tot: a.tot, vida: a.vida,
        vc: c.nf ? c.vida : null, vs: s.nf ? s.vida : null,
        d: (c.vida != null && s.vida != null) ? c.vida - s.vida : null,
        t10: quant(a, .1), opt: optimo(a), score: a.score,
        spark: l.slice().sort(function (x, y) { return x.fecha_florero < y.fecha_florero ? -1 : 1; })
          .map(function (f) { return f.m.vida; }).filter(function (x) { return x != null; })
      };
    });
  }

  function drawSpark(cv, arr) {
    var ctx = cv.getContext('2d'), w = cv.width, h = cv.height;
    ctx.clearRect(0, 0, w, h);
    if (arr.length < 2) { ctx.fillStyle = '#b0bec5'; ctx.fillRect(0, h / 2, w, 1); return; }
    var mn = Math.min.apply(null, arr), mx = Math.max.apply(null, arr), r = (mx - mn) || 1;
    ctx.beginPath();
    arr.forEach(function (y, i) {
      var px = 2 + i * (w - 4) / (arr.length - 1), py = h - 3 - (y - mn) / r * (h - 6);
      if (i) ctx.lineTo(px, py); else ctx.moveTo(px, py);
    });
    ctx.strokeStyle = arr[arr.length - 1] >= arr[0] ? '#2e7d32' : '#c62828'; ctx.lineWidth = 1.6; ctx.stroke();
  }

  function tabRanking() {
    var rows = rankRows(), k = st.sort.k, dir = st.sort.dir;
    rows.sort(function (a, b) {
      var x = a[k], y = b[k];
      if (x == null && y == null) return 0; if (x == null) return 1; if (y == null) return -1;
      if (typeof x === 'string') return dir * x.localeCompare(y);
      return dir * (x - y);
    });
    var maxV = rows.reduce(function (m, r) { return Math.max(m, r.vida || 0); }, 0) || 1;
    var cols = [['v', 'Variedad'], ['nf', 'Floreros'], ['tot', 'Tallos'], ['vida', 'Vida (d)'], ['vc', 'Con viaje'], ['vs', 'Sin viaje'],
      ['d', 'Δ viaje'], ['t10', 'Día 10% pérdida'], ['opt', '% Óptimo'], ['score', 'Calif.']];
    var h = '<div class="fp-card"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">Ranking de variedades <small>· clic en una fila para ver su perfil, marque para comparar</small></h6>' +
      '<button class="btn btn-outline-secondary btn-sm" id="fpCsv">Exportar CSV</button></div><div class="fp-scroll"><table class="fp-t"><thead><tr><th></th>' +
      cols.map(function (c) { return '<th class="srt" data-k="' + c[0] + '">' + c[1] + (st.sort.k === c[0] ? (dir > 0 ? ' ▲' : ' ▼') : '') + '</th>'; }).join('') +
      '<th>Tendencia</th></tr></thead><tbody>' +
      rows.map(function (r, i) {
        return '<tr class="clk" data-v="' + esc(r.v) + '"><td style="text-align:center"><input type="checkbox" class="fpCk" data-v="' + esc(r.v) + '"' + (st.cmp.indexOf(r.v) >= 0 ? ' checked' : '') + '></td>' +
          '<td><b>' + esc(r.v) + '</b></td><td>' + r.nf + '</td><td>' + r.tot + '</td>' +
          '<td>' + f1(r.vida) + '<span class="fp-bar" style="width:' + Math.round((r.vida || 0) / maxV * 60) + 'px"></span></td>' +
          '<td>' + f1(r.vc) + '</td><td>' + f1(r.vs) + '</td><td class="' + deltaCls(r.d) + '">' + sgn(r.d) + '</td>' +
          '<td>' + (r.t10 == null ? '—' : 'd' + r.t10) + '</td><td>' + pct(r.opt) + '</td><td>' + pct(r.score) + '</td>' +
          '<td><canvas width="90" height="26" data-s="' + i + '"></canvas></td></tr>';
      }).join('') + '</tbody></table></div></div>';
    $('fpMain').innerHTML = h;
    document.querySelectorAll('#fpMain canvas[data-s]').forEach(function (cv) { drawSpark(cv, rows[+cv.dataset.s].spark); });
    document.querySelectorAll('#fpMain th.srt').forEach(function (th) {
      th.onclick = function () {
        var kk = th.dataset.k;
        st.sort = { k: kk, dir: st.sort.k === kk ? -st.sort.dir : (kk === 'v' ? 1 : -1) };
        renderTab();
      };
    });
    document.querySelectorAll('#fpMain tr.clk').forEach(function (tr) {
      tr.onclick = function (e) { if (e.target.classList.contains('fpCk')) return; goPerfil(tr.dataset.v); };
    });
    document.querySelectorAll('#fpMain .fpCk').forEach(function (ck) {
      ck.onchange = function () {
        var v = ck.dataset.v, i = st.cmp.indexOf(v);
        if (ck.checked) {
          if (st.cmp.length >= MAX_CMP) { ck.checked = false; alert('Máximo ' + MAX_CMP + ' variedades para comparar'); return; }
          st.cmp.push(v);
        } else if (i >= 0) st.cmp.splice(i, 1);
        $('fpCmpN').style.display = st.cmp.length ? '' : 'none'; $('fpCmpN').textContent = st.cmp.length;
      };
    });
    $('fpCsv').onclick = function () {
      var escCsv = function (val) {
        if (val == null) return '';
        var s = String(val);
        if (s.indexOf(';') >= 0 || s.indexOf('"') >= 0 || s.indexOf('\n') >= 0) {
          return '"' + s.replace(/"/g, '""') + '"';
        }
        return s;
      };
      var out = [cols.map(function (c) { return escCsv(c[1]); }).join(';')];
      rows.forEach(function (r) {
        out.push([
          escCsv(r.v), r.nf, r.tot, f1(r.vida), f1(r.vc), f1(r.vs), f1(r.d),
          (r.t10 == null ? '' : 'd' + r.t10), f0(r.opt) + '%', f0(r.score) + '%'
        ].join(';'));
      });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(new Blob(['\ufeff' + out.join('\n')], { type: 'text/csv;charset=utf-8' }));
      a.download = 'perfil_variedades.csv'; a.click();
    };
  }

  // ---------- Gráficos reutilizables ----------
  function survivalChart(id, series) {
    var labels = []; for (var d = 1; d <= MAXD; d++) labels.push(d);
    mk(id, {
      type: 'line',
      data: {
        labels: labels, datasets: series.map(function (s) {
          return { label: s.label, data: s.data, borderColor: s.color, backgroundColor: 'transparent', borderWidth: 2.5, lineTension: .15, pointRadius: 2, spanGaps: true, borderDash: s.dash || [] };
        })
      },
      options: {
        scales: {
          yAxes: [{ ticks: { min: 0, max: 100, callback: function (v) { return v + '%'; } }, scaleLabel: { display: true, labelString: '% de tallos aún en florero' } }],
          xAxes: [{ scaleLabel: { display: true, labelString: 'Día de vida' } }]
        },
        tooltips: { mode: 'index', intersect: false, callbacks: { label: function (t, d) { return d.datasets[t.datasetIndex].label + ': ' + f0(t.yLabel) + '%'; } } }
      }
    });
  }

  function radarChart(id, series) {
    var items = {};
    series.forEach(function (s) { Object.keys(s.a.ev).forEach(function (k) { items[k] = 1; }); });
    var labels = Object.keys(items);
    if (!labels.length) { $(id).parentNode.innerHTML = '<div class="fp-empty">Sin evaluaciones registradas</div>'; return; }
    mk(id, {
      type: 'radar',
      data: {
        labels: labels, datasets: series.map(function (s) {
          return {
            label: s.label, borderColor: s.color, backgroundColor: s.color + '33', borderWidth: 2, pointRadius: 3,
            data: labels.map(function (k) { return s.a.ev[k] ? s.a.ev[k].s / s.a.ev[k].n / MAXP * 100 : null; })
          };
        })
      },
      options: {
        scale: { ticks: { min: 0, max: 100, stepSize: 25 }, pointLabels: { fontSize: 11 } },
        tooltips: { callbacks: { label: function (t, d) { return d.datasets[t.datasetIndex].label + ': ' + f0(t.yLabel) + '%'; } } }
      }
    });
  }

  // Barras horizontales: % de tallos por causa, una serie por escenario/variedad.
  function causesChart(id, series) {
    var tot = {};
    series.forEach(function (s) { Object.keys(s.a.cau).forEach(function (k) { tot[k] = (tot[k] || 0) + s.a.cau[k].cant; }); });
    var ids = Object.keys(tot).sort(function (x, y) { return tot[y] - tot[x]; }).slice(0, 12);
    var name = {};
    series.forEach(function (s) { Object.keys(s.a.cau).forEach(function (k) { name[k] = s.a.cau[k].nombre; }); });
    mk(id, {
      type: 'horizontalBar',
      data: {
        labels: ids.map(function (k) { return name[k]; }), datasets: series.map(function (s) {
          return { label: s.label, backgroundColor: s.color, data: ids.map(function (k) { return s.a.cau[k] && s.a.tot ? s.a.cau[k].cant / s.a.tot * 100 : 0; }) };
        })
      },
      options: {
        scales: { xAxes: [{ ticks: { min: 0, callback: function (v) { return v + '%'; } } }] },
        tooltips: { callbacks: { label: function (t, d) { return d.datasets[t.datasetIndex].label + ': ' + f1(t.xLabel) + '%'; } } }
      }
    });
  }

  function metricasTabla(cols) {
    var rows = [
      ['Floreros', function (a) { return a.nf; }],
      ['Tallos evaluados', function (a) { return a.tot; }],
      ['Vida media (d)', function (a) { return f1(a.vida); }],
      ['Mediana (d)', function (a) { return quant(a, .5) == null ? '—' : quant(a, .5); }],
      ['Día en que se pierde el 10%', function (a) { return quant(a, .1) == null ? '—' : quant(a, .1); }],
      ['% llega a 7 días', function (a) { return pct(reach(a, 7)); }],
      ['% llega a 10 días', function (a) { return pct(reach(a, 10)); }],
      ['% llega a 14 días', function (a) { return pct(reach(a, 14)); }],
      ['Final óptimo', function (a) { return pct(optimo(a)); }],
      ['Calificación', function (a) { return pct(a.score); }]
    ];
    return '<table class="fp-t"><thead><tr><th>Métrica</th>' + cols.map(function (c) { return '<th>' + c.label + '</th>'; }).join('') + '</tr></thead><tbody>' +
      rows.map(function (r) { return '<tr><td>' + r[0] + '</td>' + cols.map(function (c) { return '<td>' + r[1](c.a) + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table>';
  }

  // ---------- Perfil ----------
  function insights(l) {
    var a = agg(l), c = agg(l.filter(function (f) { return f.viaje; })), s = agg(l.filter(function (f) { return !f.viaje; })), g = agg(FIL), out = [];
    if (a.vida == null) return ['Esta variedad aún no tiene causas de fin registradas.'];
    out.push('Vida media <b>' + f1(a.vida) + ' d</b> en ' + a.nf + ' florero(s) y ' + a.tot + ' tallos; la mitad de los tallos termina hacia el día <b>' + quant(a, .5) + '</b>.');
    out.push('Empieza a perder tallos (primer 10%) hacia el día <b>' + quant(a, .1) + '</b>; el <b>' + pct(reach(a, 10)) + '</b> llega a 10 días y el <b>' + pct(reach(a, 14)) + '</b> a 14.');
    if (g.vida != null && a.vida != null) {
      var dg = a.vida - g.vida;
      out.push('Frente al promedio de la selección (' + f1(g.vida) + ' d): <b class="' + deltaCls(dg) + '">' + sgn(dg) + ' d</b>.');
    }
    if (c.vida != null && s.vida != null) {
      var d = c.vida - s.vida;
      out.push('Con viaje simulado ' + f1(c.vida) + ' d vs sin viaje ' + f1(s.vida) + ' d: el viaje ' + (d < 0 ? 'resta' : 'suma') + ' <b>' + f1(Math.abs(d)) + ' d</b>.');
    } else {
      out.push('Solo hay floreros ' + (c.nf ? 'con' : 'sin') + ' viaje: aún no se puede medir el impacto del viaje.');
    }
    var top = Object.keys(a.cau).map(function (k) { return a.cau[k]; }).filter(function (x) { return x.id !== 1; })
      .sort(function (x, y) { return y.cant - x.cant; })[0];
    if (top) out.push('Principal inconformidad: <b>' + esc(top.nombre) + '</b> (' + pct(top.cant / a.tot * 100) + ' de los tallos).');
    if (a.sd != null) {
      var cv = a.vmean ? a.sd / a.vmean : 0;
      out.push('Entre floreros el comportamiento es <b>' + (cv > .25 ? 'muy variable' : cv > .12 ? 'moderadamente variable' : 'consistente') + '</b> (desv. ' + f1(a.sd) + ' d).');
    }
    if (a.score != null) out.push('Calificación de evaluación: <b>' + pct(a.score) + '</b>.');
    return out;
  }

  function tabPerfil() {
    var vars = uniq(FIL.map(function (f) { return f.variedad; }));
    var l = FIL.filter(function (f) { return f.variedad === st.sel; });
    var con = l.filter(function (f) { return f.viaje; }), sin = l.filter(function (f) { return !f.viaje; });
    var A = agg(l), C = agg(con), S = agg(sin);
    var series = [];
    if (C.nf) series.push({ label: 'Con viaje', color: C_CON, a: C });
    if (S.nf) series.push({ label: 'Sin viaje', color: C_SIN, a: S });
    var cols = [{ label: 'Todos', a: A }]; if (C.nf) cols.push({ label: 'Con viaje', a: C }); if (S.nf) cols.push({ label: 'Sin viaje', a: S });

    var h = '<div class="fp-card d-flex flex-wrap align-items-center" style="gap:10px"><b>Variedad:</b>' +
      '<select id="fpSel" class="form-control form-control-sm" style="max-width:320px">' + vars.map(function (v) { return '<option' + (v === st.sel ? ' selected' : '') + '>' + esc(v) + '</option>'; }).join('') + '</select>' +
      '<button class="btn btn-outline-secondary btn-sm" id="fpAddCmp">+ Agregar a comparar</button></div>' +
      '<div class="fp-grid2"><div class="fp-card"><h6>Lectura rápida</h6><ul class="fp-ins">' + insights(l).map(function (x) { return '<li>' + x + '</li>'; }).join('') + '</ul></div>' +
      '<div class="fp-card"><h6>Métricas por escenario</h6>' + metricasTabla(cols) + '</div></div>' +
      '<div class="fp-grid2"><div class="fp-card"><h6>Curva de permanencia <small>· % de tallos que siguen en florero cada día</small></h6><div class="fp-chart"><canvas id="chSurv"></canvas></div></div>' +
      '<div class="fp-card"><h6>Evaluación <small>· 100% = puntaje máximo</small></h6><div class="fp-chart"><canvas id="chRadar"></canvas></div></div></div>' +
      '<div class="fp-grid2"><div class="fp-card"><h6>Causas de fin <small>· % de tallos</small></h6><div class="fp-chart"><canvas id="chCau"></canvas></div></div>' +
      '<div class="fp-card"><h6>Evolución por florero <small>· vida (d) según fecha de florero</small></h6><div class="fp-chart"><canvas id="chTrend"></canvas></div></div></div>' +
      '<div class="fp-card"><h6>Floreros de la variedad <small>· clic para ver detalle</small></h6><div class="fp-scroll"><table class="fp-t"><thead><tr><th>ID</th><th>Fecha florero</th><th>Viaje</th><th>Granel</th><th>Origen</th><th>Tipo</th><th>Tallos</th><th>Vida (d)</th><th>% Óptimo</th><th>Calif.</th></tr></thead><tbody>' +
      l.slice().sort(function (x, y) { return x.fecha_florero < y.fecha_florero ? 1 : -1; }).map(function (f) {
        return '<tr class="clk" data-id="' + f.id + '"><td>' + f.id + '</td><td>' + esc(f.fecha_florero) + '</td><td><span class="fp-badge ' + (f.viaje ? 'fp-con' : 'fp-sin') + '">' + (f.viaje ? 'Con' : 'Sin') + '</span></td><td>' + (f.granel ? 'Sí' : 'No') +
          '</td><td>' + esc(f.origen) + '</td><td>' + esc(f.tipo) + '</td><td>' + f.tallos + '</td><td>' + f1(f.m.vida) + '</td><td>' + pct(optimo(f.m)) + '</td><td>' + pct(f.m.score) + '</td></tr>' +
          '<tr class="fpDet" data-for="' + f.id + '" style="display:none"><td colspan="10" style="text-align:left;white-space:normal;background:#fafcfc">' + detalleFlorero(f) + '</td></tr>';
      }).join('') + '</tbody></table></div></div>';
    $('fpMain').innerHTML = h;

    survivalChart('chSurv', [{ label: 'Todos', color: C_ALL, dash: [6, 4], data: survival(A) }].concat(series.map(function (s) { return { label: s.label, color: s.color, data: survival(s.a) }; })));
    radarChart('chRadar', series.length ? series : [{ label: 'Todos', color: C_ALL, a: A }]);
    causesChart('chCau', series.length ? series : [{ label: 'Todos', color: C_ALL, a: A }]);

    var sorted = l.slice().sort(function (x, y) { return x.fecha_florero < y.fecha_florero ? -1 : 1; });
    mk('chTrend', {
      type: 'line',
      data: {
        labels: sorted.map(function (f) { return f.fecha_florero + ' #' + f.id; }),
        datasets: [{ label: 'Con viaje', color: C_CON, v: 1 }, { label: 'Sin viaje', color: C_SIN, v: 0 }].map(function (s) {
          return { label: s.label, borderColor: s.color, backgroundColor: s.color, fill: false, lineTension: .1, spanGaps: true, pointRadius: 4, borderWidth: 2, data: sorted.map(function (f) { return f.viaje === s.v ? f.m.vida : null; }) };
        })
      },
      options: { scales: { yAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Vida (días)' } }], xAxes: [{ ticks: { autoSkip: true, maxRotation: 60 } }] } }
    });

    $('fpSel').onchange = function () { st.sel = this.value; renderTab(); };
    $('fpAddCmp').onclick = function () {
      if (st.cmp.indexOf(st.sel) < 0) { if (st.cmp.length >= MAX_CMP) { alert('Máximo ' + MAX_CMP + ' variedades'); return; } st.cmp.push(st.sel); }
      $('fpCmpN').style.display = ''; $('fpCmpN').textContent = st.cmp.length; go('comparar');
    };
    document.querySelectorAll('#fpMain tr.clk').forEach(function (tr) {
      tr.onclick = function () {
        var d = document.querySelector('tr.fpDet[data-for="' + tr.dataset.id + '"]');
        d.style.display = d.style.display === 'none' ? '' : 'none';
      };
    });
  }

  function detalleFlorero(f) {
    var cs = f.causas.slice().sort(function (a, b) { return a.dias - b.dias; }).map(function (c) {
      return '<span class="fp-chip" style="margin-bottom:4px">' + esc(c.nombre) + ': ' + c.cant + ' tallo(s) · día ' + c.dias + '</span>';
    }).join('') || '<i>Sin causas registradas</i>';
    var ev = f.evals.map(function (e) { return esc(e.item) + ': <b>' + e.p + '</b>'; }).join(' · ') || '<i>Sin evaluación</i>';
    return '<div><b>Causas:</b> ' + cs + '</div><div class="mt-1"><b>Evaluación:</b> ' + ev + '</div>' +
      '<div class="mt-1"><b>Puntos</b> máx. apertura ' + (f.pmax == null ? '—' : f.pmax) + ' · corte ' + (f.pcorte == null ? '—' : f.pcorte) + ' · empaque ' + (f.pempaque == null ? '—' : f.pempaque) + ' · florero ' + (f.pflorero == null ? '—' : f.pflorero) + '</div>' +
      '<div class="mt-1"><b>Corte:</b> ' + esc(f.fecha_corte) + ' · <b>Grupo:</b> ' + esc(f.grupo_descripcion) + '</div>' +
      (f.observacion ? '<div class="mt-1"><b>Observación:</b> ' + esc(f.observacion).replace(/\n/g, '<br>') + '</div>' : '');
  }

  // ---------- Comparar ----------
  function tabComparar() {
    var vars = uniq(FIL.map(function (f) { return f.variedad; }));
    var h = '<div class="fp-card"><div class="d-flex flex-wrap align-items-center mb-2" style="gap:10px"><b>Comparar:</b>' +
      '<select id="fpCmpSel" class="form-control form-control-sm" style="max-width:260px"><option value="">Agregar variedad…</option>' + vars.map(function (v) { return '<option>' + esc(v) + '</option>'; }).join('') + '</select>' +
      '<select id="fpCmpVia" class="form-control form-control-sm" style="max-width:170px"><option value="">Todos los floreros</option><option value="1"' + (st.cmpViaje === '1' ? ' selected' : '') + '>Solo con viaje</option><option value="0"' + (st.cmpViaje === '0' ? ' selected' : '') + '>Solo sin viaje</option></select>' +
      '<button class="btn btn-outline-secondary btn-sm" id="fpCmpClr">Quitar todas</button></div>' +
      '<div>' + st.cmp.map(function (v, i) { return '<span class="fp-chip" style="border-left:6px solid ' + PALETA[i % PALETA.length] + '">' + esc(v) + ' <i data-v="' + esc(v) + '">×</i></span>'; }).join('') + '</div></div>';
    if (st.cmp.length < 2) {
      $('fpMain').innerHTML = h + '<div class="fp-card fp-empty">Elija al menos 2 variedades (desde el Ranking con las casillas, o con el selector de arriba).</div>';
    } else {
      var series = st.cmp.map(function (v, i) {
        var l = FIL.filter(function (f) { return f.variedad === v && (st.cmpViaje === '' || f.viaje === +st.cmpViaje); });
        return { label: v, color: PALETA[i % PALETA.length], a: agg(l) };
      });
      h += '<div class="fp-grid2"><div class="fp-card"><h6>Curvas de permanencia</h6><div class="fp-chart"><canvas id="chSurv"></canvas></div></div>' +
        '<div class="fp-card"><h6>Evaluación</h6><div class="fp-chart"><canvas id="chRadar"></canvas></div></div></div>' +
        '<div class="fp-card"><h6>Causas de fin <small>· % de tallos</small></h6><div class="fp-chart" style="height:' + Math.max(300, 60 + 22 * series.length * 8) + 'px"><canvas id="chCau"></canvas></div></div>' +
        '<div class="fp-card"><h6>Métricas</h6>' + metricasTabla(series) + '</div>';
      $('fpMain').innerHTML = h;
      survivalChart('chSurv', series.map(function (s) { return { label: s.label, color: s.color, data: survival(s.a) }; }));
      radarChart('chRadar', series);
      causesChart('chCau', series);
    }
    if (st.cmp.length < 2) $('fpMain').innerHTML = $('fpMain').innerHTML; // (sin gráficos)
    var sel = $('fpCmpSel'), via = $('fpCmpVia');
    sel.onchange = function () {
      if (!sel.value) return;
      if (st.cmp.indexOf(sel.value) < 0) { if (st.cmp.length >= MAX_CMP) { alert('Máximo ' + MAX_CMP + ' variedades'); return; } st.cmp.push(sel.value); }
      renderTab();
    };
    via.onchange = function () { st.cmpViaje = via.value; renderTab(); };
    $('fpCmpClr').onclick = function () { st.cmp = []; renderTab(); };
    document.querySelectorAll('#fpMain .fp-chip i').forEach(function (x) {
      x.onclick = function () { st.cmp.splice(st.cmp.indexOf(x.dataset.v), 1); renderTab(); };
    });
  }

  // ---------- Impacto del viaje ----------
  function tabViaje() {
    var by = groupBy(FIL, function (f) { return f.variedad; }), rows = [];
    Object.keys(by).forEach(function (v) {
      var l = by[v], c = agg(l.filter(function (f) { return f.viaje; })), s = agg(l.filter(function (f) { return !f.viaje; }));
      if (c.vida != null && s.vida != null) rows.push({ v: v, c: c, s: s, d: c.vida - s.vida });
    });
    if (!rows.length) {
      $('fpMain').innerHTML = '<div class="fp-card fp-empty">Se necesita que una misma variedad tenga floreros <b>con</b> y <b>sin</b> viaje simulado para medir el impacto.</div>';
      return;
    }
    rows.sort(function (a, b) { return a.d - b.d; });
    var avg = rows.reduce(function (a, r) { return a + r.d; }, 0) / rows.length;
    var h = '<div class="fp-card"><h6>Pérdida de vida por el viaje <small>· Δ días (con viaje − sin viaje) · ' + rows.length + ' variedades · promedio <b class="' + deltaCls(avg) + '">' + sgn(avg) + ' d</b></small></h6>' +
      '<div class="fp-chart" style="height:' + Math.max(260, rows.length * 28 + 60) + 'px"><canvas id="chViaje"></canvas></div></div>' +
      '<div class="fp-card"><div class="fp-scroll"><table class="fp-t"><thead><tr><th>Variedad</th><th>Vida con</th><th>Vida sin</th><th>Δ vida</th><th>Día 10% con</th><th>Día 10% sin</th><th>% Óptimo con</th><th>% Óptimo sin</th><th>Calif. con</th><th>Calif. sin</th><th>Floreros c/s</th></tr></thead><tbody>' +
      rows.map(function (r) {
        return '<tr class="clk" data-v="' + esc(r.v) + '"><td><b>' + esc(r.v) + '</b></td><td>' + f1(r.c.vida) + '</td><td>' + f1(r.s.vida) + '</td><td class="' + deltaCls(r.d) + '">' + sgn(r.d) + '</td><td>d' + quant(r.c, .1) + '</td><td>d' + quant(r.s, .1) +
          '</td><td>' + pct(optimo(r.c)) + '</td><td>' + pct(optimo(r.s)) + '</td><td>' + pct(r.c.score) + '</td><td>' + pct(r.s.score) + '</td><td>' + r.c.nf + ' / ' + r.s.nf + '</td></tr>';
      }).join('') + '</tbody></table></div></div>';
    $('fpMain').innerHTML = h;
    mk('chViaje', {
      type: 'horizontalBar',
      data: { labels: rows.map(function (r) { return r.v; }), datasets: [{ label: 'Δ vida (días)', data: rows.map(function (r) { return Math.round(r.d * 10) / 10; }), backgroundColor: rows.map(function (r) { return r.d < 0 ? '#c62828' : '#2e7d32'; }) }] },
      options: {
        legend: { display: false },
        scales: { xAxes: [{ scaleLabel: { display: true, labelString: 'Días de diferencia' } }] },
        onClick: function (e, els) { if (els.length) goPerfil(rows[els[0]._index].v); }
      }
    });
    document.querySelectorAll('#fpMain tr.clk').forEach(function (tr) { tr.onclick = function () { goPerfil(tr.dataset.v); }; });
  }

  // ---------- Matriz de causas ----------
  function tabMatriz() {
    var by = groupBy(FIL, function (f) { return f.variedad; }), tot = {}, name = {};
    var data = Object.keys(by).sort().map(function (v) {
      var a = agg(by[v]);
      Object.keys(a.cau).forEach(function (k) { tot[k] = (tot[k] || 0) + a.cau[k].cant; name[k] = a.cau[k].nombre; });
      return { v: v, a: a };
    });
    var ids = Object.keys(tot).sort(function (x, y) { return (x === '1' ? -1 : y === '1' ? 1 : tot[y] - tot[x]); }).slice(0, 14);
    var h = '<div class="fp-card"><h6>Matriz variedad × causa <small>· % de los tallos de la variedad · clic en una celda abre el perfil</small></h6><div class="fp-scroll"><table class="fp-t fp-heat"><thead><tr><th>Variedad</th>' +
      ids.map(function (k) { return '<th>' + esc(name[k]) + '</th>'; }).join('') + '</tr></thead><tbody>' +
      data.map(function (r) {
        return '<tr><td><b>' + esc(r.v) + '</b></td>' + ids.map(function (k) {
          var p = r.a.tot && r.a.cau[k] ? r.a.cau[k].cant / r.a.tot * 100 : 0;
          var rgb = k === '1' ? '46,125,50' : '198,40,40';
          return '<td class="h" data-v="' + esc(r.v) + '" style="background:rgba(' + rgb + ',' + (Math.min(p, 60) / 60 * .85) + ');color:' + (p > 30 ? '#fff' : '#263238') + '">' + (p ? f0(p) + '%' : '') + '</td>';
        }).join('') + '</tr>';
      }).join('') + '</tbody></table></div></div>';
    $('fpMain').innerHTML = h;
    document.querySelectorAll('#fpMain td.h').forEach(function (td) { td.onclick = function () { goPerfil(td.dataset.v); }; });
  }

  // ---------- Carga ----------
  function cargar() {
    $('fpMain').innerHTML = '<div class="fp-loading">Cargando datos…</div>';
    fetch(API + '?fini=' + $('fpFini').value + '&ffin=' + $('fpFfin').value, { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d.ok) { $('fpMain').innerHTML = '<div class="fp-card fp-empty">' + esc(d.error || 'Error') + '</div>'; return; }
        procesar(d);
        llenarSelect('fpFlor', uniq(ALL.map(function (f) { return f.producto; })), 'Todas');
        llenarSelect('fpOrigen', uniq(ALL.map(function (f) { return f.origen; })), 'Todos');
        llenarSelect('fpTipo', uniq(ALL.map(function (f) { return f.tipo; })), 'Todos');
        aplicar();
      })
      .catch(function () { $('fpMain').innerHTML = '<div class="fp-card fp-empty">No se pudo cargar la información.</div>'; });
  }

  function limpiar() {
    ['fpBuscar', 'fpFlor', 'fpOrigen', 'fpTipo', 'fpViaje', 'fpGranel', 'fpDesc'].forEach(function (id) { $(id).value = ''; });
    aplicar();
  }

  // ---------- Inicio ----------
  var hoy = new Date(); hoy.setMinutes(hoy.getMinutes() - hoy.getTimezoneOffset());
  var ini = new Date(hoy); ini.setFullYear(ini.getFullYear() - 1);
  $('fpFfin').value = hoy.toISOString().slice(0, 10);
  $('fpFini').value = ini.toISOString().slice(0, 10);

  $('fpCargar').onclick = cargar;
  $('fpLimpiar').onclick = limpiar;
  ['fpBuscar', 'fpDesc'].forEach(function (id) { $(id).addEventListener('input', aplicar); });
  ['fpFlor', 'fpOrigen', 'fpTipo', 'fpViaje', 'fpGranel'].forEach(function (id) { $(id).addEventListener('change', aplicar); });
  $('fpTabs').addEventListener('click', function (e) {
    var b = e.target.closest('.fp-tab'); if (!b || !ALL.length && !FIL.length && st.tab === b.dataset.tab) return;
    st.tab = b.dataset.tab; renderTab();
  });

  cargar();
})();

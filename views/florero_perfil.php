<?php
// Reporte interactivo "Perfil de Variedades" (floreros). La lógica está en scripts/florero_perfil.js
include_once('funciones/conexion.php');
?>
<style>
  .fp { --c1:#00796B; --c2:#ef6c00; --ink:#263238; --mut:#78909c; padding: 4px 6px 40px; color: var(--ink); }
  .fp h4.fp-title { font-weight: 800; color: var(--c1); margin: 0; }
  .fp-sub { color: var(--mut); font-size: .85rem; }
  .fp-card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:14px; margin-bottom:14px; }
  .fp-filters { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; align-items:end; }
  .fp-filters label { font-size:.72rem; font-weight:700; color:var(--mut); text-transform:uppercase; margin-bottom:2px; }
  .fp-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; margin-bottom:14px; }
  .fp-kpi { background:linear-gradient(135deg,#00796B,#004D40); color:#fff; border-radius:14px; padding:12px 14px; }
  .fp-kpi.alt { background:linear-gradient(135deg,#ef6c00,#bf360c); }
  .fp-kpi.neu { background:linear-gradient(135deg,#546e7a,#263238); }
  .fp-kpi small { display:block; opacity:.85; font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; }
  .fp-kpi b { font-size:1.6rem; line-height:1.2; }
  .fp-kpi span { font-size:.72rem; opacity:.85; display:block; }
  .fp-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
  .fp-tab { border:0; background:#eceff1; color:var(--ink); border-radius:20px; padding:8px 16px; font-weight:600; cursor:pointer; transition:.15s; }
  .fp-tab:hover { background:#cfd8dc; }
  .fp-tab.on { background:var(--c1); color:#fff; box-shadow:0 3px 10px rgba(0,121,107,.35); }
  .fp-panel { display:none; }
  .fp-panel.on { display:block; animation: fpIn .25s ease; }
  @keyframes fpIn { from { opacity:0; transform:translateY(6px);} to { opacity:1; transform:none; } }
  .fp-grid2 { display:grid; grid-template-columns:repeat(auto-fit,minmax(380px,1fr)); gap:14px; }
  .fp-chart { position:relative; height:300px; }
  .fp-card h6 { font-weight:700; margin-bottom:8px; }
  .fp-card h6 small { color:var(--mut); font-weight:500; }
  table.fp-t { width:100%; border-collapse:collapse; font-size:.85rem; }
  table.fp-t th { background:#f5f7f8; color:var(--mut); font-size:.72rem; text-transform:uppercase; padding:8px 6px; position:sticky; top:0; white-space:nowrap; text-align:right; }
  table.fp-t th:first-child, table.fp-t td:first-child { text-align:left; }
  table.fp-t th.srt { cursor:pointer; }
  table.fp-t th.srt:hover { color:var(--c1); }
  table.fp-t td { padding:7px 6px; border-top:1px solid #eef2f4; text-align:right; white-space:nowrap; }
  table.fp-t tr.clk { cursor:pointer; }
  table.fp-t tr.clk:hover td { background:#e0f2f1; }
  .fp-scroll { max-height:520px; overflow:auto; }
  .fp-badge { font-size:.7rem; padding:2px 8px; border-radius:10px; color:#fff; }
  .fp-con { background:var(--c2); } .fp-sin { background:var(--c1); }
  .fp-pos { color:#2e7d32; font-weight:700; } .fp-neg { color:#c62828; font-weight:700; }
  .fp-bar { display:inline-block; height:6px; border-radius:3px; background:var(--c1); vertical-align:middle; margin-left:6px; }
  .fp-ins { list-style:none; padding:0; margin:0; }
  .fp-ins li { padding:6px 0 6px 22px; position:relative; border-bottom:1px dashed #e0e6e9; }
  .fp-ins li:before { content:'\25B8'; position:absolute; left:4px; color:var(--c1); }
  .fp-chip { display:inline-flex; align-items:center; gap:6px; background:#e0f2f1; color:#004D40; border-radius:16px; padding:4px 10px; font-weight:600; margin:0 6px 6px 0; }
  .fp-chip i { cursor:pointer; font-style:normal; font-weight:800; }
  .fp-heat td.h { text-align:center; cursor:pointer; min-width:64px; }
  .fp-heat td.h:hover { outline:2px solid #263238; }
  .fp-empty { text-align:center; color:var(--mut); padding:30px 0; }
  .fp-loading { text-align:center; padding:50px 0; color:var(--mut); }
  @media (max-width: 480px) { .fp-grid2 { grid-template-columns:1fr; } }
</style>

<div class="fp container-fluid" id="fp">
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
    <div>
      <h4 class="fp-title">Perfil de Variedades</h4>
      <div class="fp-sub">Comportamiento de cada variedad en florero: permanencia, causas de fin, evaluación e impacto del viaje.</div>
    </div>
    <div class="d-flex" style="gap:6px">
      <a href="index.php?report=floreros_diario" class="btn btn-outline-secondary btn-sm" title="Revisión diaria en móvil">
        <i class="material-icons" style="font-size:15px;vertical-align:text-bottom">smartphone</i> Revisión diaria
      </a>
      <a href="index.php?report=52" class="btn btn-outline-secondary btn-sm" title="Reporte tabular tradicional">
        <i class="material-icons" style="font-size:15px;vertical-align:text-bottom">table_chart</i> Reporte tabular
      </a>
    </div>
  </div>

  <div class="fp-card">
    <div class="fp-filters">
      <div><label for="fpFini">Florero desde</label><input type="date" id="fpFini" class="form-control form-control-sm"></div>
      <div><label for="fpFfin">Florero hasta</label><input type="date" id="fpFfin" class="form-control form-control-sm"></div>
      <div><label for="fpBuscar">Variedad contiene</label><input type="text" id="fpBuscar" class="form-control form-control-sm" placeholder="Ej: Explorer"></div>
      <div><label for="fpFlor">Flor</label><select id="fpFlor" class="form-control form-control-sm"></select></div>
      <div><label for="fpOrigen">Origen</label><select id="fpOrigen" class="form-control form-control-sm"></select></div>
      <div><label for="fpTipo">Tipo</label><select id="fpTipo" class="form-control form-control-sm"></select></div>
      <div><label for="fpViaje">Viaje simulado</label>
        <select id="fpViaje" class="form-control form-control-sm"><option value="">Todos</option><option value="1">Con viaje</option><option value="0">Sin viaje</option></select></div>
      <div><label for="fpGranel">Guarde granel</label>
        <select id="fpGranel" class="form-control form-control-sm"><option value="">Todos</option><option value="1">Sí</option><option value="0">No</option></select></div>
      <div><label for="fpDesc">Descripción contiene</label><input type="text" id="fpDesc" class="form-control form-control-sm"></div>
      <div class="d-flex" style="gap:6px">
        <button class="btn btn-brand-green btn-sm flex-fill" id="fpCargar">Consultar</button>
        <button class="btn btn-outline-secondary btn-sm" id="fpLimpiar" title="Limpiar filtros">Limpiar</button>
      </div>
    </div>
  </div>

  <div id="fpKpis" class="fp-kpis"></div>

  <div class="fp-tabs" id="fpTabs">
    <button class="fp-tab on" data-tab="ranking">Ranking</button>
    <button class="fp-tab" data-tab="perfil">Perfil de variedad</button>
    <button class="fp-tab" data-tab="comparar">Comparar <span id="fpCmpN" class="fp-badge fp-sin" style="display:none"></span></button>
    <button class="fp-tab" data-tab="viaje">Impacto del viaje</button>
    <button class="fp-tab" data-tab="matriz">Matriz de causas</button>
  </div>

  <div id="fpMain"><div class="fp-loading">Cargando datos…</div></div>
</div>

<script>
  if (typeof Chart === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"><\/script>');
  }
</script>
<script src="scripts/florero_perfil.js?v=<?= @filemtime(__DIR__ . '/../scripts/florero_perfil.js') ?: time() ?>"></script>

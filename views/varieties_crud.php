<?php
if (is_file("funciones/conexion.php")) {
    include("funciones/conexion.php");
} else {
    include("../funciones/conexion.php");
}
?>
<style>
    .cat-card { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:16px; }
    .cat-toolbar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px; }
    .cat-toolbar .form-control { max-width:220px; }
    #catalogTable td { vertical-align: middle; }
</style>
<div class="container-fluid" style="padding-top:20px;">
    <div class="cat-card" id="catalogCrudRoot" data-table="varieties" data-endpoint="varieties" data-title="CRUD Varieties"
         data-selects='{"producto":"../CRUD/fv_fetchProducts.php","casa_comercial":"../CRUD/fv_fetchBreeders.php"}'>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0" id="catalogTitle">CRUD Varieties</h4>
            <small class="text-muted" id="catalogSubtitle">Tabla varieties</small>
        </div>
        <div class="cat-toolbar" id="crudFilters"></div>
        <div class="cat-toolbar">
            <button id="btnCrudFilter" class="btn btn-success btn-sm">Filtrar</button>
            <button id="btnCrudClear" class="btn btn-secondary btn-sm">Limpiar</button>
            <button id="btnCrudNew" class="btn btn-dark btn-sm">Nuevo</button>
        </div>
        <div class="table-responsive">
            <table id="catalogTable" class="display table table-striped" style="width:100%">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="catalogModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="crudModalFields"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btnCrudSave" class="btn btn-dark">Guardar</button>
      </div>
    </div>
  </div>
</div>
<script src="scripts/catalog_crud.js?v=1"></script>

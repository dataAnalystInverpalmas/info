<style>
	.arr-card { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; }
	.arr-toolbar { display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap; }
	.arr-toolbar .form-control { max-width:220px; }
	#arrangementTable td { vertical-align:middle; }
	#arrangementTable .btn { margin-right:6px; white-space:nowrap; }
	.arr-modal .form-label { font-weight:500; font-size:0.92rem; }
</style>

<div class="container-fluid" style="padding-top:20px;">
	<div class="arr-card">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="m-0">CRUD Arrangement</h4>
			<small class="text-muted">Tabla arrangement</small>
		</div>
		<div class="arr-toolbar">
			<input type="text" id="f_aa_tipo" class="form-control form-control-sm" placeholder="Filtrar tipo">
			<input type="text" id="f_aa_aplicar" class="form-control form-control-sm" placeholder="Filtrar aplicar">
			<button id="btnFilterArrangement" class="btn btn-success btn-sm">Filtrar</button>
			<button id="btnClearArrangement" class="btn btn-secondary btn-sm">Limpiar</button>
			<button id="btnNewArrangement" class="btn btn-dark btn-sm">Nuevo</button>
		</div>
		<div class="table-responsive">
			<table id="arrangementTable" class="display table table-striped" style="width:100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Tipo</th>
						<th>Aplicar</th>
						<th>Seccion</th>
						<th>Orden</th>
						<th>Calc.ConCiclo</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade arr-modal" id="arrangementModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Registro Arrangement</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="arrangementForm">
					<input type="hidden" name="id" id="aa_id">
					<input type="hidden" name="old_tipo" id="aa_old_tipo">
					<input type="hidden" name="old_aplicar" id="aa_old_aplicar">
					<div class="form-group">
						<label class="form-label">Tipo</label>
						<input type="text" class="form-control" name="tipo" id="aa_tipo" required>
					</div>
					<div class="form-group">
						<label class="form-label">Aplicar</label>
						<input type="text" class="form-control" name="aplicar" id="aa_aplicar" required>
					</div>
					<div class="form-group">
						<label class="form-label">Seccion</label>
						<input type="number" class="form-control" name="seccion" id="aa_seccion">
					</div>
					<div class="form-group">
						<label class="form-label">Orden</label>
						<input type="number" class="form-control" name="orden" id="aa_orden">
					</div>
					<div class="form-group">
						<label class="form-label">Calc.ConCiclo</label>
						<input type="number" class="form-control" name="calc_conciclo" id="aa_calc_conciclo">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
				<button type="button" id="saveArrangement" class="btn btn-dark">Guardar</button>
			</div>
		</div>
	</div>
</div>

<script src="scripts/arrangement_only_crud.js?v=1"></script>

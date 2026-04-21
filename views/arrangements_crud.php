<?php
if (is_file("funciones/conexion.php")) {
	include("funciones/conexion.php");
} else {
	include("../funciones/conexion.php");
}
?>

<style>
	.arr-card {
		background: #fff;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.06);
		padding: 16px;
	}

	.arr-toolbar {
		display: flex;
		gap: 8px;
		margin-bottom: 10px;
		flex-wrap: wrap;
	}

	.arr-toolbar .form-control {
		max-width: 220px;
	}

	#arrangementsTable td {
		vertical-align: middle;
	}

	#arrangementsTable .btn {
		margin-right: 6px;
		white-space: nowrap;
	}

	.arr-modal .form-label {
		font-weight: 500;
		font-size: 0.92rem;
	}
</style>

<div class="container-fluid" style="padding-top:20px;">
	<div class="arr-card">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="m-0">CRUD Arrangements</h4>
			<small class="text-muted">Tabla arrangements</small>
		</div>

		<div class="arr-toolbar">
			<input type="text" id="f_arr_variedad" class="form-control form-control-sm" placeholder="Filtrar variedad">
			<input type="text" id="f_arr_finca" class="form-control form-control-sm" placeholder="Filtrar finca">
			<input type="text" id="f_arr_tipo" class="form-control form-control-sm" placeholder="Filtrar tipo">
			<button id="btnFilterArrangements" class="btn btn-success btn-sm">Filtrar</button>
			<button id="btnClearArrangements" class="btn btn-secondary btn-sm">Limpiar</button>
			<button id="btnNewArrangements" class="btn btn-dark btn-sm">Nuevo</button>
		</div>

		<div class="table-responsive">
			<table id="arrangementsTable" class="display table table-striped" style="width:100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Variedad</th>
						<th>Finca</th>
						<th>Tipo</th>
						<th>Aplicar</th>
						<th>Medida</th>
						<th>Valor</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade arr-modal" id="arrangementsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Registro Arrangements</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
	  </div>
	  <div class="modal-body">
		<form id="arrangementsForm">
			<input type="hidden" name="id" id="ar_id">
			<input type="hidden" name="old_variedad" id="ar_old_variedad">
			<input type="hidden" name="old_finca" id="ar_old_finca">
			<input type="hidden" name="old_tipo" id="ar_old_tipo">
			<input type="hidden" name="old_aplicar" id="ar_old_aplicar">

			<div class="form-group">
				<label class="form-label">Variedad</label>
				<input type="text" class="form-control" name="variedad" id="ar_variedad" required>
			</div>
			<div class="form-group">
				<label class="form-label">Finca</label>
				<input type="text" class="form-control" name="finca" id="ar_finca" required>
			</div>
			<div class="form-group">
				<label class="form-label">Tipo</label>
				<input type="text" class="form-control" name="tipo" id="ar_tipo" required>
			</div>
			<div class="form-group">
				<label class="form-label">Aplicar</label>
				<input type="text" class="form-control" name="aplicar" id="ar_aplicar" required>
			</div>
			<div class="form-group">
				<label class="form-label">Medida</label>
				<input type="text" class="form-control" name="medidat" id="ar_medidat">
			</div>
			<div class="form-group">
				<label class="form-label">Valor</label>
				<input type="number" step="0.01" class="form-control" name="valor" id="ar_valor" required>
			</div>
		</form>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
		<button type="button" id="saveArrangements" class="btn btn-dark">Guardar</button>
	  </div>
	</div>
  </div>
</div>

<script src="scripts/arrangements_only_crud.js?v=1"></script>

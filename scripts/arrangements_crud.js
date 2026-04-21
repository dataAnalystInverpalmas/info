$(document).ready(function () {
	function mountArrangementsCrud() {
		if (!$('#arrangementsTable').length) {
			return;
		}

		var arrangementsTable = $('#arrangementsTable').DataTable({
			ajax: {
				url: '../ajax/arrangements_list.php',
				data: function (d) {
					d.variedad = $('#f_arr_variedad').val() || '';
					d.finca = $('#f_arr_finca').val() || '';
					d.tipo = $('#f_arr_tipo').val() || '';
				}
			},
			columns: [
				{ data: 'id', defaultContent: '' },
				{ data: 'variedad', defaultContent: '' },
				{ data: 'finca', defaultContent: '' },
				{ data: 'tipo', defaultContent: '' },
				{ data: 'aplicar', defaultContent: '' },
				{ data: 'medidat', defaultContent: '' },
				{ data: 'valor', defaultContent: '' },
				{
					data: null,
					render: function (data, type, row) {
						return '<button class="btn btn-sm btn-info btn-edit-arr" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
							'<button class="btn btn-sm btn-danger btn-delete-arr" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
					}
				}
			]
		});

		$('#btnFilterArrangements').on('click', function () {
			arrangementsTable.ajax.reload();
		});

		$('#btnClearArrangements').on('click', function () {
			$('#f_arr_variedad').val('');
			$('#f_arr_finca').val('');
			$('#f_arr_tipo').val('');
			arrangementsTable.ajax.reload();
		});

		$('#btnNewArrangements').on('click', function () {
			$('#arrangementsForm')[0].reset();
			$('#ar_id').val('');
			$('#ar_old_variedad').val('');
			$('#ar_old_finca').val('');
			$('#ar_old_tipo').val('');
			$('#ar_old_aplicar').val('');
			$('#arrangementsModal').modal('show');
		});

		$('#arrangementsTable').on('click', '.btn-edit-arr', function () {
			var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
			$('#ar_id').val(row.id || '');
			$('#ar_variedad').val(row.variedad || '');
			$('#ar_finca').val(row.finca || '');
			$('#ar_tipo').val(row.tipo || '');
			$('#ar_aplicar').val(row.aplicar || '');
			$('#ar_medidat').val(row.medidat || '');
			$('#ar_valor').val(row.valor || '');

			$('#ar_old_variedad').val(row.variedad || '');
			$('#ar_old_finca').val(row.finca || '');
			$('#ar_old_tipo').val(row.tipo || '');
			$('#ar_old_aplicar').val(row.aplicar || '');
			$('#arrangementsModal').modal('show');
		});

		$('#saveArrangements').on('click', function () {
			var payload = {
				id: $('#ar_id').val(),
				variedad: $('#ar_variedad').val(),
				finca: $('#ar_finca').val(),
				tipo: $('#ar_tipo').val(),
				aplicar: $('#ar_aplicar').val(),
				medidat: $('#ar_medidat').val(),
				valor: $('#ar_valor').val(),
				old_variedad: $('#ar_old_variedad').val(),
				old_finca: $('#ar_old_finca').val(),
				old_tipo: $('#ar_old_tipo').val(),
				old_aplicar: $('#ar_old_aplicar').val()
			};

			var url = payload.id ? '../ajax/arrangements_update.php' : '../ajax/arrangements_create.php';
			$.post(url, payload, function (res) {
				if (res && res.success) {
					$('#arrangementsModal').modal('hide');
					arrangementsTable.ajax.reload(null, false);
				} else {
					alert((res && res.message) ? res.message : 'Error al guardar arrangements');
				}
			}, 'json').fail(function (xhr) {
				alert('Error en la peticion: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
			});
		});

		$('#arrangementsTable').on('click', '.btn-delete-arr', function () {
			if (!confirm('Eliminar registro de arrangements?')) {
				return;
			}

			var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
			$.post('../ajax/arrangements_delete.php', {
				id: row.id || '',
				variedad: row.variedad || '',
				finca: row.finca || '',
				tipo: row.tipo || '',
				aplicar: row.aplicar || ''
			}, function (res) {
				if (res && res.success) {
					arrangementsTable.ajax.reload(null, false);
				} else {
					alert((res && res.message) ? res.message : 'Error al eliminar');
				}
			}, 'json');
		});
	}

	function toInt(value) {
		var n = parseInt(value, 10);
		return isNaN(n) ? 0 : n;
	}

	function mountArrangementCrud() {
		if (!$('#arrangementTable').length) {
			return;
		}

		var arrangementTable = $('#arrangementTable').DataTable({
			ajax: {
				url: '../ajax/arrangement_list.php',
				data: function (d) {
					d.tipo = $('#f_aa_tipo').val() || '';
					d.aplicar = $('#f_aa_aplicar').val() || '';
				}
			},
			columns: [
				{ data: 'id', defaultContent: '' },
				{ data: 'tipo', defaultContent: '' },
				{ data: 'aplicar', defaultContent: '' },
				{ data: 'seccion', defaultContent: '' },
				{ data: 'orden', defaultContent: '' },
				{ data: 'calc_conciclo', defaultContent: '' },
				{
					data: null,
					render: function (data, type, row) {
						return '<button class="btn btn-sm btn-info btn-edit-aa" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
							'<button class="btn btn-sm btn-danger btn-delete-aa" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
					}
				}
			]
		});

		$('#btnFilterArrangement').on('click', function () {
			arrangementTable.ajax.reload();
		});

		$('#btnClearArrangement').on('click', function () {
			$('#f_aa_tipo').val('');
			$('#f_aa_aplicar').val('');
			arrangementTable.ajax.reload();
		});

		$('#btnNewArrangement').on('click', function () {
			$('#arrangementForm')[0].reset();
			$('#aa_id').val('');
			$('#aa_old_tipo').val('');
			$('#aa_old_aplicar').val('');
			$('#arrangementModal').modal('show');
		});

		$('#arrangementTable').on('click', '.btn-edit-aa', function () {
			var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
			$('#aa_id').val(row.id || '');
			$('#aa_tipo').val(row.tipo || '');
			$('#aa_aplicar').val(row.aplicar || '');
			$('#aa_seccion').val(row.seccion || '');
			$('#aa_orden').val(row.orden || '');
			$('#aa_calc_conciclo').val(row.calc_conciclo || '');

			$('#aa_old_tipo').val(row.tipo || '');
			$('#aa_old_aplicar').val(row.aplicar || '');
			$('#arrangementModal').modal('show');
		});

		$('#saveArrangement').on('click', function () {
			var payload = {
				id: $('#aa_id').val(),
				tipo: $('#aa_tipo').val(),
				aplicar: $('#aa_aplicar').val(),
				seccion: toInt($('#aa_seccion').val()),
				orden: toInt($('#aa_orden').val()),
				calc_conciclo: toInt($('#aa_calc_conciclo').val()),
				old_tipo: $('#aa_old_tipo').val(),
				old_aplicar: $('#aa_old_aplicar').val()
			};

			var url = payload.id ? '../ajax/arrangement_update.php' : '../ajax/arrangement_create.php';
			$.post(url, payload, function (res) {
				if (res && res.success) {
					$('#arrangementModal').modal('hide');
					arrangementTable.ajax.reload(null, false);
				} else {
					alert((res && res.message) ? res.message : 'Error al guardar arrangement');
				}
			}, 'json').fail(function (xhr) {
				alert('Error en la peticion: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
			});
		});

		$('#arrangementTable').on('click', '.btn-delete-aa', function () {
			if (!confirm('Eliminar registro de arrangement?')) {
				return;
			}

			var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
			$.post('../ajax/arrangement_delete.php', {
				id: row.id || '',
				tipo: row.tipo || '',
				aplicar: row.aplicar || ''
			}, function (res) {
				if (res && res.success) {
					arrangementTable.ajax.reload(null, false);
				} else {
					alert((res && res.message) ? res.message : 'Error al eliminar');
				}
			}, 'json');
		});
	}

	mountArrangementsCrud();
	mountArrangementCrud();
});

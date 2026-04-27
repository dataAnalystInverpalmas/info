<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<h4 class="mb-3">Presupuesto con Asignación de Área</h4>

<div class="container-fluid">
    <div class="row">

        <!-- ===== Panel de filtros ===== -->
        <div class="col-sm-2">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <strong><i class="fas fa-filter"></i> Filtros</strong>
                </div>
                <div class="card-body p-2">

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-0">Fecha Inicial</label>
                        <input class="form-control form-control-sm" type="date" id="finicialProy">
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-0">Fecha Final</label>
                        <input class="form-control form-control-sm" type="date" id="ffinalProy">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold mb-0">Tipo</label>
                        <select id="proy_year" class="form-control form-control-sm"></select>
                    </div>

                    <button type="button" onclick="listarProy()" class="btn btn-success btn-sm btn-block">
                        <i class="fas fa-search"></i> Consultar
                    </button>

                </div>
            </div>
        </div>
        <!-- ===== Fin panel de filtros ===== -->

        <!-- ===== Tabla de datos ===== -->
        <div class="col-sm-10">
            <div class="table-responsive">
                <table class="display" id="table_proy_anual" style="width:100%">
                    <thead>
                        <tr>
                            <th>Finca</th>
                            <th>Bloque</th>
                            <th>Flor</th>
                            <th width="30px">Variedad</th>
                            <th>Temporada</th>
                            <th>Ciclo</th>
                            <th>Fecha Siembra</th>
                            <th>Tipo</th>
                            <th>Plantas</th>
                            <th>Cod Variedad</th>
                            <th>Cod Temporada</th>
                            <th>Siembra</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" style="text-align:right">Camas:</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- ===== Fin tabla de datos ===== -->

    </div>
</div>

<script src="scripts/scriptsProy.js"></script>

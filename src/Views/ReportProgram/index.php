<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<h4 class="mb-3">Presupuesto de Siembras</h4>

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
                        <input class="form-control form-control-sm" type="date" id="finicialPTO">
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-0">Fecha Final</label>
                        <input class="form-control form-control-sm" type="date" id="ffinalPTO">
                    </div>

                    <div class="form-group mb-2">
                        <label class="small font-weight-bold mb-0">Programa</label>
                        <select id="pto_year" class="form-control form-control-sm"></select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold mb-0">Casa</label>
                        <select id="pto_casa" class="form-control form-control-sm"></select>
                    </div>

                    <button type="button" onclick="listarPTO()" class="btn btn-success btn-sm btn-block">
                        <i class="fas fa-search"></i> Consultar
                    </button>

                </div>
            </div>
        </div>
        <!-- ===== Fin panel de filtros ===== -->

        <!-- ===== Tabla de datos ===== -->
        <div class="col-sm-10">
            <div class="table-responsive">
                <table class="display" id="table_pto_anual" style="width:100%">
                    <thead>
                        <tr>
                            <th>Flor</th>
                            <th>Variedad</th>
                            <th>Temporada</th>
                            <th>Ciclo</th>
                            <th>Fecha Siembra</th>
                            <th>Fecha Ensarte</th>
                            <th>Fecha Cosecha</th>
                            <th>Fecha Pico</th>
                            <th>Casa</th>
                            <th>Tipo</th>
                            <th>Programa</th>
                            <th>Raiz</th>
                            <th>Plantas</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="12" style="text-align:right">Camas:</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- ===== Fin tabla de datos ===== -->

    </div>
</div>

<script src="scripts/scriptsPTO.js"></script>

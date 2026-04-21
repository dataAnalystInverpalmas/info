<?php
if (is_file("funciones/conexion.php")) {
    include("funciones/conexion.php");
    require "vendor/autoload.php";
} else {
    include("../funciones/conexion.php");
    require "../vendor/autoload.php";
}
?>

<h4>Entrada de Material Vegetal</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label>Fecha Inicial</label>
                <input class="form-control" type="date" id="emv_fecha_ini">
                <label>Fecha Final</label>
                <input class="form-control" type="date" id="emv_fecha_fin">
                <br>
                <button type="button" onclick="emvListar()" class="btn btn-success btn-block">
                    <i class="material-icons" style="vertical-align:middle;font-size:18px">search</i> Consultar
                </button>
            </div>
        </div>

        <div class="col-sm-10">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <button id="btnEmvNuevo" type="button" class="btn btn-info">
                        <i class="material-icons" style="vertical-align:middle">library_add</i> Nueva Entrada
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="tableEmv" class="table table-bordered table-sm display" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Maquila</th>
                                <th>Proveedor</th>
                                <th>Remisión</th>
                                <th>Destino</th>
                                <th>Material</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal cabecera (Nuevo / Editar)
     ============================================================ -->
<div class="modal fade" id="modalEmvCRUD" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalEmvCRUDTitle">Nueva Entrada</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEmv">
                <div class="modal-body">
                    <input type="hidden" id="emv_id">
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label>Fecha</label>
                            <input type="date" class="form-control" id="emv_fecha" required>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Maquila <small class="text-muted">(4 dígitos)</small></label>
                            <input type="text" class="form-control" id="emv_maquila"
                                   maxlength="4" pattern="\d{1,4}" placeholder="0000" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Proveedor</label>
                        <select id="emv_proveedor" class="form-control" required>
                            <option value="">— seleccione —</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label>Remisión</label>
                            <input type="text" class="form-control" id="emv_remision" placeholder="Número de remisión">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Destino</label>
                            <input type="text" class="form-control" id="emv_destino" placeholder="Destino">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Material</label>
                        <select id="emv_material" class="form-control" required>
                            <option value="">— seleccione —</option>
                            <option value="Esqueje">Esqueje</option>
                            <option value="Planta madre">Planta madre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons" style="vertical-align:middle;font-size:18px">save</i> Guardar
                    </button>
                    <button type="button" id="btnEmvVerDetalles" class="btn btn-primary" style="display:none;">
                        <i class="material-icons" style="vertical-align:middle;font-size:18px">list_alt</i> Ir a Detalles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal detalles
     ============================================================ -->
<div class="modal fade bs-example-modal-lg" id="modalEmvDetalles" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEmvDetallesTitle">Detalles de Entrada</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Formulario para agregar un nuevo detalle -->
                <form id="formEmvDetalle">
                    <input type="hidden" id="det_entrada_id">
                    <datalist id="listVariedades"></datalist>

                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Agregar detalle</strong></div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-sm-4">
                                    <label>Variedad</label>
                                    <input type="text" class="form-control" id="det_variedad"
                                           list="listVariedades" placeholder="Escriba para buscar..." required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Cant. Recibida</label>
                                    <input type="number" class="form-control font-weight-bold" id="det_cantidad_recibida"
                                           readonly tabindex="-1" placeholder="0">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>
                                        <span class="badge badge-secondary">Raíz</span>
                                    </label>
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" id="det_raiz">
                                        <label class="form-check-label" for="det_raiz">Con raíz</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label>Facturado</label>
                                    <input type="number" class="form-control emv-suma" id="det_facturado"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col">
                                    <label>Reposición</label>
                                    <input type="number" class="form-control emv-suma" id="det_reposicion"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col">
                                    <label>Excedente</label>
                                    <input type="number" class="form-control emv-suma" id="det_excedente"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col">
                                    <label>Obsequio</label>
                                    <input type="number" class="form-control emv-suma" id="det_obsequio"
                                           min="0" value="0">
                                </div>
                                <div class="form-group col">
                                    <label>Adicional</label>
                                    <input type="number" class="form-control emv-suma" id="det_adicional"
                                           min="0" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Observación</label>
                                <input type="text" class="form-control" id="det_observacion"
                                       placeholder="Observación (opcional)">
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="material-icons" style="vertical-align:middle;font-size:18px">add_circle</i>
                                Agregar detalle
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de detalles existentes -->
                <div class="table-responsive">
                    <table id="tableEmvDetalles" class="table table-bordered table-sm display" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Variedad</th>
                                <th>Cant. Recibida</th>
                                <th>Facturado</th>
                                <th>Reposición</th>
                                <th>Excedente</th>
                                <th>Obsequio</th>
                                <th>Adicional</th>
                                <th>Raíz</th>
                                <th>Observación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/entrada_material_vegetal.js"></script>

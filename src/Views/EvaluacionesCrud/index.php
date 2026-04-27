<h4>Registro de Evaluaciones</h4>

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
                        <input class="form-control form-control-sm" type="date" id="fecha_ini">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold mb-0">Fecha Final</label>
                        <input class="form-control form-control-sm" type="date" id="fecha_fin">
                    </div>

                    <button type="button" onclick="listar()" class="btn btn-success btn-sm btn-block">
                        <i class="fas fa-search"></i> Consultar
                    </button>

                </div>
            </div>
        </div>
        <!-- ===== Fin panel de filtros ===== -->

        <!-- ===== Contenido principal ===== -->
        <div class="col-sm-10">
            <div class="row mb-2" id="nuevo">
                <div class="col-sm-12">
                    <button id="btnNuevo" type="button" class="btn btn-info">
                        <i class="material-icons" style="vertical-align:middle">library_add</i> Nueva Evaluación
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="tableOrders" class="table table-bordered table-sm display" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Evaluador</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ===== Fin contenido principal ===== -->

    </div>
</div>

<!-- ============================================================
     Modal cabecera (Nuevo / Editar)
     ============================================================ -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formOrders">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha">
                            </div>
                            <div class="form-group">
                                <select name="evaluador" id="evaluador" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal detalles
     ============================================================ -->
<div class="modal fade bs-example-modal-lg" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container-fluid">
                <div class="modal-body">
                    <form id="formOrders">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="order_id">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select id="flor" class="form-control" onchange="fetch_varieties(this.value)" required></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select id="variedad" class="form-control">
                                                    <option value="">Variedad</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="puntajes">
                                        <p class="text-muted small mb-1"><em>Calificaciones (opcional)</em></p>
                                        <div class="form-row">
                                            <div class="form-group col-sm">
                                                <input placeholder="color" id="color" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="ciclo" id="ciclo" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="produc" id="productividad" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="tCabeza" id="tCabeza" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="pFuerte" id="pFuerte" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm">
                                                <input placeholder="fApertura" id="fApertura" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="gTallo" id="gTallo" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="longitud" id="longitud" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="rFusarium" id="rFusarium" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="sEnfermedad" id="sEnfermedades" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm">
                                                <input placeholder="follaje" id="follaje" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="pSpray" id="pSpray" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="sMini" id="sMini" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="" id="" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="" id="" class="form-control" maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm-12">
                                                <textarea placeholder="Comentario" id="comentario" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <button type="submit" class="btn btn-lg btn-block btn-success btnAgregarDetalles">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 table-responsive">
                                <table id="tableDetails" class="table table-bordered table-sm" style="width:100%">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Id</th>
                                            <th>order_id</th>
                                            <th>Variedad</th>
                                            <th>Item</th>
                                            <th>Valor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </form>

                    <!-- Sección de Comentarios -->
                    <hr class="mt-3 mb-3">
                    <h6><i class="material-icons" style="vertical-align:middle;font-size:18px">comment</i> Comentarios</h6>
                    <div class="form-row mb-2">
                        <div class="col-sm-9">
                            <textarea id="texto_comentario" class="form-control" rows="2" placeholder="Escriba su comentario aquí..."></textarea>
                        </div>
                        <div class="col-sm-3 d-flex align-items-center">
                            <button type="button" class="btn btn-info btn-block btnAgregarComentario">
                                <i class="material-icons" style="vertical-align:middle">add_comment</i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 table-responsive">
                            <table id="tableComments" class="table table-bordered table-sm" style="width:100%">
                                <thead class="text-center">
                                    <tr>
                                        <th>Id</th>
                                        <th>Variedad</th>
                                        <th>Comentario</th>
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
    </div>
</div>

<script src="scripts/load_varieties.js"></script>
<script src="scripts/orders.js"></script>

<?php $view = 'salida_material'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Salida Material']; // array de permisos para estar en esta página
  $val = false;
  foreach($_SESSION['permisos'] as $key => $obj) {
    if (in_array($obj['descripcion'], $permitidos)) {
      $val = true; break;
    }
  }
  if (!$val) { ?>
  <div class="right_col" role="main">
     <br />
          <div class="">
            <h2>No tiene permisos para estar aquí.</h2>
          </div>
  </div>
<?php    
  } else {
?>

<!-- page content -->
        <div class="right_col" role="main">
           <br />
          <div class="">

            <div class="page-title">
              <div class="title_left">
                <h3>Almacén</h3>
              </div>

            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Salida de Material</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="form-guardar" action="almacen" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="alert " style="display: none;"></div>

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Asociación <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_asociacion" name="id_asociacion" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Cuadrilla <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_cuadrilla" name="id_cuadrilla" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Caja <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_tipo_caja" name="id_tipo_caja" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Cantidad <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="cantidad" name="cantidad" required placeholder="0" class="form-control col-md-7 col-sm-7 col-xs-12 numero"><sup>Presiona enter para calcular</sup>
                        </div>
                      </div>

                      <!-- 
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Material <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_material" name="id_material" required class="form-control select2">
                          </select>
                        </div>
                      </div>
                      -->

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Materiales/Insumos <span class="required">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <table id="tbl-materiales" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th width="40px">Código</th>
                                <th>Nombre</th>
                                <th width="50px">Stock</th>
                                <th width="40px">Unidad</th>
                                <th width="40px">Entrega</th>
                                <th width="70px">Tipo</th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                          <sup>Los valores serán redondeados a 2 decimales</sup>
                        </div>
                      </div>
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Limpiar</button>
                          <button id="btn-guardar" type="submit" class="btn btn-success">Guardar</button>
                          <!-- <a id="btn-guardar" class="btn btn-success">Guardar</a>-->
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>


            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Registros de salida</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-salidas" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th width="10px">#</th>
                          <th>Fecha</th>
                          <th>Semana</th>
                          <th>Asociación</th>
                          <th>Cuadrilla</th>
                          <th>Caja</th>
                          <th width="50px">Cantidad</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- modal -->
            <div id="modal-detalle" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                      
                      <div class="row"><!--  uploaded  -->
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="x_panel">
                            <div class="x_title">
                              <h2>Material despachado</h2>
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                            <label>Asociación: </label>&nbsp;<label id="dt-asociacion"></label><br>
                            <label>Cuadrilla: </label>&nbsp;<label id="dt-cuadrilla"></label><br>
                            <label>Fecha: </label>&nbsp;<label id="dt-fecha"></label><br>
                            <label>Semana: </label>&nbsp;<label id="dt-semana"></label><br>
                            <label>Caja: </label>&nbsp;<label id="dt-caja"></label><br>
                            <label>Nro Cajas: </label>&nbsp;<label id="dt-nro_cajas"></label><br>

                            <table id="tblDetalleMat" class="table table-striped table-bordered">
                                <thead>
                                  <tr>
                                    <th width="40px">Código</th>
                                    <th>Nombre</th>
                                    <th width="50px">Entregado</th>
                                    <th width="40px">Unidad</th>
                                    <th width="70px">Tipo</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                              </table>

                              <a id="btn-print-salida" class="btn btn-success ">Imprimir</a>

                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->

              <!-- SECCIÓN PARA IMPRIMIR SALIDA DE MATERIAL  - HIDE -->
              <div id="printSalidaMeterial" style="display: none;" ><!--  uploaded  -->
              <div class="row" ><!--  uploaded  -->
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="x_panel">
                    <div class="x_title">
                      <h2>Material despachado</h2>
                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <form class="form-horizontal form-label-left">
                        <div class="form-group">
                          <label>Asociación: </label>
                          <label id="dt-asociacion-print"></label>
                        </div>

                        <div class="form-group">
                          <label>Cuadrilla: </label>
                          <label id="dt-cuadrilla-print"></label>
                        </div>

                        <div class="form-group">
                          <label>Fecha: </label>
                          <label id="dt-fecha-print"></label>
                        </div>

                        <div class="form-group">
                          <label>Semana: </label>
                          <label id="dt-semana-print"></label>
                        </div>

                        <div class="form-group">
                          <label>Caja: </label>
                          <label id="dt-caja-print"></label>
                        </div>

                        <div class="form-group">
                          <label>Nro Cajas: </label>
                          <label id="dt-nro_cajas-print"></label>
                        </div>

                        <div class="form-group">
                          <table id="tblDetalleMatPrint" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th width="40px">Código</th>
                                <th>Nombre</th>
                                <th width="50px">Entregado</th>
                                <th width="40px">Unidad</th>
                                <th width="70px">Tipo</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                          </table>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div><!-- fin row -->
              </div>
              <!-- FIN IMPRIMIR -->

          </div>
        </div>

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
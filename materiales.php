<?php $view = 'materiales'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Material / Insumo']; // array de permisos para estar en esta página
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
                    <h2>Registrar Material / Insumo</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="form-guardar" action="almacen" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="alert " style="display: none;"></div>

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">
                      <input type="hidden" name="id_almacen" value="1">

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Código <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="codigo" name="codigo" required="required" class="form-control col-md-7 col-xs-12">
                          <span class="form-control-feedback right" aria-hidden="true"><i class="fa fa-key" aria-hidden="true"></i></span>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Nombre <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Descripción 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="descripcion" name="descripcion" placeholder="Descripción" class="form-control col-md-7 col-xs-12" rows="5"></textarea>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Stock Mínimo <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="stock_minimo" name="stock_minimo" required placeholder="0.0" class="form-control col-md-7 col-sm-7 col-xs-12 decimal">
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <select id="unidad_medida" name="unidad_medida" required class="form-control col-md-7 col-sm-7 col-xs-12">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Stock
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="stock" name="stock" placeholder="0.0" class="form-control col-md-7 col-sm-7 col-xs-12 decimal"> <sup>Si tiene stock puede ingresar la cantidad</sup>
                        </div>
                        
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Validar Stock Mínimo <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="stock_requerido" name="stock_requerido" class="form-control">
                            <option value="1" selected>SI</option>
                            <option value="0">NO</option>
                          </select>
                        </div>
                      </div>   

                       <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="tipo" name="tipo" class="form-control">
                            <option value="MATERIAL" selected>MATERIAL</option>
                            <option value="INSUMO">INSUMO</option>
                          </select>
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
                    <h2>Almacén</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-materiales" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th width="10px">#</th>
                          <th width="40px">Código</th>
                          <th>Nombre</th>
                          <th width="50px">Stock</th>
                          <th width="40px">Unidad</th>
                          <th width="70px">Tipo</th>
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
            <div id="modal-materiales" class="modal fade" tabindex="-1" role="dialog">
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
                              <h2>Agregar stock</h2>
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                            <input type="hidden" id="id-material">
                              <label>Stock actual: </label>&nbsp;<label id="stock-actual"></label>&nbsp;<label class="unidad"></label><br>
                              <label>Stock mínimo: </label>&nbsp;<label id="stock-minimo"></label>&nbsp;<label class="unidad"></label>
                              <form data-parsley-validate class="form-horizontal form-label-left">
                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Ingreso <span class="required">*</span>
                                  </label>
                                  <div class="col-md-3 col-sm-3 col-xs-12">
                                    <input type="text" id="stock-nuevo" name="stock-nuevo" placeholder="0.0" class="form-control col-md-7 col-sm-7 col-xs-12 decimal">
                                    <span class="form-control-feedback right" aria-hidden="true"><b class="unidad"></b></span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Proveedor <span class="required">*</span>
                                  </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="proveedor" name="proveedor" placeholder="Proveedor" class="form-control col-md-7 col-xs-12">
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Observación 
                                  </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea id="observacion" name="observacion" placeholder="Observación" class="form-control col-md-7 col-xs-12" rows="5"></textarea>
                                  </div>
                                </div>
                                <br>
                                <a class="btn btn-primary" id="btn-guardar-stock">Guardar</a>
                              </form>
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

          </div>
        </div>

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
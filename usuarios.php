<?php $view = 'usuarios'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Configuración','Usuarios']; // array de permisos para estar en esta página
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
                <h3>Configuración</h3>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Usuarios<small></small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div class="col-md-9 col-sm-9 col-xs-12">

                      <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="x_panel">
                            <div class="x_title">
                              <h2>Registrar <small>nuevo usuario</small></h2>
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                              <br />
                              <form id="form-guardar" action="usuario" method="post" data-parsley-validate class="form-horizontal form-label-left">

                                <div class="alert " style="display: none;"></div>

                                <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Trabajador <span class="required">*</span> </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="id_trabajador" name="id_trabajador" required class="form-control select2">
                                    </select>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Usuario <span class="required">*</span>
                                  </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="username" name="username" required="required" class="form-control col-md-7 col-xs-12" >
                                    <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Clave <span class="required">*</span>
                                  </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="password" id="password" name="password" required="required" class="form-control col-md-7 col-xs-12" >
                                    <span class="fa fa-key form-control-feedback right" aria-hidden="true"></span>
                                  </div>
                                </div>
                                

                                <div class="form-group">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo </label>
                                  <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="id_tipousuario" name="id_tipousuario" required class="form-control">
                                    </select>
                                  </div>
                                </div>
                              
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="reset" class="btn btn-primary">Limpiar</button>
                                    <input type="submit" id="btn-guardar" type="submit" class="btn btn-success" value="Guardar">
                                  </div>
                                </div>

                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <table id="datatable-usuarios" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Tipo Usuario</th>
                                <th>Cargo</th>
                                <th>Activo</th>
                                <th>Permisos</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                          </table>
                      </div>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- modal -->
            <div id="modal-permisos" class="modal fade" tabindex="-1" role="dialog">
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
                              <h2>Permisos</h2>
                              
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                            <table id="datatable-permisos" class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Permiso</th>
                                  <th>Activo</th>
                                </tr>
                              </thead>
                              <tbody>
                                
                              </tbody>
                            </table>

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
        <!-- /page content -->

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
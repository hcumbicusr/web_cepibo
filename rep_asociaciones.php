<?php $view = 'rep_asociaciones'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Reportes','Asociaciones']; // array de permisos para estar en esta página
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
                <h3>Reportes</h3>
              </div>

            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Producción de Asociaciones</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form id="form-reporte-1" action="reporte" method="post" enctype="multipart/form-data" data-parsley-validate class="form-vertical form-label-left">

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Inicio <span class="required">*</span></label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_inicio">
                          <input id="f_inicio" name="f_inicio" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="add-on glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Fin <span class="required">*</span></label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_fin">
                          <input id="f_fin" name="f_fin" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="add-on glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Limpiar</button>
                          <button id="btn-consultar" type="submit" class="btn btn-success">Consultar</button>
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
                    <h2>Resultado</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    
                  <div id="reporte1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <!-- /page content -->

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
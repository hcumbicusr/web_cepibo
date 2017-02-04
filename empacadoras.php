<?php $view = 'empacadoras'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Empacadoras']; // array de permisos para estar en esta página
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
                <h3>Asociaciones</h3>
              </div>

            </div>

            <div class="clearfix"></div>
            <div class="row">
              
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Empacadoras</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="form-guardar" action="asociacion" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="alert " style="display: none;"></div>

                      <!-- <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>"> -->

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Asociación <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_asociacion" name="id_asociacion" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Empacadora <span class="required">*</span> </label>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                          <input type="text" id="nombre" name="nombre" required class="form-control">
                        </div>
                      </div>
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Limpiar</button>
                          <button id="btn-guardar" type="submit" class="btn btn-success">Crear</button>
                          <!-- <a id="btn-guardar" class="btn btn-success">Guardar</a>-->
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>

            </div> <!-- end row -->

          </div>
        </div>
        <!-- /page content -->

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
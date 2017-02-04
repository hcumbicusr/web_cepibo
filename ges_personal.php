<?php $view = 'ges_personal'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Ges. Personal']; // array de permisos para estar en esta página
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
                <h3>RR.HH.</h3>
              </div>

            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Registrar <small>nuevo trabajador</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="form-guardar" action="trabajador" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="alert " style="display: none;"></div>

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nombres <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="nombres" name="nombres" required="required" class="form-control col-md-7 col-xs-12" autofocus>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Apellidos <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="apellidos" name="apellidos" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="dni" name="dni" class="form-control numero" data-inputmask="'mask': '99999999'" maxlength="8" minlength="8" >
                          <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Dirección</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="direccion" name="direccion" class="form-control col-md-7 col-xs-12" type="text" >
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Teléfono</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="telefono" name="telefono" class="form-control col-md-7 col-xs-12 numero" type="text" maxlength="9">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Celular <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="celular" name="celular" class="form-control col-md-7 col-xs-12 numero" type="text" maxlength="9">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Email <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="email" name="email" class="form-control col-md-7 col-xs-12" type="email" maxlength="150">
                        </div>
                      </div>
                     
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Género <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="gender" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="genero" value="M" checked="true"> &nbsp; Masculino &nbsp;
                            </label>
                            <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="genero" value="F"> Femenino
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Asociación <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_asociacion" name="id_asociacion" class="form-control">
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Cargo <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_cargo" name="id_cargo" class="form-control">
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
              
            </div>

          </div>
        </div>



<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
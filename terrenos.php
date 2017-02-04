<?php $view = 'terrenos'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Terrenos']; // array de permisos para estar en esta página
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
                    <h2>Registrar Terreno de productor</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="form-guardar" action="trabajador" method="post" enctype="multipart/form-data" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="alert " style="display: none;"></div>

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Productor <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="id_productor" name="id_productor" required class="form-control select2">
                          </select>
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Área Total <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="area_total" name="area_total" required="required" class="form-control col-md-7 col-xs-12 decimal">
                          <span class="form-control-feedback right" aria-hidden="true"><b>Ha.</b></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Área Cultivo <span class="required">*</span>
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="area_cultivo" name="area_cultivo" required="required" class="form-control col-md-7 col-xs-12 decimal">
                          <span class="form-control-feedback right" aria-hidden="true"><b>Ha.</b></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Área Desarrollo 
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <input type="text" id="area_desarrollo" name="area_desarrollo" class="form-control col-md-7 col-xs-12 decimal" value="0.0">
                          <span class="form-control-feedback right" aria-hidden="true"><b>Ha.</b></span>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Referencia 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="referencia" name="referencia" placeholder="Referencia" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Certificación 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="certificacion" name="certificacion" placeholder="Separado por comas(,)" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Condición <span class="required">*</span> </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="condicion" name="condicion" class="form-control">
                            <option value="PROPIETARIO" selected>PROPIETARIO</option>
                            <option value="ARRENDATARIO">ARRENDATARIO</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Documentación 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="text" id="documentacion" name="documentacion" placeholder="Separado por comas(,)" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Observación 
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="observacion" name="observacion" placeholder="Alguna observación" class="form-control col-md-7 col-xs-12" rows="5"></textarea>
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
                    <h2>Productores</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatable-terrenos" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Código</th>
                          <th>Productor</th>
                          <!--<th>dni</th>-->
                          <th>Condición</th>
                          <th>Área total (Ha)</th>
                          <th>Área cultivo (Ha)</th>
                          <th>Área desarrollo (Ha)</th>
                          <th>Referencia</th>
                          <th>Certificación</th>
                          <th>Asociación</th>
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

<?php 
// valida permisos
  } // fin if valida permisos
?>
<?php include_once 'footer.inc.php'; ?>
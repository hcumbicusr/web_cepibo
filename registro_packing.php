<?php $view = 'registro_packing'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Packing']; // array de permisos para estar en esta página
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
                <h3>Packing</h3>
              </div>

            </div>

            <div class="clearfix"></div>
            <div class="row">

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Registro de Packing</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    
                    <form id="form-guardar" action="packing" method="post" enctype="multipart/form-data" data-parsley-validate class="form-vertical form-label-left">

                      <input type="hidden" name="usuario_reg" value="<?php echo $_SESSION["username"]; ?>">

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Vapor <span class="required">*</span>
                        </label>
                        <div id="div_vapor" class="input-group col-md-9 col-sm-9 col-xs-12">
                          <select id="id_vapor" name="id_vapor" required class="form-control select2">
                          </select>
                        </div>
                      </div>
                      
                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Cliente <span class="required">*</span>
                        </label>
                        <div id="div_cliente" class="input-group col-md-9 col-sm-9 col-xs-12">
                          <select id="id_cliente" name="id_cliente" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">N°Contenedor <span class="required">*</span>
                        </label>
                        <div id="div_contenedor" class="input-group col-md-8 col-sm-8 col-xs-12">
                          <select id="id_contenedor" name="id_contenedor" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Tipo de Funda <span class="required">*</span>
                        </label>
                        <div id="div_tipo_funda" class="input-group col-md-8 col-sm-8 col-xs-12">
                          <select id="id_tipo_funda" name="id_tipo_funda" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Puerto Origen <span class="required">*</span>
                        </label>
                        <div id="div_id_puerto_origen" class="input-group col-md-8 col-sm-8 col-xs-12">
                          <select id="id_puerto_origen" name="id_puerto_origen" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Puerto Destino <span class="required">*</span>
                        </label>
                        <div id="div_id_puerto_destino" class="input-group col-md-8 col-sm-8 col-xs-12">
                          <select id="id_puerto_destino" name="id_puerto_destino" required class="form-control select2">
                          </select>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-5 col-sm-5 col-xs-12">N°Termoregistro <span class="required">*</span></label>
                        <div class="input-group col-md-7 col-sm-7 col-xs-12">
                          <input type="text" id="nro_termoregistro" name="nro_termoregistro" class="form-control" >
                        </div>
                      </div>

                      <div class="form-group col-md-3 col-sm-3 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">N°Guía <span class="required">*</span></label>
                        <div class="input-group col-md-8 col-sm-8 col-xs-12">
                          <input type="text" id="nro_guia" name="nro_guia" class="form-control" >
                        </div>
                      </div>

                      <div class="form-group col-md-2 col-sm-2 col-xs-12">
                        <label class="control-label col-md-6 col-sm-6 col-xs-12">Semana</label>
                        <div class="input-group col-md-6 col-sm-3 col-xs-12">
                          <input type="text" id="nro_semana" name="nro_semana" readonly class="form-control" >
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Hr.Llegada Contenedor <span class="required">*</span></label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_llegada_contenedor">
                          <input id="f_llegada_contenedor" name="f_llegada_contenedor" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="add-on glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Hr.Salida Contenedor</label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_salida_contenedor">
                          <input id="f_salida_contenedor" name="f_salida_contenedor" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Inicio de Llenado</label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_inicio_llenado">
                          <input id="f_inicio_llenado" name="f_inicio_llenado" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-6 col-xs-12">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12">Fin de Llenado</label>
                        <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_fin_llenado">
                          <input id="f_fin_llenado" name="f_fin_llenado" class="form-control" type="text" readonly>
                          <span class="input-group-addon btn-info">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>

                      
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="reset" class="btn btn-primary">Limpiar</button>
                          <button id="btn-guardar-packing" type="submit" class="btn btn-success">Guardar</button>
                        </div>
                      </div>


                    </form>
                  </div>
                </div>
              </div>
              
            </div> <!-- end row -->

            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Packing <small>Pendientes</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <p>Proceso para completar el registro del packing</p>
                    <div id="wizard" class="form_wizard wizard_horizontal">
                      <ul class="wizard_steps">
                        <li>
                          <a href="#step-1">
                            <span class="step_no">1</span>
                            <span class="step_descr">
                                              Paso 1<br />
                                              <small>Selecciona el packing</small>
                                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="#step-2">
                            <span class="step_no">2</span>
                            <span class="step_descr">
                                              Paso 2<br />
                                              <small>Registro de cajas embarcadas</small>
                                          </span>
                          </a>
                        </li>
                      </ul>
                      <div id="step-1">
                        <form class="form-horizontal form-label-left" style="min-height: 500px">
                          <small>Doble click sobre la fila para finalizar registro</small>
                          <div class="table-responsive">
                          <table id="datatable-packing" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th width="10px">*</th>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th width="60px">Contenedor</th>
                                <th>Funda</th>
                                <th>Vapor</th>
                                <th>Pto.Origen</th>
                                <th>Pto.Destino</th>
                                <th>N°Registro</th>
                                <th>N°Guia</th>
                                <th width="50px">Semana</th>
                              </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                          </table>
                          </div>
                        </form>
                      </div>
                      <div id="step-2">
                        <div class="x_content" style="min-height: 600px">
                          <div id="itemsBD">
                          </div>
                          <br>
                          <div id="addItem">
                          </div>
                          <br>
                          <center>
                            <a id="btn-plus" class="btn btn-primary" style="border-radius: 25px;"><i class="glyphicon glyphicon-plus"></i></a>
                          </center>
                          <br>
                          <div class="clearfix"></div>
                          <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="table-responsive">
                              <table id="datatable-detalle-packing" class="table table-striped table-bordered">
                                <thead>
                                  <tr>
                                    <th>Asociación</th>
                                    <th>Tipo de Caja</th>
                                    <th>F.Corte</th>
                                    <th>Empacadora</th>
                                    <th>Código</th>
                                    <th>Nombre del Productor</th>
                                    <th>Nro de Cajas</th>
                                    <!-- <th></th>-->
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
                    <!-- End SmartWizard Content -->

                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Packinglist <small>Historial</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="table-responsive">
                    <table id="datatable-packinglist" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Código</th>
                          <th>Cliente</th>
                          <th width="60px">Contenedor</th>
                          <th>Funda</th>
                          <th>Vapor</th>
                          <th>Pto.Origen</th>
                          <th>Pto.Destino</th>
                          <th>N°Registro</th>
                          <th>N°Guia</th>
                          <th width="50px">Semana</th>
                          <th>Ver</th>
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



            <div id="modal-add-item" class="modal fade" tabindex="-1" role="dialog">
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
                              <h2>Nuevo Item</h2>
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                              <form id="form-guardar-item" action="packing" method="post"  enctype="multipart/form-data"   data-parsley-validate class="form-horizontal form-label-left">
                                <input type="hidden" id="id_element" >
                                <input type="hidden" id="ctrl" >
                                <input type="hidden" id="fnSuccess" >
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nombre
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                      <input type="text" id="nombre_item" name="nombre_item" required="required" class="form-control col-md-7 col-xs-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" id="btn-guardar-item" class="btn btn-success">Guardar</button>
                                  </div>
                                </div>

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



              <!-- finalizar packing -->
              <div id="modal-finalizar-packing" class="modal fade moodal-dialog-fullwidth" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document" style="overflow-y: scroll; max-height:90%;  margin-top: 5px; margin-bottom:5px;">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">Finalizar Packing</h4>
                    </div>
                    <div class="modal-body">

                      <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="x_panel">
                            <div class="x_title">
                              <h2>Packing</h2>
                              <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                              <form id="form-finalizar-packing" action="packing" method="post"  enctype="multipart/form-data"   data-parsley-validate class="form-vertical form-label-left col-md-8 col-sm-8 col-xs-12">
                                
                                <input type="hidden" id="id_packing_f" name="id_packing" >
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Vapor: </label>
                                  <div id="div_vapor" class="input-group col-md-9 col-sm-9 col-xs-12">
                                    <label id="id_vapor_f" class="control-label"></label>
                                  </div>
                                </div>
                                
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Cliente: </label>
                                  <div id="div_cliente" class="input-group col-md-9 col-sm-9 col-xs-12">
                                    <label id="id_cliente_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">N°Contenedor: </label>
                                  <div id="div_contenedor" class="input-group col-md-8 col-sm-8 col-xs-12">
                                    <label id="id_contenedor_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Tipo de Funda: </label>
                                  <div id="div_tipo_funda" class="input-group col-md-8 col-sm-8 col-xs-12">
                                    <label id="id_tipo_funda_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Puerto Origen: </label>
                                  <div id="div_id_puerto_origen" class="input-group col-md-8 col-sm-8 col-xs-12">
                                    <label id="id_puerto_origen_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12" for="last-name">Puerto Destino: </label>
                                  <div id="div_id_puerto_destino" class="input-group col-md-8 col-sm-8 col-xs-12">
                                    <label id="id_puerto_destino_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-5 col-sm-5 col-xs-12">N°Termoregistro: </label>
                                  <div class="input-group col-md-7 col-sm-7 col-xs-12">
                                    <label id="nro_termoregistro_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                  <label class="control-label col-md-5 col-sm-5 col-xs-12">N°Guía: </label>
                                  <div class="input-group col-md-7 col-sm-7 col-xs-12">
                                    <label id="nro_guia_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                  <label class="control-label col-md-6 col-sm-6 col-xs-12">Semana: </label>
                                  <div class="input-group col-md-6 col-sm-3 col-xs-12">
                                    <label id="nro_semana_f" class="control-label"></label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12">Hr.Llegada Contenedor <span class="required">*</span></label>
                                  <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_llegada_contenedor_f">
                                    <input id="f_llegada_contenedor_f" name="f_llegada_contenedor" class="form-control" type="text" readonly>
                                    <span class="input-group-addon btn-info">
                                      <span class="add-on glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12">Hr.Salida Contenedor <span class="required">*</span></label>
                                  <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_salida_contenedor_f">
                                    <input id="f_salida_contenedor_f" name="f_salida_contenedor" class="form-control" type="text" readonly>
                                    <span class="input-group-addon btn-info">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12">Inicio de Llenado <span class="required">*</span></label>
                                  <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_inicio_llenado_f">
                                    <input id="f_inicio_llenado_f" name="f_inicio_llenado" class="form-control" type="text" readonly>
                                    <span class="input-group-addon btn-info">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                  <label class="control-label col-md-4 col-sm-4 col-xs-12">Fin de Llenado <span class="required">*</span></label>
                                  <div class="input-group date col-md-8 col-sm-8 col-xs-12 f_fin_llenado_f">
                                    <input id="f_fin_llenado_f" name="f_fin_llenado" class="form-control" type="text" readonly>
                                    <span class="input-group-addon btn-info">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>

                                
                                <div class="form-group">
                                  <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button id="btn-clean-packinglist" type="reset" class="btn btn-primary">Limpiar</button>
                                    <button id="btn-guardar-packinglist" type="submit" class="btn btn-success">Finalizar</button>
                                  </div>
                                </div>

                              </form>
                              
                            </div>
                          </div>

                        </div>
                      </div>

                      <center>
                      <form id="form-exportar" method="post" action="<?php echo PATH."/backend/helpers/ExportAs.php"; ?>" target="_blank" >
                        <input type="hidden" name="format" value="xls" />
                        <input type="hidden" name="title" value="Packinglist" />
                        <input type="hidden" id="data" name="data" />
                        <input type="hidden" id="id_export" name="id_export" />
                        <input type="hidden" id="file" name="file" value="Packinglist" />
                        <a id="btn-exportar-packinglist" class="btn btn-default" style="display: none;"><i class="fa fa-file-excel-o"></i> Exportar</a>
                      </form>
                      </center>
                      
                      <div class="table-responsive">
                      <table id="datatable-finalizar-packinglist" class="table table-striped table-bordered" style="font-size: 11px;">
                        <thead>
                          <tr>
                            <th>Asociación</th>
                            <th>Tipo de Caja</th>
                            <th>F.Corte</th>
                            <th>Empacadora</th>
                            <th>Código</th>
                            <th>Nombre del Productor</th>
                            <th>Nro de Cajas</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                            <th>18</th>
                            <th>19</th>
                            <th>20</th>
                          </tr>
                        </thead>
                        <tbody>
                          
                        </tbody>
                      </table>
                      </div>

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.modal -->

              <!-- Loading -->
              <div id="modal-loading" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">Cargando</h4>
                    </div>
                    <div class="modal-body">
                      
                      <div class="row"><!--  uploaded  -->
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <div class="x_panel" style="text-align: center;">
                            <img src="images/loading.gif">
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
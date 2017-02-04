<?php $view = 'home'; include_once 'header.inc.php'; ?>
<?php
//valida permisos
  $permitidos = ['Home']; // array de permisos para estar en esta página
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

            <div class="clearfix"></div>
            <div class="row">
              

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel" style="margin: 0 auto; text-align: center;">
                  <h1>CENTRAL PIURANA DE ASOCIACIONES DE PEQUEÑOS PRODUCTORES DE BANANO ORGÁNICO-CEPIBO</h1>
                  <h2>  Avenida Jose de Lama # 1605 – Sullana – Perú</h2>
                  <h2>Telfono: 073-490087</h2>
                  <img src="images/logo.png" style="margin: 0 auto; width: 60%;">
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
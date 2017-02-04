<?php
session_start();
if (!empty($_SESSION['name_session'])) {
  if ($_SESSION['name_session'] == 'cepibo') {
    header("Location: home.php"); // pagina admin
  } else {
    header("Location: ../"); // pagina promocional
  }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cepibo</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="css/custom.css" rel="stylesheet">

    <link rel="icon" type="image/icon" href="images/favicon.ico">

  </head>

  <body style="background:#669932;">
    <div class="">
      <a class="hiddenanchor" id="toregister"></a>
      <a class="hiddenanchor" id="tologin"></a>

      <div id="wrapper">
        <div id="login" class=" form">
          <section class="login_content">
            <div align="center">
              <img src="images/logo.png" width="60%">
            </div>
            <form>
              <h1>Acceso</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" id="username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" id="password" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="#" id="btn-login">Ingresar</a>
                <!-- <a class="reset_pass" href="#">¿Olvidó su contraseña?</a> -->
              </div>
              <div class="clearfix"></div>
              <br>
              <div style="display: none;" class=" licence alert alert-warning"></div>
              <div class="clearfix"></div>
              <div class="separator">

                <div class="clearfix"></div>
                <br />
                <div>
                  <p style="color: #fff;">&copy;2016 Todos los derechos reservados.</p>
                  <label style="display: none;">Autor: Henry Cumbicus. hcumbicusr@gmail.com</label>
                </div>
              </div>
            </form>
          </section>
        </div>

      </div>
    </div>

    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="js/index.js"></script>
  </body>
</html>
<?php 
session_start();
if (empty($_SESSION['name_session'])) {
  header("Location: index.php"); // pagina login
}else {
  if ($_SESSION['name_session'] != 'cepibo') {
    header("Location: index.php"); // pagina login
  }
}
include 'backend/helpers/constantes.php';
//echo "<pre>";die(var_dump($_SESSION));
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Administrador</title>

    <!-- Bootstrap -->
    <link href="<?php echo PATH.'/'; ?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="<?php echo PATH.'/'; ?>js/datepicker/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="<?php echo PATH.'/'; ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- jVectorMap -->
    <link href="<?php echo PATH.'/'; ?>css/maps/jquery-jvectormap-2.0.3.css" rel="stylesheet"/>

    <!-- Custom Theme Style -->
    <link href="<?php echo PATH.'/'; ?>css/custom.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="<?php echo PATH.'/'; ?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">

    <!-- PNotify -->
    <link href="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="<?php echo PATH.'/'; ?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo PATH.'/'; ?>vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
    

    <link href="<?php echo PATH.'/'; ?>css/<?php echo $view; ?>.css" rel="stylesheet">

    <link rel="icon" type="image/icon" href="images/favicon.ico">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">

        <?php include_once 'menu.inc.php'; ?>

        <!-- top navigation -->
        <div class="top_nav">

          <div class="nav_menu">
            <nav class="" role="navigation">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/user.png" alt=""><?php echo $_SESSION['username']; ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="mi_perfil.php">  Perfil</a>
                    </li>
                    <li><a href="javascript:void(0)" class="btn-logout" ><i class="fa fa-sign-out pull-right"></i> Salir</a>
                    </li>
                  </ul>
                </li>

              </ul>
            </nav>
          </div>

        </div>
        <!-- /top navigation -->
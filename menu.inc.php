<div class="col-md-3 left_col menu_fixed">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="home.php" class="site_title"><i class="glyphicon glyphicon-certificate"></i> <span>CEPIBO</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile">
              <div class="profile_pic">
                <img src="images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Bienvenido,</span>
                <h2><?php echo $_SESSION['username']; ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3><?php echo $_SESSION['nom_tipo_usuario']; ?></h3>
                <ul class="nav side-menu">
                  <?php $permisos = $_SESSION['permisos'];
                    for ($i=0; $i < count($permisos); $i++) {  
                      if ($permisos[$i]['id_padre'] == '0') { // solo los item principales
                      ?>
                    <li>
                      <a href="<?php echo $permisos[$i]['nombre_pagina']; ?>"><i class="<?php echo $permisos[$i]['class_icon']; ?>"></i> <?php echo $permisos[$i]['descripcion']; ?> <?php if(!empty($permisos[$i+1])){ echo ($permisos[$i]['id'] == $permisos[$i+1]['id_padre'])? "<span class='fa fa-chevron-down'></span>" : ''; } ?></a>

                              <ul class="nav child_menu">
                            <?php for ($j=0; $j < count($permisos); $j++) { ?>
                            <?php if ($permisos[$j]['id_padre'] == $permisos[$i]['id']) { ?>
                                <li>
                                  <a href="<?php echo $permisos[$j]['nombre_pagina']; ?>"><?php echo $permisos[$j]['descripcion']; ?></a>
                                </li>
                            <?php } // end if 
                              } // end for into ?>
                              </ul>
                    </li>
                  <?php }  // end if
                    }// end for ?>
                  
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a class="btn-logout" data-toggle="tooltip" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>
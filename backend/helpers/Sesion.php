<?php
function iniciarSesion($param)
{        
    if (!empty($param)) { // si se han ingresado los parametros
        include_once './Funciones.php';
        include_once './constantes.php';
        extract($param);  // $usuario, $clave
        
        $data =  http_build_query(array(
                "solicitud" => "login",
                "username" => "$username",
                "password" => "$password",
                "function" => "login"
            )
        );
        $opciones = array('http' => 
            array( 
                'method'  => 'POST', 
                'header'  => 'Content-type: application/x-www-form-urlencoded', 
                'content' => $data 
            ) 
        ); 

        $contexto  = stream_context_create($opciones); 
        //die(var_dump(PATH));
        $resultado = file_get_contents(PATH."/".API_NAME."/".MOD_USUARIOS, false, $contexto); 
        //die(var_dump($resultado));
        //echo $resultado;
        //die();
        //Funciones::CallAPI("POST", "http://".IP_SERVER.PORT_SERVER."/".API_NAME."/".API_VERSION."/".MODULE_USUARIOS."/Usuarios.php", $data);
        
        $result = json_decode($resultado, true); // array con todos los permisos del usuario
        //echo "<pre>";
        //die(var_dump($result));
        $user = $result['user']; // array con todos los permisos del usuario
        //die(var_dump($user));
        $permisos = $result['permisos']; // array con todos los permisos del usuario
        $licence = $result['licence']; // array con todos los permisos del usuario
        //$num =  count($permisos); // cantidad de permisos

        if (!empty($user)){ // si se encontraron permisos para el usuario
            //die(var_dump($config));
            session_start();
            $_SESSION['last_access'] = time();    
            $_SESSION['name_session'] = "cepibo";
            $_SESSION['inicio_sesion'] = date("d/m/Y H:i:s");       
            $_SESSION['id_user'] = $user[0]['id'];
            $_SESSION['id_tipo_usuario'] = $user[0]['id_tipousuario'];
            $_SESSION['nom_tipo_usuario'] = $user[0]['nom_tipo_usuario'];
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['nombres'] = $user[0]['nombres'];
            $_SESSION['apellidos'] = $user[0]['apellidos'];
            $_SESSION['email'] = $user[0]['email'];
            $_SESSION['telefono'] = $user[0]['telefono'];
            $_SESSION['direccion'] = $user[0]['direccion'];
            $_SESSION['permisos'] = $permisos;

            //die(var_dump($_SESSION));
            echo json_encode(array("estado" => "success", "message" => "Bienvenido ".$_SESSION['username'], "licence" => $licence));
            exit();

        } else { // el usuario no tiene permisos
            echo json_encode(array("estado" => "empty", "message" => "El usuario ingresado no se ha encontrado", "licence" => $licence));
            exit();
        }
        
    }else{ // cuando el array param esta vacio
        echo json_encode(array("estado" => "error", "message" => "Debe ingresar usuario y clave"));
        exit();
    }
}

function cerrarSesion ()
{
    session_start();
    $_SESSION = array();
    session_destroy();
    echo json_encode(array("estado" => "success", "mensaje" => "Sesión finalizada"));
    exit();
    //return true;
}


function init(){
    switch ($_POST['solicitud']) {
        case 'login': iniciarSesion($_POST);
            break;
        case 'logout': cerrarSesion();
            break;
        default:
            break;
    }
}

init();
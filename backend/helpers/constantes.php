<?php
//Configuracion de la fecha segun la region
date_default_timezone_set('America/Lima');
setlocale(LC_ALL,"es_ES");
/**
 * constantes para conexion con API
 */

define("IP_SERVER" , "localhost"); // IP del servidor que contiente la API
define("PORT_SERVER" , ":80");
define("API_NAME" , "backend");
define("DS" , DIRECTORY_SEPARATOR);
define("PY_NAME" , "py_cepibo"); 

/*
define("IP_SERVER" , "localhost"); // IP del servidor que contiente la API
define("PORT_SERVER" , ":88");
define("API_NAME" , "/proancosys/terminal/API_SERVER/proanco_api");
*/
//define("PATH", "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/".PY_NAME); //desarrollo
define("PATH", "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']); // produccion

define("MOD_USUARIOS" , "usuario");
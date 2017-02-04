<?php
require_once '../helpers/HttpHelper.php';
header('Access-Control-Allow-Origin: *');  
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
//die(var_dump($_POST));
$method 	= $_SERVER['REQUEST_METHOD'];

$controller = ucfirst($_REQUEST['controller']);
$controllers = scandir('./controllers');
//$controllersName = null;
//echo "<pre>";
//die(var_dump($_REQUEST));
HttpHelper::requestHelper();

for ($i=0; $i < count($controllers); $i++) { //Obtiene el nombre limpio del controller
	if (strpos($controllers[$i], 'Controller') !== false) {
		$controllersName[] = substr(trim($controllers[$i]), 0, strpos(trim($controllers[$i]), 'Controller'));
	}
}
//die(var_dump($_REQUEST));
$aux_controller = pluralControllers($controller); // valida el controller en plural

$arr = explode("_", $controller);
//die(var_dump($arr));
$controller = "";
for ($i=0; $i < count($arr) ; $i++) { 
	$controller .= ucfirst(strtolower(trim($arr[$i])));
}
//die(var_dump($controller));
if ( in_array($controller, $controllersName) ) { // valido si existe el controller ingresado por url
	$controller = ucfirst(strtolower(trim($controller))).'Controller';
	include_once 'controllers/'.$controller.'.php';
	$obj = new $controller($_REQUEST, $method);
	echo $obj->response(); //devuelve respuesta
	exit;
}
if ( $aux_controller !== false ){ // valido con los auxiliares => plural
	$controller = ucfirst(strtolower(trim($aux_controller))).'Controller';
	include_once 'controllers/'.$controller.'.php';
	$obj = new $controller($_REQUEST, $method);
	echo $obj->response(); //devuelve respuesta
	exit;
} else {
	echo json_encode(['message' => 'El controlador '.$controller.' NO existe.', 'code' => '404']);
	exit;
}



// valida terminos en plural
function pluralControllers($controller) {
	$arr = [
		"producto" => "productos",
		"categoria" => "categorias",
		"usuario" => "usuarios"
	];

	if (in_array($controller, $arr)) {
		return array_search($controller, $arr);
	}
	return  false;
}
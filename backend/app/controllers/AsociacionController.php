<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class AsociacionController extends Controller implements IController{
	private $table = 'asociaciones';
	private $conn = null;
	private $params;
	private $http_method;
	private $type_search;
	private $functionPref;
	private $result;

	public function __construct($params = array(), $http_method = 'GET') {
		unset($params['_']);
		$this->params 		= $params;
		$this->http_method 	= $http_method;
		$this->functionPref = empty($params['function']) ? null : $params['function'];

		//-------------- conexion a BD
		$class = new Database();
		$this->conn = $class->selectManager();

		//die(var_dump($params));

		switch ($http_method) {
		 	case 'GET':
		 		if (empty($this->functionPref)) {
			 		if (empty($this->params)){
			 			$this->index();
			 		} else {
			 			$this->search();
			 		}
		 		} else {
		 			unset($this->params['function']);
		 			call_user_func(array($this,$this->functionPref));
		 		}
		 		break;
		 	case 'POST':
		 		if (empty($this->functionPref)) {
			 		$this->create();
		 		} else {
		 			unset($this->params['function']);
		 			call_user_func(array($this,$this->functionPref));
		 		}
		 		break;
		 	case 'PUT':
		 		if (empty($this->functionPref)) {
			 		$this->update();
		 		} else {
		 			unset($this->params['function']);
		 			call_user_func(array($this,$this->functionPref));
		 		}
		 		break;
		 	case 'DELETE':
		 		if (empty($this->functionPref)) {
			 		$this->delete();
		 		} else {
		 			unset($this->params['function']);
		 			call_user_func(array($this,$this->functionPref));
		 		}
		 		break;
		 	
		 	default://GET
				if (empty($this->functionPref)) {
			 		if (empty($this->params)){
			 			$this->index();
			 		} else {
			 			$this->search();
			 		}
		 		} else {
		 			unset($this->params['function']);
		 			call_user_func(array($this,$this->functionPref));
		 		}
		 		break;
		 }
	}

	//---------------------------------------------------------------------------------------

	public function index() {
		$this->result = $this->conn->select("SELECT * FROM ".$this->table." WHERE estado = '1'");
	}

	public function search() {
		if ($this->type_search == 'filtro') {
			$condicion = 'AND';
		}else {
			$condicion = 'OR';
		}
		foreach ($this->params as $key => $value) {
			$where[]= " `".$key."` = '".$value."'";
		}
		$this->result = $this->conn->select("SELECT * FROM ".$this->table." WHERE ". implode(" $condicion ", $where), true, true);
	}

	public function update() {
		$this->result = ['message' => 'Sin implementar PUT'];
	}

	public function create() {
		
		die(var_dump($_POST));

		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'OK'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => 'Ocurrió un error'];
		}
	}

	public function delete() {
		$this->result = ['message' => 'Sin implementar DELETE'];
	}

	//--------------------------- funciones personalizadas -----------------------------------------------------
	public function getCargos() {
		$this->result = $this->conn->select("SELECT * FROM cargos WHERE activo = '1'");
	}

	public function getAsociaciones() {
		$this->result = ["data" =>  $this->conn->select("SELECT * FROM ".$this->table." WHERE estado = '1'")];
	}

	public function getEmpacadoraByAsociacion() { // retorna el id de la relacion asociacion_empacadora
		$id_asociacion = $this->params['id_asociacion'];
		$this->result = ["data" =>  $this->conn->select("SELECT ae.*, e.nombre FROM asociacion_empacadora ae INNER JOIN empacadoras e ON ae.id_empacadora = e.id WHERE ae.id_asociacion = '$id_asociacion' AND ae.estado = '1'")];
	}

	public function getCuadrillas()
	{
		$id_asociacion = $this->params['id_asociacion'];
		$query = "SELECT c.* from cuadrillas c WHERE c.id_asociacion = '$id_asociacion' and c.activo = '1'";
		$this->result = $this->conn->select($query);
	}
	
	public function getSiguienteCuadrilla()
	{
		$id_asociacion = $this->params['id_asociacion'];
		$this->result = $this->conn->select("SELECT ifnull(Max(substring(nombre,11)),0)+1 nro from cuadrillas where id_asociacion = '$id_asociacion'")[0];
	}

	public function saveCuadrilla()
	{
		if (empty($_POST['nombre'])) {
			$this->result = ['estado' => 'warning', 'message' => 'No se ha ingresado nombre de cuadrilla.'];
			exit;
		}
		$val = $this->conn->insert($_POST)->inTable("cuadrillas");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => 'Ocurrió un error'];
		}
	}

	public function saveEmpacadora()
	{
		if (empty($_POST['nombre'])) {
			$this->result = ['estado' => 'warning', 'message' => 'No se ha ingresado nombre de empacadora.'];
			exit;
		}
		$date = date("Y-m-d H:i:s");
		$val = $this->conn->insert(['nombre' => strtoupper($_POST['nombre']), "created_at" => $date], false)->inTable("empacadoras");
		if ($val === true) {
			$emp = $this->conn->select("SELECT id FROM empacadoras WHERE nombre = '".strtoupper($_POST['nombre'])."' AND created_at = '$date'", false)[0];
			$val = $this->conn->insert(["id_asociacion"=>$_POST['id_asociacion'], "id_empacadora" => $emp['id']], false)->inTable("asociacion_empacadora");
			if ($val===true) {
				$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
			} else {
				$this->result = ['estado'=> 'error', 'message' => 'Ocurrió un error'];
			}
		} else {
			$this->result = ['estado'=> 'error', 'message' => 'Ocurrió un error'];
		}
		$this->conn->close();
	}

	public function verificaDatos() {
		$option = strtoupper($this->params['dato']);
		$valor = trim($this->params['valor']);
		$message = "El $option ya está registrado.";
		switch ($option) {
			case 'EMPACADORA':
				$query = "SELECT * FROM empacadoras WHERE nombre = '$valor' AND estado = '1' ";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'EMAIL':
				$query = "SELECT * FROM productores WHERE trim(email) = '$valor'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
		}
		$this->result = $message;
	}



	
	//--------------------------- fin funciones personalizadas --------------------------------------------------



	// utilizando metodo heredado
	public function response($type = 'json') {
		if ($type = 'json') {
			return parent::responseJson($this->result);
		}else {
			return parent::response($this->result);
		}
	}

}
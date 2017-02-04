<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class TrabajadorController extends Controller implements IController{
	private $table = 'trabajador';
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
		$this->result = $this->conn->select("SELECT * FROM ".$this->table);
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
		
		//die(var_dump($_POST));
		//$val = $this->conn->insert($_POST,true, true)->inTable($this->table);
		$val = $this->conn->spInsert('sp_registrar_trabajador', $_POST);

		if ($val== 'OK') {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function delete() {
		$this->result = ['message' => 'Sin implementar DELETE'];
	}

	//--------------------------- funciones personalizadas -----------------------------------------------------
	public function verificaDatos() {
		$option = strtoupper($this->params['dato']);
		$valor = trim($this->params['valor']);
		$message = "El $option ya está registrado.";
		switch ($option) {
			case 'DNI':
				$query = "SELECT * FROM trabajador WHERE trim(dni) = '$valor'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'EMAIL':
				$query = "SELECT * FROM trabajador WHERE trim(email) = '$valor'";
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
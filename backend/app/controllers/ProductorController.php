<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class ProductorController extends Controller implements IController{
	private $table = 'productores';
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
		$sql = $this->conn->select("SELECT p.*, '' as asociacion FROM ".$this->table ." p ", false);
		for ($i=0; $i < count($sql); $i++) { 
			$aso = $this->conn->select("SELECT a.nombre FROM productor_asociacion pa INNER JOIN asociaciones a ON pa.id_asociacion = a.id WHERE pa.activo = '1' and pa.id_productor = '".$sql[$i]['id']."' ", false);
			$aux = array();
			for ($j=0; $j < count($aso); $j++) { 
				$aux[] = $aso[$j]['nombre'];
			}
			$sql[$i]['asociacion'] = implode(", ", array_unique($aux));
		}
		$this->result = ["data" => $sql];
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
		$val = $this->conn->spInsert('sp_registrar_productor', $_POST);

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
				$query = "SELECT * FROM productores WHERE trim(dni) = '$valor'";
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

	public function saveTerreno() {
		unset($_POST['function']);
		//die(var_dump($_POST));
		$val = $this->conn->spInsert('sp_registrar_terreno', $_POST);

		if ($val== 'OK') {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function listTerrenos()
	{
		$query = "SELECT t.*, concat(p.apellidos, ' ', p.nombres) as productor, p.dni, pt.condicion, pt.observacion, pt.documentacion, pt.url_docs, a.nombre as asociacion  FROM productores p INNER JOIN productor_terreno pt ON p.id = pt.id_productor AND pt.estado = '1' INNER JOIN terrenos t ON pt.id_terreno = t.id AND t.estado = '1' INNER JOIN  asociaciones a ON pt.id_asociacion = a.id ";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function getProductorByAsociacion()
	{
		$id_asociacion = $this->params['id_asociacion'];
		$sql = "SELECT p.id, concat(p.nombres,' ', p.apellidos) as nombre FROM productor_asociacion pa INNER JOIN productores p ON pa.id_productor = p.id WHERE pa.id_asociacion = '$id_asociacion' AND pa.activo = '1'";
		$this->result = ["data" => $this->conn->select($sql)]; // retorna arr con  el ID del productor
	}

	public function getTerrenosByProductorAsociacion()
	{
		$id_asociacion = $this->params['id_asociacion'];
		$id_productor = $this->params['id_productor'];
		$sql = "SELECT pt.id, concat(t.codigo,' - ', t.area_total,t.unidad_medida,' , ', t.area_cultivo,t.unidad_medida, ' - ', pt.condicion) as nombre FROM productor_terreno pt INNER JOIN terrenos t ON pt.id_terreno = t.id WHERE pt.id_asociacion = '$id_asociacion' AND pt.id_productor = '$id_productor' AND pt.estado = '1'";
		$this->result = ["data" => $this->conn->select($sql)]; // retorna arr con  el ID del productor_terreno
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
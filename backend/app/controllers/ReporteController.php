<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class ReporteController extends Controller implements IController{
	private $table = '*';
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
	public function getProduccionAsociaciones()
	{
		include_once  '../helpers/Funciones.php';
		//die(var_dump($_POST));
		$f_inicio = $_POST['f_inicio'];
		$f_fin = $_POST['f_fin'];
		$date = date("d/m/Y");
		$f_inicio = empty($f_inicio)? $date." 00:00:00" : $f_inicio." 00:00:00";
		$f_fin = empty($f_fin)? $date." 23:59:59" : $f_fin." 23:59:59";
		$f_inicio = Funciones::formatoFechas($f_inicio);
		$f_fin = Funciones::formatoFechas($f_fin);
		$arr = [];
		$a = "SELECT * FROM asociaciones WHERE estado = '1'";
		$asociaciones = $this->conn->select($a, false);

		foreach ($asociaciones as $key => $column) {
			//UNIX_TIMESTAMP(pl.f_corte)
			$q = "SELECT DATE(pl.f_corte) as f_corte, SUM(pl.nro_cajas) as total_cajas FROM packing_list pl ";
			$q .= " INNER JOIN packing p ON pl.id_packing = p.id ";
			$q .= " INNER JOIN productor_terreno pt ON pl.id_productor_terreno = pt.id ";
			$q .= " WHERE p.estado IN('1','2') ";
			$q .= " AND pt.id_asociacion = '".$column['id']."'  ";
			if (!empty($_POST['f_inicio']) && !empty($_POST['f_fin'])) {
				$q .= " AND ( pl.f_corte BETWEEN '$f_inicio' AND '$f_fin') ";
			} else if (!empty($_POST['f_inicio']) && empty($_POST['f_fin'])) {
				$q .= " AND  pl.f_corte >= '$f_inicio' ";
			} else if (empty($_POST['f_inicio']) && !empty($_POST['f_fin'])) {
				$q .= " AND  pl.f_corte <= '$f_fin' ";
			}
			$q .= " GROUP BY DATE(pl.f_corte) ";
			$q .= " ORDER BY pl.f_corte ";
			$cajas = $this->conn->select($q, false);
			$value_hc = [];
			if (!empty($cajas)) {
				foreach ($cajas as $k => $row) {
					$value_hc[] = [ $row['f_corte'], intval($row['total_cajas']) ];
				}
				
				$arr[] = [
					"name" => $column['nombre'],
					"data" => $value_hc
				];
			}
		}

		$this->result = $arr;
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
<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class AlmacenController extends Controller implements IController{
	private $table = 'almacen';
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
			case 'COD_MATERIAL':
				$query = "SELECT * FROM materiales WHERE trim(codigo) = '$valor'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'NOMB_MATERIAL':
				$query = "SELECT * FROM materiales WHERE trim(nombre) = '$valor'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
		}
		$this->result = $message;
	}

	public function saveMaterial() {
		unset($_POST['function']);
		//die(var_dump($_POST));
		$val = $this->conn->spInsert('sp_registrar_material', $_POST);

		if ($val== 'OK') {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function listUnidadMedida() {
		$query = "SELECT * from unidad_medida where activo = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function listMateriales()
	{
		$query = "SELECT m.* from materiales m where m.estado = '1' order by m.codigo";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function listTipoCaja()
	{
		$query = "SELECT m.* from tipo_caja m where m.activo = '1'";
		$this->result = ["data" => $this->conn->select($query)];
	}
	
	public function listRegistroSalidas()
	{
		$query = "SELECT sm.*, tc.nombre as caja, date_format(sm.created_at, '%d/%m/%Y') as fecha, WEEKOFYEAR(sm.created_at) as semana, a.nombre as asociacion, c.nombre as cuadrilla from salida_material sm inner join cuadrillas c on sm.id_cuadrilla = c.id inner join tipo_caja tc on sm.id_tipo_caja = tc.id inner join asociaciones a on c.id_asociacion = a.id  where sm.estado = '1'";
		$this->result = ["data" => $this->conn->select($query)];
	}


	public function listMaterialesCaja()
	{
		$id_tipo_caja = $this->params['id_tipo_caja'];
		$query = "SELECT m.*, mtc.multiplo, mtc.calcular from material_tipo_caja mtc inner join materiales m on mtc.id_material = m.id  where mtc.id_tipo_caja = '$id_tipo_caja' and mtc.activo = '1' and m.estado = '1' order by m.tipo desc, m.nombre";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function listMaterialesSinCaja()
	{
		$id_tipo_caja = $this->params['id_tipo_caja'];
		$query = "SELECT m.* from materiales m where m.id NOT IN(SELECT mtc.id_material FROM material_tipo_caja mtc WHERE mtc.id_tipo_caja = '$id_tipo_caja' AND mtc.activo = '1') and m.estado = '1' order by m.tipo desc, m.nombre";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function getDetalleMaterial()
	{
		$id = $this->params['id'];
		$query = "SELECT m.* from materiales m where m.estado = '1' and m.id = '$id'";
		$res = $this->conn->select($query);
		if (!empty($res)) {
			$this->result = $res[0];
		} else {
			$this->result = $res;
		}
	}

	public function saveSalidaMaterial()
	{
		//die(var_dump($_POST));
		$date = date("Y-m-d H:i:s");
		$data_1 = [
			"id_cuadrilla" => $_POST['id_cuadrilla'],
			"id_tipo_caja" => $_POST['id_tipo_caja'],
			"cantidad" => $_POST['cantidad'],
			"created_at" => $date
		];
		// primero: se crea la "orden" de salida para el tipo de caja
		$val_1 = $this->conn->insert($data_1,false)->inTable("salida_material");
		if ($val_1 === true) {
			$salida = $this->conn->select("SELECT id from salida_material where id_cuadrilla = '".$_POST['id_cuadrilla']."' and id_tipo_caja = '".$_POST['id_tipo_caja']."' and created_at = '$date'",false);
			$arr_ids = $_POST['id_materiales'];
			$arr_data = $_POST['materiales'];
			for ($i=0; $i < count($arr_data); $i++) { 
				$data = [
					"id_salida_material" => $salida[0]['id'],
					"id_material" => $arr_ids[$i],
					"cantidad" => $arr_data[$i]
				];
				//segundo: se registran los detalles de la "orden" de salida
				$val_2 = $this->conn->insert($data,false)->inTable("salida_material_detalle");
				//tercero: actualizar stock de materiales
				$val_3 = $this->conn->update("UPDATE materiales SET stock = stock - ".floatval($arr_data[$i])." WHERE id = '".$arr_ids[$i]."'", false);
			}
			$this->result = ["estado" => "success", "message" => "Registrado correctamente."];

/*
			//---------------- envío de correo ---------------
			include '../helpers/Funciones.php';
			$html = <<<EOT
<!DOCTYPE html>
<html lang='es'>
<head>
	<title>Registro de salida de material</title>
</head>
<body>
<h1>Salida de material</h1>
<div class="x_content">
  <form class="form-horizontal form-label-left">
    <div class="form-group">
      <label>Asociación: </label>
      <label id="dt-asociacion-print"></label>
    </div>

    <div class="form-group">
      <label>Cuadrilla: </label>
      <label id="dt-cuadrilla-print"></label>
    </div>

    <div class="form-group">
      <label>Fecha: </label>
      <label id="dt-fecha-print"></label>
    </div>

    <div class="form-group">
      <label>Semana: </label>
      <label id="dt-semana-print"></label>
    </div>

    <div class="form-group">
      <label>Caja: </label>
      <label id="dt-caja-print"></label>
    </div>

    <div class="form-group">
      <label>Nro Cajas: </label>
      <label id="dt-nro_cajas-print"></label>
    </div>

    <div class="form-group">
      <table id="tblDetalleMatPrint" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th width="40px">Código</th>
            <th>Nombre</th>
            <th width="50px">Entregado</th>
            <th width="40px">Unidad</th>
            <th width="70px">Tipo</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </form>
</div>
</body>
</html>
EOT;
			
			$ruta_adjunto = "";
			$email = [
				"name" => "",
				"email" => "",
				"message" => $html,
				"subject" => "Salida de Material",
				"title" => "Almacén CEPIBO",
				"destinatarios" => ['hcumbicusr@gmail.com']
				//"adjunto" => ["ruta" => "", "nombre" => ""]
			];
			Funciones::enviarMailPHPMailer($email);
			//------------------------------------------------
*/		
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
		$this->conn->close();
	}

	public function getDetalleSalida()
	{
		$id_salida = $this->params['id_salida'];
		$query = "SELECT smd.*, date_format(sm.created_at,'%d/%m/%Y %H:%i %p') as fecha, WEEKOFYEAR(sm.created_at) as semana, sm.cantidad as nro_cajas, m.codigo, m.nombre, m.unidad_medida, m.tipo FROM salida_material_detalle smd INNER JOIN salida_material sm on smd.id_salida_material = sm.id INNER JOIN materiales m on smd.id_material = m.id WHERE smd.id_salida_material = '$id_salida' AND smd.activo = '1' ORDER BY m.tipo desc";
		$this->result = ["data" => $this->conn->select($query)];
	}


	public function getMaterialesCaja()
	{
		$id_tipo_caja = $this->params['id_tipo_caja'];
		$query = "SELECT mtc.*, m.codigo, m.nombre, m.tipo, m.unidad_medida FROM material_tipo_caja mtc INNER JOIN materiales m ON mtc.id_material = m.id WHERE mtc.id_tipo_caja = '$id_tipo_caja' AND mtc.activo = '1' ORDER BY m.tipo desc";
		$this->result = ["data" => $this->conn->select($query)];
	}

	public function quitarMaterial()
	{
		$id_mtc = $_POST['id_material_tipo_caja'];
		$val = $this->conn->update("UPDATE material_tipo_caja SET activo = '0' WHERE id = '$id_mtc'");
		if ($val === true) {
			$this->result = ["estado" => "success", "message" => "El material ha sido retirado del tipo de caja."];
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
	}

	public function addStock()
	{
		//die(var_dump($_POST));
		$stock = floatval($_POST['stock']);
		$id = $_POST['id'];
		$proveedor = strtoupper($_POST['proveedor']);
		$observacion = $_POST['observacion'];

		$data = [
			"id_material" => $id,
			"cantidad" => $stock,
			"origen" => "",
			"proveedor" => $proveedor,
			"observacion" => $observacion
		];

		$ins = $this->conn->insert($data, false)->inTable("ingreso_material");
		if ($ins === true) {
			$val = $this->conn->update("UPDATE materiales SET stock = stock + $stock WHERE id = '$id'");
			if ($val === true) {
				$this->result = ["estado" => "success", "message" => "Registrado correctamente."];
			} else {
				$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
			}
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
	}

	public function saveMaterialCaja()
	{
		//die(var_dump($_POST));
		$val = $this->conn->insert($_POST)->inTable("material_tipo_caja");
		if ($val === true) {
			$this->result = ["estado" => "success", "message" => "Registrado correctamente."];
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
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
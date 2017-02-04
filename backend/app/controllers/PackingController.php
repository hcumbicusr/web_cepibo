<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class PackingController extends Controller implements IController{
	private $table = 'packing';
	private $conn = null;
	private $params;
	private $http_method;
	private $type_search;
	private $functionPref;
	private $result;

	public function __construct($params = array(), $http_method = 'GET') {
		unset($params['_']);
		$this->http_method 	= $http_method;
		$this->functionPref = empty($params['function']) ? null : $params['function'];
		if (empty($params['function'])) unset($params['function']);
		$this->params 		= $params;

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
		$sql = "SELECT p.* ";
		$sql .= ", c.numero as contenedor";
		$sql .= ", cl.nombre as cliente";
		$sql .= ", v.nombre as vapor";
		$sql .= ", tf.nombre as tipo_funda";
		$sql .= ", po.nombre as puerto_origen";
		$sql .= ", pd.nombre as puerto_destino";
		$sql .= " FROM packing p ";
		$sql .= " INNER JOIN contenedor c ON p.id_contenedor = c.id ";
		$sql .= " INNER JOIN clientes cl ON p.id_cliente = cl.id ";
		$sql .= " INNER JOIN vapor v ON p.id_vapor = v.id ";
		$sql .= " INNER JOIN tipo_funda tf ON p.id_tipo_funda = tf.id ";
		$sql .= " INNER JOIN puertos po ON p.id_puerto_origen = po.id ";
		$sql .= " INNER JOIN puertos pd ON p.id_puerto_destino = pd.id ";
		$sql .= " WHERE p.estado = '2' "; //0 = inactivo, 1 = finalizado, 2 = en proceso
		$sql .= " ORDER BY p.codigo "; 
		
		$sql = $this->conn->select($sql);
		
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
		include '../helpers/Funciones.php';
		//die(var_dump($_POST));
		$f_llegada_contenedor = trim($_POST['f_llegada_contenedor']);
		$f_salida_contenedor = trim($_POST['f_salida_contenedor']);
		$f_inicio_llenado = trim($_POST['f_inicio_llenado']);
		$f_fin_llenado = trim($_POST['f_fin_llenado']);

		$_POST['f_llegada_contenedor'] = Funciones::parseDate($f_llegada_contenedor);
		$_POST['f_salida_contenedor'] = Funciones::parseDate($f_salida_contenedor);
		$_POST['f_inicio_llenado'] = Funciones::parseDate($f_inicio_llenado);
		$_POST['f_fin_llenado'] = Funciones::parseDate($f_fin_llenado);

		Funciones::filtraArray($_POST); // filtra caracteres raros
		//select concat('PK',lpad(ifnull(Max(substring(codigo,3)),0)+1,10,'0')) from packing;
		$codigo = $this->conn->select("select concat('PK',lpad(ifnull(Max(substring(codigo,3)),0)+1,10,'0')) as codigo from packing;", false)[0];
		$_POST['codigo'] = $codigo['codigo'];
		$val = $this->conn->insert($_POST)->inTable("packing");

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
		$aguja = ["#","'","\"","´","$"];
		$option = strtoupper($this->params['dato']);
		$option = str_replace($aguja, "", $option);
		$valor = trim($this->params['valor']);
		$valor = str_replace($aguja, "", $valor);
		$message = "El $option ya está registrado.";
		switch ($option) {
			case 'ID_VAPOR':
				$query = "SELECT * FROM vapor WHERE trim(nombre) = '$valor' and activo = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'ID_CLIENTE':
				$query = "SELECT * FROM clientes WHERE trim(nombre) = '$valor' and estado = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'ID_CONTENEDOR':
				$query = "SELECT * FROM contenedor WHERE trim(numero) = '$valor' and estado = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'ID_TIPO_FUNDA':
				$query = "SELECT * FROM tipo_funda WHERE trim(nombre) = '$valor' and activo = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'ID_PUERTO_ORIGEN':
				$query = "SELECT * FROM puertos WHERE trim(nombre) = '$valor' and activo = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
			case 'ID_PUERTO_DESTINO':
				$query = "SELECT * FROM puertos WHERE trim(nombre) = '$valor' and activo = '1'";
				$rest = $this->conn->select($query);
				if (count($rest) == 0) {$message = true;}
				break;
		}
		$this->result = $message;
	}

	public function getVapor()
	{
		$query = "SELECT v.* FROM vapor v WHERE v.activo = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}
	public function getClientes()
	{
		$query = "SELECT c.* FROM clientes c WHERE c.estado = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}
	public function getContenedores()
	{
		$query = "SELECT c.id, c.numero as nombre FROM contenedor c WHERE c.estado = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}
	public function getTipoFunda()
	{
		$query = "SELECT t.* FROM tipo_funda t WHERE t.activo = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}
	public function getPuertos()
	{
		$query = "SELECT p.* FROM puertos p WHERE p.activo = '1' ";
		$this->result = ["data" => $this->conn->select($query)];
	}


	public function saveItemVapor() {
		//unset($_POST['function']);
		//die(var_dump($_POST));
		$aguja = ["#","'","\"","´","$"];
		$nombre = @strtoupper(trim($_POST['nombre_item']));
		$nombre = str_replace($aguja, "", $nombre);
		$val = $this->conn->insert(["nombre"=>$nombre])->inTable("vapor");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function saveItemCliente() {
		//unset($_POST['function']);
		//die(var_dump($_POST));
		$aguja = ["#","'","\"","´","$"];
		$nombre = @strtoupper(trim($_POST['nombre_item']));
		$nombre = str_replace($aguja, "", $nombre);
		$val = $this->conn->insert(["nombre"=>$nombre])->inTable("clientes");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function saveItemContenedor() {
		//unset($_POST['function']);
		//die(var_dump($_POST));
		$aguja = ["#","'","\"","´","$"];
		$nombre = @strtoupper(trim($_POST['nombre_item']));
		$nombre = str_replace($aguja, "", $nombre);
		$data = [
			"numero" => $nombre,
			"descripcion" => null,
			"marca" => null,
			"modelo" => null,
			"payload" => "0",
			"largo" => null,
			"ancho" => null,
			"altura" => null,
			"certificacion" => ""
		];
		$val = $this->conn->insert($data)->inTable("contenedor");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function saveItemTipoFunda() {
		//unset($_POST['function']);
		//die(var_dump($_POST));
		$aguja = ["#","'","\"","´","$"];
		$nombre = @strtoupper(trim($_POST['nombre_item']));
		$nombre = str_replace($aguja, "", $nombre);
		$val = $this->conn->insert(["nombre"=>$nombre])->inTable("tipo_funda");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function saveItemPuerto() {
		//unset($_POST['function']);
		//die(var_dump($_POST));
		$aguja = ["#","'","\"","´","$"];
		$nombre = @strtoupper(trim($_POST['nombre_item']));
		$nombre = str_replace($aguja, "", $nombre);
		$val = $this->conn->insert(["nombre"=>$nombre])->inTable("puertos");
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function savePackingList()
	{
		//die(var_dump($_POST));
		include '../helpers/Funciones.php';
		$date = date("Y-m-d H:i:s");
		$pallets = $_POST['pallets'];
		$_POST['f_corte'] = Funciones::formatoFechas($_POST['f_corte']." 00:00:00");
		$packing_list = [
			"id_packing" => $_POST['id_packing'],
			"id_productor_terreno" => $_POST['id_productor_terreno'],
			"id_tipo_caja" => $_POST['id_tipo_caja'],
			"id_asociacion_empacadora" => $_POST['id_asociacion_empacadora'],
			"f_corte" => $_POST['f_corte'],
			"nro_cajas" => '0',
			"created_at" => $date
		];
		$pl = $this->conn->insert($packing_list, false)->inTable("packing_list");
		if ($pl===true) {
			$id_packing_list = $this->conn->select("SELECT id FROM packing_list WHERE id_packing = '".$_POST['id_packing']."' AND created_at = '$date'", false)[0];
			$nro_cajas = 0;
			for ($i=0; $i < count($pallets); $i++) { 
				if (intval($pallets[$i]) > 0) { // solo registras las cantidades de cajas mayores a 0
					$nro_cajas += intval($pallets[$i]);
					$detalle_list = [
						"id_packing_list" => $id_packing_list['id'],
						"nro_pallet" => ($i+1),
						"cantidad" => $pallets[$i],
						"created_at" => $date
					];
					$pl = $this->conn->insert($detalle_list, false)->inTable("packing_list_detalle");
				}
			}
			$val = $this->conn->update("UPDATE packing_list SET nro_cajas = '$nro_cajas' WHERE id_packing = '".$_POST['id_packing']."'  AND created_at = '$date'", false);
			//$val = $this->conn->update("UPDATE packing SET estado = '1' WHERE id = '".$_POST['id_packing']."'", false);
			if ($val===true) {
				$this->result = ['estado'=> 'success', 'message' => 'Registrado correctamente.'];
			} else {
				$this->result = ['estado'=> 'error', 'message' => $val];
			}
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
		$this->conn->close();
	}

	public function getDetallePacking()
	{
		$id_packing = $this->params['id_packing'];
		$query = "SELECT pl.* ";
		$query .= " , concat(p.apellidos,' ',p.nombres) as nombre_productor";
		$query .= " , t.codigo as codigo_terreno";
		$query .= " , a.nombre as asociacion";
		$query .= " , tc.nombre as tipo_caja";
		$query .= " , e.nombre as empacadora";
		$query .= " , date_format(pl.f_corte, '%d/%m/%Y') as f_corte_format";
		$query .= " FROM packing_list pl ";
		$query .= " INNER JOIN productor_terreno pt ON pl.id_productor_terreno = pt.id";
		$query .= " INNER JOIN productores p ON pt.id_productor = p.id";
		$query .= " INNER JOIN terrenos t ON pt.id_terreno = t.id";
		$query .= " INNER JOIN asociaciones a ON pt.id_asociacion = a.id";
		$query .= " INNER JOIN tipo_caja tc ON pl.id_tipo_caja = tc.id";
		$query .= " INNER JOIN asociacion_empacadora ae ON pl.id_asociacion_empacadora = ae.id";
		$query .= " INNER JOIN empacadoras e ON ae.id_empacadora = e.id";
		$query .= " WHERE pl.id_packing = '$id_packing' ";
		$query .= " AND pl.estado = '1' ";

		$rs = $this->conn->select($query, false);

		$arr = array();

		for ($i=0; $i < count($rs); $i++) { 
			$q = "SELECT pld.* FROM packing_list_detalle pld WHERE pld.id_packing_list = '".$rs[$i]['id']."'";
			$pl = $this->conn->select($q, false);
			$arr[] = [
				"pl" => $rs[$i],
				"pallets" => $pl
			];
		}
		$this->conn->close();
		$this->result = ["data" => $arr];
	}

	public function getDatosPackingById()
	{
		$id_packing = $this->params['id_packing'];
		$sql = "SELECT p.* ";
		$sql .= ", date_format(ifnull(p.f_llegada_contenedor,'0000-00-00 00:00:00'), '%d/%m/%Y %h:%i %p') as f_llegada_contenedor_format";
		$sql .= ", date_format(ifnull(p.f_inicio_llenado,'0000-00-00 00:00:00'), '%d/%m/%Y %h:%i %p') as f_inicio_llenado_format";
		$sql .= ", date_format(ifnull(p.f_fin_llenado,'0000-00-00 00:00:00'), '%d/%m/%Y %h:%i %p') as f_fin_llenado_format";
		$sql .= ", date_format(ifnull(p.f_salida_contenedor,'0000-00-00 00:00:00'), '%d/%m/%Y %h:%i %p') as f_salida_contenedor_format";
		$sql .= ", c.numero as contenedor";
		$sql .= ", cl.nombre as cliente";
		$sql .= ", v.nombre as vapor";
		$sql .= ", tf.nombre as tipo_funda";
		$sql .= ", po.nombre as puerto_origen";
		$sql .= ", pd.nombre as puerto_destino";
		$sql .= " FROM packing p ";
		$sql .= " INNER JOIN contenedor c ON p.id_contenedor = c.id ";
		$sql .= " INNER JOIN clientes cl ON p.id_cliente = cl.id ";
		$sql .= " INNER JOIN vapor v ON p.id_vapor = v.id ";
		$sql .= " INNER JOIN tipo_funda tf ON p.id_tipo_funda = tf.id ";
		$sql .= " INNER JOIN puertos po ON p.id_puerto_origen = po.id ";
		$sql .= " INNER JOIN puertos pd ON p.id_puerto_destino = pd.id ";
		$sql .= " WHERE p.estado IN ('1','2') "; //0 = inactivo, 1 = finalizado, 2 = en proceso
		$sql .= "  AND p.id = '$id_packing' "; 
		$sql .= " ORDER BY p.f_llegada_contenedor "; 
		
		$sql = $this->conn->select($sql)[0];
		
		$this->result = ["data" => $sql];
	}

	public function finalizarPacking()
	{
		//die(var_dump($_POST));
		include '../helpers/Funciones.php';
		$_POST['f_llegada_contenedor'] = Funciones::parseDate($_POST['f_llegada_contenedor']);
		$_POST['f_salida_contenedor'] = Funciones::parseDate($_POST['f_salida_contenedor']);
		$_POST['f_inicio_llenado'] = Funciones::parseDate($_POST['f_inicio_llenado']);
		$_POST['f_fin_llenado'] = Funciones::parseDate($_POST['f_fin_llenado']);

		$update = "UPDATE packing SET f_llegada_contenedor = '".$_POST['f_llegada_contenedor']."'";
		$update .= ", f_salida_contenedor = '".$_POST['f_salida_contenedor']."' ";
		$update .= ", f_inicio_llenado = '".$_POST['f_inicio_llenado']."' ";
		$update .= ", f_fin_llenado = '".$_POST['f_fin_llenado']."' ";
		$update .= ", estado = '1' ";
		$update .= ", updated_at = now() ";
		$update .= "WHERE id = '".$_POST['id_packing']."' ";
		$val = $this->conn->update($update, false);
		
		include_once '../helpers/constantes.php';
		//guardar excel
		$data =  http_build_query(array(
                "format" => "xls",
                "title" => "",
                "id_export" => $_POST['id_packing'],
                "file" => "Packinglist",
                "accion" => "save"
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
        $resultado = file_get_contents(PATH."/".API_NAME."/helpers/ExportAs.php", false, $contexto); 
        //die(var_dump($resultado));
        //$val = true;
		if ($val===true) {
			$this->result = ['estado'=> 'success', 'message' => 'Se ha finalizado el packing'];
			//mail de reporte
			$cod = $this->conn->select("SELECT codigo FROM packing WHERE id = '".$_POST['id_packing']."'")[0];
			global $config;
			$archivo = $config["path"].DS."download".DS."Packinglist ".$cod['codigo'].".xlsx"; // directorio de descargas
			$arrDatos = [
				"name" => "",
				"email" => "",
				"subject" => "Finalización de Packinglist ".$cod['codigo'],
				"message" => "<h2>Se ha finalizado el Packinglist ".$cod['codigo']." </h2>",
				"title" => "CEPIBO SYS",
				"formato" => "",
				"adjunto" => [
					"ruta" => $archivo,
					"nombre" => $cod['codigo'].".xlsx"
				],
				"destinatarios" => [
					"hcumbicusr@gmail.com"
				]
			];
			$v = Funciones::enviarMailPHPMailer($arrDatos);
			//die(var_dump($v));
		} else {
			$this->result = ['estado'=> 'error', 'message' => $val];
		}
	}

	public function listPackingListByID () {
		$id_packing = $this->params['id_packing'];
		$query = "SELECT pl.* ";
		$query .= " , concat(p.apellidos,' ',p.nombres) as nombre_productor";
		$query .= " , t.codigo as codigo_terreno";
		$query .= " , a.nombre as asociacion";
		$query .= " , tc.nombre as tipo_caja";
		$query .= " , e.nombre as empacadora";
		$query .= " , date_format(pl.f_corte, '%d/%m/%Y') as f_corte_format";
		$query .= " FROM packing_list pl ";
		$query .= " INNER JOIN productor_terreno pt ON pl.id_productor_terreno = pt.id";
		$query .= " INNER JOIN productores p ON pt.id_productor = p.id";
		$query .= " INNER JOIN terrenos t ON pt.id_terreno = t.id";
		$query .= " INNER JOIN asociaciones a ON pt.id_asociacion = a.id";
		$query .= " INNER JOIN tipo_caja tc ON pl.id_tipo_caja = tc.id";
		$query .= " INNER JOIN asociacion_empacadora ae ON pl.id_asociacion_empacadora = ae.id";
		$query .= " INNER JOIN empacadoras e ON ae.id_empacadora = e.id";
		$query .= " WHERE pl.id_packing = '$id_packing' ";
		$query .= " AND pl.estado = '1' ";

		$rs = $this->conn->select($query, false); // packing list

		$arr = array();
		$n_pallets = 20;
		// cantidad de pallets = 20
		$date = date("d/m/Y H:i:s");
			for ($i=0; $i < count($rs); $i++) { 
				$q = "SELECT pld.* FROM packing_list_detalle pld WHERE pld.id_packing_list = '".$rs[$i]['id']."' AND pld.activo = '1'";
				$pl_ = $this->conn->select($q, false);
				if (count($pl_) != 20) { // 20 pallets exactos
					for ($j=0; $j < $n_pallets; $j++) { 
						$aux = [
							"id" => "0",
							"id_packing_list" => $rs[$i]['id'],
							"nro_pallet" => ($j+1),
							"cantidad" => "0",
							"activo" => "9",
							"created_at" => $date,
							"updated_at" => null
						];
						
						$flg = false;
						for ($x=0; $x < count($pl_); $x++) { 
							if ($pl_[$x]['nro_pallet'] == ($j+1)) {
								$pl[$j] = $pl_[$x];
								$flg = true;
								break;
							}
						}
						if (!$flg) {
							$pl[$j] = (object)$aux;
						}
					}
				}else {
					$pl = $pl_;
				}

				$arr[] = [
					"pl" => $rs[$i],
					"pallets" => $pl
				];
			}
		
		
		$this->conn->close();
		$this->result = ["data" => $arr];
	}


	public function getAllPackingList()
	{
		$sql = "SELECT p.* ";
		$sql .= ", c.numero as contenedor";
		$sql .= ", cl.nombre as cliente";
		$sql .= ", v.nombre as vapor";
		$sql .= ", tf.nombre as tipo_funda";
		$sql .= ", po.nombre as puerto_origen";
		$sql .= ", pd.nombre as puerto_destino";
		$sql .= " FROM packing p ";
		$sql .= " INNER JOIN contenedor c ON p.id_contenedor = c.id ";
		$sql .= " INNER JOIN clientes cl ON p.id_cliente = cl.id ";
		$sql .= " INNER JOIN vapor v ON p.id_vapor = v.id ";
		$sql .= " INNER JOIN tipo_funda tf ON p.id_tipo_funda = tf.id ";
		$sql .= " INNER JOIN puertos po ON p.id_puerto_origen = po.id ";
		$sql .= " INNER JOIN puertos pd ON p.id_puerto_destino = pd.id ";
		$sql .= " WHERE p.estado <> '0' "; //0 = inactivo, 1 = finalizado, 2 = en proceso
		$sql .= " ORDER BY p.codigo desc "; 
		
		$sql = $this->conn->select($sql);
		
		$this->result = ["data" => $sql];
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
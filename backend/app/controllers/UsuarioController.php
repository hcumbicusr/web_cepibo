<?php
require_once '../Config.inc.php';
require_once 'Controller.php';
require_once 'IController.php';

class UsuarioController extends Controller implements IController{
	private $table = 'usuarios';
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
		$id = $this->conn->select("SELECT id FROM usuarios WHERE username = '".$_POST['username']."' ", false);
		if (count($id) == 0){
			$_POST['password'] = md5($_POST['password']);
			$val = $this->conn->insert($_POST, false)->inTable($this->table);
			$id = $this->conn->select("SELECT id FROM usuarios WHERE username = '".$_POST['username']."' ", false)[0];
			$this->conn->insert(["id_menu"=>"1","id_usuario"=>$id['id'],"usuario"=>$_POST['usuario_reg']])->inTable("permisos_menu");
		} else {
			$this->result = ['estado'=> 'error', 'message' => 'El usuario ya esxite.'];
			exit;
		}

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
	public function login() {
		global $config;

		$py_name = $config['nameApp'];
		$actual = date("Y-m-d H:i:s");
		$user = "";
		$permisos = array();
		$msgLicence = "";

		$username = trim($this->params['username']);
		$password = md5($this->params['password']);

		$licence = $this->conn->select("SELECT (f_licence > '$actual') valida FROM sys_config WHERE py_name = '$py_name' ORDER BY created_at DESC LIMIT 1 ", false);

		if (!empty($licence)) {
			if ($licence[0]['valida'] == '1') {
				$user = $this->conn->select("SELECT u.*, t.nombres, t.apellidos, t.email, t.telefono, t.direccion, tu.nombre as nom_tipo_usuario FROM usuarios u INNER JOIN tipousuario tu ON u.id_tipousuario = tu.id INNER JOIN trabajador t on u.id_trabajador = t.id WHERE (u.username = '$username' ) AND u.password = '$password' AND u.activo = '1' ", false);
				$permisos = null;
				if (count($user) > 0) {
					$permisos = $this->conn->select("SELECT m.* FROM `permisos_menu` pm INNER JOIN menu m ON pm.id_menu = m.id WHERE pm.id_usuario = '".$user[0]['id']."' AND m.activo = '1' and pm.activo = '1' ORDER BY m.orden", false);
				}
			} else {
				include '../helpers/Funciones.php';
				$msgLicence = Funciones::msgLicence();
			}
		} else {
			include '../helpers/Funciones.php';
			$msgLicence = Funciones::msgLicence();
		}
		
		$this->conn->close();

		$this->result = [
			"user" => $user,
			"permisos" => $permisos,
			"licence" => $msgLicence
		];
		
	}

	public function listPermisosByUser(){
		$id_usuario = $this->params['id_usuario'];
		$query = "SELECT pm.id as id_permiso_menu, m.* FROM permisos_menu pm INNER JOIN menu m ON pm.id_menu = m.id WHERE id_usuario = '$id_usuario' AND m.activo = '1' AND pm.activo = '1' ORDER BY m.orden asc";
		$this->result = $this->conn->select($query);
	}
	
	public function listAdminPermisosByUser(){
		$id_usuario = $this->params['id_usuario'];
		$query = "SELECT m.*, case ifnull((SELECT pm.id FROM permisos_menu pm WHERE id_usuario = '$id_usuario' AND m.id = pm.id_menu AND pm.activo = '1'),'0') when '0' then '0' else '1' end as permiso, ifnull((SELECT pm.id FROM permisos_menu pm WHERE id_usuario = '$id_usuario' AND m.id = pm.id_menu AND pm.activo = '1'),'0') as id_permiso_menu FROM menu m WHERE m.activo = '1'  ORDER BY m.orden asc ";

		$this->result = $this->conn->select($query);
	}

	public function listUsuarios(){
		$query = "SELECT u.*, t.email, tu.nombre as tipo_usuario, c.nombre as cargo FROM usuarios u INNER JOIN tipousuario tu ON u.id_tipousuario = tu.id INNER JOIN trabajador t ON u.id_trabajador = t.id LEFT JOIN trabajador_cargo tc ON t.id = tc.id_trabajador LEFT JOIN cargos c ON tc.id_cargo = c.id";
		$this->result = $this->conn->select($query);
	}

	public function cambiarEstado(){
		$id_usuario = $this->params['id_usuario'];
		$activo = $this->params['estado'];
		$val = $this->conn->update("UPDATE usuarios SET activo = '$activo', updated_at = now() WHERE id = '$id_usuario'");
		if ($val === true) {
			$this->result = ["estado" => "success", "message" => "OK"];
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
	}

	public function getTipoUsuario()
	{
		$this->result = $this->conn->select("SELECT * FROM tipousuario WHERE activo = '1'");
	}

	public function adminPermisos(){
		$id = $this->params['id'];
		$id_usuario = $this->params['id_usuario'];
		$activo = $this->params['estado'];

		unset($_POST['function']);
		unset($_POST['id']);
		unset($_POST['estado']);

		if (intval($id) > 0) {
			$val = $this->conn->update("UPDATE permisos_menu SET activo = '$activo', updated_at = now() WHERE id = '$id'");
		} else {
			$val = $this->conn->insert($_POST)->inTable("permisos_menu");
		}
		if ($val === true) {
			$this->result = ["estado" => "success", "message" => "OK"];
		} else {
			$this->result = ["estado" => "error", "message" => "Ocurrió un error."];
		}
	}

	public function getTrabajadoresSinUsuario()
	{
		$this->result = $this->conn->select("SELECT t.* FROM trabajador t WHERE t.id NOT IN(select u.id_trabajador from usuarios u) AND t.estado = '1'");
	}


	public function verificaDatos() {
		$option = strtoupper($this->params['dato']);
		$valor = trim($this->params['valor']);
		$message = "El $option ya está registrado.";
		switch ($option) {
			case 'USERNAME':
				$query = "SELECT * FROM usuarios WHERE trim(username) = '$valor'";
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

	public function sugiereUsuario()
	{
		$id_trabajador = $this->params['id_trabajador'];

		$this->result = $this->conn->select("select lower(concat(substring(nombres,1,1), substring(apellidos,1,locate(' ', trim(apellidos))) )) username from trabajador where id = '$id_trabajador'")[0];
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
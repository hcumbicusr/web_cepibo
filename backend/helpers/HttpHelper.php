<?php
class HttpHelper {

	public static function requestHelper() {
		unset($_REQUEST['controller']);
		unset($_REQUEST['_']);

		if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
			if (strpos($_SERVER["REQUEST_URI"], "?")) {
				list($base, $params) = explode("?", $_SERVER["REQUEST_URI"]);
				$params .= "&";
				do {
					$key = substr($params, 0, strpos($params, "="));
					$params = substr($params, strpos($params, "=") + 1);
					$val = substr($params, 0, strpos($params, "&"));
					$params = substr($params, strpos($params, "&") + 1);
					$arr_params[$key] = $val;
				}while (strpos($params, "&"));
				//die(var_dump($arr_params));
				$_REQUEST = $arr_params;
			}
		}else {
			$input = file_get_contents("php://input");
			
				$params = $input;
				$params .= "&";
				do {
					$key = substr($params, 0, strpos($params, "="));
					$params = substr($params, strpos($params, "=") + 1);
					$val = substr($params, 0, strpos($params, "&"));
					$params = substr($params, strpos($params, "&") + 1);
					$arr_params[$key] = $val;
				}while (strpos($params, "&"));
				//die(var_dump($arr_params));
				$_REQUEST = $arr_params;
		}
	}
	
}
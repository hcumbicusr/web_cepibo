<?php
class Controller {
	public function __construct(){

	}

	public function response($params) {
		return $params;
	}

	public function responseJson($params) {
		return json_encode($params);
	}
	
}
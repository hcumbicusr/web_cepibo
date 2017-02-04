<?php
interface IController {
	/**
	* Método que se ejecuta por default
	* Verbo Http GET
	*/
	public function index();

	/**
	* Método que se ejecuta por default, utiliza array params
	* Verbo Http GET
	*/
	public function search();

	/**
	* Método que se ejecuta por default, utiliza array params['id']
	* Verbo Http PUT
	*/
	public function update();

	/**
	* Método que se ejecuta por default, utiliza array params
	* Verbo Http POST
	*/
	public function create();

	/**
	* Método que se ejecuta por default, utiliza array params['id']
	* Verbo Http DELETE
	*/
	public function delete();

	/**
	* Método de respuesta ARRAY o JSON
	*/
	public function response();
}
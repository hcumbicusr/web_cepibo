<?php
/**
 * Clase que sirve de modelo para la implementacion de los diferentes gestores de BD
 * @package    uF
 * @subpackage configuration
 * @author     Cumbicus Rivera Henry <hcumbicusr@gmail.com>
*/
interface IDatabase{
    /**
     *  Funcion que realiza la conexion con la base de datos
     *              retorna el enlace de la conexion
     *  @return $con
     */
   public function connect($database);   
   /**
     *  Funcion que cierra  la conexion con la base de datos
    *   retorna true - false de cierre     
     *  @return $con
     */
   public function close(); 
   /**
    * Funcion que realizara las consultas de seleccion
    * @param $con link de conexion 
    * @param $query consulta sql 
    */
   public function select($query);
   /**
    * Funcion que realizara las consultas de insercion
    * @param $con link de conexion 
    * @param $arr array de campos datos para insert
    */
   public function insert($arr);
   /**
    * Funcion que realizara las consultas de actualizacion o modificacion
    * @param $con link de conexion 
    * @param $query consulta sql 
    */
   public function update($query);
   /**
    * Funcion que realizara las consultas de eliminacion
    * @param $con link de conexion 
    * @param $query consulta sql 
    */
   public function delete($query);
   /**
    * Funcion que ejecuta procedures de seleccion solo INPUT
    * @param $con link de conexion 
    * @param $procedure nombre del procedimiento almacenado
    * @param $input parametros de entrada separados por comas (,) (utilizar apostrofes) : default = NULL
    */
   public function spSelect($procedure,$input);
   /**
    * Funcion que ejecuta procedures con variables INPUT y OUTPUT(1)
    * @param $con link de conexion
    * @param $procedure Nombre del procedimiento almacenado
    * @param $input Parametros de entrada del procedimiento almacenado separados por comas (,) (utilizar apóstrofes)
    * @return Boolean $output default [true] => si posee parametro de salida, [false] => no posee parámetro de salida
    */
   public function spInsert($procedure,$input,$output);
   
   }
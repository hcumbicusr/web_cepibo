<?php
/**
 * Clase que sirve de redireccionador o selector del gestor de base de datos que
 * se elija en el archivo Config.inic.php $config['managerDataBase']
 * @package    uF
 * @subpackage configuration
 * @author     Cumbicus Rivera Henry <hcumbicusr@gmail.com>
*/
/**
 * Requiere del archivo mysql.php para funcionar
 */
require_once 'ClassConfig.php';
require_once 'ClassMysqli.php';

class Database{
    public function selectManager() {
            switch (ClassConfig::get('managerDataBase')) {
                
                case 'mysqli':
                    return new ClassMysqli();
                    break;
                case 'mssql':
                    return ['message'=>'Driver sin emplementar.'];
                    break;
                default :
                    return new ClassMysqli();
                    break;
            }
    }
}
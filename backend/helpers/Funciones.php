<?php

class Funciones {
    /**
     * @todo genera codigo html para mostrar un combobox
     * @param array $array Array bidimensional de BD
     * @param int $filas = 0 Cantidad de filas
     * @param int $columnas = 0 Cantidad de columnas
     * @return String Cadena de <option>
     */
    public static function armarCombo ($array, $selected = NULL, $filas = 0, $columnas =0)
    {              
        //die(var_dump($array));
        if ($filas == 0)
        {
            $filas = count($array);
        }        
        if ($columnas == 0) { #si no hay campos especificados
            for ($i = 0; $i < 1; $i++) {
                $columnas = ((count($array[$i])));    
            }
        }        
        for ($i = 0; $i < $filas; $i++) {
            $cbo = "\n<option value='";
            for ($j = 0; $j < $columnas; $j++) { 
                $id = $array[$i]['id'];
                $value = $array[$i]['nombre'];       
            }
            $cbo .="$id' ";
            if (!empty($selected) && ($id) == $selected)
            {
                $cbo .=" selected ";
            }
            $cbo .=" >";
            
            $cbo .=$value;
            $cbo .="</option>";
            $option[$i] = $cbo;
            $cbo = "";
        }               
        for ($i = 0; $i < count($option); $i++) {
            $cbo .= $option[$i]."\n";
        } 
        return $cbo;        
    }
    
    /**
     * @author hcumbicusr <hcumbicusr@gmail.com>
     * @name filtraCaracteresEspeciales
     * @param String $cadena String o Array a ser reemplazado
     */
    public static function filtraCaracteresEspeciales ($cadena) 
    {
        $search = array('Á','É','Í','O','Ú','á','é','í','ó','ú','Ü','ü','Ñ','ñ');
        $replace = array('&#193;','&#201;','&#205;','&#211;','&#218;','&#225;','&#233;','&#237;','&#243;','&#250;','&#220;','&#252;','&#209;','&#241;');               
        return str_replace($search, $replace,$cadena);        
    }
    
    public static function convertirEspecialesHtml($str){
        if (!isset($GLOBALS["carateres_latinos"])){
            $todas = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
            $etiquetas = get_html_translation_table(HTML_SPECIALCHARS, ENT_NOQUOTES);
            $GLOBALS["carateres_latinos"] = array_diff($todas, $etiquetas);
        }
        $str = strtr($str, $GLOBALS["carateres_latinos"]);
        return $str;
    }
    
    function array_envia($array) { 
        $tmp = serialize($array); 
        $tmp = urlencode($tmp); 
        return $tmp; 
   } 
   
   function array_recibe($array) { 
        $tmp = stripslashes($array); 
        $tmp = urldecode($tmp); 
        $tmp = unserialize($tmp); 
       return $tmp; 
   } 

   //--------msg expires licence
  public static function msgLicence($msg = null) {
    if (empty($msg)) {
      $msg = "Su licencia ha expirado, por favor contactar al proveedor.\n * Developer: Henry C.\n * RPC: 956727976 \n Email: hcumbicusr@gmail.com \n Atte. Administrador. Gracias. ";
    }
    return $msg;
  }
   
   public static function sessionTime ($transcurrido,$limite)
   {
       if ($transcurrido > $limite) {
           return true;
       }       
       return false;
   }
   
   /**
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *
     * @param $string
     *  string la cadena a sanear
     *
     * @return $string
     *  string saneada
     */
    function reemplazarString($string)
    {        
        $string = trim($string);        
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", "\"",
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "`", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", " "),
            '',
            $string
        );        
        return $string;
    }
    /**
     * @param array $param Filtra caracteres de inyeccion sql
     */
    public static function filtraArray(&$param)
    {
        //die(var_dump($param));
         $sql = array(
             "--",
             "'",
             "´"
         );
        if (is_array($param)){
            foreach ($param as $key => $value) {
                $param[$key] = trim(str_replace($sql, "", $value));
            }
        }else
        {
            $param = trim(str_replace($sql, "", $param));
        }
                  
    }

    /**
     * PAram: Array - String 
     * @param array $param Convierte carateres extraños  //--> Ã³ Ãº y Ã± // a normal
     */
    public static function filtraCaracteres(&$param)
    {
        if (is_array($param)){
            foreach ($param as $key => $value) {
                $param[$key] = trim(utf8_decode($value));
            }
        }else
        {
            $param = trim(utf8_decode($param));
        }
                  
    }
    
    public static  function encodeStrings ($string, $n = 1)
    {
        $cad = $string;
        for ($i = 0; $i < $n; $i++)
        {
            $cad = base64_encode($cad);
        }
        return $cad;
    }
    
    public static  function decodeStrings ($string, $n = 1)
    {
        $cad = $string;
        for ($i = 0; $i < $n; $i++)
        {
            $cad = base64_decode($cad);
        }
        return $cad;
    }
    /**
     * Conversion de unidades
     * @author http://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
     */
    
    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * $temp[0] = IP
     * $temp[1] = SO
     * $temp[2] = NAVEGADOR
     */
    public static function DatosBrowser()
    {
        $temp=array();
        $ip=$_SERVER['REMOTE_ADDR'];
        $datos=$_SERVER['HTTP_USER_AGENT'];
        array_push($temp,$ip);
        if(strpos($datos,"Windows")!==false)
          array_push($temp,"Windows");
        elseif(strpos($datos,"Mac")!==false)
          array_push($temp,"Mac");
        elseif(strpos($datos,"Linux")!==false)
          array_push($temp,"Linux");

        if(strpos($datos,"MSIE")!==false)
          array_push($temp,"Internet Explorer");
        elseif(strpos($datos,"Firefox")!==false)
          array_push($temp,"Firefox");
        elseif(strpos($datos,"Chrome")!==false)
          array_push($temp,"Google Chrome");
        elseif(strpos($datos,"Safari")!==false)
          array_push($temp,"Safari");
        elseif(strpos($datos,"Opera")!==false)
          array_push($temp,"Opera");
        else
          array_push($temp,"Navegador desconocido");

        return $temp;   

  }
  
  /**
   * @todo Funcion que suma letras, solo funciona desde A hasta AZ ... util para EXCEL
   * @var CHAR letra
   * @var INT nro aumento
   * @return CHAR Letra adicionada
   * 
   */
  
  public static function sumaLetrasExcel($letra,$n)
    {
       $letra = strtoupper($letra);
       $alfabeto = [
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        ];
       //if (in_array($letra, $alfabeto))
        $k = 0;
       foreach ($alfabeto as $key => $value) {
         if ($letra == $value) {
          $k = $key + $n; // letra intervalo
          break;
         }
       }
       return $alfabeto[$k];
    }
  
    //calcula el nombre del dia de una fecha determinada
    public static function nombreDia($fecha) //formato dd-mm-aaaa
    {
        $fecha = str_replace("/", "-", $fecha);
        $fechats = strtotime($fecha); //a timestamp 
        date("w", $fechats);
        switch (date("w", $fechats)){ 
            case 0: return "DOMINGO"; 
            case 1: return "LUNES"; 
            case 2: return "MARTES"; 
            case 3: return "MIERCOLES"; 
            case 4: return "JUEVES"; 
            case 5: return "VIERNES"; 
            case 6: return "SABADO"; 
        }  
    }
    
    /**
     * @param sql $sql Ejecuca una consulta SQL - Base de datos
     * @return boolean True - False
     */
    public static function cargaNuevaBD($sql)
    {
        $objBD = new Class_Db();
        $con = $objBD->selectManager()->connect();
        $result = $objBD->selectManager()->insert($con, $sql);
        //die(var_dump($result));
        return $result; //true - false
    }
  
    /**
     * @param void  Info PHP
     */
    public static function versionPHP()
    {
        echo phpinfo();
    }
    
    public static function generaToken($longitud = 6)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $cad = "";
        for($i=0;$i<$longitud;$i++) {
          $cad .= substr($str,rand(0,62),1);
        }
        return $cad.md5(date("YmdHis"));
    }

    public static function generaCadena($longitud = 3)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $cad = "";
        for($i=0;$i<$longitud;$i++) {
          $cad .= substr($str,rand(0,62),1);
        }
        return $cad;
    }
    
    /**
    * @param Int $total Total de elementos
    * @param Int $tpp Total de elementos a mostrar Por Pagina
    * @return Int nro de paginas
    */
    public static function nroPaginas($total,$tpp)
    {
            $mod = $total % $tpp;
            $div = intval($total / $tpp);
            $npag = 0;

            if ($mod != 0)
            {
                    if ($mod <= $tpp)
                    {
                            $npag++;
                    }
            }
            $npag += $div;
            return $npag;
    }
    
    /**
    * @param $npag Int Nro de paginas
    * @param $pact Int Pagina actual
    * @return Array Paginas antes, paginas despues
    */
    public static function pagAntSig($npag,$pact = 1)
    {
            $p_ant = 0;
            $p_sig = 0;

            if ($pact <= 1)
            {
                    $p_ant = 0;
                    $p_sig = $npag - 1;
            }else if ($pact > 1 && $pact <= $npag)
            {
                    $p_ant = $pact - 1;
                    $p_sig = $npag - $pact;
            }else if ($pact > $npag)
            {
                    $p_ant = $npag - 1;
                    $p_sig = 0;
            }

            return array("anterior" => $p_ant, "siguiente" => $p_sig);
    }
    
    public static function convierteHtmlCodeASTexto($htmlCode)
    {
        return html_entity_decode(strtolower($htmlCode),ENT_HTML5,"UTF-8");
    }

    public static function testErrorJSON() {
        switch(json_last_error()) {
                case JSON_ERROR_NONE:
                    echo ' - Sin errores';
                break;
                case JSON_ERROR_DEPTH:
                    echo ' - Excedido tamaño máximo de la pila';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Desbordamiento de buffer o los modos no coinciden';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Encontrado carácter de control no esperado';
                break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Error de sintaxis, JSON mal formado';
                break;
                case JSON_ERROR_UTF8:
                    echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
                break;
                default:
                    echo ' - Error desconocido';
                break;
            }
    }



    //---------------------- EXTERNO

####
## Eliminar una imagen
####
/**
* @param $ruta ruta completa del archivo a eliminar
*/
public static function eliminarArchivo($ruta) {
    /*
    if(isset($_GET['eliminar'])){
        $archivo = $_GET['eliminar'];
        $directorio = dirname(__FILE__);
        if(unlink($directorio.'/'.$archivo)){
            header("Location: cargarImagen.php?accion=eliminado");
            exit;
        }
        
    }
    */
    //die(var_dump($ruta));
    if(isset($ruta)){
        if(unlink($ruta)){
            return true;
        }else {
            return false;
        }
    }
}

##
## RECIBIR FORMULARIO
## Aqui pueden ir los campos que uno quiera
##
/**
* Carga una imagen original y su reduccion
* @param $file variable global tipo $_FILES
* @param $ruta ruta donde cargar la imagen [ / ]
* @param $ruta_small ruta donde cargar la imagen [ / ]
*/
public static function cargarImagen($file, $ruta, $name_pic, $ruta_small, $alto_, $ancho_, $alto_small, $ancho_small) {

    if(!empty($file)){ // comprobamos que se ha enviado el formulario
        //die(var_dump($file));
        // comprobar que han seleccionado una foto
        if($file["name"] != ""){ // El campo foto contiene una imagen...
            // Primero, hay que validar que se trata de un JPG/GIF/PNG
            $allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "GIF", "PNG");
            $foto = str_replace(" ","_",$file['name']);
            //die(var_dump($foto));
            list($nombre_img,$extension) = explode(".",$foto);
            $foto = $name_pic.".".$extension;
            $foto_small = $name_pic."_small.".$extension;
            //$extension = end(explode(".", $file["name"]));
            if ((($file["type"] == "image/gif")
                    || ($file["type"] == "image/jpeg")
                    || ($file["type"] == "image/png")
                    || ($file["type"] == "image/pjpeg"))
                    && in_array($extension, $allowedExts)) {
                // el archivo es un JPG/GIF/PNG, entonces...
                
                //$extension = end(explode('.', $file['name']));
                //$foto = substr(md5(uniqid(rand())),0,10).".".$extension;

                if (file_exists($ruta.$foto)) {
                    return ["success" => false, "error"=>"Documento repetido."];
                }

                //$directorio = dirname(__FILE__); // directorio de tu elección
                //$directorio = dirname($ruta); // directorio de tu elección
                
                // almacenar imagen en el servidor -- LA ruta viene con el ->> /
                //self::resizeImagen($ruta, $ruta_small, $foto, $alto_small, $ancho_small,$foto,".jpg");
                $va = getimagesize($file['tmp_name']);
                //die(var_dump($va));
                $error = "";
                if (move_uploaded_file($file['tmp_name'], $ruta.$foto)) {
                    $val = self::resizeImagen($ruta, $ruta_small, $foto, $alto_small, $ancho_small,$foto_small,$extension);
                    if ($val["estado"] === false) {
                      $val = copy($ruta.$foto, $ruta_small.$foto_small);
                      if ($val === false) {
                        $error = "Error al carga la imagen.";
                        unlink($ruta.$foto);
                      }
                    } else {
                      $val = $val['estado'];
                    }
                    //die(var_dump($val));
                    return ["success" => $val, "name_pic" => $foto, "name_pic_small" => $foto_small, "error" => $error];
                }
                //$minFoto = 'min_'.$foto;
                //$resFoto = 'res_'.$foto;
                //resizeImagen($directorio.'/', $foto, 65, 65,$minFoto,$extension);
                
                //unlink($directorio.'/'.$foto);
                
            } else { // El archivo no es JPG/GIF/PNG
                return ["success" => false, "error"=>"Formato imagen incorrecto"];
              }
            
        } else { // El campo foto NO contiene una imagen
            return ["success" => false, "error"=>"No se puede distinguir el nombre de la imagen"];
        }
            
    } // fin del submit
    return ["success" => false, "error" => "El archivo no se ha podido procesar."];

}
####
## Función para redimencionar las imágenes
## utilizando las liberías de GD de PHP
####

/**
* @param $ruta ruta donde esta la imagen original
* @param $ruta_s ruta donde se guardarpa la reduccion de imagen
* @param $nombre nombre dela imagen
* @param $alto alto
* @param $ancho ancho
* @param $nombreN nombre nuevo de la imagen
* @param $extension extension de la nueva imagen
*reduccion de imagen
*list($nombre_img,$extension) = explode(".",str_replace(" ","_",$base));
*resizeImagen($target_dir,$target_dir_reduccion, str_replace(" ","_",$base), 450, 450,$nombre_img.".jpg",$extension);
*/

public static function resizeImagen($ruta, $ruta_s, $nombre, $alto, $ancho, $nombreN, $extension, $scope = 'SELF'){
    $rutaImagenOriginal = $ruta.$nombre; //dirección de imagen original
    if($extension == 'GIF' || $extension == 'gif'){
      $img_original = @imagecreatefromgif($rutaImagenOriginal);
    }
    if($extension == 'jpg' || $extension == 'JPG'){
      $img_original = @imagecreatefromjpeg($rutaImagenOriginal);
    }
    if($extension == 'png' || $extension == 'PNG'){
      $img_original = @imagecreatefrompng($rutaImagenOriginal);
    }
    //die(var_dump($rutaImagenOriginal));
    //die(var_dump($extension));

    if($img_original === false){
      return ["estado" => false, "mensaje" => "Error en la imagen seleccionada."];
    }
    $max_ancho = $ancho;
    $max_alto = $alto;
    list($ancho,$alto)=getimagesize($rutaImagenOriginal);
    $x_ratio = $max_ancho / $ancho;
    $y_ratio = $max_alto / $alto;
    if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho 
        $ancho_final = $ancho;
                $alto_final = $alto;
        } elseif (($x_ratio * $alto) < $max_alto){
                $alto_final = ceil($x_ratio * $alto);
                $ancho_final = $max_ancho;
        } else{
                $ancho_final = ceil($y_ratio * $ancho);
                $alto_final = $max_alto;
        }
    $tmp=imagecreatetruecolor($ancho_final,$alto_final);
    if($extension == 'png' || $extension == 'PNG'){
      imagealphablending($tmp, false);
      imagesavealpha($tmp,true);
      $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
      imagefilledrectangle($tmp, 0, 0, $ancho_final, $alto_final, $transparent);
    }
    imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
    //imagedestroy($img_original);
    //$calidad=75;
    if($extension == 'GIF' || $extension == 'gif'){
      imagegif($tmp,$ruta_s.$nombreN);
    }
    if($extension == 'jpg' || $extension == 'JPG'){
      imagejpeg($tmp,$ruta_s.$nombreN);
    }
    if($extension == 'png' || $extension == 'PNG'){
      imagepng($tmp,$ruta_s.$nombreN);
    }
    imagedestroy($tmp);
    
    return ["estado" => true, "mensaje" => "Imagen redimensionada"];
}

// ----
/**
* filtra caracteres por utf8 y htmlentities
* @param $param String
*/
public static function utf8_string($param) {
  return htmlentities(utf8_decode($param));
}

// formato de fecha DD/MM/AAAA hh:mm:ss --->> AAAA-MM-DD 00:00:00

public static function formatoFechas ($param, $type = 'datetime') {
  if ($type == 'datetime') {
    list($fecha,$hora) = explode(' ',trim($param));
    list($d,$m,$a) = explode('/', $fecha);
    $fecha = $a."-".$m."-".$d." ".$hora;
  } else {
    list($d,$m,$a) = explode('/', $fecha);
    $fecha = $a."-".$m."-".$d;
  }
  //die(var_dump($fecha));
  return $fecha;
}



//_-----------------------------------------------------------------------------------------------------------
//_------------ resize image
function thumbnail($image, $width, $height) {

  if($image[0] != "/") { // Decide where to look for the image if a full path is not given
    if(!isset($_SERVER["HTTP_REFERER"])) { // Try to find image if accessed directly from this script in a browser
      $image = $_SERVER["DOCUMENT_ROOT"].implode("/", (explode('/', $_SERVER["PHP_SELF"], -1)))."/".$image;
    } else {
      $image = implode("/", (explode('/', $_SERVER["HTTP_REFERER"], -1)))."/".$image;
    }
  } else {
    $image = $_SERVER["DOCUMENT_ROOT"].$image;
  }
  $image_properties = getimagesize($image);
  $image_width = $image_properties[0];
  $image_height = $image_properties[1];
  $image_ratio = $image_width / $image_height;
  $type = $image_properties["mime"];

  if(!$width && !$height) {
    $width = $image_width;
    $height = $image_height;
  }
  if(!$width) {
    $width = round($height * $image_ratio);
  }
  if(!$height) {
    $height = round($width / $image_ratio);
  }

  if($type == "image/jpeg") {
    header('Content-type: image/jpeg');
    $thumb = imagecreatefromjpeg($image);
  } elseif($type == "image/png") {
    header('Content-type: image/png');
    $thumb = imagecreatefrompng($image);
  } else {
    return false;
  }

  $temp_image = imagecreatetruecolor($width, $height);
  imagecopyresampled($temp_image, $thumb, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
  $thumbnail = imagecreatetruecolor($width, $height);
  imagecopyresampled($thumbnail, $temp_image, 0, 0, 0, 0, $width, $height, $width, $height);

  if($type == "image/jpeg") {
    imagejpeg($thumbnail);
  } else {
    imagepng($thumbnail);
  }

  imagedestroy($temp_image);
  imagedestroy($thumbnail);

}
//_-----------------------------------------------------------------------------------------------------------


/**
* Funcion que envía correos
* @param $mi_mail Array que contiene:
* - destinatarios = array()
* - titulo
* - mensaje = soporta HTML
* - remitente 
* @return boolean $envio 
*/
public static function enviarMail($mi_mail){

  //die(var_dump($mi_mail));

  $destinatarios = $mi_mail['destinatarios']; //array
  if (!empty($mi_mail['cco'])){
    $cco_arr = $mi_mail['cco']; //array con copia oculta
  }
  $titulo = $mi_mail['titulo']; 
  $mensaje = $mi_mail['mensaje']; //html
  $remitente = $mi_mail['remitente']; 
  
  $link = '<br>';
  if (!empty($mi_mail['link'])) {
      $link .= $mi_mail['link'];
  }
  $link .= '<br>';
  
  $para = '';
  $cc = ''; // con copia
  $cco = ''; // con copia oculta
  
  if(is_array($destinatarios)){
      for ($i = 0; $i < count($destinatarios); $i++) {
            if ($i == 0) {
                $para = $destinatarios[$i];
            }else{
                $cc .=  $destinatarios[$i].", ";
            }
        }
  }else{ // s un string concatenado por comas (,)
      $para = $destinatarios;
  }

  if (!empty($cco_arr)){
    for ($i = 0; $i < count($cco_arr); $i++) {
        $cco .=  $cco_arr[$i].", ";
    }
    $cco = substr($cco, 0, -2); // elimina la ultima coma y espacio en blanco al final
  }
  
  if (!empty($cc)){
    $cc = substr($cc, 0, -2); // elimina la ultima coma y espacio en blanco al final
  }  

  // Para enviar un correo HTML, debe establecerse la cabecera Content-type
  $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
  $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

  // Cabeceras adicionales
  $cabeceras .= 'To: '.$para . "\r\n";
  $cabeceras .= 'From: '.$remitente . "\r\n";

  if (!empty($cc)){
      $cabeceras .= 'Cc: '.$cc . "\r\n";
  }
  if (!empty($cco)){
      $cabeceras .= 'Bcc: '.$cco . "\r\n";
  }
  $mensaje = $link.$mensaje;
  // Enviarlo
  $envio = mail($para, $titulo, $mensaje, $cabeceras);
  return $envio; // TRUE,FALSE
}



//----------- urls amigables
  function urls_amigables($url) {

    // Tranformamos todo a minusculas

    $url = strtolower($url);

    //Rememplazamos caracteres especiales latinos

    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');

    $repl = array('a', 'e', 'i', 'o', 'u', 'n');

    $url = str_replace ($find, $repl, $url);

    // Añaadimos los guiones

    $find = array(' ', '&', '\r\n', '\n', '+'); 
    $url = str_replace ($find, '-', $url);

    // Eliminamos y Reemplazamos demás caracteres especiales

    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');

    $repl = array('', '-', '');

    $url = preg_replace ($find, $repl, $url);

    return $url;

  }



//------------PHPmailer

  public static function enviarMailPHPMailer($arrDatos) {


    include_once "../libreries/mailer/class.phpmailer.php";
    include_once "../libreries/mailer/class.smtp.php";
    
    include_once '../Config.inc.php';

    //die(var_dump($arrDatos));
    
    //self::filtraGET_POST($_POST);
    $name       = @trim($arrDatos['name']); 
    $email      = @trim($arrDatos['email']); 
    $subject    = @trim($arrDatos['subject']); 
    $message    = @trim($arrDatos['message']);
    $title      = @trim($arrDatos['title']);
    $formato    = @trim($arrDatos['formato']);
    $adjunto    = $arrDatos['adjunto'];

    $arrEnvio   = $arrDatos['destinatarios'];
    
    if (empty($formato))
    {
        $formato = 'contacto';
    }

    //Especificamos los datos y configuración del servidor
    $mail = new PHPMailer();         
    //Especificamos los datos y configuración del servidor
    $mail->IsSMTP();
    
    $mail->CharSet = 'UTF-8';
    
    //Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
    // 0 = off (producción)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug  = 0;   
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = 'smtp.gmail.com'; // SMTP a utilizar. Por ej. smtp.elserver.com
    $mail->Username = "_@gmail.com"; // Correo completo a utilizar
    $mail->Password = "*"; // Contraseña
    $mail->Port = 465; // Puerto a utilizar

    //Agregamos la información que el correo requiere
    //Con estas pocas líneas iniciamos una conexión con el SMTP. Lo que ahora deberíamos hacer, es configurar el mensaje a enviar, el //From, etc.
    $mail->From = $title; // Desde donde enviamos (Para mostrar)
    $mail->FromName = $title;

    if (!empty($arrEnvio)) {
        for ($i=0; $i < count($arrEnvio); $i++) { 
            if  ($i == 0) {
                if (!empty($arrEnvio[$i])) {
                  $mail->addAddress($arrEnvio[$i]);
                }
            } else {
                if (!empty($arrEnvio[$i])) 
                  $mail->addCC($arrEnvio[$i]);
            }
        }
    }

    //die();
    
    $mail->addBCC("hcumbicusr@gmail.com");
    //die(var_dump($adjunto));
    if (!empty($adjunto)) {
      $mail->AddAttachment($adjunto['ruta']);
    }
            
    $mail->Subject = $subject; // Este es el titulo del email.
    
    //$mensaje_arr = self::formato_email($title, $message, $formato);
    //$mensaje = $mensaje_arr['header'].$mensaje_arr['content'].$mensaje_arr['footer'];
    $mensaje = $message;

    //die(var_dump($mensaje));
    
    $mail->Body = ($mensaje); // Mensaje a enviar
    $mail->IsHTML(true);
    
    //Enviamos el correo electrónico

    //die(var_dump($mail->Send()));

    if($mail->Send()){
        $success = true;
    }else{
        $success = false;
    }

    return $success;
  }


  //-------  formato
  public static function formato_email($title, $message, $formato = 'contacto')
    {
        $header_c = "<h2>Alerta de $title:</h2><br>";
        $header_c .= "<b>Mensaje:</b><br>";
        $content_c = "<p align=\"justify\">".$message."</p><br><br>";
        $footer_c = "-------------------------------------------------------------------------<br>";
        $footer_c .= "Fin del mensaje";
        
        // opinion
        $header_o = "<h2>Mensaje de Opinión (www.disclosure.com):</h2><br>";
        $header_o .= "<b>Mensaje:</b><br>";
        $content_o = "<p align=\"justify\">".Funciones::filtraCaracteresEspeciales($message)."</p><br><br>";
        $footer_o = "-------------------------------------------------------------------------<br>";
        $footer_o .= "Fin del mensaje";
        
        $formato_email = 
        array( 
                "contacto" => 
                array(
                        "header" => $header_c,
                        "content" => $content_c,
                        "footer" => $footer_c
                ),
                "opinion" => 
                array(
                        "header" => $header_o,
                        "content" => $content_o,
                        "footer" => $footer_o
                )
        );
        return $formato_email[$formato];
    }



    //--------- enviar SMS con modem usb GSM
    public static function enviarSMS($data = array()) {
      //die(var_dump($data));

      //---------------------------------------------------------------------------------------
      $objGsmOut = new COM ("ActiveXperts.GsmOut");

      $objGsmOut->LogFile          = 'C:\ActiveXpertsLog'; 
      $objGsmOut->Device           = 'ZTE Proprietary USB Modem';
      $objGsmOut->DeviceSpeed      = '0'; 

      $objGsmOut->EnterPin         ( '0000' );
      
      $objGsmOut->MessageRecipient = $data['destinatario'];
      $objGsmOut->MessageData      = $data['mensaje'];
        
      if($objGsmOut->LastError == 0)
      {
        $objGsmOut->Send;
      }
        
      $result = $objGsmOut->GetErrorDescription($objGsmOut->LastError);

      if (trim(strtolower($result)) == 'success') {
        $val = true;
      } else {
        $val = false;
      }

      return $val;

    }

    // valida la que la fecha ingresada tenga el formato correcto
    public static function verifyDate($date, $format = 'd/m/Y', $strict = false)
    {
        //format = 'm/d/Y';
        $dateTime = DateTime::createFromFormat($format, $date);
        if ($strict) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {
                return false;
            }
        }
        return $dateTime !== false;
    }


    /**
    * foramto fecha: DD/MM/YYYY hh:mm AM
    */
  public static function parseDate($value)
  {
    $return = null;
    if (!empty($value)) {
      $date = explode(" ", $value); // date, hora_min, AM_PM
      $fecha = explode("/", $date[0]);
      $fecha = $fecha[2]."-".$fecha[1]."-".$fecha[0]; // YYYY-MM-DD
      $hora = explode(":", $date[1]);
      $h = intval($hora[0]);
      $meridian = $date[2];
      if ($meridian == 'PM') {
        if ($h < 12){
          $h += 12; // cambio a 24horas
        } else {
          $h -= 12;
        }
      }
      $hora = $h.":".$hora[1].":00"; // hh:mm:ss
      $return = $fecha." ".$hora;
    }
    return $return;
  }


}
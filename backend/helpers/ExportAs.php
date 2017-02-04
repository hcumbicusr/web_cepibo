<?php
include 'constantes.php';
include_once '../Config.inc.php';
$date = date("d/m/y H:i:s");
/**
* @param format = xls
* @param title = titulo del archivo
* @param id_export = id para realizar el reporte
* @param file = nombre del archivo php del reporte
* @param accion = export / save
*/
$option = (empty($_POST['format']))? 'xls' : $_POST['format'];
$title = (empty($_POST['title']))? "Descarga $date.$option" : $_POST['title'].".$option";
$id_export = (empty($_POST['id_export']))? "" : $_POST['id_export'];
$name = (empty($_POST['file']))? "" : ucfirst($_POST['file']);
$accion = (empty($_POST['accion']))? "export" : $_POST['accion'];
//$path = dirname(__FILE__).PY_NAME.DS.API_NAME.DS."download".DS; // directorio de descargas
global $config;
$path = $config["path"].DS."download".DS; // directorio de descargas
//echo $path;exit;
//die(var_dump($_SERVER['DOCUMENT_ROOT']));
switch ($option) {
	case 'pdf':
		require_once '../libreries/dompdf/dompdf_config.inc.php';
	    $dompdf = new DOMPDF();
	    $dompdf->load_html( $_POST['data'] );
	    $dompdf->render();
	    $dompdf->stream("$title");
		break;
	case 'xls':
		require "../export/Export".$name."_xls.php";
		$class = "Export".$name."_xls";
		$objExport = new $class();
		$objWriter = $objExport::get($id_export);
		if (is_object($objWriter)) {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename='".$objExport::$titulo_archivo.".xlsx'");
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            if ($accion == 'save') {
            	$objWriter->save($path.$objExport::$titulo_archivo.'.xlsx');
            }
		} else {
			echo $objWriter;
		}
        
		break;
	case 'doc':
		header("Content-type: application/vnd.ms-word; name='word'");
		//header("Content-type: application/octet-stream");
		header("Content-Disposition: filename=$title");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $_POST['data'];
		break;
}
exit();
<?php
$tipo = isset($_REQUEST['t']) ? $_REQUEST['t'] : 'excel';
$extension = '.xls';

if($tipo == 'word') $extension = '.doc';

// Si queremos exportar a PDF
if($tipo == 'pdf'){
    require_once 'lib/dompdf/dompdf_config.inc.php';
    
    $dompdf = new DOMPDF();
    $dompdf->load_html( file_get_contents( 'http://localhost/tutoriales/pdf/alumnos.php' ) );
    $dompdf->render();
    $dompdf->stream("mi_archivo.pdf");
} else{
    require_once 'alumnos.php';
    
    header("Content-type: application/vnd.ms-$tipo");
    header("Content-Disposition: attachment; filename=mi_archivo$extension");
    header("Pragma: no-cache");
    header("Expires: 0");    
}
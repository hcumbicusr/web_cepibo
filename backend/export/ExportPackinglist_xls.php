<?php
require '../Config.inc.php';
Class ExportPackinglist_xls {
    public static $titulo_archivo = "";
    /**
    * @return objExcel
    */
    public static function get($id_packing) {
        //include_once 'Funciones.php';

        $class = new Database();
        $con = $class->selectManager();

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
        //datos para el encabezado
        $datosCabecera = $con->select($sql, false)[0];

        //datos para la tabla
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

        $rs = $con->select($query, false); // packing list

        $arr = array();
        $n_pallets = 20;
        // cantidad de pallets = 20
        $date = date("d/m/y H:i:s");
        for ($i=0; $i < count($rs); $i++) { 
            $q = "SELECT pld.* FROM packing_list_detalle pld WHERE pld.id_packing_list = '".$rs[$i]['id']."' AND pld.activo = '1'";
            $pl_ = $con->select($q, false);
            if (count($pl_) != 20) { // 20 pallets exactos
                for ($j=0; $j < $n_pallets; $j++) { 
                    $aux = [
                        "id" => "0",
                        "id_packing_list" => $rs[$i]['id'],
                        "nro_pallet" => ($j+1),
                        "cantidad" => "",
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
                        $pl[$j] = $aux;
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
        $misDatos = $arr;
        $con->close();
        // fin datos tabla

        //-----Horometro anterior
        //$con = $objDB->selectManager()->connect();
        //$input = "'".$misDatos[0]['f_alquiler']."',".$misDatos[0]['id_vehiculo'];
        //$horom = $objDB->selectManager()->spSelect($con, "sp_horometro_anterior", $input); //Horometro
        //$horom_ant = $horom[0]['horom_ant']; // horometro anterior a este reporte
        //$petroleo_ant = $horom[0]['petroleo']; // petroleo anterior
        //--------------------------------fin horom
        //echo "<pre>";
        //die(var_dump($misDatos));

        //die(var_dump($operario));

        if(count($misDatos) > 0 ){ // existen datos para elaborar el excel
            if (PHP_SAPI == 'cli')
            {
                die('Este archivo solo se puede ver desde un navegador web');
            }
            /** Se agrega la libreria PHPExcel */
            require_once '../libreries/PHPExcel/PHPExcel.php';
            
            // Se crea el objeto PHPExcel
            $objPHPExcel = new PHPExcel();
            
            //incluir una imagen            
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('../../images/logo.png'); //ruta
            $objDrawing->setHeight(80); //altura
            $objDrawing->setCoordinates('D1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 
            
            //fin: incluir una imagen

            // Se asignan las propiedades del libro
            //session_start();
            $objPHPExcel->getProperties()->setCreator("CEPIBO") //Autor
                                         ->setLastModifiedBy("CEPIBO") //Ultimo usuario que lo modificó
                                         ->setTitle("Packinglist ".$datosCabecera['codigo'])
                                         ->setSubject("Packinglist ".$datosCabecera['codigo'])
                                         ->setDescription("Detalle de Packinglist ".$datosCabecera['codigo'])
                                         ->setKeywords("packing packinglist reporte")
                                         ->setCategory("Reporte excel");

            $cabecera_1 = "CENTRAL PIURANA DE ASOCIACIONES DE PEQUEÑOS";
            $cabecera_2 = "PRODUCTORES DE BANANO ORGÁNICO-CEPIBO";
            $cabecera_3 = "Avenida Jose de Lama # 1605 – Sullana – Perú";
            $cabecera_4 = "Telfono: 073-490087";
            $tituloReporte = "PACKINGLIST DE CONTENEDOR";
            
            $titulo_archivo = "Packinglist ".$datosCabecera['codigo'];
            self::$titulo_archivo = $titulo_archivo;
            
            $titulosColumnas = array(
                'Asociación', 
                'Tipo de Cajas', 
                'Fecha de Corte', 
                'Empacadora',
                'Código',
                'Nombre del Productor',
                'Número de Cajas',
                'PALLETS'
            );
            $titulosColumnas_pallets = array(
                '1',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
                '10',
                '11',
                '12',
                '13',
                '14',
                '15',
                '16',
                '17',
                '18',
                '19',
                '20'
            );
            
            $inicio_reg = 16;
            //------------------cliente - obra
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:AA1'); //combinar celdas para encabezado
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:AA2'); //combinar celdas para encabezado
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:AA3'); //combinar celdas para encabezado
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:AA4'); //combinar celdas para encabezado
                
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A5:AA5'); //titulo del reporte

                //cabecera de tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A14:A15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B14:B15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C14:C15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D14:D15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E14:E15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F14:F15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G14:G15'); //titulo de la tabla
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H14:AA14'); //titulo de la tabla - COLSPAN 20

                //datos del packing
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A7:B7'); //LBL VAPOR
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C7:D7'); //VAPOR
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A9:B9'); //LBL TIPO FUNDA
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C9:D9'); //TIPO FUNDA
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A11:B11'); //LBL HORA LLEGADA
                //$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A11:A12'); //LBL HORA LLEGADA
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C11:D11'); //HORA LLEGADA
                //$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C11:D12'); //HORA LLEGADA
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J7:O7'); //LBL NRO CONTENEDOR
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('P7:U7'); //NRO CONTENEDOR
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J9:O9'); //LBL NRO TERMOREGISTRO
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('P9:U9'); //NRO TERMOREGISTRO
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J11:O11'); //LBL HORA FIN
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('P11:U11'); //NRO TERMOREGISTRO
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('V8:W8'); //lbl semana
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('X8:AA8'); //NRO semana
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('V10:W10'); //lbl GUIA
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('X10:AA10'); //NRO GUIA

                // Se agregan lso datos del packing
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A7',"VAPOR")
                        ->setCellValue('A9',"TIPO DE FUNDA")
                        ->setCellValue('A11',"HORA LLEGADA DE CONTENEDOR")
                        ->setCellValue('E7',"CLIENTE")
                        ->setCellValue('E9',"HORA SALIDA DEL CONTENEDOR")
                        ->setCellValue('E11',"INICIO LLENADO DE CONTENEDOR")
                        ->setCellValue('J7',"N°DE CONTENEDOR")
                        ->setCellValue('J9',"N°DE TERMOREGISTRO")
                        ->setCellValue('J11',"FINAL LLENADO DE CONTENEDOR")
                        ->setCellValue('V8',"SEM")
                        ->setCellValue('V10',"GUIA N°")
                        ;
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('C7', $datosCabecera['vapor'])
                        ->setCellValue('C9', $datosCabecera['tipo_funda'])
                        ->setCellValue('C11', $datosCabecera['f_llegada_contenedor_format'])
                        ->setCellValue('F7', $datosCabecera['cliente'])
                        ->setCellValue('F9', $datosCabecera['f_salida_contenedor_format'])
                        ->setCellValue('F11', $datosCabecera['f_inicio_llenado_format'])
                        ->setCellValue('P7', $datosCabecera['contenedor'])
                        ->setCellValue('P9', $datosCabecera['nro_termoregistro'])
                        ->setCellValue('P11',$datosCabecera['f_fin_llenado_format'])
                        ->setCellValue('X8', $datosCabecera['nro_semana'])
                        ->setCellValue('X10', $datosCabecera['nro_guia'])
                        ;

                // Se agregan los titulos de LA TABLA
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1',$cabecera_1)
                        ->setCellValue('A2',$cabecera_2)
                        ->setCellValue('A3',$cabecera_3)
                        ->setCellValue('A4',$cabecera_4)
                        ->setCellValue('A5',$tituloReporte)

                        ->setCellValue('A14',$titulosColumnas[0])
                        ->setCellValue('B14',$titulosColumnas[1])
                        ->setCellValue('C14',$titulosColumnas[2])
                        ->setCellValue('D14',$titulosColumnas[3])
                        ->setCellValue('E14',$titulosColumnas[4])
                        ->setCellValue('F14',$titulosColumnas[5])
                        ->setCellValue('G14',$titulosColumnas[6])
                        ->setCellValue('H14',$titulosColumnas[7])
                        ->setCellValue('H15',$titulosColumnas_pallets[0])
                        ->setCellValue('I15',$titulosColumnas_pallets[1])
                        ->setCellValue('J15',$titulosColumnas_pallets[2])
                        ->setCellValue('K15',$titulosColumnas_pallets[3])
                        ->setCellValue('L15',$titulosColumnas_pallets[4])
                        ->setCellValue('M15',$titulosColumnas_pallets[5])
                        ->setCellValue('N15',$titulosColumnas_pallets[6])
                        ->setCellValue('O15',$titulosColumnas_pallets[7])
                        ->setCellValue('P15',$titulosColumnas_pallets[8])
                        ->setCellValue('Q15',$titulosColumnas_pallets[9])
                        ->setCellValue('R15',$titulosColumnas_pallets[10])
                        ->setCellValue('S15',$titulosColumnas_pallets[11])
                        ->setCellValue('T15',$titulosColumnas_pallets[12])
                        ->setCellValue('U15',$titulosColumnas_pallets[13])
                        ->setCellValue('V15',$titulosColumnas_pallets[14])
                        ->setCellValue('W15',$titulosColumnas_pallets[15])
                        ->setCellValue('X15',$titulosColumnas_pallets[16])
                        ->setCellValue('Y15',$titulosColumnas_pallets[17])
                        ->setCellValue('Z15',$titulosColumnas_pallets[18])
                        ->setCellValue('AA15',$titulosColumnas_pallets[19])
                        ;
                
                //Se agregan los datos         
                $i = $inicio_reg;
                
                require 'Funciones.php';
                for ($j = 0; $j < count($misDatos); $j++) {
                    $pl = $misDatos[$j]['pl'];
                    $pallets = $misDatos[$j]['pallets'];
                    $objPHPExcel->setActiveSheetIndex(0)                    
                            ->setCellValue('A'.$i,  $pl['asociacion'])
                            ->setCellValue('B'.$i,  $pl['tipo_caja'])
                            ->setCellValue('C'.$i,  $pl['f_corte_format'])
                            ->setCellValue('D'.$i,  $pl['empacadora'])
                            ->setCellValue('E'.$i,  $pl['codigo_terreno'])
                            ->setCellValue('F'.$i,  $pl['nombre_productor'])
                            //->setCellValue('G'.$i,  $pl['nro_cajas'])
                            ->setCellValue('G'.$i,  "=SUM(H$i:AA$i)")
                            ;
                    $letra_anterior = "G";
                    $letra = "";
                    for ($x=0; $x < count($pallets); $x++) {
                        $letra = Funciones::sumaLetrasExcel(($x==0)?$letra_anterior:$letra, 1);
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($letra.$i, $pallets[$x]['cantidad']);
                    }
                    $i++;
                }
                
                $n = $i;
                $objPHPExcel->setActiveSheetIndex(0)
                        ->mergeCells("A".($i).":F".($i))
                        ->setCellValue('A'.($i),  "TOTAL");
                $letra_anterior = "F";
                $letra = "";
                for ($x=0; $x <= count($pallets); $x++) { // desde 0 porque agrega una letra más
                    $letra = Funciones::sumaLetrasExcel(($x==0)?$letra_anterior:$letra, 1);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($letra.($i), "=SUM(".$letra."$inicio_reg:".$letra.($n-1).")");
                }
                
                //die();
                $estiloCabeceraReporte = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 8,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );
                $estiloSubCabeceraReporte = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 8,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );
                $estiloTituloReporte = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 9,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );

                $estiloEtiquetasReporte = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => true,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 8,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );

                $estiloTablaReporte_center = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 8,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );
                $estiloTablaReporte_left = array(
                    
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => false,
                        'italic'    => false,
                        'strike'    => false,
                        'size' => 8,
                        'color'     => array(
                            'rgb' => '000000'
                            )
                    ),
                    'borders' => array(
                        'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE                    
                        )
                    ), 
                    'alignment' =>  array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'   => 0,
                                'wrap'          => TRUE
                    )
                );

                $estiloTituloColumnas = array(
                    'font' => array(
                        'name'      => 'Arial',
                        'bold'      => true,      
                         'size' => 8,
                        'color'     => array(
                        'rgb' => '000000'
                        )
                    ),
                    'fill'  => array(
                        //'type'      => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startcolor' => array(
                            'rgb' => 'BDBDBD'
                            ),
                        'endcolor'   => array(
                            'argb' => 'FFA4A4A4'
                            )
                        ),
                    'borders' => array(
                        'top'     => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                        'color' => array(
                            'rgb' => '000000'
                            )
                        ),
                        'bottom'     => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                        'color' => array(
                            'rgb' => '000000'
                            )
                        ),
                        'left'     => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                        'color' => array(
                            'rgb' => '000000'
                            )
                        ),
                        'right'     => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                        'color' => array(
                            'rgb' => '000000'
                            )
                        )
                    ),
                    'alignment' =>  array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'wrap'          => TRUE
                    ));

                $estiloInformacion = new PHPExcel_Style();
                $estiloInformacion->applyFromArray(                
                        array(
                            'font' => array(
                                    'name'      => 'Arial',
                                    'bold'      => false,
                                    'italic'    => false,
                                    'strike'    => false,
                                    'size' => 8,
                                    'color'     => array(
                                        'rgb' => '000000'
                                        )
                                ),

                            'fill' => array(
                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                'color'     => array('argb' => 'ffffff')
                                ),
                            'borders' => array(
                                'left'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    ),
                                'right'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    ),
                                'bottom'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    )
                                ),
                            'alignment' =>  array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap'          => TRUE
                            )
                        )
                 );

                $estiloPieTabla = new PHPExcel_Style();
                $estiloPieTabla->applyFromArray(                
                        array(
                            'font' => array(
                                'name'      => 'Arial',
                                'size'      => 8,
                                'bold'      => true,
                                'color'     => array(
                                    'rgb' => '000000'
                                    )
                                ),
                            'fill' => array(
                                'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                'color'     => array('argb' => 'ffffff')
                                ),
                            'borders' => array(
                                'left'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    ),
                                'right'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    ),
                                'bottom'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                                    'color' => array(
                                        'rgb' => '3a2a47'
                                        )
                                    )
                                ),
                            'alignment' =>  array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap'          => TRUE
                            )
                        )
                 );

                $objPHPExcel->getActiveSheet()->getStyle('A1:AA2')->applyFromArray($estiloCabeceraReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A3:AA4')->applyFromArray($estiloSubCabeceraReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A5:AA5')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->getActiveSheet()->getStyle('A7:AA12')->applyFromArray($estiloEtiquetasReporte);

                $objPHPExcel->getActiveSheet()->getStyle('A14:AA15')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, 'A'.$inicio_reg.':AA'.($i-1));

                //$objPHPExcel->getActiveSheet()->getStyle("A$inicio_reg:A".($i-1))->applyFromArray($estiloTablaReporte_left);
                //$objPHPExcel->getActiveSheet()->getStyle("A".($i).":AA".($i))->applyFromArray($estiloEtiquetasReporte);
                $objPHPExcel->getActiveSheet()->setSharedStyle($estiloPieTabla, "A".($i).":AA".($i));
                
                //----------ancho de columnas
                // diferencia con ancho real en excel 0.72 -> se debe adicionar
                $razon = 0.72;
                
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(18.29 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(23.86 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15.43 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(28.14 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10.71 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(44.29 + $razon);
                 $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10.71 + $razon);

                $letra_anterior = "G";
                $letra = "";
                for ($x=0; $x < count($pallets); $x++) {
                    $letra = Funciones::sumaLetrasExcel(($x==0)?$letra_anterior:$letra, 1);
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($letra)->setWidth(2.43 + $razon);
                }
                        
                // Se asigna el nombre a la hoja
                $objPHPExcel->getActiveSheet()->setTitle($datosCabecera['codigo']);

                // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
                $objPHPExcel->setActiveSheetIndex(0);
                // Inmovilizar paneles 
                //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
                //$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,16);
                //die();
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                // Se envia el archivo al navegador web, con el nombre que se indica (Excel2007)
                
                return $objWriter;
        }
        else{
            return '<h2>No hay resultados para mostrar</h2>';
        }
    }// end get
} // end class
<?php   

    // error_reporting(E_ALL);
    // ini_set('display_errors', 'On');
    if(isset($_POST['valTotal2'])){
        writeTablaFiltrada($_POST['valTotal2']);
    }else{
        die ("hola");
    }
        
    function writeTablaFiltrada($valtotal2){
        include_once "config.php";
        // echo "esta incluyendo gestion";
      /*   print_r("WriteTablaFiltrada me estoy ejecutando"); */
      // echo "esta ejecutando writeTableFiltrada";
        // if(isset($_POST['valTotal2'])){
      /*   print_r("Existo soy Gestion dentro del isset"); */
        
            $valoresTotal = json_decode($valtotal2);
            
            $cadenaHeaders ="";
            $segundaCadena ="";
            $cadenaValoresTotal ="";
            $i=0;
            $cadenaCampos="<thead><tr>";
            foreach($valoresTotal as $clave => $valor ){

                $cadenaCampos .= "<th>". $clave ."</th>";

                $cadenaHeaders.= $clave;
                $cadenaHeaders .= ',';

                $segundaCadena .= 'column_name ="' . $clave . '" ';
                $segundaCadena .= ' OR ';

                if($valor!="" && $i==0){
                    $cadenaValoresTotal.= 'WHERE ';
                    $i++;
                }
                if(is_array($valor) && $valor!=array()){ /* array()-> array Vacio */
                        $cadenaValoresTotal .= '(';
                    foreach($valor as $vlr ){
                        $cadenaValoresTotal .= $clave .'="' .$vlr .'" OR ';
                    }
                    $cadenaValoresTotal = substr($cadenaValoresTotal,0,-3);
                    $cadenaValoresTotal .= ') ';
                    $cadenaValoresTotal .= ' AND ';
                
                } else if(!is_array($valor) && $valor!=""){ /* length */
                    $cadenaValoresTotal .= $clave . '="' . $valor . '" ';
                    $cadenaValoresTotal .= ' AND ';
                }
            
            }
            $cadenaCampos .= "</tr></thead>";
            $cadenaValoresTotal = substr($cadenaValoresTotal,0,-4);
            $segundaCadena = substr($segundaCadena,0,-3);
            /* --- */
            $cadenaHeaders = substr($cadenaHeaders,0,-1);/* vas hacia atrás. */

            $queryCampos = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='Inscripciones' AND ($segundaCadena)");
            $queryFiltro = $pdo->query("SELECT $cadenaHeaders FROM Inscripciones $cadenaValoresTotal");


            /*$cadenaCampos="";
            while($rowCampos = $queryCampos->fetch(PDO::FETCH_ASSOC)){  
                $cadenaCampos .= "<th>". $rowCampos[] ."</th>";
            }*/
           // echo $cadenaCampos;

           
            $cadenaFiltros="<tbody>";
            while($rowFiltros = $queryFiltro->fetch(PDO::FETCH_ASSOC)){
                $cadenaFiltros.="<tr>";
                foreach($rowFiltros as $valor ){  
                    $cadenaFiltros.= "<td>" . $valor . "</td>";
                }
                $cadenaFiltros.="</tr>";
            }
            $cadenaFiltros .= "</tbody>";
            $jsondata = array();
            $jsondata["success"] = true;
            $jsondata["data"]["tablafiltros"] = $cadenaCampos.$cadenaFiltros;
            header('Content-type: application/json; charset=UTF-8');
            // var_dump( $jsondata);
	        echo json_encode($jsondata);

           // echo $cadenaFiltros;
           
        // }
        
    }

?>


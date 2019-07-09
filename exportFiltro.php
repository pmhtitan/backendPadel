<?php
include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', '1');

$cadenaHeaders ="";
$segundaCadena ="";
$cadenaValoresTotal ="";
$i=0;
/* if (!isset($_POST['valTotal'])){die("no hay post");} */

if (isset($_POST['valTotal'])){
       /*  $valoresTotal =json_decode($_POST['valoresTotales']); */
        $valoresTotal = json_decode(stripslashes($_POST['valTotal']));
        $cadenaFinalHeaders = array();
        $cadenaFinalContent = array();       
        print_r($valoresTotal);

 print_r($_POST['valTotal']); 
 }
    foreach($valoresTotal as $clave => $valor ){

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
    $cadenaValoresTotal = substr($cadenaValoresTotal,0,-4);
    $segundaCadena = substr($segundaCadena,0,-3);
/* --- */
    $cadenaHeaders = substr($cadenaHeaders,0,-1);/* vas hacia atrás. */
    echo "SELECT $cadenaHeaders FROM Inscripciones $cadenaValoresTotal";
    $query = $pdo->query("SELECT $cadenaHeaders FROM Inscripciones $cadenaValoresTotal");
    /* $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
    print_r($resultado); */

    if($query->rowCount() > 0){

        echo "paso por rowCount";

        //create a file pointer
        $filename = "Inscripciones_Seleccionadas_" . date('Y-m-d-H') . ".csv";
        $f = fopen('filtros/' . $filename , 'w+');
        $delimiter = ";";

         //set headers to download file rather than displayed
         header('Content-Type: text/csv');
         header('Content-Disposition: attachment; filename="' . $filename . '";');

        //echo "SELECT column_name FROM information_schema.columns WHERE table_name='Inscripciones' AND $segundaCadena";
        $q = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='Inscripciones' AND ($segundaCadena)");
     /*    $resultadoHeaders = $q->fetchAll(PDO::FETCH_ASSOC); */
       /*  print_r($resultadoHeaders);  */
        print_r("Soy segunda cadena: ". $segundaCadena);
       $pasoPorAquiNVeces = 1;
        while($row = $q->fetch(PDO::FETCH_ASSOC)){
             array_push($cadenaFinalHeaders, $row['column_name']); 
            /* $cadenaFinalHeaders[] = $row['column_name']; */
            echo $pasoPorAquiNVeces;
            $pasoPorAquiNVeces++;
            //fputcsv($f, $lineData, $delimiter);
        }
         print_r($cadenaFinalHeaders); 
      /*   echo implode(', ', $cadenaFinalHeaders); */
        fputcsv($f, $cadenaFinalHeaders, $delimiter);

        //output each row of the data, format line as csv and write to file pointer
       
           while($row = $query->fetch(PDO::FETCH_ASSOC)){  
              //$resultado = $query->fetchAll(PDO::FETCH_ASSOC); 
              //print_r($resultado);  
              /*foreach($row as $clav => $val){  
                  print_r($row); */ 
                 print_r($row);
                 
               array_push($cadenaFinalContent, $row); 
            //  $cadenaFinalContent[] = $row;
               
           
           /*}*/fputcsv($f, $row, $delimiter);
            /* break;*/
        } 
          /*   print_r($cadenaFinalContent); */
        //}

        //move back to beginning of file
        fseek($f, 0);
            
       //output all remaining data on a file pointer
        fpassthru($f); 

        ?>
        <script src="/paneldecontrol/assets/jquery/jquery-3.4.1.min.js"></script>
        <script>
         
            var urlReload = "welcome.php";
            $(document).ready(function(){
             $.ajax({url: "welcome.php",
             success: function(result){
                $(".divDescargar").html("<a href='filtros/<?php echo $filename ?>' class='btn btn-primary'>Descargar CSV</a>");
            //    window.location = urlReload;
                 }
             });
            });
        </script>
        <?php 

    }
// }

    if (isset($_POST['datoP'])){

    $datoProb = $_POST['datoP'];

    echo $datoProb;
    ?>
    <script>
    alert("Tengo datoP : " + <?php echo $datoProb ?>);
        </script>

    <?php
    }
    
    exit;
/* foreach($dataHeaders as $header){
    $cadenaHeaders .= $header ;
        $cadenaHeaders .= ',';
        $cadenaHeaders = substr($cadenaHeaders,0,-1); *//* vas hacia atrás. */
        /* $cadenaHeaders.length -1 */
        /* palabra */
    /* } */

/* if (isset($_POST['valoresCheckHeaders']) && isset($_POST['valoresCheckSelect']) && isset($_POST['headers_de_selects'])){
    $querie = $pdo->query("SELECT $cadenaHeaders FROM Inscripciones WHERE $cadenaValoresTotal");

} */

/* SELECT Nombre, Apellido, Email, Ciudad, Politica, Sede FROM Inscripciones WHERE Politica = 'SI' AND Sede ='IPE ATENAS' OR Sede ='IPE OPORTO' */

?>
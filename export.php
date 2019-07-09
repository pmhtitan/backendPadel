<?php
//include database configuration file
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 'On');

//get records from database
$query = $pdo->query("SELECT * FROM Inscripciones ORDER BY id DESC");
$q = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='Inscripciones'");
if($query->rowCount() > 0){
    $delimiter = ";";
    $filename = "Inscripciones_" . date('Y-m-d') . ".csv";
    
    //create a file pointer
    $f = fopen('filtros/' . $filename , 'w+');
    
    //set column headers
  /*   $table_fields = $q->fetchColumn(); */
   // $table_fields = $q->fetchAll(PDO::FETCH_ASSOC);
    //$nombres = json_encode($table_fields);
    //echo $nombres;
    $cadenaHead = array();
    $cadenaCont = array();
    while($row = $q->fetch(PDO::FETCH_ASSOC)){
        array_push($cadenaHead, $row['column_name']);
        //fputcsv($f, $lineData, $delimiter);
    }
    fputcsv($f, $cadenaHead, $delimiter);

    $fields = array('Nombre', 'Apellido', 'Email', 'Telefono', 'Ciudad', 'FechaNacimiento', 'Sexo', 'ConoCircuito', 'Rol', 'ParticiparCircuito', 'Sede');
    //fputcsv($f, $nombres, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        /*  array_push($cadenaCont, $row);  */
       // $lineData = array($row['Nombre'], $row['Apellido'], $row['Email'], $row['Telefono'], $row['Ciudad'], $row['FechaNacimiento'], $row['Sexo'], $row['ConoCircuito'], $row['Rol'], $row['ParticiparCircuito'], $row['Sede']);
        fputcsv($f, $row, $delimiter);
    }
    //print_r($lineData);
    
    //move back to beginning of file
    fseek($f, 0);
    
    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);
}
exit;

?>
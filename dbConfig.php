<?php
//DB details
$dbHost     = 'localhost';
$dbUsername = 'user_rec_IPE';
$dbPassword = 'pass_rec_IPE';
$dbName     = 'Recogida_IPE';

//Create connection and select DB
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if($db->connect_error){
    die("Unable to connect database: " . $db->connect_error);
}
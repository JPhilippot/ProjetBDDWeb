<?php
session_start();  //Debut de session -> toujours l'inclure dans les fichiers .php
$DB_host = "localhost";
$DB_user = "root";
$DB_pass = "";
$DB_name ="BDDWeb";

try{
    $dbh = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $e){
    echo $e->getMessage();
    die();
}

require_once 'User.php';
$user= new User($dbh);
?>

<?php
session_start();
$DB_host = "localhost";
$DB_user = "root";
$DB_pass = "";
$DB_name ="BDDWeb";

try{
    $dbh = new PDO("mysql:host={$DB_host};dbname={$DB_name};charset=UTF8",$DB_user,$DB_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $e){
    echo $e->getMessage();
    die();
}

require_once 'User.php';
$user= new User($dbh);

?>

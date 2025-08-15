<?php
session_start();
//connect database
$sname = "localhost";
$uname = "root";
$pwd = "";
$database = "panahan_turnament";
$conn = new mysqli( $sname,  $uname,  $pwd,  $database);

if(!$conn){
    die("connection failed : ". mysqli_connect_error());
}
?>
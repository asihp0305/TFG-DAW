<?php
session_start();

$_SESSION=array();

session_destroy();

if(!isset($_POST["auto"])){
header("Location:../index.php");
exit;
}
?>
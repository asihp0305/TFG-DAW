<?php
require_once __DIR__ . '/../sec/security.php';
include_once "../clases/clase_citas.php";
require_once("../BBDD//BBDD.php");



$cita = new citas();

$option = filter_input(INPUT_POST, 'opt', FILTER_SANITIZE_NUMBER_INT);

switch($option){

    case 1:
        $id_cita = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_SPECIAL_CHARS);

        if($cita->act_notas($id_cita, $notas)){
            echo 'ok';
        }else{
            echo 'error';
        }

    case 2:
        $id_cita = filter_input(INPUT_POST, 'id_cita', FILTER_SANITIZE_NUMBER_INT);
        if($cita->cancelar_cita($id_cita)){
            echo 'ok';
        }else{
            echo 'error';
        }

    default:
        break;
}

?>
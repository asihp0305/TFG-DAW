<?php
require_once __DIR__ . '/../sec/security.php';
include_once "../clases/clase_user.php";
require_once("../BBDD//BBDD.php");



$user = new usuario();
$option = filter_input(INPUT_POST, 'opt', FILTER_SANITIZE_NUMBER_INT);

switch ($option) {
    case '1':
        //Saneamos los datos que nos envian por el formulario
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $surname1 = filter_input(INPUT_POST, 'surname1', FILTER_SANITIZE_SPECIAL_CHARS);
        $surname2 = filter_input(INPUT_POST, 'surname2', FILTER_SANITIZE_SPECIAL_CHARS);
        $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
        $tel_num = filter_input(INPUT_POST, 'tel_num', FILTER_SANITIZE_SPECIAL_CHARS);
        $birth_date = filter_input(INPUT_POST, 'birth_date', FILTER_SANITIZE_SPECIAL_CHARS);

        $user->crear_paciente($email,$name,$surname1,$surname2,$dni,$tel_num,$birth_date);
        
        break;

    case '2':
        //saneamos los datos que llegan del formulario
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $ape1 = filter_input(INPUT_POST, 'apellido1', FILTER_SANITIZE_SPECIAL_CHARS);
        $ape2 = filter_input(INPUT_POST, 'apellido2', FILTER_SANITIZE_SPECIAL_CHARS);
        $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
        $especialidad = filter_input(INPUT_POST, 'especialidad', FILTER_SANITIZE_SPECIAL_CHARS);

        $user->crear_trabajador($nombre,$ape1,$ape2,$dni,$email,$especialidad);

        break;
}
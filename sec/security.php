<?php
session_start();
require_once __DIR__ . '/../BBDD/BBDD.php';

if (!isset($_POST["user"]) && isset($_SESSION["user"])) {
    return;
}
//esto es para el login
//vamos a usar variables superglobales q son $_POST y $_SESSION
else if (isset($_POST["user"]) && isset($_POST["password"])) {
    $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);


    $filt = $db->prepare("SELECT * FROM usuarios where usr = ?");
    $filt->bind_param('s', $user);

    $filt->execute();
    $res = $filt->get_result();

    $vec = $res->fetch_assoc();

    if (!password_verify($pass, $vec['password'])) {
        header("Location: http://{$_SERVER['HTTP_HOST']}/Malpartida-Dental/index.php");
        exit;
    } else {
        $_SESSION["id"] = $vec["id"];
        $_SESSION["user"] = $vec["usr"];
        //$_SESSION["name"] = $vec["nombre"];
        $_SESSION["rol"] = $vec["rol"];
        
        if($vec['rol'] == 'paciente'){
            $filt = $db->prepare('SELECT nombre, id from pacientes where usuario_id = ? ');
            $filt->bind_param('i',$vec['id']);
            $filt->execute();
            $res = $filt->get_result();
            $vec = $res->fetch_assoc();
        }else{
            $filt = $db->prepare('SELECT nombre, id from trabajadores where usuario_id = ? ');
            $filt->bind_param('i',$vec['id']);
            $filt->execute();
            $res = $filt->get_result();
            $vec = $res->fetch_assoc();
        }

        $arrayNombre = explode(' ',$vec['nombre']);
        $_SESSION["name"] = $arrayNombre[0];
        $_SESSION['id_rol'] = $vec['id'];

        // Forzar la zona horaria a España (Península y Baleares)
        date_default_timezone_set('Europe/Madrid');
    }
} else {
    header("Location: http://{$_SERVER['HTTP_HOST']}/Malpartida-Dental/vistas/login.php");
    exit;
}
    ?>
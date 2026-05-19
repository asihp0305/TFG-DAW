<?php
session_start();
include("../BBDD/BBDD.php");

if (!isset($_POST["user"]) && isset($_SESSION["user"])) {
    return;
}
//esto es para el login
//vamos a usar variables superglobales q son $_POST y $_SESSION
else if (isset($_POST["user"]) && isset($_POST["password"])) {
    $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);


    $filt = $db->prepare("SELECT * FROM usuarios where user = ?");
    $filt->bind_param('ss', $user);

    $filt->execute();
    $res = $filt->get_result();

    $vec = $res->fetch_assoc();

    if (!password_verify($pass, $vec['pass'])) {
        header("Location: http://{$_SERVER['HTTP_HOST']}/Malpartida-Dental/vistas/login.php");
        exit;
    } else {
        $_SESSION["id"] = $vec["id"];
        $_SESSION["user"] = $vec["user"];
        $_SESSION["name"] = $vec["nombre"];
        $_SESSION["rol"] = $vec["rol"];
        // Forzar la zona horaria a España (Península y Baleares)
        date_default_timezone_set('Europe/Madrid');
    }
} else {
    header("Location: http://{$_SERVER['HTTP_HOST']}/Malpartida-Dental/vistas/login.php");
    exit;
}
    ?>
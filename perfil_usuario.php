<?php
include('sec/security.php');
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Document</title>
</head>
<body>
    <div id="contenido">
        <?php 
            // Comprobamos el rol que guardamos en la sesión durante el login
            if ($_SESSION['rol'] == 'paciente') {
                include('portales/cliente.php');
            } else if ($_SESSION['rol'] == 'trabajador' || $_SESSION['rol'] == 'admin') {
                include('portales/worker.php');
            }
        ?>  
    </div>
</body>
</html>

<?php
include('sec/security.php')
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
        <div id="titulo">
                <h2>Bienvenid@ <?php echo $_SESSION['name'] ?></h2>
        </div>
        <div id="contenido_botones">
            <h3>¿Que deseas hacer?</h3>
            <div id="botones">
                <button id="ver_citas">Ver mis citas</button>
                <button id="ver_historial">Ver mi historial médico</button>
                <?php 
                    $filt = $db->prepare('SELECT tutor_legal from pacientes where usuario_id = ?');
                    $filt->bind_param('i', $_SESSION['id']);
                    $filt->execute();
                    $res = $filt->get_result();

                    $vec = $res->fetch_assoc();

                    if($vec['tutor_legal'] == 1 ){
                ?>
                <button id="ver_menores">Gestionar mis menores</button>
                <?php }?>
            </div>

            <div id="citas"></div>
            <div id="historial"></div>
            <div id="menores"></div>
        </div>
   </div>
</body>
</html>
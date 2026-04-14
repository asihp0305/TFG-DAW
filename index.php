<?php session_start()?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/cssIndex.css">
    <title>Document</title>
</head>
<body>
    <!-- navbar con el logo,botones de las diferentes paginas ,y un boton de iniciar sesion que cambie segun este iniciada la sesion o no  -->
    <div id="navBar">
        <div id="DVlogo">
            <img src="Imagenes/logo_minimalista.png" alt="logo Malpartida Dental" width="65px" height="45px" >
        </div>
        <div id="DVpaginas" >
            <a href="#">CONÓCENOS</a><!-- link que lleva a la pagina de conocenos -->
            <a href="#">PEDIR CITA</a><!-- link que lleva a la pagina de pedir cita(en esta controlar que esté la sesion iniciada para acceder o mandar a un login) -->
        </div>
        <div id="DVusuario">
            <!-- hacer un if que en caso de que esté la sesion iniciada muestre el perfil del usuario o un boton para iniciar sesion -->
             <?php 
                if(isset($_SESSION["id"])){
             ?>
            <img src="Imagenes/icono_log.png" alt="Sesion iniciada" width="55px" border-radius= "50%">
             <?php }else{?>
            <button id="IniSesion">Iniciar sesión</button>
            <?php }?>
        </div>
    </div>
    <div id="contenido">
        <h1>BIENVENIDOS A MALPARTIDA DENTAL</h1>
        <div id="imagenes_clinica">
            <section>
                <img src="Imagenes/recepcion.JPG" alt="imagen recepcion">
                <img src="Imagenes/puerta_clinica.JPG" alt="imagen recepcion">
                <img src="Imagenes/placa_clinica.JPG" alt="imagen recepcion">
                <img src="Imagenes/sala_espera.JPG" alt="imagen recepcion">
                <img src="Imagenes/consulta1.JPG" alt="imagen recepcion">
            </section>
        </div>
    </div>
</body>
</html>
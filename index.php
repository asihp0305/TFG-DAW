<?php session_start()?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/cssIndex.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Document</title>
</head>
<body>
    <!-- navbar con el logo,botones de las diferentes paginas ,y un boton de iniciar sesion que cambie segun este iniciada la sesion o no  -->
    <div id="navBar">
        <div id="DVlogo">
            <img src="Imagenes/logo_minimalista.png" alt="logo Malpartida Dental" width="65px" height="45px" >
        </div>
        <div id="DVpaginas" >
            <a href="#">BIENVENIDOS</a><!-- link que lleva a la bienvenida -->
            <a href="#">QUIENES SOMOS</a><!-- link que lleva al apartado de quienes somos -->
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

        <div id="quienes_somos">
            <h2>¿QUIENES SOMOS?</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. 
                Quasi reprehenderit aperiam laudantium quia earum mollitia debitis, 
                similique enim voluptates, itaque voluptas. Molestias quibusdam quasi 
                consequuntur possimus dignissimos eveniet voluptatibus sed!
            </p>
        </div>

    </div>

    <div id="modalOverlay" class="modal-oculto">
        <div id="modalLogin"></div>
    </div>
</body>
<script>
$(document).ready(function() {
    // 1. Mostrar el modal al hacer clic en Iniciar Sesión
    $('#IniSesion').click(function(e){
        e.preventDefault(); // Evita recargas raras
        
        $.ajax({
            type: 'POST', // En este caso podría ser GET, ya que solo pides la vista
            url: 'vistas/login.php',
            success: function(data){
                // Metemos el formulario en la caja blanca
                $('#modalLogin').html(data);
                // Le quitamos la clase oculto y lo mostramos con una animación
                $('#modalOverlay').removeClass('modal-oculto').hide().fadeIn(300);
            }
        });
    });

    // 2. Ocultar el modal al hacer clic FUERA de la caja blanca
    $('#modalOverlay').click(function(e){
        // Comprobamos que hemos hecho clic en el fondo (#modalOverlay) y no dentro del login
        if(e.target.id === 'modalOverlay') {
            $(this).fadeOut(300, function() {
                $(this).addClass('modal-oculto');
                $('#modalLogin').empty(); // Limpiamos el HTML para la próxima vez
            });
        }
    });
});
</script>
</html>
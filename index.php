<?php session_start()?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Malpartida Dental - Clínica Odontológica</title>
    <meta name="description" content="Portal web y gestión de citas para la clínica Malpartida Dental.">
    <link rel="stylesheet" href="./css/cssIndex.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <!-- navbar con el logo,botones de las diferentes paginas ,y un boton de iniciar sesion que cambie segun este iniciada la sesion o no  -->
    <div id="navBar">
        <div id="DVlogo">
            <img src="Imagenes/logo_minimalista.png" alt="logo Malpartida Dental" width="65px" height="45px" >
        </div>
        <div id="DVpaginas" >
            <a href="#">BIENVENIDOS</a><!-- link que lleva a la bienvenida -->
            <a href="#quienes_somos">QUIENES SOMOS</a><!-- link que lleva al apartado de quienes somos -->
        </div>
        <div id="DVusuario" class="dropdown-wrapper">
            <?php 
                if(isset($_SESSION["id"])){
            ?>
                <img src="Imagenes/icono_log.png" alt="Sesion iniciada" class="avatar-dropdown" width="55px">
                <div class="dropdown-content">
                    <a href="perfil_usuario.php">Mi Portal</a>
                    <a href="#" id="btn-cambiar-pass-interno">Cambiar Contraseña</a>
                    <a href="sec/log_out.php">Cerrar Sesión</a>
                </div>
            <?php }else{?>
                    <a href="#" id="IniSesion">Iniciar Sesión</a>
            <?php }?>
        </div>
    </div>
    <div id="contenido">
        <h1>BIENVENIDOS A MALPARTIDA DENTAL</h1>
        
        <div id="imagenes_clinica">
            <section>
                <img src="Imagenes/recepcion.JPG" alt="Mostrador principal de recepción de Malpartida Dental">
                <img src="Imagenes/puerta_clinica.JPG" alt="Fachada exterior y puerta de entrada a la clínica">
                <img src="Imagenes/placa_clinica.JPG" alt="Placa oficial del Ilustre Colegio Oficial de Dentistas de Extremadura">
                <img src="Imagenes/sala_espera.JPG" alt="Sala de espera para pacientes con sillas y decoración vegetal">
                <img src="Imagenes/consulta1.JPG" alt="Gabinete dental equipado con sillón y material odontológico">
            </section>
        </div>

       <div id="quienes_somos" style="max-width: 900px; margin: 40px auto; text-align: center; padding: 0 20px;">
            <h2 style="color: rgb(47, 46, 46); margin-bottom: 20px; font-family: 'Anton', sans-serif; letter-spacing: 1px;">NUESTRA FILOSOFÍA</h2>
            
            <p style="font-size: 16px; line-height: 1.8; color: #444; margin-bottom: 15px; text-align: justify;">
                En <strong>Malpartida Dental</strong> no solo cuidamos de tu sonrisa, sino que redefinimos el estándar de la salud bucodental. Nacida en el corazón de Malpartida de Cáceres, nuestra clínica ha evolucionado hasta consolidarse como un centro de referencia y de <strong>alto reconocimiento a nivel estatal</strong>. 
            </p>

            <p style="font-size: 16px; line-height: 1.8; color: #444; margin-bottom: 15px; text-align: justify;">
                Gran parte de este prestigio recae en la trayectoria de nuestro cirujano principal. Tras años de experiencia ejerciendo en los centros más exigentes de todo el territorio nacional, su labor ha sido galardonada con <strong>múltiples premios estatales</strong> que avalan su excelencia médica y quirúrgica. Su liderazgo nos permite abordar desde los tratamientos preventivos hasta las cirugías maxilofaciales más complejas con absoluta garantía de éxito.
            </p>

            <p style="font-size: 16px; line-height: 1.8; color: #444; margin-bottom: 15px; text-align: justify;">
                Además, nos definimos como un espacio <strong>vanguardista e inconformista</strong>. Entendemos que la odontología moderna debe ir irremediablemente unida a la innovación técnica. Por ello, apostamos fuertemente por la integración de nuevas tecnologías en todos nuestros gabinetes: diagnósticos por imagen de altísima precisión, flujos de trabajo 100% digitales y aparatología de última generación, buscando siempre la mejora continua, tiempos de recuperación más cortos y el máximo confort para nuestros pacientes.
            </p>

            <p style="font-size: 16px; line-height: 1.8; color: #444; text-align: center; font-weight: bold; margin-top: 30px;">
                Experiencia premiada y tecnología de vanguardia, ahora a tu alcance.
            </p>
        </div>

    </div>

    <footer id="footer-clinica">
        <div class="footer-container">
            <div class="footer-columna">
                <h4>Malpartida Dental</h4>
                <p>Tu clínica de confianza en Malpartida de Cáceres. Innovación y trato cercano para tu salud bucodental.</p>
                <p class="mvp-badge">Demo Operativa (v1.0)</p>
            </div>
            
            <div class="footer-columna">
                <h4>Contacto y Ubicación</h4>
                <p>📍 C. Cruz, 9, 10910</p>
                <p>📍 Malpartida de Cáceres, Cáceres</p>
                <p>📞 601 645 375</p>
            </div>
            
            <div class="footer-columna">
                <h4>Horario de Atención</h4>
                <p>Lunes - Jueves: 09:00 - 14:00 | 15:00 - 19:00</p>
                <p>Viernes: 09:00 - 17:00</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Malpartida Dental. Asier Hernández Parra Desarrollo a medida - Proyecto DAW.</p>
        </div>
    </footer>

    <div id="modalOverlay" class="modal-oculto">
        <div id="modalLogin"></div>
    </div>
    <div id="modal-cambiar-pass" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="cerrar-modal" id="cerrar-modal-pass">&times;</span>
        <h3 style="text-align: center; margin-bottom: 20px; color: rgb(47, 46, 46);">Cambiar Contraseña</h3>
        
        <form id="form-cambiar-pass" class="formulario-estandar">
            <div class="form-group">
                <label for="pass_antigua">Contraseña Actual:</label>
                <input type="password" id="pass_antigua" name="pass_antigua" required>
            </div>
            
            <div class="form-group">
                <label for="pass_nueva">Nueva Contraseña:</label>
                <input type="password" id="pass_nueva" name="pass_nueva" required>
            </div>
            
            <div class="form-group">
                <label for="pass_confirmar">Confirmar Nueva Contraseña:</label>
                <input type="password" id="pass_confirmar" name="pass_confirmar" required>
            </div>
            
            <button type="submit" id="btn-guardar-pass" class="btn-submit" style="width: 100%;">Actualizar Contraseña</button>
            <div id="mensaje_respuesta_pass" class="mensaje-respuesta"></div>
        </form>
    </div>
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

     // 1. Abrir el modal desde el desplegable
        $('#btn-cambiar-pass-interno').click(function(e) {
            e.preventDefault();
            $('#modal-cambiar-pass').fadeIn(200);
        });

        // 2. Cerrar el modal
        $('#cerrar-modal-pass').click(function() {
            $('#modal-cambiar-pass').fadeOut(200);
            $('#form-cambiar-pass')[0].reset(); // Limpiar inputs al cerrar
            $('#mensaje_respuesta_pass').removeClass('exito error').text('');
        });

        // 3. Procesar el formulario
        $('#form-cambiar-pass').submit(function(e) {
            e.preventDefault();

            let pass_antigua = $('#pass_antigua').val();
            let pass_nueva = $('#pass_nueva').val();
            let pass_confirmar = $('#pass_confirmar').val();
            let divMensaje = $('#mensaje_respuesta_pass');
            let btnGuardar = $('#btn-guardar-pass');

            // Validación Front-End
            if (pass_nueva !== pass_confirmar) {
                divMensaje.removeClass('exito').addClass('error').text('Las contraseñas nuevas no coinciden.');
                return;
            }
            if (pass_nueva.length < 6) {
                divMensaje.removeClass('exito').addClass('error').text('La contraseña debe tener al menos 6 caracteres.');
                return;
            }

            btnGuardar.text('Actualizando...').prop('disabled', true);

            $.ajax({
                url: 'controladores/controlador_usuarios.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    opt: 6, // Usaremos el caso 8 para esto
                    pass_antigua: pass_antigua,
                    pass_nueva: pass_nueva
                },
                success: function(respuesta) {
                    if (respuesta.status === 'ok') {
                        divMensaje.removeClass('error').addClass('exito').text('¡Contraseña actualizada con éxito!');
                        $('#form-cambiar-pass')[0].reset();
                        // Opcional: Cerrar el modal automáticamente tras 2 segundos
                        setTimeout(() => {
                            $('#modal-cambiar-pass').fadeOut(200);
                        }, 2000);
                    } else {
                        divMensaje.removeClass('exito').addClass('error').text(respuesta.mensaje);
                    }
                },
                error: function() {
                    divMensaje.removeClass('exito').addClass('error').text('Error de conexión con el servidor.');
                },
                complete: function() {
                    btnGuardar.text('Actualizar Contraseña').prop('disabled', false);
                }
            });
        });

});
</script>
</html>
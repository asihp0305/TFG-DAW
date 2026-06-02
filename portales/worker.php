<?php
require_once __DIR__ . '/../sec/security.php';
if($_SESSION['rol'] != 'trabajador' && $_SESSION['rol'] != 'admin'){
    echo "No tienes permisos para ver esto.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Empleado - Malpartida Dental</title>
    <link rel="stylesheet" href="css/cssWorker.css"> 
</head>
<body>

<div id="navBar">
    <div id="DVlogo">
        <img src="Imagenes/logo_minimalista.png" alt="Logo Malpartida Dental" width="65px" height="45px">
        <span class="portal-tag">Portal Empleado</span>
    </div>
    
    <div id="DVpaginas">
        <button id="btn-agenda" class="nav-link">Agenda Diaria</button>
        <button id="btn-dar-cita" class="nav-link">Dar Cita</button>
        <button id="btn-crear-usr" class="nav-link">Crear Paciente</button>
        
        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){ ?>
            <button id="btn-crear-trabajador" class="nav-link">Alta Empleado</button>
        <?php } ?>
    </div>
    
    <div class="dropdown-wrapper">
    <img src="Imagenes/icono_log.png" alt="Mi Perfil" class="avatar-dropdown" width="55px">
    
    <div class="dropdown-content">
        <a href="index.php">Inicio</a>
        <a href="#" id="btn-cambiar-pass-interno">Cambiar Contraseña</a>
        <a href="sec/log_out.php">Cerrar Sesión</a>
    </div>
</div>
</div>

<div id="titulo">
    <h3>Bienvenid@, <?php echo htmlspecialchars($_SESSION['name']); ?></h3>
</div>

<div id="vista-dinamica">
    <p style="text-align: center; color: #666;">Selecciona una opción del menú superior para comenzar.</p>
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
<script>
$(document).ready(function() {
    
    // Función de Salida
    $('#btn-logout').click(function() {
        window.location.href = 'sec/log_out.php'; // Ruta corregida para salir desde /portales
    });

    // Cargar Agenda
    $('#btn-agenda').click(function() {
        $('#vista-dinamica').html('<p style="text-align:center;">Cargando agenda...</p>');
        $.ajax({
            url: 'vistas/agenda_trabajador.php', 
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });

    // Cargar Dar Cita
    $('#btn-dar-cita').click(function() {
        $('#vista-dinamica').html('<p style="text-align:center;">Cargando módulo de citas...</p>');
        $.ajax({
            url: 'vistas/dar_cita.php', 
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });

    // Cargar Crear Paciente
    $('#btn-crear-usr').click(function() {
        $('#vista-dinamica').html('<p style="text-align:center;">Cargando formulario...</p>');
        $.ajax({
            url: 'vistas/crear_paciente.php',
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });

    // Cargar Crear Trabajador (Solo visible si el DOM tiene el botón generado por PHP)
    $('#btn-crear-trabajador').click(function() {
        $('#vista-dinamica').html('<p style="text-align:center;">Cargando panel de administración...</p>');
        $.ajax({
            url: 'vistas/crear_trabajador.php',
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
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
        if(pass_nueva !== pass_confirmar) {
            divMensaje.removeClass('exito').addClass('error').text('Las contraseñas nuevas no coinciden.');
            return;
        }
        if(pass_nueva.length < 6) {
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
                if(respuesta.status === 'ok') {
                    divMensaje.removeClass('error').addClass('exito').text('¡Contraseña actualizada con éxito!');
                    $('#form-cambiar-pass')[0].reset();
                    // Opcional: Cerrar el modal automáticamente tras 2 segundos
                    setTimeout(() => { $('#modal-cambiar-pass').fadeOut(200); }, 2000);
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
</body>
</html>
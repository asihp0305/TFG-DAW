<?php
require_once __DIR__ . '/../sec/security.php';
$gestion_menor = false;
if (isset($_POST['id_menor'])) {
    $gestion_menor = true;
}
?>
<link rel="stylesheet" href="css/cssPaciente.css">
<!-- Se ubica dentro de cliente que esta dentro de contenido -->
<div id="navBar">
    <div id="DVlogo">
        <img src="Imagenes/logo_minimalista.png" alt="Logo Malpartida Dental" width="65px" height="45px">
        <span class="portal-tag">Portal Paciente</span>
    </div>

    <div id="DVpaginas">
        <button id="ver_citas" class="nav-link">Mis Citas</button>
        <button id="ver_historial" class="nav-link">Historial Médico</button>
        <?php
        if ($_SESSION['es_tutor'] == 1) {
        ?>
            <button id="ver_menores" class="nav-link">Personas a Cargo</button>
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
    <h2>Bienvenid@ <?php echo $_SESSION['name'] ?></h2>
</div>

<div id="contenido_botones">
    <div id="citas"></div>
    <div id="historial"></div>
    <div id="menores"></div>
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
        // Acción: Ver Citas Próximas
        $('#ver_citas').click(function() {
            $('#historial').empty();
            $('#menores').empty();
            $('#citas').html('<p>Cargando tus citas...</p>');

            $.ajax({
                url: 'vistas/citas_paciente.php',
                type: 'POST',
                data: {
                    id: '<?php echo $_SESSION['id']; ?>'
                },
                success: function(data) {
                    $('#citas').html(data);
                }
            });
        });

        // Acción: Ver Historial Médico
        $('#ver_historial').click(function() {
            $('#citas').empty();
            $('#menores').empty();
            $('#historial').html('<p>Cargando tu historial médico...</p>');

            $.ajax({
                url: 'vistas/hist_paciente.php',
                type: 'POST',
                data: {
                    id: '<?php echo $_SESSION['id']; ?>'
                },
                success: function(data) {
                    $('#historial').html(data);
                }
            });
        });

        // Acción: Ver Citas de Menores
        $('#ver_menores').click(function() {
            $('#citas').empty();
            $('#historial').empty();
            $('#menores').html('<p>Cargando citas de personas a cargo...</p>');

            $.ajax({
                url: 'vistas/menores_paciente.php',
                type: 'POST',
                success: function(data) {
                    $('#menores').html(data);
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
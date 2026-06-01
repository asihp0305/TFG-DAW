<?php
require_once __DIR__ . '/../sec/security.php';
if($_SESSION['rol'] == 'trabajador' || $_SESSION['rol'] == 'admin'){
?>

<div class="form-container" id="altaPacienteContenedor">
    <div id="titulo">
        <h3>Alta de Nuevo Paciente</h3>
        <p class="subtitulo">Rellene los datos para registrar un nuevo paciente en la clínica.</p>
    </div>
    
    <form id="formAltaPaciente" class="formulario-estandar">
        
        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">Datos Personales</legend>
            
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="fila-inputs">
                <div class="form-group" style="flex: 1;">
                    <label for="surname1">Primer Apellido:</label>
                    <input type="text" id="surname1" name="surname1" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="surname2">Segundo Apellido:</label>
                    <input type="text" id="surname2" name="surname2">
                </div>
            </div>

            <div class="form-group">
                <label for="dni">DNI / NIE:</label>
                <input type="text" id="dni" name="dni" required pattern="[0-9]{8}[A-Za-z]{1}" title="Debe contener 8 números y una letra">
            </div>

            <div class="form-group">
                <label for="birth_date">Fecha de Nacimiento:</label>
                <input type="date" id="birth_date" name="birth_date" required>
            </div>
        </fieldset>

        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">Datos de Contacto</legend>
            
            <div class="form-group">
                <label for="tel_num">Teléfono:</label>
                <input type="tel" id="tel_num" name="tel_num" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
        </fieldset>

        <button type="submit" id="btnGuardarPaciente" class="btn-submit">
            Guardar y Enviar Accesos
        </button>
        
    </form>

    <div id="mensajeAlta" class="mensaje-respuesta"></div>
</div>

<script>
$(document).ready(function() {
    $('#formAltaPaciente').submit(function(e) {
        e.preventDefault(); 

        let btn = $('#btnGuardarPaciente');
        let divMensaje = $('#mensajeAlta');

        // Efecto visual de carga
        btn.text('Guardando paciente y enviando email...').prop('disabled', true);
        
        // Limpiamos mensajes y clases anteriores
        divMensaje.removeClass('exito error').text('');

        // Llamada AJAX
        $.ajax({
            type: 'POST',
            url: '../controladores/controlador_usuarios.php', 
            data: $(this).serialize() + '&opt=1', 
            success: function(respuesta) {
                divMensaje.addClass('exito').text('¡Paciente creado con éxito! Se ha enviado el correo con las credenciales.');
                $('#formAltaPaciente')[0].reset(); 
            },
            error: function() {
                divMensaje.addClass('error').text('Error crítico de conexión con el servidor.');
            },
            complete: function() {
                btn.text('Guardar y Enviar Accesos').prop('disabled', false);
            }
        });
    });
});
</script>
<?php } ?>
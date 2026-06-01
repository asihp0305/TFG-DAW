<?php
require_once __DIR__ . '/../sec/security.php';
// Blindaje extra: Solo el admin puede ver y procesar este formulario
if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){
?>

<div class="form-container" id="altaTrabajadorContenedor">
    <div id="titulo">
        <h3>Alta de Nuevo Trabajador</h3>
        <p class="subtitulo">Rellene los datos para registrar un nuevo empleado en el sistema.</p>
    </div>

    <form id="form_crear_trabajador" class="formulario-estandar">
        
        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">Datos Personales</legend>
            
            <div class="form-group">
                <label for="nombre_trab">Nombre:</label>
                <input type="text" id="nombre_trab" name="nombre" required>
            </div>

            <div class="fila-inputs">
                <div class="form-group" style="flex: 1;">
                    <label for="apellido1_trab">Primer Apellido:</label>
                    <input type="text" id="apellido1_trab" name="apellido1" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="apellido2_trab">Segundo Apellido:</label>
                    <input type="text" id="apellido2_trab" name="apellido2" required>
                </div>
            </div>

            <div class="form-group">
                <label for="dni_trab">DNI:</label>
                <input type="text" id="dni_trab" name="dni" pattern="[0-9]{8}[A-Za-z]{1}" title="Debe contener 8 números seguidos de una letra" required>
            </div>
        </fieldset>

        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">Datos Profesionales y Acceso</legend>

            <div class="form-group">
                <label for="especialidad_trab">Especialidad:</label>
                <select id="especialidad_trab" name="especialidad" required>
                    <option value="" disabled selected>Seleccione una especialidad...</option>
                    <option value="auxiliar">Auxiliar</option>
                    <option value="cirujano">Cirujano</option>
                    <option value="pediatra">Pediatra</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email_trab">Correo Electrónico:</label>
                <input type="email" id="email_trab" name="email" required>
            </div>
        </fieldset>

        <button type="submit" id="btn_guardar_trab" class="btn-submit">Registrar Empleado</button>
        
        <div id="mensaje_respuesta" class="mensaje-respuesta"></div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#form_crear_trabajador').submit(function(e) {
        e.preventDefault(); 
        
        let boton = $('#btn_guardar_trab');
        let divMensaje = $('#mensaje_respuesta');

        boton.text('Guardando empleado...').prop('disabled', true);
        divMensaje.removeClass('exito error').text('');
        
        // Serializamos y enviamos al controlador con la opción 2
        let datosFormulario = $(this).serialize() + '&opt=2'; 
        
        $.ajax({
            type: 'POST',
            url: 'controladores/controlador_usuarios.php', 
            data: datosFormulario,
            success: function(respuesta) {
                divMensaje.addClass('exito').text('¡Trabajador creado con éxito! Se ha enviado el correo con las credenciales.');
                $('#form_crear_trabajador')[0].reset(); 
            },
            error: function() {
                divMensaje.addClass('error').text('Error de conexión con el servidor.');
            },
            complete: function() {
                boton.text('Registrar Empleado').prop('disabled', false);
            }
        });
    });
});
</script>
<?php } else {
    echo "<p style='text-align:center; color:red; margin-top:50px;'>Acceso denegado. Se requieren permisos de administrador.</p>";
}
?>
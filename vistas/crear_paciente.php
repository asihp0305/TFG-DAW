<?php
require_once __DIR__ . '/../sec/security.php';
if($_SESSION['rol'] == 'trabajador' || $_SESSION['rol'] == 'admin'){
?>

<div class="contenedor-formulario" id="altaPacienteContenedor">
    <h2>Alta de Nuevo Paciente</h2>
    
    <form id="formAltaPaciente">
        <fieldset style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
            <legend style="font-weight: bold; font-family: 'Anton', sans-serif;">Datos Personales</legend>
            
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required style="width: 100%; margin-bottom: 10px;">

            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label for="surname1">Primer Apellido:</label>
                    <input type="text" id="surname1" name="surname1" required style="width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label for="surname2">Segundo Apellido:</label>
                    <input type="text" id="surname2" name="surname2" style="width: 100%;">
                </div>
            </div>

            <label for="dni">DNI / NIE:</label>
            <input type="text" id="dni" name="dni" required pattern="[0-9]{8}[A-Za-z]{1}" title="Debe contener 8 números y una letra" style="width: 100%; margin-bottom: 10px;">

            <label for="birth_date">Fecha de Nacimiento:</label>
            <input type="date" id="birth_date" name="birth_date" required style="width: 100%; margin-bottom: 10px;">
        </fieldset>

        <fieldset style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
            <legend style="font-weight: bold; font-family: 'Anton', sans-serif;">Datos de Contacto</legend>
            
            <label for="tel_num">Teléfono:</label>
            <input type="tel" id="tel_num" name="tel_num" required style="width: 100%; margin-bottom: 10px;">

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required style="width: 100%; margin-bottom: 10px;">
        </fieldset>

        <button type="submit" id="btnGuardarPaciente" style="width: 100%; padding: 12px; background-color: rgb(47, 46, 46); color: white; border: none; font-weight: bold; cursor: pointer;">
            Guardar y Enviar Accesos
        </button>
    </form>

    <div id="mensajeAlta" style="display: none; margin-top: 15px; padding: 10px; text-align: center; font-weight: bold; border-radius: 4px;"></div>
</div>

<script>
$(document).ready(function() {
    $('#formAltaPaciente').submit(function(e) {
        e.preventDefault(); // Detiene la recarga tradicional del formulario

        let btn = $('#btnGuardarPaciente');
        let divMensaje = $('#mensajeAlta');

        // 1. Efecto visual de carga
        btn.text('Guardando paciente y enviando email...').prop('disabled', true);
        divMensaje.slideUp(); // Ocultamos el mensaje anterior si lo hubiera

        // 2. Llamada AJAX
        $.ajax({
            type: 'POST',
            url: 'controladores/controlador_usuarios.php', // Archivo intermedio que llamará a tu función
            data: $(this).serialize() + '&opt=1', // Empaqueta todos los inputs mágicamente
            success: function(respuesta) {
                divMensaje.css({'background-color': '#d4edda', 'color': '#155724', 'border': '1px solid #c3e6cb'});
                divMensaje.text('¡Paciente creado con éxito! Se ha enviado el correo con las credenciales.').slideDown();
            },
            error: function() {
                divMensaje.css({'background-color': '#f8d7da', 'color': '#721c24'});
                divMensaje.text('Error crítico de conexión con el servidor.').slideDown();
            },
            complete: function() {
                // 3. Pase lo que pase, restauramos el botón a su estado original
                btn.text('Guardar y Enviar Accesos').prop('disabled', false);
            }
        });
    });
});
</script>
<?php }?>
<?php
require_once('../sec/security.php');
?>

<div id="titulo">
    <h3>
        Bienvenid@ <?php echo $_SESSION['name']  ?>
    </h3>
</div>

<div id="opciones">
    <div id="botones" style="margin-bottom: 20px;">
        <button id="btn-dar-cita">DAR CITA</button>
        <button id="btn-agenda">AGENDA</button>
        <button id="btn-crear-usr">CREAR USUARIO</button>
    </div>

    <div id="vista-dinamica">
        <p>Selecciona una opción del menú superior.</p>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Al pulsar "CREAR USUARIO"
    $('#btn-crear-usr').click(function() {
        // Ponemos un texto de carga opcional
        $('#vista-dinamica').html('<p>Cargando formulario...</p>');
        
        // Llamamos a la vista que ya has creado
        $.ajax({
            url: 'vistas/crear_paciente.php',
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });

    // Al pulsar "AGENDA"
    $('#btn-agenda').click(function() {
        $('#vista-dinamica').html('<p>Cargando agenda...</p>');
        $.ajax({
            url: 'vistas/agenda_trabajador.php', // Crearemos este archivo ahora
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });

    // Al pulsar "DAR CITA"
    $('#btn-dar-cita').click(function() {
        $('#vista-dinamica').html('<p>Cargando módulo de citas...</p>');
        $.ajax({
            url: 'vistas/dar_cita.php', // Crearemos este archivo después
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    });
});
</script>
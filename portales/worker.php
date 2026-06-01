<?php
require_once __DIR__ . '/../sec/security.php';
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
    
    <div id="DVusuario">
        <button id="btn-logout">Cerrar Sesión</button>
    </div>
</div>

<div id="titulo">
    <h3>Bienvenid@, <?php echo htmlspecialchars($_SESSION['name']); ?></h3>
</div>

<div id="vista-dinamica">
    <p style="text-align: center; color: #666;">Selecciona una opción del menú superior para comenzar.</p>
</div>

<script>
$(document).ready(function() {
    
    // Función de Salida
    $('#btn-logout').click(function() {
        window.location.href = '../sec/log_out.php'; // Ruta corregida para salir desde /portales
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

});
</script>
</body>
</html>
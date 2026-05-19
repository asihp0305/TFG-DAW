<?php
include('../sec/security.php');
?>

<!-- Se encuentra detro de trabajador que esta dentro de contenido -->
<div id="titulo">
    <h3>
        Bienvenid@ <?php echo $_SESSION['name']  ?>
    </h3>
</div>
<div id="opciones">
    <div id="botones">
        <button>DAR CITA</button>
        <button>AGENDA</button>
        <button>CREAR USUARIO</button>
    </div>

    <div id="dar-cita"></div>
    <div id="agenda"></div>
    <div id="crear-usr"></div>
</div>
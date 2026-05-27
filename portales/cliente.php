<?php
require_once __DIR__ . '/../sec/security.php';
$gestion_menor = false;
if(isset($_POST['id_menor'])){
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
                if($_SESSION['es_tutor'] == 1 ){
                ?>
                <button id="ver_menores" class="nav-link">Personas a Cargo</button>
                <?php }?>
            </div>
            
            <div id="DVusuario">
                <button id="btn_logout" onclick="window.location.href='sec/log_out.php'">Cerrar Sesión</button>
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
            data: { id: '<?php echo $_SESSION['id']; ?>' },
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
            data: { id: '<?php echo $_SESSION['id']; ?>' },
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
});
</script>
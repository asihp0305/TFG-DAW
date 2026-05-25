<?php
require_once __DIR__ . '/../sec/security.php';
$gestion_menor = false;
if(isset($_POST['id_menor'])){
    $gestion_menor = true;
}
?>
<link rel="stylesheet" href="css/cssPaciente.css">
<!-- Se ubica dentro de cliente que esta dentro de contenido -->
        <div id="titulo">
                <h2>Bienvenid@ <?php echo $_SESSION['name'] ?></h2>
        </div>
        <div id="contenido_botones">
            <h3>¿Que deseas hacer?</h3>
            <div id="botones">
                <button id="ver_citas">Ver mis citas</button>
                <button id="ver_historial">Ver mi historial médico</button>
                <?php 
                if(!isset($gestion_menor)){
                    $filt = $db->prepare('SELECT tutor_legal from pacientes where usuario_id = ?');
                    $filt->bind_param('i', $_SESSION['id']);
                    $filt->execute();
                    $res = $filt->get_result();

                    $vec = $res->fetch_assoc();

                    if($vec['tutor_legal'] == 1 ){
                ?>
                <button id="ver_menores">Gestionar mis menores</button>
                <?php } }?>
            </div>

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
});
</script>
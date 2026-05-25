<?php
include('../sec/security.php');

$query = <<<SQL
        SELECT 
            ci.id, 
            ci.fecha, 
            ci.hora_inicio, 
            tr.nombre AS nombre_trabajador, 
            serv.nombre AS nombre_servicio, 
            ci.notas
        FROM citas ci
        INNER JOIN trabajadores tr 
            ON ci.trabajador_id = tr.id
        INNER JOIN servicios serv 
            ON ci.servicio_id = serv.id
        WHERE paciente_id = ?
        SQL;

$filt = $db->prepare($query);
$filt->bind_param('i', $_POST['id']);
$filt->execute();
$res = $filt->get_result();



for($i = 0; $i < $res->num_rows; $i++){
    $vec = $res->fetch_assoc();

    // fecha de la cita juntando los datos de la base de datos
    $str_fecha_cita = $vec['fecha'].' '.$vec['hora_inicio'];

    $fecha_cita = new DateTime($str_fecha_cita);
    $fecha_actual = new DateTime(); // fecha exacta del momento actual

    $limite_cancelacion = clone $fecha_actual;
    $limite_cancelacion->modify('+24 hours');
?>

<div class="cita">
    <div class="fecha">
        <h3><?php echo $vec['fecha'].' '. $vec['hora_inicio'] ?></h3>
    </div>
    <div class="servicio">
        <p> <?php echo $vec['nombre_servicio'] ?> </p>
    </div>
    <div class="profesional">
        <p> <?php echo $vec['nombre_trabajador']?> </p>
    </div>
    <div class="acciones">
        <?php if($vec['notas'] != null){ ?>
            <button class="btn_notas">VER NOTAS</button>
        <?php } ?>
        
        <?php if($fecha_actual > $limite_cancelacion){ ?>
            <button class="btn_cancelar" idCita="<?php echo $vec['id'] ?>">CANCELAR</button>
        <?php } ?>
    </div>
</div>

<?php } ?>

<script>
$(document).ready(function() {
    
    // Capturamos el clic en el botón de cancelar
    $('.btn_cancelar').click(function() {
        
        // Cogemos la ID de la cita que guardaste en el atributo idCita
        let idCitaSeleccionada = $(this).attr('idCita');
        
        // Buscamos el contenedor padre (la tarjeta de la cita entera) para animarla luego
        let tarjetaCita = $(this).closest('.cita');

        // Pedimos confirmación al usuario
        if(confirm("¿Estás seguro de que deseas cancelar esta cita? Esta acción no se puede deshacer.")) {
            
            // Cambiamos el texto del botón mientras el servidor procesa la petición
            let boton = $(this);
            boton.text("CANCELANDO...").prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'controladores/controlador_citas.php',
                data: {
                    opt: 3, // Opción 3: Cancelar Cita
                    id: idCitaSeleccionada
                },
                success: function(respuesta) {
                    if(respuesta.trim() === 'ok') {
                        // Animación suave para que la cita desaparezca de la pantalla
                        tarjetaCita.slideUp(400, function() {
                            $(this).remove(); // La borramos del HTML cuando acabe la animación
                        });
                    } else {
                        alert("Hubo un problema al cancelar la cita en la base de datos.");
                        boton.text("CANCELAR").prop('disabled', false); // Restauramos el botón
                    }
                },
                error: function() {
                    alert("Error crítico de conexión con el servidor.");
                    boton.text("CANCELAR").prop('disabled', false);
                }
            });
        }
    });

});
</script>
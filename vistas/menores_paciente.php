<?php
include('../sec/security.php');

$query = <<<SQL
        SELECT 
            ci.id, 
            ci.fecha, 
            ci.hora_inicio, 
            tr.nombre AS nombre_trabajador, 
            tr.apellidos AS apellidos_trabajador, 
            serv.nombre AS nombre_servicio, 
            p.nombre AS nombre_menor,
            ci.notas
        FROM citas ci
        INNER JOIN trabajadores tr ON ci.trabajador_id = tr.id
        INNER JOIN servicios serv ON ci.servicio_id = serv.id
        INNER JOIN pacientes p ON ci.paciente_id = p.id
        WHERE p.tutor_id = ? AND ci.estado = 'pendiente'
        ORDER BY ci.fecha ASC, ci.hora_inicio ASC
        SQL;

$filt = $db->prepare($query);
// Usamos el id_rol porque es el ID del paciente adulto
$filt->bind_param('i', $_SESSION['id_rol']);
$filt->execute();
$res = $filt->get_result();
?>

<div class="historial-grid">
    <?php
    if ($res->num_rows === 0) {
        echo "<p class='no-datos'>No hay citas pendientes para menores a tu cargo.</p>";
    } else {
        for ($i = 0; $i < $res->num_rows; $i++) {
            $vec = $res->fetch_assoc();
            $fecha_cita = new DateTime($vec['fecha'].' '.$vec['hora_inicio']);
            $fecha_actual = new DateTime(); 
            $limite_cancelacion = (clone $fecha_actual)->modify('+24 hours');
            
            $fecha_formateada = date('d/m/Y', strtotime($vec['fecha']));
            $hora_formateada = date('H:i', strtotime($vec['hora_inicio']));
    ?>
        <div class="cita">
            <div class="cita-info">
                <p><strong>👶 Paciente:</strong> <span style="color: #007bff; font-weight: bold;"><?php echo htmlspecialchars($vec['nombre_menor']); ?></span></p>
                <p><strong>📅 Fecha y Hora:</strong> <?php echo $fecha_formateada; ?> a las <?php echo $hora_formateada; ?></p>
                <p><strong>🦷 Tratamiento:</strong> <?php echo htmlspecialchars($vec['nombre_servicio']); ?></p>
                <p><strong>👨‍⚕️ Profesional:</strong> Dr/a. <?php echo htmlspecialchars($vec['nombre_trabajador'].' '. $vec['apellidos_trabajador']); ?></p>
            </div>
            
            <div class="acciones">
                <?php if($fecha_actual < $limite_cancelacion){ ?>
                    <button class="btn_cancelar" idCita="<?php echo $vec['id']; ?>">CANCELAR CITA</button>
                <?php } else { ?>
                    <span style="font-size: 12px; color: #d9534f;">Cancelación no disponible (< 24h)</span>
                <?php } ?>
            </div>
        </div>
    <?php 
        }
    } 
    ?>
</div>

<script>
$(document).ready(function() {
    $('.btn_cancelar').click(function() {
        let idCitaSeleccionada = $(this).attr('idCita');
        let tarjetaCita = $(this).closest('.cita');

        if(confirm("¿Estás seguro de que deseas cancelar la cita de este menor?")) {
            let boton = $(this);
            boton.text("CANCELANDO...").prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'controladores/controlador_citas.php',
                data: { opt: 3, id: idCitaSeleccionada },
                success: function(respuesta) {
                    if(respuesta.trim() === 'ok') {
                        tarjetaCita.slideUp(400, function() { $(this).remove(); });
                    } else {
                        alert("Error al cancelar. Asegúrate de tener permisos sobre este paciente.");
                        boton.text("CANCELAR CITA").prop('disabled', false); 
                    }
                }
            });
        }
    });
});
</script>
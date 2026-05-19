<?php
require_once('../sec/security.php');

// Verificamos por seguridad que sea trabajador
if($_SESSION['rol'] != 'trabajador' && $_SESSION['rol'] != 'admin'){
    echo "No tienes permisos para ver esto.";
    exit;
}
// Por defecto vemos el día de hoy, a menos que el usuario filtre por otro día
$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
?>

<div id="selector_fecha" style="text-align: center; margin-bottom: 20px;">
    <button id="btn-dia-anterior">&laquo; Día Anterior</button>
    
    <h3 style="display: inline-block; margin: 0 15px;">
        Agenda del día: <input type="date" id="fecha_agenda" value="<?php echo $fecha_filtro; ?>">
    </h3>
    
    <button id="btn-dia-siguiente">Día Siguiente &raquo;</button>
    <br><br>
    <button id="BotBuscarFecha">Buscar Fecha Seleccionada</button>
</div>

<div id="Tabla">
    <table border="1px solid black" align="center">
        <tr>
            <th>Hora</th>
            <th>Paciente</th>
            <th>Servicio</th>
            <th>Notas Médicas</th>
            <th>Guardar Notas</th>
        </tr>
        
        <?php
        $trabajador_id = $_SESSION['id'];
        
        // Consulta uniendo citas, pacientes y servicios
        $query = "
            SELECT 
                ci.id,
                ci.hora_inicio,
                pa.nombre AS paciente_nombre,
                se.nombre AS servicio_nombre,
                ci.notas
            FROM citas ci
            INNER JOIN pacientes pa ON ci.paciente_id = pa.id
            INNER JOIN servicios se ON ci.servicio_id = se.id
            WHERE ci.trabajador_id = ? AND ci.fecha = ?
            ORDER BY ci.hora_inicio ASC
        ";

        $filt = $db->prepare($query);
        $filt->bind_param("is", $trabajador_id, $fecha_filtro);
        $filt->execute();
        $res = $filt->get_result();

        // Usamos el for clásico de tu profesor
        for ($i = 0; $i < $res->num_rows; $i++) {
            $vec = $res->fetch_assoc();
            $hora_formateada = date('H:i', strtotime($vec['hora_inicio']));
        ?>
            <tr>
                <td><?php echo $hora_formateada ?></td>
                <td><?php echo $vec["paciente_nombre"] ?></td>
                <td><?php echo $vec["servicio_nombre"] ?></td>
                
                <td><input type="text" value="<?php echo htmlspecialchars($vec["notas"] ?? '') ?>" id="notas_<?php echo $vec["id"] ?>"></td>
                <td><button type="button" class="BotUP" idup="<?php echo $vec["id"] ?>">Actualizar Notas</button></td>
            </tr>
        <?php } ?>
        
    </table>
</div>

<script>
$(document).ready(function() {

    // Función auxiliar para recargar la vista con una nueva fecha
    function recargarAgenda(nuevaFecha) {
        $.ajax({
            url: 'vistas/agenda_trabajador.php?fecha=' + nuevaFecha,
            type: 'GET',
            success: function(data) {
                $('#vista-dinamica').html(data);
            }
        });
    }

    // 1. Botón Buscar (cuando eliges una fecha manualmente en el calendario)
    $("#BotBuscarFecha").click(function() {
        let fechaSeleccionada = $("#fecha_agenda").val();
        recargarAgenda(fechaSeleccionada);
    });

    // 2. Botón Día Anterior
    $("#btn-dia-anterior").click(function() {
        let inputFecha = $("#fecha_agenda")[0];
        let fecha = new Date(inputFecha.value);
        fecha.setDate(fecha.getDate() - 1); // Restamos 1 día
        
        let nuevaFechaStr = fecha.toISOString().split('T')[0]; // Formateamos a YYYY-MM-DD
        recargarAgenda(nuevaFechaStr);
    });

    // 3. Botón Día Siguiente
    $("#btn-dia-siguiente").click(function() {
        let inputFecha = $("#fecha_agenda")[0];
        let fecha = new Date(inputFecha.value);
        fecha.setDate(fecha.getDate() + 1); // Sumamos 1 día
        
        let nuevaFechaStr = fecha.toISOString().split('T')[0];
        recargarAgenda(nuevaFechaStr);
    });

    // 4. Actualizar las notas de la cita en la misma fila
    $(".BotUP").click(function () {
        let laid = $(this).attr("idup");
        let notas_nuevas = $("#notas_" + laid).val();
        
        // Cambiamos el texto del botón temporalmente para que el usuario sepa que está cargando
        let boton = $(this);
        let textoOriginal = boton.text();
        boton.text("Guardando...");

        $.ajax({
            type: "POST",
            url: "controladores/controlador_citas.php",
            data: {
                id: laid,
                notas: notas_nuevas,
                opt: 1
            },
            success: function (respuesta) {
                if(respuesta.trim() === "ok") {
                    // Ponemos el botón verde 2 segundos para dar feedback visual de éxito
                    boton.text("¡Guardado!");
                    boton.css("background-color", "#d4edda"); 
                    setTimeout(() => {
                        boton.text(textoOriginal);
                        boton.css("background-color", "");
                    }, 2000);
                } else {
                    alert("Hubo un problema al guardar las notas.");
                    boton.text(textoOriginal);
                }
            },
            error: function() {
                alert("Error de conexión con el servidor.");
                boton.text(textoOriginal);
            }
        });
    });
});
</script>
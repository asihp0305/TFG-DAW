<?php
include('../sec/security.php');

// Recoger ordenación y control anti-inyección
$orden = isset($_POST['orden']) ? $_POST['orden'] : 'DESC';
if ($orden !== 'ASC' && $orden !== 'DESC') {
    $orden = 'DESC'; 
}

// Recoger filtro de servicio
$servicio_filtro = isset($_POST['servicio']) ? $_POST['servicio'] : 'todos';

// Base de la consulta para el historial (Citas con estado 'completada')
$query = "
    SELECT 
        ci.id, 
        ci.fecha, 
        ci.hora_inicio, 
        tr.nombre AS nombre_trabajador, 
        serv.nombre AS nombre_servicio, 
        ci.notas
    FROM citas ci
    INNER JOIN trabajadores tr ON ci.trabajador_id = tr.id
    INNER JOIN servicios serv ON ci.servicio_id = serv.id
    INNER JOIN pacientes p ON ci.paciente_id = p.id
    WHERE p.usuario_id = ? AND ci.estado = 'completada'
";

if ($servicio_filtro !== 'todos') {
    $query .= " AND serv.id = ? ";
}

$query .= " ORDER BY ci.fecha $orden, ci.hora_inicio $orden";

$filt = $db->prepare($query);

if ($servicio_filtro !== 'todos') {
    $filt->bind_param('ii', $_SESSION['id'], $servicio_filtro);
} else {
    $filt->bind_param('i', $_SESSION['id']);
}

$filt->execute();
$res = $filt->get_result();

// Consultar los servicios existentes para poblar el selector de filtro
$felt = $db->prepare( 'SELECT id, nombre FROM servicios ORDER BY nombre ASC');
$felt->execute();
$res_servicios = $felt->get_result();
?>
<div class="historial-filtros">
    <div class="filtro-grupo">
        <label for="orden_historial">Ordenar por fecha:</label>
        <select id="orden_historial">
            <option value="DESC" <?php echo $orden === 'DESC' ? 'selected' : ''; ?>>Más reciente primero</option>
            <option value="ASC" <?php echo $orden === 'ASC' ? 'selected' : ''; ?>>Más antigua primero</option>
        </select>
    </div>

    <div class="filtro-grupo">
        <label for="servicio_historial">Filtrar por Tratamiento:</label>
        <select id="servicio_historial">
            <option value="todos" <?php echo $servicio_filtro === 'todos' ? 'selected' : ''; ?>>Todos los tratamientos</option>
            <?php 
            if ($res_servicios && $res_servicios->num_rows > 0) {
                while ($srv = $res_servicios->fetch_assoc()) {
                    $selected = ((string)$servicio_filtro === (string)$srv['id']) ? 'selected' : '';
                    echo "<option value='{$srv['id']}' {$selected}>" . htmlspecialchars($srv['nombre']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="historial-grid">
    <?php
    if ($res->num_rows === 0) {
        echo "<p class='no-datos'>No se han encontrado tratamientos completados en tu historial.</p>";
    } else {
        for ($i = 0; $i < $res->num_rows; $i++) {
            $vec = $res->fetch_assoc();
            $fecha_formateada = date('d/m/Y', strtotime($vec['fecha']));
            $hora_formateada = date('H:i', strtotime($vec['hora_inicio']));
    ?>
            <div class="cita">
                <div class="cita-info">
                    <p><strong>📅 Fecha y Hora:</strong> <?php echo $fecha_formateada; ?> a las <?php echo $hora_formateada; ?></p>
                    <p><strong>🦷 Tratamiento:</strong> <?php echo htmlspecialchars($vec['nombre_servicio']); ?></p>
                    <p><strong>👨‍⚕️ Profesional:</strong> Dr/a. <?php echo htmlspecialchars($vec['nombre_trabajador']); ?></p>
                    <p><strong>📝 Notas Médicas:</strong> <span class="notas-texto"><?php echo htmlspecialchars($vec['notas'] ?? 'Sin anotaciones.'); ?></span></p>
                </div>
            </div>
    <?php 
        }
    } 
    ?>
</div>

<script>
$(document).ready(function() {
    // Función AJAX para refrescar el componente de historial de forma interna al cambiar filtros
    function actualizarFiltrosHistorial() {
        let ordenSel = $('#orden_historial').val();
        let servicioSel = $('#servicio_historial').val();
        
        $.ajax({
            url: 'vistas/historial_paciente.php',
            type: 'POST',
            data: {
                id: '<?php echo $_POST['id']; ?>',
                orden: ordenSel,
                servicio: servicioSel
            },
            success: function(data) {
                $('#historial').html(data);
            }
        });
    }

    // Escuchar el cambio de los Selectors
    $('#orden_historial, #servicio_historial').change(function() {
        actualizarFiltrosHistorial();
    });
});
</script>
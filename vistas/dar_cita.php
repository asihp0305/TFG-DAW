<?php 
require_once __DIR__ . '/../sec/security.php';
if($_SESSION['rol'] != 'trabajador' && $_SESSION['rol'] != 'admin'){
    echo "No tienes permisos para ver esto.";
    exit;
}
// Impedir que se cojan citas en el pasado
$fecha_hoy = date('Y-m-d');

// Cargamos los servicios disponibles para el primer select
$query_servicios = "SELECT id, nombre FROM servicios ORDER BY nombre ASC";
$res_servicios = $db->query($query_servicios);
?>
<div class="form-container" id="agendarCitaContenedor">
    <div id="titulo">
        <h3>Agendar Nueva Cita</h3>
        <p class="subtitulo">Asignación de tratamientos, profesionales y horarios.</p>
    </div>

    <form id="form_dar_cita" class="formulario-estandar">
        
        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">1. Paciente</legend>
            
            <div class="fila-inputs">
                <div class="form-group" style="flex: 1;">
                    <label for="dni_busqueda">DNI del Paciente:</label>
                    <input type="text" id="dni_busqueda" name="dni_paciente" placeholder="Ej: 12345678A" required autocomplete="off">
                    <span class="helper-text" id="hint_dni">Escribe el DNI y pulsa Enter o sal del campo para buscar.</span>
                </div>
                
                <div class="form-group" style="flex: 2;">
                    <label for="nombre_paciente_display">Nombre del Paciente:</label>
                    <input type="text" id="nombre_paciente_display" readonly placeholder="Esperando validación de DNI..." style="background-color: rgba(0,0,0,0.05); cursor: not-allowed;">
                    <input type="hidden" id="id_paciente_oculto" name="id_paciente">
                </div>
            </div>
        </fieldset>

        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">2. Detalles Principales</legend>
            
            <div class="fila-inputs">
                <div class="form-group" style="flex: 1;">
                    <label for="fecha_cita">Fecha de la cita:</label>
                    <input type="date" id="fecha_cita" name="fecha" min="<?php echo $fecha_hoy; ?>" required>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="servicio_cita">Tipo de Consulta:</label>
                    <select id="servicio_cita" name="servicio_id" required>
                        <option value="" disabled selected>Seleccione un tratamiento...</option>
                        <?php 
                        if($res_servicios){
                            while($srv = $res_servicios->fetch_assoc()){
                                echo "<option value='{$srv['id']}'>" . htmlspecialchars($srv['nombre']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset class="fieldset-integrado">
            <legend class="legend-integrado">3. Asignación y Disponibilidad</legend>
            
            <div class="form-group">
                <label for="profesional_cita">Profesional Principal:</label>
                <select id="profesional_cita" name="trabajador_id" required disabled>
                    <option value="" disabled selected>Esperando tipo de consulta...</option>
                </select>
            </div>

            <div class="fila-inputs">
                <div class="form-group" style="flex: 1;">
                    <label for="hora_cita">Hora Disponible:</label>
                    <select id="hora_cita" name="hora_inicio" required disabled>
                        <option value="" disabled selected>Esperando fecha y profesional...</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="auxiliar_cita">Auxiliar de Apoyo:</label>
                    <select id="auxiliar_cita" name="auxiliar_id" required disabled>
                        <option value="" disabled selected>Esperando hora de la cita...</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <button type="submit" id="btn_agendar_cita" class="btn-submit" disabled>Confirmar Cita</button>
        <div id="mensaje_respuesta_cita" class="mensaje-respuesta"></div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Aquí es donde programaremos toda la cascada de eventos AJAX
    
    // 1. Evento: Al salir del campo DNI (blur) -> Buscar Paciente
    $('#dni_busqueda').blur(function() {
        let dni = $(this).val().trim();
        let hint = $('#hint_dni');
        if(dni.length == 9 ) {
            hint.text('Buscando en la base de datos...').css('color', '#007bff');

            $.ajax({
                url: 'controladores/controlador_usuarios.php',
                type: 'POST',
                dataType: 'json',//esperamos un json de respuesta
                data:{opt: 3, dni: dni},
                success: function(respuesta){
                    if(respuesta.status == 'ok'){
                        //Paciente encontrado
                        $('#nombre_paciente_display').val(respuesta.nombre_completo).css('background-color', '#e8f5e9'); // Fondo verdecito
                        $('#id_paciente_oculto').val(respuesta.id_paciente);
                        hint.text('Paciente verificado.').css('color', '#28a745');

                        //desbloqueo del siguiente paso
                        $('#servicio_cita').prop('disabled', false);
                    }else{
                        // Paciente no encontrado
                        $('#nombre_paciente_display').val('').css('background-color', 'rgba(0,0,0,0.05)');
                        $('#id_paciente_oculto').val('');
                        hint.text('No existe ningún paciente con ese DNI.').css('color', '#d9534f');
                        
                        // Bloqueamos el siguiente paso por seguridad
                        $('#servicio_cita').prop('disabled', true).val('');           
                    }
                }
            })
        }else{
            hint.text('El DNI debe tener al menos 9 caracteres.').css('color', '#d9534f');
        }
    });

    // 2. Evento: Al cambiar el Servicio -> Buscar Profesionales compatibles
    $('#servicio_cita').change(function() {
        let servicio_id = $(this).val();
        let selectProf = $('#profesional_cita');

        //efecto de carga
        selectProf.prop('disabled', true).html('<option value="" disabled selected>Cargando profesionales...</option>');

        $.ajax({
            url: 'controladores/controlador_usuarios.php',
            type: 'POST',
            dataType: 'json',
            data:{opt: 4, id_serv: servicio_id},
            success:function(respuesta){
                //limpieza del select
                selectProf.empty().append('<option value="" disabled selected>Seleccione un profesional...</option>');

                if(respuesta.status == 'ok'){
                    //recorremos el json para formar los options
                    $.each(respuesta.data, function(index, prof){
                        let nombre = prof.nombre

                        let especialidad = prof.especialidad.charAt(0).toUpperCase() + prof.especialidad.slice(1);

                        selectProf.append('<option value="' + prof.id + '">Dr/a. ' + nombre + ' (' + especialidad + ')</option>');
                    })
                    //desbloqueo del select
                    selectProf.prop('disabled',false);
                }else{
                    selectProf.append('<option value="" disabled>No hay profesionales disponibles</option>');
                }
            },
            error:function(){
                selectProf.empty().append('<option value="" disabled>Error de conexión</option>');
            }
        })
    });

    // 3. Evento: Al cambiar Fecha o Profesional -> Buscar Horas libres
    $('#fecha_cita, #profesional_cita').change(function() {
        let fecha = $('#fecha_cita').val();
        let trab_id = $('#profesional_cita').val();
        let selectHora = $('#hora_cita');
        let servicio_id = $('#servicio_cita').val();
        if(fecha && trab_id) {
            selectHora.prop('disabled', true).html('<option value="" disabled selected>Calculando huecos consecutivos...</option>');
            $('#auxiliar_cita').prop('disabled', true).html('<option value="" disabled selected>Esperando hora de la cita...</option>'); 

            $.ajax({
                url: 'controladores/controlador_citas.php',
                type: 'POST',
                dataType:'json',
                data:{opt:3, fecha: fecha, trabajador_id: trab_id, servicio: servicio_id},
                success:function(respuesta){
                    selectHora.empty().append('<option value="" disabled selected>Seleccione una hora...</option>');

                    if(respuesta.status === 'ok'){
                        $.each(respuesta.datos, function(index, hora){
                            selectHora.append('<option value="' + hora + '">' + hora + '</option>');
                        });
                        selectHora.prop('disabled',false);
                    }else if(respuesta.status === 'lleno'){
                        selectHora.append('<option value="" disabled>Agenda llena para este día</option>');
                    }
                },
                error:function(){
                    selectHora.empty().append('<option value="" disabled>Error al cargar horario</option>');
                }
            });
        }
    });

    // 4. Evento: Al cambiar la Hora -> Buscar Auxiliares libres
    $('#hora_cita').change(function() {
        let fecha = $('#fecha_cita').val();
        let hora = $(this).val();
        let selectAux = $('#auxiliar_cita');
        let btnGuardar = $('#btn_agendar_cita')
        if(fecha && hora) {
            selectAux.prop('disabled', true).html('<option value="" disabled selected>Buscando auxiliares...</option>');
            btnGuardar.prop('disabled', true); // Mantenemos el botón de guardar bloqueado por si acaso

            $.ajax({
                url: 'controladores/controlador_usuarios.php',
                type: 'POST',
                dataType: 'json',
                data:{opt:5, fecha: fecha, hora: hora},
                success:function(respuesta){
                    selectAux.empty().append('<option value="" disabled selected>Seleccione un auxiliar...</option>');
                    if(respuesta.status === 'ok'){
                        $.each(respuesta.datos, function(index,aux){
                            selectAux.append('<option value="' + aux.id + '">' + aux.nombre + '</option>');
                        });

                        // Añadimos una opción para consultas que no requieran auxiliar
                        selectAux.append('<option value="ninguno">Sin auxiliar asignado</option>');
                        selectAux.prop('disabled', false);

                       // Cuando elijan auxiliar, por fin desbloqueamos el botón de enviar
                        selectAux.change(function(){
                            btnGuardar.prop('disabled', false).css({
                                'background-color': '#28a745', 
                                'transform': 'scale(1.02)'
                            });
                        });
                    }else{
                        selectAux.append('<option value="ninguno">Sin auxiliares disponibles</option>');
                        selectAux.prop('disabled', false);
                        // Si no hay auxiliares, al menos dejamos guardar la cita sin él
                        btnGuardar.prop('disabled', false).css('background-color', '#28a745');
                    }
                }
            });
        }
    });

    $('#form_dar_cita').submit(function(e){
        e.preventDefault();

        let btn_guardar = $('#btn_agendar_cita');
        let div_mensaje = $('#mensaje_respuesta_cita');

        btn_guardar.text('Registrando la cita...');
        div_mensaje.removeClass('exito error').text('');

        let datos_cita = $(this).serialize() + '&opt=4';

        $.ajax({
            url: 'controladores/controlador_citas.php',
            type: 'POST',
            dataType: 'json',
            data: datos_cita,
            success: function(respuesta){
                if(respuesta.status === 'ok'){
                    div_mensaje.addClass('exito').text('¡Cita agendada correctamente en el sistema!');
                    
                    //reset del formulario
                    $('#form_dar_cita')[0].reset();
                    $('#nombre_paciente_display').val('').css('background-color', 'rgba(0,0,0,0.05)');

                    btn_guardar.css({
                        'background-color': 'rgb(47, 46, 46)', 
                        'transform': 'none'
                    }).text('Confirmar Cita');
                }else{
                    div_mensaje.addClass('error').text('Error: ' + respuesta.mensaje);
                    btn_guardar.prop('disabled', false).text('Confirmar Cita');
                }
            },
            error: function(){
                divMensaje.addClass('error').text('Error crítico de conexión con el servidor.');
                btnGuardar.prop('disabled', false).text('Confirmar Cita');
            }
        })


    });
});
</script>
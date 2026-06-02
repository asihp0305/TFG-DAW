<?php

class citas{

    function act_notas($id_cita, $nueva_nota){
        include('../BBDD/BBDD.php');

        $filt = $db->prepare('UPDATE citas SET notas = ? WHERE id = ?');
        $filt->bind_param('si',$nueva_nota, $id_cita);
       
        if($filt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function cancelar_cita($id_cita){
        include('../BBDD/BBDD.php');

        $id_ejecutor = $_SESSION['id_rol'];

        $filt = $db->prepare('UPDATE citas SET estado = "cancelada" WHERE id = ? AND (paciente_id = ? OR trabajador_id = ? or tutor_id = ?)');
        $filt->bind_param('iiii',$id_cita,$id_ejecutor,$id_ejecutor,$id_ejecutor);

       if($filt->execute()){
            // Comprobamos si MySQL realmente modificó alguna fila
            if($filt->affected_rows > 0) {
                return true; // Éxito, se canceló
            } else {
                return false; // Fallo: la cita no existe o el usuario no tiene permiso
            }
        }else{
            return false;
        }
    }

    function horas_libres($fecha, $trabajador_id,$duracion_minutos){
        include('../BBDD/BBDD.php');

        //Devuelve el dia de la semana: Lunes = 1, Martes = 2...Domingo = 7
        $dia_semana = date('N', strtotime($fecha));

        if($dia_semana >= 6){
            return [];
        }
        if($dia_semana == 5){
            $horario = [
                '09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
                '13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30',
                '17:00'
            ];
        }else{
            $horario = [
                '09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
                '13:00','13:30','14:00','15:00','15:30','16:00','16:30','17:00',
                '17:30','18:00','18:30','19:00'
            ];
        }

        $horas_ocupadas = [];

   // 2. Extraer TODAS las horas ocupadas en bloques de 30 minutos (Doctor y Salas)
        $query_ocupadas = "
            SELECT hora_inicio, hora_fin, trabajador_id 
            FROM citas 
            WHERE fecha = ? AND estado != 'cancelada'
        ";
        $filt = $db->prepare($query_ocupadas);
        $filt->bind_param('s', $fecha);
        $filt->execute();
        $res = $filt->get_result();
        
        $conteo_salas = [];

        while($row = $res->fetch_assoc()){
            // Convertimos la hora de inicio y fin de cada cita en bloques (ej: 10:00 a 11:00 son dos bloques: 10:00 y 10:30)
            $inicio = strtotime($row['hora_inicio']);
            $fin = strtotime($row['hora_fin']);
            
            while($inicio < $fin){
                $hora_str = date('H:i', $inicio);
                
                // A. Si es de este doctor, la hora queda totalmente bloqueada
                if($row['trabajador_id'] == $trabajador_id){
                    $horas_ocupadas[] = $hora_str;
                }
                
                // B. Contamos cuántas salas se están usando en este bloque
                if(!isset($conteo_salas[$hora_str])) $conteo_salas[$hora_str] = 0;
                $conteo_salas[$hora_str]++;
                
                if($conteo_salas[$hora_str] >= 2){
                    $horas_ocupadas[] = $hora_str; // Clínica llena
                }
                
                $inicio = strtotime('+30 minutes', $inicio); // Siguiente bloque
            }
        }
        
        $horas_ocupadas = array_unique($horas_ocupadas);

        // 3. LA MAGIA: Buscar huecos CONSECUTIVOS basados en la duración
        $bloques_necesarios = ceil($duracion_minutos / 30); // Si dura 60 min, son 2 bloques
        $horas_validas = [];
        
        foreach($horario as $indice => $hora_evaluada){
            $es_valida = true;
            
            // Comprobamos si esta hora y las siguientes "X" horas también están libres
            for($i = 0; $i < $bloques_necesarios; $i++){
                $indice_futuro = $indice + $i;
                
                // Si el bloque se sale del horario (ej: intenta dar cita de 60 min a las 13:30) 
                // O si ese bloque está en la lista de horas bloqueadas
                if(!isset($horario[$indice_futuro]) || in_array($horario[$indice_futuro], $horas_ocupadas)){
                    $es_valida = false;
                    break;
                }
            }
            
            // Filtro del tiempo pasado (No dar citas hoy antes de la hora actual)
            if($fecha === date('Y-m-d') && $hora_evaluada <= date('H:i')){
                $es_valida = false;
            }
            
            if($es_valida){
                $horas_validas[] = $hora_evaluada;
            }
        }
        
        return array_values($horas_validas);
    }


    function duracion_servicio($id_serv){
        include('../BBDD/BBDD.php');

        $query = "
            SELECT duracion_minutos FROM servicios WHERE id = ?
        ";
        $filt = $db->prepare($query);
        $filt->bind_param('i',$id_serv);
        $filt->execute();

        $res = $filt->get_result();

        if($res->num_rows > 0){
            return $res->fetch_assoc()['duracion_minutos'];
        }else{
            return 30;
        }
    }

    function crear_cita($paciente_id, $trabajador_id, $servicio_id, $fecha, $hora_inicio, $hora_fin,$auxiliar_id ){
        include('../BBDD/BBDD.php');

        //poner el valor del auxiliar a null en caso de que no haya un auxiliar
        $aux_val = ($auxiliar_id === 'ninguno' || empty($auxiliar_id)) ? NULL : $auxiliar_id;

        if($aux_val == $auxiliar_id){     
            $query = "
                INSERT INTO citas (paciente_id,  trabajador_id, servicio_id, fecha, hora_inicio, estado, auxiliar_id, hora_fin)
                VALUES (?,?,?,?,?,'pendiente',?,?)
            ";

            $filt = $db->prepare($query);
            $filt->bind_param('iiissis',$paciente_id,$trabajador_id,$servicio_id,$fecha,$hora_inicio,$aux_val,$hora_fin);

            return $filt->execute();
        }else{
            $query = "
                INSERT INTO citas (paciente_id,  trabajador_id, servicio_id, fecha, hora_inicio, estado, hora_fin)
                VALUES (?,?,?,?,?,'pendiente',?)
            ";

            $filt = $db->prepare($query);
            $filt->bind_param('iiisss',$paciente_id,$trabajador_id,$servicio_id,$fecha,$hora_inicio,$hora_fin);

            return $filt->execute();
        }
    }

}

?>
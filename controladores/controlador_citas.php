<?php
require_once __DIR__ . '/../sec/security.php';
include_once "../clases/clase_citas.php";
require_once("../BBDD//BBDD.php");



$cita = new citas();

$option = filter_input(INPUT_POST, 'opt', FILTER_SANITIZE_NUMBER_INT);

switch($option){

    case 1:
        $id_cita = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_SPECIAL_CHARS);

        if($cita->act_notas($id_cita, $notas)){
            echo 'ok';
        }else{
            echo 'error';
        }

    case 2:
        $id_cita = filter_input(INPUT_POST, 'id_cita', FILTER_SANITIZE_NUMBER_INT);
        if($cita->cancelar_cita($id_cita)){
            echo 'ok';
        }else{
            echo 'error';
        }
        break;

    case '3':
        
        $fecha = filter_input(INPUT_POST,'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
        $trabajador_id = filter_input(INPUT_POST,'trabajador_id', FILTER_SANITIZE_NUMBER_INT);
        $servicio_id = filter_input(INPUT_POST,'servicio',FILTER_SANITIZE_NUMBER_INT);

        $duracion = $cita->duracion_servicio($servicio_id);

        $horas = $cita->horas_libres($fecha,$trabajador_id,$duracion);

        header('Content-Type: application/json');

        if(!empty($horas)){
            echo json_encode([
                'status' => 'ok',
                'datos' => $horas
            ]);
        }else{
            echo json_encode(['status'=>'lleno']);
        }


        break;

    case '4':
        $id_paciente = filter_input(INPUT_POST, 'id_paciente',FILTER_SANITIZE_NUMBER_INT);
        $id_trabajador = filter_input(INPUT_POST, 'trabajador_id',FILTER_SANITIZE_NUMBER_INT);
        $id_auxiliar = filter_input(INPUT_POST, 'auxiliar_id',FILTER_SANITIZE_SPECIAL_CHARS);
        $id_servicio = filter_input(INPUT_POST, 'servicio_id',FILTER_SANITIZE_NUMBER_INT);
        $fecha = filter_input(INPUT_POST,'fecha',FILTER_SANITIZE_SPECIAL_CHARS);
        $hora_inicio = filter_input(INPUT_POST,'hora_inicio',FILTER_SANITIZE_SPECIAL_CHARS);
    
        //Calculo de la hora de fin
        $duracion = $cita->duracion_servicio($id_servicio);

        $inicio_timestamp = strtotime($hora_inicio);
        $fin_timestamp = strtotime('+$duracion minutes', $inicio_timestamp);
        $hora_fin = date('H:i',$fin_timestamp);

        header('Content-Type: application/json');

        $exito = $cita->crear_cita($id_paciente,$id_trabajador,$id_servicio,$fecha,$hora_inicio,$hora_fin,$id_auxiliar);

        if($exito){
            echo json_encode(['status' => 'ok']);
        }else{
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo insertar en la base de datos.']);
        }
        break;

    default:
        break;
}

?>
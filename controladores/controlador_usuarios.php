<?php
require_once __DIR__ . '/../sec/security.php';
include_once "../clases/clase_user.php";
require_once("../BBDD//BBDD.php");



$user = new usuario();
$option = filter_input(INPUT_POST, 'opt', FILTER_SANITIZE_NUMBER_INT);

switch ($option) {
    case '1':
        //Saneamos los datos que nos envian por el formulario
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $surname1 = filter_input(INPUT_POST, 'surname1', FILTER_SANITIZE_SPECIAL_CHARS);
        $surname2 = filter_input(INPUT_POST, 'surname2', FILTER_SANITIZE_SPECIAL_CHARS);
        $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
        $tel_num = filter_input(INPUT_POST, 'tel_num', FILTER_SANITIZE_SPECIAL_CHARS);
        $birth_date = filter_input(INPUT_POST, 'birth_date', FILTER_SANITIZE_SPECIAL_CHARS);

        $user->crear_paciente($email,$name,$surname1,$surname2,$dni,$tel_num,$birth_date);
        
        break;

    case '2':
        //saneamos los datos que llegan del formulario
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);
        $ape1 = filter_input(INPUT_POST, 'apellido1', FILTER_SANITIZE_SPECIAL_CHARS);
        $ape2 = filter_input(INPUT_POST, 'apellido2', FILTER_SANITIZE_SPECIAL_CHARS);
        $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
        $especialidad = filter_input(INPUT_POST, 'especialidad', FILTER_SANITIZE_SPECIAL_CHARS);

        $user->crear_trabajador($nombre,$ape1,$ape2,$dni,$email,$especialidad);

        break;

    case '3':
        $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $paciente = $user->buscar_paciente($dni);

        // Configuramos la cabecera para que el navegador sepa que es un JSON
        header('Content-Type: application/json');

        if($paciente){
            echo json_encode([
                'status'=>'ok',
                'id_paciente'=>$paciente['id'],
                'nombre_completo'=>$paciente['nombre']
            ]);
        }else{
            echo json_encode(['status'=>'error']);
        }
        break;

    case '4':
        $id_cita = filter_input(INPUT_POST, 'id_serv', FILTER_SANITIZE_NUMBER_INT);

        $doctores = $user->obtener_doctores($id_cita);

        header('Content-Type: application/json');

        if(!empty($doctores)){
            echo json_encode([
                'status'=>'ok',
                'data'=> $doctores
            ]);
        }else{
            echo json_encode(['status'=>'error']);
        }
        break;

    case '5':
        $fecha = filter_input(INPUT_POST,'fecha',FILTER_SANITIZE_SPECIAL_CHARS);
        $hora = filter_input(INPUT_POST,'hora',FILTER_SANITIZE_SPECIAL_CHARS);

        $auxiliares = $user->obtener_auxiliares_libres($fecha,$hora);

        header('Content-Type: application/json');
        if(!empty($auxiliares)){
            echo json_encode(['status' => 'ok', 'datos' => $auxiliares]);
        }else{
            echo json_encode(['status' => 'error']);
        }
        break;

    case '6':
        $id_usuario = $_SESSION['id']; // Obtenemos quién es desde la sesión, muy seguro
        
        $pass_antigua = filter_input(INPUT_POST, 'pass_antigua', FILTER_SANITIZE_SPECIAL_CHARS);
        $pass_nueva = filter_input(INPUT_POST, 'pass_nueva', FILTER_SANITIZE_SPECIAL_CHARS);
        
        header('Content-Type: application/json');
        
        if($id_usuario && $pass_antigua && $pass_nueva) {
            $resultado = $user->change_pass($id_usuario, $pass_nueva, $pass_antigua);
            
            if($resultado === true) {
                echo json_encode(['status' => 'ok']);
            } else if ($resultado === "incorrecta") {
                echo json_encode(['status' => 'error', 'mensaje' => 'La contraseña actual es incorrecta.']);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Error al actualizar en la base de datos.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'Faltan datos por completar.']);
        }
        break;
}
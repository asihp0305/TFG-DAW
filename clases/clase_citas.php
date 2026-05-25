<?php

class citas{

    function act_notas($id_cita, $nueva_nota){
        require_once('../BBDD/BBDD.php');

        $filt = $db->prepare('UPDATE citas SET notas = ? WHERE id = ?');
        $filt->bind_param('si',$nueva_nota, $id_cita);
       
        if($filt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function cancelar_cita($id_cita){
        require_once('../BBDD/BBDD.php');
        
        $id_ejecutor = $_SESSION['id'];

        $filt = $db->prepare('UPDATE citas SET estado = "cancelada" WHERE id = ? AND (paciente_id = ? OR trabajador_id = ?)');
        $filt->bind_param('iii',$id_cita,$id_ejecutor,$id_ejecutor);

        if($filt->execute()){
            return true;
        }else{
            return false;
        }
    }

}

?>
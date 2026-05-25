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

        $filt = $db->prepare('UPDATE citas SET estado = "cancelada" WHERE id = ? AND (paciente_id = ? OR trabajador_id = ?)');
        $filt->bind_param('iii',$id_cita,$id_ejecutor,$id_ejecutor);

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

}

?>
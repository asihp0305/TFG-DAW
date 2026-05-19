<?php

class citas{

    function act_notas($id_cita, $nueva_nota){
        require_once('../BBDD/BBDD.php');

        $filt = $db->prepare('UPDATE citas SET notas = ? WHERE id = ?');
        $filt->bind_param('si',$nueva_nota, $id_cita);
        $filt->execute();
    }

}

?>
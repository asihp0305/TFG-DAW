<?php


class usuario{

    function crear_usuario($email, $rol, $creator_id, $name, $surname1, $surname2){
        include('../BBDD/BBDD.php');
        require_once('../sec/enviar_correo.php');

        //Metodo para generar el nombre de usuario

        // limpiamos
        $buscar = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ'];
        $reemplazar = ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n'];

        $clean_name = str_replace($buscar, $reemplazar, $name);
        $clean_surname1 = str_replace($buscar, $reemplazar, $surname1);
        $clean_surname2 = str_replace($buscar, $reemplazar, $surname2);

        // Pasamos a minusculas el primer apellido
        $clean_surname1 = strtolower($clean_surname1);

        //creamos la base para el usuario
        $base_usuario = substr($clean_name, 0, 1) . $clean_surname1 . substr($clean_surname2, 0, 1);
        $usuarioFinal = $base_usuario;

        $existe = true;
        $cont = 1;
        // 3. Bucle para comprobar si ya existe en la base de datos
        while ($existe) {
            // Usamos sentencias preparadas por seguridad (tu columna se llama 'usr')
            $filt = $db->prepare("SELECT id FROM usuarios WHERE usr = ?");
            $filt->bind_param("s", $usuarioFinal);
            $filt->execute();
            $res = $filt->get_result(); // Guardamos el resultado temporalmente

            if ($res->num_rows > 0) {
                // Si el usuario ya existe, le sumamos un número (ggalan2, ggalan3...)
                $cont++;
                $usuarioFinal = $base_usuario . $cont;
            } else {
                // Si no existe (num_rows es 0), salimos del bucle
                $existe = false;
            }
            $filt->close();
        }


        //Autogeneracion de contraseña
        // Definimos los caracteres permitidos (letras y números)
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // Desordenamos la cadena y cortamos la longitud deseada
        $pass = substr(str_shuffle($caracteres), 0, 8);
        // hasheo de la contrasena para mas seguridad
        $has = password_hash($pass, PASSWORD_DEFAULT);

        $filt = $db->prepare('INSERT INTO usuarios (usr, password, email, rol, creator_id) values(?, ?, ?, ?, ?)');
        $filt->bind_param('ssssi', $usuarioFinal, $has, $email, $rol, $creator_id);
        $filt->execute();
        $user_id = $filt->insert_id;
        $filt->close();

        //enviado del emnail con las credenciales al usuario
        $estadoCorreo = enviarCredencialesPaciente($email, $name, $usuarioFinal, $pass);
        
        if ($estadoCorreo === true) {
            return $user_id; 
        } else {
            // El paciente se guardó, pero el correo falló (lo ideal para debugear)
            return "Paciente creado, pero falló el email: " . $estadoCorreo;
        }
    }

    function crear_paciente($email, $name, $surname1, $surname2, $dni, $tel_num, $birth_date){
        include('../BBDD/BBDD.php');

        $nombre_completo = $name .' '. $surname1 .' '. $surname2; 
        $rol = 'paciente';

        //Insercion del usuario
        $usr_id = $this->crear_usuario($email,$rol,$_SESSION['id'], $name, $surname1, $surname2);

        //Insercion del paciente
        $filt = $db->prepare("INSERT INTO pacientes (usuario_id, nombre, dni, telefono, fecha_nacimiento) VALUES (?, ?, ?, ?, ?)");
        $filt->bind_param("issss", $usr_id, $nombre_completo, $dni, $tel_num, $birth_date);
        $filt->execute();
        $filt->close();
        
    }


    function crear_trabajador($nombre, $ape1, $ape2,$dni, $mail, $especialidad){
        include('../BBDD/BBDD.php');

        $nombre_completo = $nombre .' '. $ape1 .' '. $ape2;
        $rol = 'trabajador';

        //Insercion del usuario
        $usr_id = $this->crear_usuario($mail,$rol,$_SESSION['id'],$nombre, $ape1,$ape2);

        //Insercion del trabajador
        $filt = $db->prepare('INSERT INTO trabajadores (usuario_id, nombre, dni, especialidad) VALUES (?,?,?,?)');
        $filt->bind_param('isss',$usr_id,$nombre_completo,$dni,$especialidad);
        $filt->execute();
        $filt->close();

    }

    function buscar_paciente($dni){
        include('../BBDD/BBDD.php');

        $filt = $db->prepare(' SELECT id, nombre FROM pacientes where dni = ?');
        $filt->bind_param('s',$dni);
        $filt->execute();

        $res = $filt->get_result();
        
        if($res->num_rows > 0){
            return $res->fetch_assoc();
        }else{
            return false;
        }
    }

    function obtener_doctores($servicio_id){
        include('../BBDD/BBDD.php');

        if($servicio_id == 1){
            $query = 'SELECT id,nombre,especialidad FROM trabajadores WHERE especialidad = "auxiliar" ORDER BY nombre ASC';
            $res = $db->query($query);
            $doctores = [];
            
            if($res && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $doctores[] = $row;
                }
            }
        }else if($servicio_id == 4){
            $query = 'SELECT id,nombre,especialidad FROM trabajadores WHERE especialidad = "pediatra" ORDER BY nombre ASC';
            $res = $db->query($query);
            $doctores = [];
            
            if($res && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $doctores[] = $row;
                }
            }
        }else{     
            $query = 'SELECT id,nombre,especialidad FROM trabajadores WHERE especialidad = "cirujano" ORDER BY nombre ASC';
            $res = $db->query($query);
            $doctores = [];
            
            if($res && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $doctores[] = $row;
                }
            }
        }

        return $doctores;
    }

    function obtener_auxiliares_libres($fecha, $hora){
        include('../BBDD/BBDD.php');

        $query = "
            SELECT id, nombre
            FROM trabajadores 
            WHERE especialidad = 'auxiliar' 
            AND id NOT IN (
                SELECT auxiliar_id 
                FROM citas 
                WHERE fecha = ? AND hora_inicio = ? AND estado != 'cancelada'
                AND auxiliar_id IS NOT NULL
            )
        ";
        // Formateamos la hora para asegurarnos de que encaja con el formato de la BD
        $hora_formateada = $hora. ':00';

        $filt = $db->prepare($query);
        $filt->bind_param('ss',$fecha,$hora_formateada);
        $filt->execute();

        $res = $filt->get_result();

        $auxiliares = [];
        while($row = $res->fetch_assoc()){
            $auxiliares[] = $row;
        }

        return $auxiliares;

    }


    function change_pass($id_user,$pass_nueva,$pass_antigua){
        include('../BBDD/BBDD.php');

        $query_busqueda = 'SELECT password from usuarios where id = ? ';

        $filt = $db->prepare($query_busqueda);
        $filt->bind_param('i',$id_user);
        $filt->execute();

        $res = $filt->get_result();
        $vec = $res->fetch_assoc();

        if(password_verify($pass_antigua, $vec['password'])){
            $has = password_hash($pass_nueva, PASSWORD_DEFAULT);

            $query_act = 'UPDATE usuarios SET password = ? where id = ?';
            $filt = $db->prepare($query_act);
            $filt->bind_param('si',$has,$id_user);

            return $filt->execute();
        }else{
            return 'incorrecta';
        } 
    }
}
?>
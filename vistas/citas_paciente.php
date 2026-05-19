<?php
include('../sec/security.php')
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        $query = <<<SQL
                SELECT 
                    ci.id, 
                    ci.fecha, 
                    ci.hora_inicio, 
                    tr.nombre AS nombre_trabajador, 
                    tr.apellidos AS apellidos_trabajador, 
                    serv.nombre AS nombre_servicio, 
                    ci.notas
                FROM citas ci
                INNER JOIN trabajadores tr 
                    ON ci.trabajador_id = tr.id
                INNER JOIN servicios serv 
                    ON ci.servicio_id = serv.id
                WHERE paciente_id = ?
                SQL;

        $filt = $db->prepare($query);
        $filt->bind_param('i',$_POST['id']);
        $filt->execute();
        $res = $filt->get_result();

        for($i = 0; $i < $res->num_rows; $i++){
            $vec = $res->fetch_assoc();

            // fecha de la cita juntando los datos de la base de datos
            $str_fecha_cita = $vec['fecha'].' '.$vec['hora_inicio'];

            $fecha_cita = new DateTime($str_fecha_cita);
            $fecha_actual = new DateTime();// fecha exacta del momento que se le da a consultar cita

            $limite_cancelacion = clone $fecha_actual;
            $limite_cancelacion->modify('+24 hours');
    ?>

    <div class="cita">
        <div class="fecha">
            <h3><?php echo $vec['fecha'].' '. $vec['hora_inicio'] ?></h3>
        </div>
        <div class="servicio">
            <p> <?php echo $vec['nombre_servicio'] ?> </p>
        </div>
        <div class="profesional">
            <p> <?php echo $vec['nombre_trabajador'].' '. $vec['apellidos_trabajador'] ?> </p>
        </div>
        <div class="acciones">
            <?php
                if($vec['notas'] != null){
            ?>
            <button class="btn_notas">VER NOTAS</button>
            <?php
                }
                if($fecha_actual > $limite_cancelacion){
            ?>
            <?php //darle una vuelta para ver si hacerlo con js para que al darle se muestre diferente con lo de notas ?>
            <button class="btn_cancelar" idCita = '<?php echo $vec['id']?>'>CANCELAR</button>
            <?php }?>
        </div>
    </div>

    <?php }?>
</body>
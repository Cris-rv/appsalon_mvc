<?php
    foreach($alertas as $key => $mensajes) : // $key = error, $mensajes = mensajes de error, primero iteramos para acceder al tipo de alerta ya sea error o exito y luego iteramos sobre los mensajes para mostrarlos en pantalla
        foreach($mensajes as $mensaje) :
        ?>
            <div class="alertas <?php echo $key; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php
        endforeach;
    endforeach;
?>
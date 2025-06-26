<h1 class="nombre-pagina">Panel de Administracion</h1>

<?php include __DIR__ . '/../templates/barra.php'; ?>

<h2>Buscar Citas</h2>

<div class="busqueda">
    <form action="" class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input 
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo $fecha ?>"
            />
        </div>
    </form>
</div>

<?php
    if(count($citas) === 0) {
    ?> <div class="alertas error"> No hay citas para hoy </div>  <?php
    }
?>

<div class="citas-admin">
    <ul class="citas">
        <?php
            $idCita = 0; 
            foreach($citas as $key => $cita) { 
            if($idCita !== $cita->id) {
            $total = 0;  // La variable $total se crea aqui porque el valor debe crearse una sola vez      
        ?>

        <li>
            <p>ID: <span><?php echo $cita->id; ?></span></p>
            <p>Hora: <span><?php echo $cita->hora; ?></span></p>
            <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>      
            <p>Email: <span><?php echo $cita->email; ?></span></p>      
            <p>Telefono: <span><?php echo $cita->telefono; ?></span></p> 
            <h3>Servicios</h3>
            <?php $idCita = $cita->id; }  // Fin del if
            $total += $cita->precio; // y solo se suma despues del if para que sume cuantos servicios hayan, pero no reinice su valor segun el if
            ?> 
            <p class="servicio">
                <?php echo $cita->servicio ?>
            </p>
            <?php 
                $actual = $cita->id; // Nos retorna el id que del arreglo actual
                $proximo = $citas[$key + 1]->id ?? 0; // al evaluar $key + 1 recorre el arreglo y nos retorna el id en cada uno hasta que llegue a un id diferente y es ahi cuando sabemos si es la ultima iteracion del if

                if(esUltimo($actual, $proximo)) {
                ?><p class="total">Total: <span>$<?php echo $total;?></span></p>
                
                    <form action="/api/eliminar" method="POST">
                        <input type="hidden" name="id" value="<?php echo $cita->id ?>">
                        <input type="submit" class="boton-eliminar" value="Eliminar">
                    </form>
                
                <?php } } //Fin de foreach ?>
    </ul>
</div>

<?php 
$script = "<script src='build/js/buscador.js'></script>"; 
?>

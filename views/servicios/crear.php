<h1 class="nombre-pagina">Nuevo Servicio</h1>
<p class="descripcion-pagina">LLena todos los campos para crear un nuevo servicio</p> 

<?php include_once __DIR__ . '/../templates/barra.php'?>

<?php include __DIR__ . '/../templates/alertas.php'?>

<form class="formulario" action="/servicios/crear" method="POST">

    <?php include __DIR__ . '/formulario.php' ?>

<input type="submit" class="boton" value="Crear Servicio">
</form>
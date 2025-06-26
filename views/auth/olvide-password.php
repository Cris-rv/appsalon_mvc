<h1 class="nombre-pagina">Olvidaste tu Password</h1>
<p class="descripcion-pagina">Si olvidaste tu password ingresa tu correo y enviaremos las instrucciones por E-mail</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" method="POST" action="/olvide">
    <div class="campo">
        <label for="email">Email</label>
        <input 
         type="email"
         name="email"
         id="email"
         placeholder="Tu E-mail"
        />
    </div>

    <input type="submit" class="boton" value="Enviar Instrucciones">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesion</a>
    <a href="/crear-cuenta">¿No tienes una cuenta? Crea una</a>
</div>
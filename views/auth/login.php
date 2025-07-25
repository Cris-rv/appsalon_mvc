<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesion con tus datos</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input 
         type="email"
         name="email"
         id="email"
         placeholder="Tu E-mail"
         value="<?php echo s($auth->email); ?>"
        />
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input 
         type="password"
         name="password"
         id="password"
         placeholder="Tu Password"
        />
    </div>

    <input type="submit" class="boton" value="Iniciar Sesion">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una</a>
    <a href="/olvide">¿Has olvidado tu password?</a>
</div>
<div class="contenedor login">

<?php include_once __DIR__.'/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">

        <p class="descripcion-pagina">Iniciar Sesión</p>

        <form action="/" class="formulario" method="POST">

            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    placeholder="Tu Email"
                    name="email"
                />
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Password"
                    name="password"
                />
            </div>

            <input type="submit" class="boton" value="Iniciar Sesión">

            <div class="acciones">
                <a href="/crear">¿Aún no tienes cuenta? Crear una</a>
                <a href="/olvide">¿Olvidaste tu Password?</a>
            </div>

        </form>
    </div> <!--.contenedor-sm-->
</div>
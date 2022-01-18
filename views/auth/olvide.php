<div class="contenedor olvide">

<?php include_once __DIR__.'/../templates/nombre-sitio.php' ?>

<div class="contenedor-sm">
    
    <p class="descripcion-pagina">Escribe tu Email y Reestablece tu password</p>
    
    <?php include_once __DIR__.'/../templates/alertas.php' ?>

        <form action="/olvide" class="formulario" method="POST" novalidate>

            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    placeholder="Tu Email"
                    name="email"
                />
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">

            <div class="acciones">
                <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
                <a href="/crear">¿Aún no tienes cuenta? Crear una</a>
            </div>

        </form>
    </div> <!--.contenedor-sm-->
</div>
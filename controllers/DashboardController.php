<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index (Router $router) {

        if(!isset($_SESSION)) {
            session_start();
        }

        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);


        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto (Router $router) {
        
        if(!isset($_SESSION)) {
            session_start();
        }
        isAuth();
        $alertas = [];

        if ( $_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);
            
            //Validación
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {

                //Generar una URL Única
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el Proyecto
                $proyecto->guardar();

                //Redireccionar
                header('Location: /proyecto?id='. $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' =>  $alertas
        ]);
    }

    public static function proyecto (Router $router) {
        
        if(!isset($_SESSION)) {
            session_start();
        }

        $token = $_GET['id'];
        if (!$token) {
            header('Location: /dashboard');
        }
        //Revisar que el usuario que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if ( $proyecto->propietarioId !==  $_SESSION['id'] ) header('Location: /dashboard');


        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function perfil (Router $router) {

        if(!isset($_SESSION)) {
            session_start();
        }

        isAuth();

        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPerfil();

            if (empty($alertas)) {

                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    //Mensaje de Error
                    Usuario::setAlerta('error', 'Email no Válido');
                    $alertas = $usuario->getAlertas();  
                } else {
                    //Guardar el Registro
                    $usuario->guardar();

                    //Asignar nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                    }
                    
            }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function cambiar_password(Router $router) {

        if(!isset($_SESSION)) {
            session_start();
        }
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

             if(empty($alertas)) {
                 $resultado = $usuario->comprobar_password();

                 if($resultado) {

                     $usuario->password = $usuario->password_nuevo;
                     
                    //Eliminar propiedades No necesarias
                    unset($usuario->password_actual);
                    unset($usuario->passowrd_nuevo);

                    //Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }

                     //Asignar el nuevo Password


                 } else {
                     Usuario::setAlerta('error', 'Password Incorrecto');
                     $alertas = $usuario->getAlertas();
                 }
             }
        }
        
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }

}
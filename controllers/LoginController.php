<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión'
        ]);
    }
    
    public static function logout() {
        echo "Desde Logout";
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }

        public static function crear(Router $router) {
            
            $usuario = new Usuario;
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST') {

                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNuevaCuenta();
               
                if (empty($alertas)) {
                    $existeUsuario = Usuario::where('email', $usuario->email);
                    //Comprobar que el Usuario no esta ya registrado
                    if ($existeUsuario) {
                        Usuario::setAlerta('error', 'El Usuario ya está registrado');
                        $alertas = Usuario::getAlertas();
                       
                    } else {
                        //Hashear el password
                        $usuario->hashPassword();

                        //Eliminar password2
                        unset($usuario->password2);

                        //Generar un token
                        $usuario->crearToken();

                        //Crear nuevo usuario
                        $resultado = $usuario->guardar();

                        //Enviar email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();

                        if ($resultado) {
                            header('Location: /mensaje');
                        }
                    }
                }
                
            }
            $router->render('auth/crear', [
                'titulo' => 'Crear Cuenta en UpTask',
                'usuario' => $usuario,
                'alertas' => $alertas
            ]);
        }

        public static function olvide(Router $router) {
            
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuario = new Usuario($_POST);
                 $alertas = $usuario->validarEmail();

                 if (empty($alertas)){
                     //Buscar el usuario
                     $usuario = Usuario::where('email', $usuario->email);
                     
                     if ( $usuario && $usuario->confirmado ) {

                         //Generar un nuevo token
                         unset($usuario->password2);
                         $usuario->crearToken();

                         //Actualizar al usuario
                         $usuario->guardar();

                         //Enviar email
                         $email = new Email( $usuario->email, $usuario->nombre, $usuario->token );
                         $email->enviarReestablecer();

                         //Imprimir alerta
                         Usuario::setAlerta('exito', 'Hemos Enviado las instrucciones');
                         Usuario::setAlerta('exito', 'Revisa tu email');

                     } else {
                         Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                     }
                 }
                
            }
            $alertas = Usuario::getAlertas();
            $router->render('auth/olvide',[
                'titulo' => 'Olvidé mi Password',
                'alertas' => $alertas
            ]);
        }

        public static function reestablecer(Router $router) {
            
            $token = s($_GET['token']);
            $alertas = [];
            $mostrar = true;

            if (!$token) header('Location: /');

            //Identificar al usuario con este token
            $usuario = Usuario::where('token' , $token);
            if (empty($usuario)) {
                Usuario::setAlerta('error', 'Token no Válido');
                $mostrar = false;
            }     
    
            if($_SERVER['REQUEST_METHOD'] === 'POST') {

                //Añadir nuevo password
                $usuario->sincronizar($_POST);

                //Validar el nuevo password
                $alertas = $usuario->validarPassword();

                if(empty($alertas)) {
                    //Hashear el nuevo password
                    $usuario->hashPassword();

                    //Eliminar el token
                    $usuario->token = '';

                    //Guardar el usuario en la BD
                    $resultado = $usuario->guardar();

                    //Redireccionar
                    if ($resultado) {
                        header('Location: /');
                    }
                }

            }
            $alertas = Usuario::getAlertas();
            $router->render('auth/reestablecer', [
                'titulo' => 'Reestablecer Password',
                'alertas' => $alertas,
                'mostrar' => $mostrar
            ]);
        }

        public static function mensaje(Router $router) {
        
            $router->render('auth/mensaje', [
                'titulo' => 'Cuenta Creada Exitosamente'
            ]);
        }
        
        public static function confirmar(Router $router) {

            $token = s($_GET['token']);
            $alertas = [];

            if(!$token) header('Location: /');

            //Encontrar al usuario con este token
            $usuario = Usuario::where('token', $token);
            
            if (empty($usuario)) {
                //No se encontró usuario con ese token
                Usuario::setAlerta('error', 'Token No Válido');
            } else {
                //Confirmar la cuenta
                $usuario->confirmado = 1;
                unset($usuario->password2);
                $usuario->token = '';
                
                //Guardar en la BD
                $usuario->guardar();
                Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
            }
            
            $alertas = Usuario::getAlertas();
            $router->render('auth/confirmar', [
                'titulo' => 'Confirma tu cuenta UpTask',
                'alertas' => $alertas
            ]);
        }
        

}
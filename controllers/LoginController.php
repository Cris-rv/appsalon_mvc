<?php 

namespace Controllers;

use Classes\Email;
use MVC\Router;
use Model\Usuario;

class LoginController {

    public static function login(Router $router) {

        $auth = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {
                    // Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {

                            // Autenticar al usuario
                            if(!isset($_SESSION)) {
                                session_start();
                            };

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            // Redireccionamiento
                            if($usuario->admin === "1") {
                                $_SESSION['admin'] = $usuario->admin ?? null;
                                header('Location: /admin');
                            } else {
                                header('Location: /cita');
                            }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            } 
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/login', [
            'auth' => $auth,
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        if(!isset($_SESSION)) {
            session_start();
        };

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
            $usuario = Usuario::where('email', $auth->email);

                // Si hay coincidencias en la base dadtos y si el usuario esta confirmado cambiamos contraseña
                if($usuario && $usuario->confirmado === "1") {
                    
                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();
                    
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        // Si el token no es valido no encuentra al usuario
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        } 

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                
                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }
            }
        }

        // debuguear($usuario);

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {

        $usuario = new Usuario;

        // Alertas Vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Agregamos los datos recibidos del usuario y los mapeamos en las llaves de nuestro __construct en usuarios para no perder su referencia por errores o al actualizar la pagina
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas este vacio
            if(empty($alertas)) {
                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // hashear el password
                    $usuario->hashPassword();

                    // Generar un token unico
                    $usuario->crearToken();
                    // Crear el usuario - Primero guardamos para no perder accidentalmente el token al redireccionar al usuario
                    $resultado = $usuario->guardar();

                    // Enviar el email                    
                    if($resultado) {
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();
                        header('Location: /mensaje');
                    }
                    
                }
                
            }

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function confirmar(Router $router) {

        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'El token no es valido');            
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        //Mostrar las alertas
        $alertas = Usuario::getAlertas();
        
        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [

        ]);
    }
}
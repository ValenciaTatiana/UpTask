<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                // Validar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                } else {
                    /** El Usuario Existe */
                    // Validar password
                    if(password_verify($_POST['password'], $usuario->password)) {
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('Location: /proyectos');
                        
                    } else {
                        Usuario::setAlerta('error', 'El password es incorrecto');
                    }
                }
                //debuguear($usuario);
            }
        }
        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            // Para crear titulos dinamicos
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(Router $router) {

       
    }

    public static function crear(Router $router) {

        $alertas = [];
        // Instanciar el Usuario
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Crear el Token
                    $usuario->generarToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header('Location: /mensaje');
                    }

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email -> enviarConfirmacion();
                }
            }
        }

        $router->render('auth/crear', [
            // Para crear titulos dinamicos
            'titulo' => 'Crea Tu Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            if(empty($alertas)) {
                //Buscar email usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {
                    /** Usuario encontrado */
                    // Generar nuevo token
                    $usuario->generarToken();
                    unset($usuario->password2);
    
                    // Actualizar el usuario
                    $usuario->guardar();
    
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
    
                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu Email');
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            // Para crear titulos dinamicos
            'titulo' => 'Olvide Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        
        $token = s($_GET['token']);
        $mostrar = true;
        $alertas = [];

        if(!$token) header('Location: /');

        // Encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Añadir el nuevo password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear password
                $usuario->hashPassword();
                $usuario->token = "";

                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            // Para crear titulos dinamicos
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [
            // Para crear titulos dinamicos
            'titulo' => 'Mensaje Reestablecer Password'
        ]);
    }

    public static function confirmar(Router $router) {
        // Obtener el token
        $token = s($_GET['token']);
        $alertas = [];

        if(!$token) header('Location: /');

        // Encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = "";
            // Eliminar password2
            unset($usuario->password2);

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            // Para crear titulos dinamicos
            'titulo' => 'Confirmar nuevo Password',
            'alertas' => $alertas
        ]);

    }
}
<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }

        // Render a la vista
        $router->render('auth/login', [
            // Para crear titulos dinamicos
            'titulo' => 'Iniciar Sesión'
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
        }

        $router->render('auth/olvide', [
            // Para crear titulos dinamicos
            'titulo' => 'Olvide Password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {


        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        $router->render('auth/reestablecer', [
            // Para crear titulos dinamicos
            'titulo' => 'Reestablecer Password'
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
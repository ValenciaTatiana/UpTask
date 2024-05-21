<?php

namespace Controllers;

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



        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        $router->render('auth/crear', [
            // Para crear titulos dinamicos
            'titulo' => 'Crea Tu Cuenta'
        ]);
    }

    public static function olvide(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        $router->render('auth/olvide', [
            // Para crear titulos dinamicos
            'titulo' => 'Olvide Password'
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

        $router->render('auth/confirmar', [
            // Para crear titulos dinamicos
            'titulo' => 'Confirmar nuevo Password'
        ]);

    }
}
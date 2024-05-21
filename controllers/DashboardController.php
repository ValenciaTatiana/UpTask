<?php

namespace Controllers;

class DashboardController {
    public static function index () {
        echo 'Desde el index';
    }

    public static function crear_proyecto () {
        echo 'Desde el crear_proyecto';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }

    public static function proyecto () {
        echo 'Desde el proyecto';

    }

    public static function perfil () {
        echo 'Desde el perfil';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }

    public static function cambiar_password () {
        echo 'Desde el cambiar-password';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }
}
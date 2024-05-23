<?php

namespace Controllers;

use Model\Proyecto;
use MVC\Router;

class DashboardController {
    public static function index (Router $router) {

        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            // Para crear titulos dinamicos
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto (Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            // Validación de campo 
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // Generar URL única
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el Proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }


        }

        $router->render('dashboard/crear-proyecto', [
            // Para crear titulos dinamicos
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);

        
    }

    public static function proyecto (Router $router) {
        session_start();
        isAuth();

        // Revisar que la persona que visita el proyecto, sea el propietario
        $token = $_GET['id'];

        if(!$token) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            // Para crear titulos dinamicos
            'titulo' => $proyecto->proyecto
        ]);

    }

    public static function perfil (Router $router) {
        session_start();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }

        $router->render('dashboard/perfil', [
            // Para crear titulos dinamicos
            'titulo' => 'Perfil'
            //'alertas' => $alertas
        ]);

    }

    public static function cambiar_password (Router $router) {
        echo 'Desde el cambiar-password';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }
}
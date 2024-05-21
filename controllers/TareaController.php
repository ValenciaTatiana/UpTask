<?php

namespace Controllers;

class TareaController {
    public static function index() {
        echo 'Desde index';
    }

    public static function crear() {
        echo 'Desde crear';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }

    public static function actualizar() {
        echo 'Desde actualizar';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }

    public static function eliminar() {
        echo 'Desde eliminar';

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        }
    }
}


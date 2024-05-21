<?php 

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';

// Conectarnos a la base de datos
// Use me importa la Clase ActiveRecord con el namespace Model
use Model\ActiveRecord;
ActiveRecord::setDB($db); // Llamar metodo statico de la clase ActiveRecord
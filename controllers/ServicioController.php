<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {

    public static function index(Router $router) {
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        $servicios = Servicio::all();

        $router->render('servicios/index', [
            'admin' => $_SESSION['admin'],
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router) {
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        $servicio = new Servicio;

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $servicio->sincronizar($_POST);
            
            // Asignamos a la variable de alertas para mostrar mensaje de error, de lo contrario no mostraria nada ya que alertas esta vacio
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/crear', [
            'admin' => $_SESSION['admin'],
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        if(!isset($_SESSION)) {
            session_start();
        };

        isAdmin();

        if(!is_numeric($_GET['id'])) return;

        $servicio = Servicio::find($_GET['id']);
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();

                header('Location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'admin' => $_SESSION['admin'],
            'nombre' => $_SESSION['nombre'],
            'alertas' => $alertas,
            'servicio' => $servicio
        ]);
    }

    public static function eliminar() {
        if(!isset($_SESSION)) {
            session_start();
        };
        
        isAdmin();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $servicio = Servicio::find($id);

            $servicio->eliminar();
            header('Location: /servicios');
        }

    }
}
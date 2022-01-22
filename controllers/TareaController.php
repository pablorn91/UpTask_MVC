<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {
    public static function index(){
        $proyectoId = $_GET['id'];

        if (!$proyectoId) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $proyectoId);

        if (!isset($_SESSION)) {
          session_start();
        }

        if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ) header('Location: /404');

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

       echo json_encode(['tareas' => $tareas]);
    }

    public static function crear(){
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!isset($_SESSION)){
                session_start();
            }

            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url', $proyectoId);

            if (!$proyecto || $proyecto->propietarioId !== $_SESSION['id'] ) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al agregar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 
            //Todo Bien, instancias y crear tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo' => 'exito',
                'mensaje' => 'Tarea Creada Exitosamente',
                'id' => $resultado['id'],
                'proyectoId' =>$proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }
    public static function actualizar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }
    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
    }
}
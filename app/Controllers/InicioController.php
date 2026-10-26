<?php

namespace App\Controllers;

use App\Models\Noticia;
use App\Models\AppItem;

class InicioController 
{
    public function index()
    {
        $noticias = [];
        $apps = [];

        if (class_exists('App\Models\Noticia')) {
            $noticias = Noticia::obtenerTodas();
        }

        if (class_exists('App\Models\AppItem')) {
            $apps = AppItem::obtenerTodos();
        }

        $baseDir = dirname(__DIR__, 2);
        
        // 1. Escanear directamente todas las combinaciones posibles
        $rutasParaProbar = [
            $baseDir . '/app/views/inicio/index.php',
            $baseDir . '/app/views/inicio.php',
            $baseDir . '/app/views/home/index.php',
            $baseDir . '/app/views/home.php',
            $baseDir . '/views/inicio/index.php',
            $baseDir . '/views/inicio.php',
            $baseDir . '/views/home/index.php',
            $baseDir . '/views/home.php',
            $baseDir . '/views/index.php',
            $baseDir . '/app/views/index.php'
        ];

        foreach ($rutasParaProbar as $ruta) {
            // Convertir barras a formato de Windows/Linux
            $rutaNormalizada = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $ruta);
            if (file_exists($rutaNormalizada)) {
                require_once $rutaNormalizada;
                return;
            }
        }

        // 2. Si no encuentra ninguna, mostrar qué carpetas y archivos existen en app/views o views
        echo "<h2>Diagnóstico de carpetas de vistas</h2>";
        $carpetasAExaminar = [
            'app/views' => $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views',
            'views' => $baseDir . DIRECTORY_SEPARATOR . 'views'
        ];

        foreach ($carpetasAExaminar as $nombre => $dir) {
            echo "<h3>Carpeta: <code>$nombre</code></h3>";
            if (is_dir($dir)) {
                $archivos = scandir($dir);
                echo "<ul>";
                foreach ($archivos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..') {
                        echo "<li><strong>$archivo</strong>" . (is_dir($dir . DIRECTORY_SEPARATOR . $archivo) ? " (Carpeta)" : "") . "</li>";
                    }
                }
                echo "</ul>";
            } else {
                echo "<p style='color:red;'>La carpeta no existe.</p>";
            }
        }
    }
}
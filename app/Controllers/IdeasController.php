<?php

namespace App\Controllers;

use App\Models\Idea;

class IdeasController
{
    public function index()
    {
        $ideasPublicas = [];
        
        try {
            $ideasPublicas = Idea::obtenerTodas();
        } catch (\Throwable $e) {
            // Si ocurre error en BD se captura para evitar pantalla en blanco
            error_log($e->getMessage());
        }

        $basePath = dirname(__DIR__, 2);
        $vista = $basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'ideas' . DIRECTORY_SEPARATOR . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            die("Error: No se encuentra la vista en la ruta: " . $vista);
        }
    }

        public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo      = trim($_POST['title'] ?? '');
            $descripcion = trim($_POST['description'] ?? '');
            $categoria   = trim($_POST['category'] ?? 'General');
            $userId      = $_SESSION['user_id'] ?? 1;
            $adjuntoNombre = null;

            // Procesar la subida del archivo
            if (isset($_FILES['adjunto']) && $_FILES['adjunto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/ideas/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = pathinfo($_FILES['adjunto']['name'], PATHINFO_EXTENSION);
                $adjuntoNombre = time() . '_' . uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . $adjuntoNombre;

                move_uploaded_file($_FILES['adjunto']['tmp_name'], $uploadFile);
            }

            if (!empty($titulo) && !empty($descripcion)) {
                try {
                    Idea::crear($userId, $titulo, $descripcion, $categoria, $adjuntoNombre);
                    header("Location: /yaconecta/public/ideas?msg=submitted");
                    exit;
                } catch (\PDOException $e) {
                    die("Error al guardar en la base de datos: " . $e->getMessage());
                }
            }

            header("Location: /yaconecta/public/ideas");
            exit;
        }
    }
}
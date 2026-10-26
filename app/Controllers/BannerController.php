<?php

namespace App\Controllers;

use App\Models\Banner;

class BannerController
{
    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario']) || !in_array((int)($_SESSION['usuario']['role_id'] ?? 0), [1, 2], true)) {
            http_response_code(403);
            exit('Acceso restringido. Inicia sesión con una cuenta administradora.');
        }
    }

    public function admin()
    {
        $this->requireAdmin();
        $banners = [];
        if (class_exists('App\Models\Banner')) {
            $banners = Banner::obtenerTodos();
        }

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'banners' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function store()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('App\Models\Banner')) {
            try {
                $urlMedia = $_POST['url_media'] ?? '';

                // Procesamiento de subida de archivo si se adjunta
                if (isset($_FILES['archivo_media']) && $_FILES['archivo_media']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['archivo_media']['name'], PATHINFO_EXTENSION));
                    $nuevoNombre = 'banner_' . time() . '.' . $ext;
                    $dirTarget = dirname(__DIR__, 2) . '/public/uploads/banners/';

                    if (!file_exists($dirTarget)) {
                        mkdir($dirTarget, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['archivo_media']['tmp_name'], $dirTarget . $nuevoNombre)) {
                        $urlMedia = '/yaconecta/public/uploads/banners/' . $nuevoNombre;
                    }
                }

                $data = [
                    'titulo'       => $_POST['titulo'] ?? '',
                    'subtitulo'    => $_POST['subtitulo'] ?? '',
                    'etiqueta'     => $_POST['etiqueta'] ?? 'CULTURA YAJUEGO',
                    'tipo_media'   => $_POST['tipo_media'] ?? 'imagen',
                    'url_media'    => $urlMedia,
                    'enlace_boton' => $_POST['enlace_boton'] ?? '',
                    'texto_boton'  => $_POST['texto_boton'] ?? 'Ver más',
                    'orden'        => $_POST['orden'] ?? 1
                ];

                if (!empty($data['titulo']) && !empty($data['url_media'])) {
                    Banner::crear($data);
                }
            } catch (\Throwable $e) {
                error_log("Error al crear banner: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/admin/banners?status=created');
        exit;
    }

    public function delete()
    {
        $this->requireAdmin();
        $id = $_GET['id'] ?? null;
        if ($id && class_exists('App\Models\Banner')) {
            Banner::eliminar((int)$id);
        }

        header('Location: /yaconecta/public/admin/banners?status=deleted');
        exit;
    }
}

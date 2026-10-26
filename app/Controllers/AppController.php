<?php

namespace App\Controllers;

use App\Models\AppItem;

class AppController {

    // --- Vista Pública de Apps ---
    public function index() {
        $categoria = trim($_GET['cat'] ?? $_GET['category'] ?? '');
        $busqueda  = trim($_GET['search'] ?? $_GET['q'] ?? '');

        if (method_exists(AppItem::class, 'obtenerTodos')) {
            $apps = AppItem::obtenerTodos($busqueda, $categoria, 'activa');
            $appsParaCategorias = AppItem::obtenerTodos('', '', 'activa');
        } else {
            $apps = [];
            $appsParaCategorias = [];
        }
        $aplicativos = $apps;
        $categoriasApps = array_values(array_unique(array_filter(array_column($appsParaCategorias, 'category'))));
        sort($categoriasApps, SORT_NATURAL | SORT_FLAG_CASE);
        $pageTitle = 'Ya.Net | Aplicaciones Corporativas';

        $basePath = dirname(__DIR__, 2);
        
        if (file_exists($basePath . '/views/apps-yajuego/index.php')) {
            require_once $basePath . '/views/apps-yajuego/index.php';
        } elseif (file_exists($basePath . '/views/apps/index.php')) {
            require_once $basePath . '/views/apps/index.php';
        } else {
            die("Error: No se encontró la vista de aplicativos.");
        }
    }

    // --- Panel Admin Apps (GET /admin/apps) ---
    public function admin() {
        $busqueda  = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoria = trim($_GET['cat'] ?? '');
        $estado    = trim($_GET['estado'] ?? '');

        if (method_exists(AppItem::class, 'obtenerTodos')) {
            $apps = AppItem::obtenerTodos($busqueda, $categoria, $estado);
            $appsParaFiltros = AppItem::obtenerTodos();
            $resumenApps = AppItem::obtenerResumen();
        } else {
            $apps = [];
            $appsParaFiltros = [];
            $resumenApps = ['total'=>0,'activas'=>0,'inactivas'=>0,'categorias'=>0];
        }
        $categoriasApps = array_values(array_unique(array_filter(array_column($appsParaFiltros, 'category'))));
        sort($categoriasApps, SORT_NATURAL | SORT_FLAG_CASE);
        $pageTitle = 'Ya.Net | Administración Aplicaciones Corporativas';

        $basePath = dirname(__DIR__, 2);
        require_once $basePath . '/views/admin/apps/index.php';
    }

    // --- Guardar / Actualizar App (POST /admin/apps/store) ---
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            $data = [
                'name'        => trim($_POST['name'] ?? $_POST['nombre'] ?? ''),
                'url'         => trim($_POST['url'] ?? ''),
                'category'    => trim($_POST['category'] ?? $_POST['categoria'] ?? 'General'),
                'description' => trim($_POST['description'] ?? $_POST['descripcion'] ?? ''),
                'is_active'   => ($_POST['is_active'] ?? '1') === '1'
            ];

            try {
                if ($id && method_exists(AppItem::class, 'actualizar')) {
                    AppItem::actualizar((int)$id, $data);
                    header("Location: /yaconecta/public/admin/apps?msg=updated");
                    exit;
                } else {
                    AppItem::crear($data);
                    header("Location: /yaconecta/public/admin/apps?msg=created");
                    exit;
                }
            } catch (\Throwable $e) {
                die("<h3>Error al guardar en la Base de Datos:</h3><p>" . $e->getMessage() . "</p>");
            }
        }

        header("Location: /yaconecta/public/admin/apps?msg=error");
        exit;
    }

    // --- Eliminar App (GET/POST /admin/apps/delete) ---
    public function delete() {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if ($id && method_exists(AppItem::class, 'eliminar')) {
            AppItem::eliminar((int)$id);
        }

        header("Location: /yaconecta/public/admin/apps?msg=deleted");
        exit;
    }
}

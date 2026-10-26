<?php

namespace App\Controllers;

use App\Models\Clasificado;

class ClasificadoController
{
    private function verificarPermisoAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $roleId = isset($_SESSION['usuario']['role_id']) ? (int) $_SESSION['usuario']['role_id'] : 0;

        if (!in_array($roleId, [1, 2], true)) {
            header('HTTP/1.1 403 Forbidden');
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>
                    <h2 style='color:#0A2540;'>Acceso Restringido</h2>
                    <p style='color:#666;'>Solo los administradores pueden moderar clasificados.</p>
                    <a href='/yaconecta/public/clasificados' style='color:#FF6B00; font-weight:bold;'>← Volver a Clasificados</a>
                  </div>";
            exit;
        }
    }

    public function index(): void
    {
        $todosLosAnuncios = Clasificado::obtenerAprobados();
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoriaFiltro = trim($_GET['categoria'] ?? '');
        $orden = trim($_GET['orden'] ?? 'recientes');
        $anuncios = array_values(array_filter($todosLosAnuncios, static function (array $item) use ($busqueda, $categoriaFiltro): bool {
            $text = implode(' ', [(string)($item['title'] ?? ''), (string)($item['description'] ?? ''), (string)($item['usuario_nombre'] ?? ''), (string)($item['category'] ?? '')]);
            return ($busqueda === '' || stripos($text, $busqueda) !== false)
                && ($categoriaFiltro === '' || ($item['category'] ?? '') === $categoriaFiltro);
        }));
        if ($orden === 'precio_asc') usort($anuncios, static fn(array $a, array $b): int => (float)($a['price'] ?? PHP_FLOAT_MAX) <=> (float)($b['price'] ?? PHP_FLOAT_MAX));
        elseif ($orden === 'precio_desc') usort($anuncios, static fn(array $a, array $b): int => (float)($b['price'] ?? 0) <=> (float)($a['price'] ?? 0));
        else usort($anuncios, static fn(array $a, array $b): int => (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0));
        $categorias = array_values(array_unique(array_filter(array_map(static fn(array $item): string => trim((string)($item['category'] ?? '')), $todosLosAnuncios))));
        sort($categorias, SORT_NATURAL | SORT_FLAG_CASE);
        $conteoCategorias = [];
        foreach ($todosLosAnuncios as $item) {
            $category = trim((string)($item['category'] ?? 'Otros')) ?: 'Otros';
            $conteoCategorias[$category] = ($conteoCategorias[$category] ?? 0) + 1;
        }
        $totalAnuncios = count($todosLosAnuncios);
        $pageTitle = 'Ya.Net | Entre Nosotros (Clasificados)';

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'clasificados' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function store(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagenNombre = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ext, $permitidas)) {
                    $directorio = dirname(__DIR__, 2) . '/public/uploads/clasificados/';
                    if (!is_dir($directorio)) {
                        mkdir($directorio, 0777, true);
                    }
                    $imagenNombre = 'clasificado_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $directorio . $imagenNombre);
                }
            }

            $userId = $_SESSION['usuario']['id'] ?? 1;

            $datos = [
                'user_id'      => $userId,
                'title'        => $_POST['title'] ?? '',
                'description'  => $_POST['description'] ?? '',
                'category'     => $_POST['category'] ?? 'otro',
                'price'        => $_POST['price'] ?? null,
                'contact_info' => $_POST['contact_info'] ?? '',
                'image_path'   => $imagenNombre
            ];

            if (!empty($datos['title']) && !empty($datos['contact_info'])) {
                Clasificado::crear($datos);
            }
        }

        header('Location: /yaconecta/public/clasificados?status=submitted');
        exit;
    }

    public function admin(): void
    {
        $this->verificarPermisoAdmin();

        $todosLosAnuncios = Clasificado::obtenerTodos();
        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoriaFiltro = trim($_GET['categoria'] ?? '');
        $estadoFiltro = trim($_GET['estado'] ?? '');
        $sedeFiltro = trim($_GET['sede'] ?? '');
        $anuncios = array_values(array_filter($todosLosAnuncios, static function (array $item) use ($busqueda, $categoriaFiltro, $estadoFiltro): bool {
            $text = implode(' ', [(string)($item['title'] ?? ''), (string)($item['description'] ?? ''), (string)($item['usuario_nombre'] ?? ''), (string)($item['category'] ?? '')]);
            return ($busqueda === '' || stripos($text, $busqueda) !== false)
                && ($categoriaFiltro === '' || ($item['category'] ?? '') === $categoriaFiltro)
                && ($estadoFiltro === '' || ($item['status'] ?? '') === $estadoFiltro);
        }));
        $categoriasClasificados = array_values(array_unique(array_filter(array_map(static fn(array $item): string => trim((string)($item['category'] ?? '')), $todosLosAnuncios))));
        sort($categoriasClasificados, SORT_NATURAL | SORT_FLAG_CASE);
        $conteoCategorias = [];
        foreach ($todosLosAnuncios as $item) {
            $category = trim((string)($item['category'] ?? 'Otros')) ?: 'Otros';
            $conteoCategorias[$category] = ($conteoCategorias[$category] ?? 0) + 1;
        }
        arsort($conteoCategorias);
        $totalPublicaciones = count($todosLosAnuncios);
        $totalPendientes = count(array_filter($todosLosAnuncios, static fn(array $item): bool => ($item['status'] ?? '') === 'pending'));
        $totalPublicadas = count(array_filter($todosLosAnuncios, static fn(array $item): bool => ($item['status'] ?? '') === 'approved'));
        $totalRechazadas = count(array_filter($todosLosAnuncios, static fn(array $item): bool => ($item['status'] ?? '') === 'rejected'));
        $publicacionesRecientes = array_slice($todosLosAnuncios, 0, 3);
        $pageTitle = 'Ya.Net | Administración de Clasificados Internos';

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'clasificados' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    public function updateStatus(): void
    {
        $this->verificarPermisoAdmin();

        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && in_array($status, ['approved', 'rejected'])) {
            Clasificado::cambiarEstado((int)$id, $status);
        }

        header('Location: /yaconecta/public/admin/clasificados?status=updated');
        exit;
    }

    public function delete(): void
    {
        $this->verificarPermisoAdmin();

        $id = $_GET['id'] ?? null;
        if ($id) {
            Clasificado::eliminar((int)$id);
        }

        header('Location: /yaconecta/public/admin/clasificados?status=deleted');
        exit;
    }
}

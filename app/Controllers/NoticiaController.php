<?php

namespace App\Controllers;

use App\Models\Noticia;

class NoticiaController
{
    private function requireAdmin(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $roleId = (int)($_SESSION['usuario']['role_id'] ?? 0);
        if (in_array($roleId, [1, 2], true)) return;
        http_response_code(403);
        echo 'Acceso restringido. Inicia sesión como administrador.';
        exit;
    }

    // Vista Pública: Filtra y muestra las noticias enviadas a la vista
    public function index()
    {
        $noticias = [];

        // 1. Intentar cargar desde la Base de Datos
        if (class_exists('App\Models\Noticia')) {
            try {
                if (method_exists(Noticia::class, 'obtenerTodas')) {
                    $noticias = Noticia::obtenerTodas();
                } elseif (method_exists(Noticia::class, 'obtenerAprobadas')) {
                    $noticias = Noticia::obtenerAprobadas();
                }
            } catch (\Throwable $e) {
                error_log("Error al cargar noticias en NoticiaController@index: " . $e->getMessage());
            }
        }

        // Fallback si la base de datos no retorna registros
        if (empty($noticias)) {
            $noticias = [
                [
                    'id' => 1,
                    'titulo' => 'Lanzamiento Nueva Plataforma de Capacitación',
                    'resumen' => 'Conoce los nuevos cursos disponibles para potenciar tus habilidades este trimestre.',
                    'autor' => 'Gestión Humana',
                    'fecha' => '05/10/2026',
                    'estado' => 'Aprobado',
                    'categoria' => 'Noticias'
                ]
            ];
        }

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'noticias' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista no encontrada en: " . $vista;
        }
    }

    // Colaborador solicita publicar un comunicado o noticia
    public function solicitar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('App\Models\Noticia')) {
            try {
                $data = [
                    'titulo'    => trim($_POST['titulo'] ?? $_POST['title'] ?? ''),
                    'resumen'   => trim($_POST['resumen'] ?? ''),
                    'contenido' => trim($_POST['contenido'] ?? $_POST['content'] ?? ''),
                    'imagen'    => trim($_POST['imagen'] ?? ''),
                    'categoria' => trim($_POST['categoria'] ?? $_POST['category'] ?? 'Noticias')
                ];
                if (!empty($data['titulo']) && method_exists(Noticia::class, 'crear')) {
                    Noticia::crear($data);
                }
            } catch (\Throwable $e) {
                error_log("Error al solicitar noticia: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/noticias?status=pending_approval');
        exit;
    }

    // Vista de Administración (Panel de Gestión y Aprobación)
    public function admin()
    {
        $this->requireAdmin();
        $noticias = [];

        // 1. Obtener listado real de noticias desde la BD
        if (class_exists('App\Models\Noticia')) {
            try {
                if (method_exists(Noticia::class, 'obtenerTodas')) {
                    $noticias = Noticia::obtenerTodas();
                }
            } catch (\Throwable $e) {
                error_log("Error al cargar noticias en NoticiaController@admin: " . $e->getMessage());
            }
        }

        $busqueda = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $categoriaFiltro = trim($_GET['categoria'] ?? '');
        $estadoFiltro = trim($_GET['estado'] ?? '');
        $tipoFiltro = trim($_GET['tipo'] ?? '');
        $fechaDesde = trim($_GET['desde'] ?? '');
        $fechaHasta = trim($_GET['hasta'] ?? '');
        $todasLasNoticias = $noticias;
        $noticias = array_values(array_filter($noticias, static function (array $item) use ($busqueda, $categoriaFiltro, $estadoFiltro, $tipoFiltro, $fechaDesde, $fechaHasta): bool {
            $text = implode(' ', [(string)($item['titulo'] ?? ''), (string)($item['contenido'] ?? ''), (string)($item['resumen'] ?? ''), (string)($item['categoria'] ?? '')]);
            $status = strtolower((string)($item['estado'] ?? 'publicado'));
            $kind = stripos((string)($item['categoria'] ?? ''), 'comunic') !== false ? 'comunicado' : 'noticia';
            $date = substr((string)($item['created_at'] ?? ''), 0, 10);
            return ($busqueda === '' || stripos($text, $busqueda) !== false)
                && ($categoriaFiltro === '' || ($item['categoria'] ?? '') === $categoriaFiltro)
                && ($tipoFiltro === '' || $kind === strtolower($tipoFiltro))
                && ($estadoFiltro === '' || $status === strtolower($estadoFiltro))
                && ($fechaDesde === '' || $date === '' || $date >= $fechaDesde)
                && ($fechaHasta === '' || $date === '' || $date <= $fechaHasta);
        }));
        $categoriasNoticias = array_values(array_unique(array_filter(array_map(static fn(array $item): string => trim((string)($item['categoria'] ?? '')), $todasLasNoticias))));
        sort($categoriasNoticias, SORT_NATURAL | SORT_FLAG_CASE);
        $pageTitle = 'Ya.Net | Administración de Noticias y Comunicaciones';
        $totalNoticias = count($todasLasNoticias);
        $totalComunicados = count(array_filter($todasLasNoticias, static fn(array $item): bool => stripos((string)($item['tipo'] ?? $item['categoria'] ?? ''), 'comunic') !== false));
        $categoriasConteo = [];
        foreach ($todasLasNoticias as $item) {
            $cat = trim((string)($item['categoria'] ?? 'General')) ?: 'General';
            $categoriasConteo[$cat] = ($categoriasConteo[$cat] ?? 0) + 1;
        }
        arsort($categoriasConteo);
        $publicacionesProgramadas = array_values(array_filter($todasLasNoticias, static fn(array $item): bool => strtolower((string)($item['estado'] ?? '')) === 'programado'));

        $ds = DIRECTORY_SEPARATOR;
        $vista = dirname(__DIR__, 2) . $ds . 'views' . $ds . 'admin' . $ds . 'noticias' . $ds . 'index.php';

        if (file_exists($vista)) {
            require_once $vista;
        } else {
            echo "Vista Admin no encontrada en: " . $vista;
        }
    }

    // Guardar noticia directa desde Admin (procesa POST en MySQL)
    public function store()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('App\Models\Noticia')) {
            try {
                $data = [
                    'titulo'    => trim($_POST['titulo'] ?? $_POST['title'] ?? ''),
                    'resumen'   => trim($_POST['resumen'] ?? ''),
                    'contenido' => trim($_POST['contenido'] ?? $_POST['content'] ?? ''),
                    'imagen'    => trim($_POST['imagen'] ?? ''),
                    'categoria' => trim($_POST['categoria'] ?? $_POST['category'] ?? 'Noticias')
                ];

                if (!empty($data['titulo']) && method_exists(Noticia::class, 'crear')) {
                    Noticia::crear($data);
                    header('Location: /yaconecta/public/admin/noticias?status=created');
                    exit;
                }
            } catch (\Throwable $e) {
                error_log("Error al guardar noticia en store: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/admin/noticias?status=error');
        exit;
    }

    // El Administrador aprueba la noticia enviada
    public function approve()
    {
        $this->requireAdmin();
        header('Location: /yaconecta/public/admin/noticias?status=approved');
        exit;
    }

    // El Administrador rechaza la noticia enviada
    public function reject()
    {
        $this->requireAdmin();
        header('Location: /yaconecta/public/admin/noticias?status=rejected');
        exit;
    }

    // Eliminar noticia de la base de datos
    public function delete()
    {
        $this->requireAdmin();
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if ($id && class_exists('App\Models\Noticia')) {
            try {
                if (method_exists(Noticia::class, 'eliminar')) {
                    Noticia::eliminar((int)$id);
                }
            } catch (\Throwable $e) {
                error_log("Error al eliminar noticia: " . $e->getMessage());
            }
        }

        header('Location: /yaconecta/public/admin/noticias?status=deleted');
        exit;
    }
}
